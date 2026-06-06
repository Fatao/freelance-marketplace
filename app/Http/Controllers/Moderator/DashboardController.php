<?php

namespace App\Http\Controllers\Moderator;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Complaint;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'pending_orders'     => Order::where('status', 'on_moderation')->count(),
            'pending_complaints' => Complaint::where('status', 'pending')->count(),
            'approved_today'     => Order::where('status', 'published')
                ->whereDate('published_at', today())->count(),
            'rejected_today'     => Order::where('status', 'rejected')
                ->whereDate('updated_at', today())->count(),
        ];

        $pendingOrders = Order::with(['client.clientProfile', 'category'])
            ->where('status', 'on_moderation')
            ->orderBy('created_at')
            ->limit(10)
            ->get();

        return view('moderator.dashboard', compact('stats', 'pendingOrders'));
    }
}