<?php

namespace App\Http\Controllers\Import;

use App\Events\OrderCreated;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Propaganistas\LaravelPhone\PhoneNumber;
use Throwable;
use Illuminate\Support\Facades\Log;
use App\Models\Order;

class ImportCsvController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'file'        => 'required|file|mimes:csv,txt',
            'showroom_id' => 'required|integer',
            'site_id'     => 'required|integer',
        ]);

        $file = fopen($request->file('file')->getRealPath(), 'r');

        while (($row = fgetcsv($file, 0, ';')) !== false) {
            try {
                $order = new Order();

                $order->client_name = $row[0] ?? 'Новый клиент';
                $order->phone       = $this->formatPhone($row[1] ?? null);
                $order->entry_point = $row[2] ?? null;

                // from request
                $order->showroom_id = $request->showroom_id;
                $order->site_id     = $request->site_id;

                $order->status_id = 1;
                $order->source_id = 67;

                $order->save();
                OrderCreated::dispatch($order);

            } catch (Throwable $e) {
                Log::error('CSV import row error', [
                    'row' => $row,
                    'error' => $e->getMessage()
                ]);
            }
        }

        fclose($file);

        return response()->json([
            'status' => 'success',
            'message' => 'CSV imported successfully'
        ]);
    }

    private function formatPhone(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }

        try {
            return PhoneNumber::make($phone, 'RU')
                ->formatE164();
        } catch (Throwable $e) {
            return preg_replace('/[^0-9+]/', '', $phone);
        }
    }
}
