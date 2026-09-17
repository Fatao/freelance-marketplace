<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Models\OrderApplication;
use App\Models\ExternalOrder;
use App\Models\CrawlerLog;

class ReportService
{
    public function getStats(string $from, string $to): array
    {
        return [
            'orders_published'   => Order::where('status', 'published')
                ->whereBetween('published_at', [$from, $to])->count(),
            'orders_completed'   => Order::where('status', 'completed')
                ->whereBetween('updated_at', [$from, $to])->count(),
            'orders_moderation'  => Order::where('status', 'on_moderation')
                ->whereBetween('created_at', [$from, $to])->count(),
            'orders_in_progress' => Order::where('status', 'in_progress')
                ->whereBetween('updated_at', [$from, $to])->count(),
            'applications'       => OrderApplication::whereBetween('created_at', [$from, $to])->count(),
            'external_found'     => ExternalOrder::whereBetween('discovered_at', [$from, $to])->count(),
            'crawler_errors'     => CrawlerLog::whereBetween('started_at', [$from, $to])->sum('errors'),
            'new_users'          => User::whereBetween('created_at', [$from, $to])->count(),
        ];
    }

    public function exportCsv(array $rows, string $filename)
    {
        $handle = fopen('php://temp', 'r+');
        fputs($handle, "\xEF\xBB\xBF");
        foreach ($rows as $row) {
            fputcsv($handle, $row, ';');
        }
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function exportXlsx(array $rows, string $filename)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Отчёт');

        foreach ($rows as $rowIndex => $row) {
            foreach ($row as $colIndex => $value) {
                $sheet->setCellValueByColumnAndRow($colIndex + 1, $rowIndex + 1, $value);
            }
        }

        $sheet->getStyle('A1:H1')->getFont()->setBold(true);
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $path   = storage_path("app/public/{$filename}");
        $writer->save($path);

        return response()->download($path, $filename)->deleteFileAfterSend();
    }
}