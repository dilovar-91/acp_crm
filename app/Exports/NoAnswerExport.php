<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class NoAnswerExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths
{


    private $from;
    private $to;
    private $showoom_id;
    use Exportable;

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 10,
            'C' => 35,
            'D' => 20,
            'E' => 20,
            'F' => 20,
            'G' => 150,
        ];
    }



    public function map($order): array
    {
        return [
            $order->id,
            $order->showroom->name,
            $order->client_name,
            $order->phone,
            $order->status->name,
            $order->type->name,
            $order->status->comment,
        ];
    }


    public function headings(): array
    {
        return [
            ['ID', 'Салон', 'Клиент', 'Телефон', 'Статус', 'Тип', 'Комментарий'],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_TEXT,
            'G' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function __construct($from, $to, $showoom_id)
    {
        $this->from = $from;
        $this->to = $to;
        $this->showoom_id = $showoom_id;
    }

    /**
    * @return Collection
    */
    public function collection()
    {
        return Order::where('showroom_id', $this->showoom_id)->where('status_id', 3)->between($this->from, $this->to)->get();
    }
}
