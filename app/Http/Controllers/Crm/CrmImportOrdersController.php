<?php

namespace App\Http\Controllers\Crm;

use App\Events\OrderExcelCreated;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Propaganistas\LaravelPhone\PhoneNumber;
use Throwable;

class CrmImportOrdersController extends Controller
{
    private const HEADER_ALIASES = [
        'phone' => ['phone', 'телефон', 'тел', 'mobile', 'mobile_tel', 'сотовый'],
        'name' => ['name', 'имя', 'client_name', 'клиент', 'фио', 'ф.и.о.'],
        'utm_source' => ['utm_source', 'utm source', 'utm', 'источник'],
    ];

    /**
     * showroom_id => site_id для импорта с /crm/import-orders.
     * Заполните site_id для каждого салона.
     */
    private const SHOWROOM_SITE_MAP = [       
        1 => 5,
        2 => 7751,
        4 => 7750,
        5 => 7753,       
        10 => 7752,        
        15 => 7754,
        17 => 7755,
    ];

    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt',
            'showroom_id' => 'required|integer|exists:showrooms,id',
        ]);

        $parsed = $this->parseSpreadsheet($request->file('file')->getRealPath());

        if ($parsed === null) {
            return response()->json(['message' => 'Файл пуст'], 422);
        }

        $result = $this->importRows(
            $parsed['dataRows'],
            $parsed['hasHeader'],
            $parsed['columnMap'],
            (int) $request->showroom_id
        );

        return response()->json([
            'status' => 'success',
            'imported' => $result['imported'],
            'skipped' => $result['skipped'],
            'errors' => $result['errors'],
        ]);
    }

    /**
     * @return array{hasHeader: bool, columnMap: array, dataRows: array}|null
     */
    private function parseSpreadsheet(string $filePath): ?array
    {
        $spreadsheet = IOFactory::load($filePath);
        $rows = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        if (count($rows) === 0) {
            return null;
        }

        $firstRow = array_shift($rows);
        $hasHeader = $this->isHeaderRow($firstRow);
        $columnMap = $this->resolveColumns($hasHeader ? $firstRow : null);
        $dataRows = $hasHeader ? $rows : array_merge([$firstRow], $rows);

        return [
            'hasHeader' => $hasHeader,
            'columnMap' => $columnMap,
            'dataRows' => $dataRows,
        ];
    }

    /**
     * @return array{imported: int, skipped: int, errors: array<int, array{line: int, message: string}>}
     */
    private function importRows(
        array $dataRows,
        bool $hasHeader,
        array $columnMap,
        int $showroomId
    ): array {
        $imported = 0;
        $skipped = 0;
        $errors = [];

        foreach ($dataRows as $index => $row) {
            $line = $hasHeader ? $index + 2 : $index + 1;

            try {
                $rowResult = $this->importRow($row, $columnMap, $showroomId, $line);

                if ($rowResult === 'skipped') {
                    $skipped++;
                    continue;
                }

                if ($rowResult === 'imported') {
                    $imported++;
                    continue;
                }

                $errors[] = $rowResult;
            } catch (Throwable $e) {
                Log::error('CRM Excel order import row error', [
                    'line' => $line,
                    'row' => $row,
                    'error' => $e->getMessage(),
                ]);
                $errors[] = ['line' => $line, 'message' => $e->getMessage()];
            }
        }

        return [
            'imported' => $imported,
            'skipped' => $skipped,
            'errors' => $errors,
        ];
    }

    /**
     * @return 'imported'|'skipped'|array{line: int, message: string}
     */
    private function importRow(array $row, array $columnMap, int $showroomId, int $line)
    {
        $phone = $this->cell($row, $columnMap['phone']);
        $name = $this->cell($row, $columnMap['name']);
        $utmSource = $this->cell($row, $columnMap['utm_source']);

        if (!$phone && !$name && !$utmSource) {
            return 'skipped';
        }

        if (!$phone) {
            return ['line' => $line, 'message' => 'Не указан телефон'];
        }

        $order = $this->createOrder($phone, $name, $utmSource, $showroomId);
        $order->save();

        OrderExcelCreated::dispatch($order);

        return 'imported';
    }

    private function createOrder(
        string $phone,
        ?string $name,
        ?string $utmSource,
        int $showroomId
    ): Order {
        $order = new Order();
        $order->client_name = $name ?: 'Новый клиент';
        $order->phone = $this->formatPhone($phone);
        $order->utm_source = $utmSource ?: null;
        $order->showroom_id = $showroomId;
        $order->site_id = $this->resolveSiteId($showroomId);
        $order->status_id = 1;
        $order->source_id = 43;

        return $order;
    }

    private function resolveSiteId(int $showroomId): ?int
    {
        if (!array_key_exists($showroomId, self::SHOWROOM_SITE_MAP)) {
            return null;
        }

        $siteId = self::SHOWROOM_SITE_MAP[$showroomId];

        return $siteId !== null ? (int) $siteId : null;
    }

    private function isHeaderRow(array $row): bool
    {
        foreach ($row as $value) {
            $normalized = mb_strtolower(trim((string) $value));
            if ($normalized === '') {
                continue;
            }
            foreach (self::HEADER_ALIASES as $aliases) {
                if (in_array($normalized, $aliases, true)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function resolveColumns(?array $headerRow): array
    {
        $map = [
            'phone' => 'A',
            'name' => 'B',
            'utm_source' => 'C',
        ];

        if (!$headerRow) {
            return $map;
        }

        foreach ($headerRow as $col => $value) {
            $normalized = mb_strtolower(trim((string) $value));
            if ($normalized === '') {
                continue;
            }
            foreach (self::HEADER_ALIASES as $field => $aliases) {
                if (in_array($normalized, $aliases, true)) {
                    $map[$field] = $col;
                }
            }
        }

        return $map;
    }

    private function cell(array $row, string $col): ?string
    {
        $value = $row[$col] ?? null;
        if ($value === null || trim((string) $value) === '') {
            return null;
        }

        return trim((string) $value);
    }

    private function formatPhone(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }

        try {
            return PhoneNumber::make($phone, 'RU')->formatE164();
        } catch (Throwable $e) {
            return preg_replace('/[^0-9+]/', '', $phone) ?: null;
        }
    }
}
