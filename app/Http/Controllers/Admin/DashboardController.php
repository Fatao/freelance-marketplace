<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\OrderApplication;
use App\Models\ExternalOrder;
use App\Models\CrawlerLog;
use App\Models\CrawlerSource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users_total'       => User::count(),
            'users_freelancer'  => User::where('role', 'freelancer')->count(),
            'users_client'      => User::where('role', 'client')->count(),
            'orders_published'  => Order::where('status', 'published')->count(),
            'orders_moderation' => Order::where('status', 'on_moderation')->count(),
            'orders_in_progress'=> Order::where('status', 'in_progress')->count(),
            'orders_completed'  => Order::where('status', 'completed')->count(),
            'applications_total'=> OrderApplication::count(),
            'external_total'    => ExternalOrder::count(),
            'external_new'      => ExternalOrder::where('status', 'new')->count(),
            'crawler_errors'    => CrawlerLog::sum('errors'),
            'sources_active'    => CrawlerSource::where('status', 'active')->count(),
        ];

        $popularCategories = Order::select('category_id', DB::raw('count(*) as total'))
            ->whereNotNull('category_id')
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->with('category')
            ->limit(5)
            ->get();

        $recentOrders = Order::with(['client', 'category'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $recentLogs = CrawlerLog::with('source')
            ->orderByDesc('started_at')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats', 'popularCategories', 'recentOrders', 'recentLogs'
        ));
    }

    public function reports(Request $request)
    {
        $from = $request->get('from', now()->startOfMonth()->format('Y-m-d'));
        $to   = $request->get('to', now()->format('Y-m-d'));

        $data = [
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

        $ordersByDay = Order::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as total')
            )
            ->whereBetween('created_at', [$from, $to])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.reports', compact('data', 'from', 'to', 'ordersByDay'));
    }

    public function export(Request $request)
    {
        $request->validate([
            'from'   => 'required|date',
            'to'     => 'required|date|after_or_equal:from',
            'format' => 'required|in:csv,xlsx',
        ]);

        $from   = $request->from;
        $to     = $request->to;
        $format = $request->format;

        $orders = Order::with(['client', 'category'])
            ->whereBetween('created_at', [$from, $to])
            ->get();

        $rows[] = ['ID', 'Название', 'Заказчик', 'Категория', 'Статус', 'Бюджет от', 'Бюджет до', 'Дата создания'];

        foreach ($orders as $o) {
            $rows[] = [
                $o->id,
                $o->title,
                $o->client->name,
                $o->category?->name ?? '—',
                $o->status,
                $o->budget_min,
                $o->budget_max,
                $o->created_at->format('d.m.Y'),
            ];
        }

        if ($format === 'csv') {
            return $this->exportCsv($rows, "report_{$from}_{$to}.csv");
        }

        return $this->exportXlsx($rows, "report_{$from}_{$to}.xlsx");
    }

    private function exportCsv(array $rows, string $filename)
    {
        $handle = fopen('php://temp', 'r+');
        // BOM for Excel UTF-8
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

    private function exportXlsx(array $rows, string $filename)
    {
        // Uses PhpSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Отчёт');

        foreach ($rows as $rowIndex => $row) {
            foreach ($row as $colIndex => $value) {
                $sheet->setCellValueByColumnAndRow($colIndex + 1, $rowIndex + 1, $value);
            }
        }

        // Bold header
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);

        // Auto width
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $path   = storage_path("app/public/{$filename}");
        $writer->save($path);

        return response()->download($path, $filename)->deleteFileAfterSend();
    }
}