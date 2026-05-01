<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class InhouseExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithColumnFormatting
{
    use Exportable;

    private $orders;

    public function __construct(Collection $orders)
    {
        $this->orders = $orders;
    }

    public function collection()
    {
        return $this->orders;
    }

    public function map($item): array
    {
        return [
            $item->id,
            $item->type_id === 12
                ? 'WhatsApp'
                : ($item->site->title ?? $item->line_number ?? ($item->site_id === null && $item->source_id > 0
                ? optional($item->source)->name
                : 'Не определено')),
            optional($item->type)->name,
            trim(
                (optional($item->status)->name ?? '') .
                ($item->status_id === 7 && $item->trash ? " ({$item->trash->name})" : '') .
                ($item->status_id === 6 && $item->arrival_status ? " ({$item->arrival_status->name})" : '')
            ),
            $item->utm_source,
            $item->utm_medium,
            $item->utm_campaign,
            $item->utm_content,
            $item->utm_term,
            optional($item->region)->name,
            $item->created_at ? Carbon::parse($item->created_at)->format('d.m.Y H:i') : '',
            trim(($item->mark->name ?? '') . ' ' . ($item->model->name ?? '') . ' ' . ($item->complectation ?? '')),
            $item->client_name,
            $item->phone,
            $item->will_arrive ? Carbon::parse($item->will_arrive)->format('d.m.Y') : '',
            $item->retries,
            $item->updated_at ? Carbon::parse($item->updated_at)->format('d.m.Y H:i') : '',
            $item->callback ? Carbon::parse($item->callback)->format('d.m.Y H:i') : '',
            $item->comment,
            $item->general_comment,
        ];
    }

    public function headings(): array
    {
        return [
            'ID',
            'Источник',
            'Тип',
            'Статус',
            'UTM источник',
            'UTM тип трафика',
            'UTM кампания',
            'UTM контент',
            'UTM ключевое слово',
            'Регион',
            'Дата создания',
            'Авто',
            'Имя клиента',
            'Телефон',
            'Приедет',
            'Повторы',
            'Дата обновления',
            'Обратный звонок',
            'Комментарий',
            'Общий комментарий',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,
            'B' => 25,
            'C' => 20,
            'D' => 22,
            'E' => 16,
            'F' => 16,
            'G' => 16,
            'H' => 16,
            'I' => 16,
            'J' => 26,
            'K' => 20,
            'L' => 40,
            'M' => 30,
            'N' => 18,
            'O' => 16,
            'P' => 13,
            'Q' => 14,
            'R' => 16,
            'S' => 65,
            'T' => 50,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'K' => NumberFormat::FORMAT_DATE_DATETIME,
            'O' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'Q' => NumberFormat::FORMAT_DATE_DATETIME,
            'R' => NumberFormat::FORMAT_DATE_DATETIME,
        ];
    }
}
