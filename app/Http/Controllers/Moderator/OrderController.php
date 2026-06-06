
<?php

namespace App\Http\Controllers\Moderator;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['client.clientProfile', 'category'])
            ->where('status', 'on_moderation')
            ->orderBy('created_at')
            ->paginate(20);

        return view('moderator.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['client.clientProfile', 'category', 'skills', 'statusHistory.changedBy']);
        return view('moderator.orders.show', compact('order'));
    }

    public function approve(Order $order)
    {
        $order->update(['status' => 'published', 'published_at' => now()]);

        OrderStatusHistory::create([
            'order_id'   => $order->id,
            'changed_by' => Auth::id(),
            'old_status' => 'on_moderation',
            'new_status' => 'published',
            'comment'    => 'Одобрен модератором',
        ]);

        app(NotificationService::class)->send(
            $order->client_id,
            'order_published',
            'Ваш заказ «' . $order->title . '» опубликован.',
            ['order_id' => $order->id]
        );

        app(NotificationService::class)->notifyMatchingSavedSearches($order->id);

        return redirect()->route('moderator.orders.index')->with('success', 'Заказ опубликован.');
    }

    public function reject(Request $request, Order $order)
    {
        $request->validate(['reason' => 'required|string|max:500']);

        $order->update(['status' => 'rejected', 'rejection_reason' => $request->reason]);

        OrderStatusHistory::create([
            'order_id'   => $order->id,
            'changed_by' => Auth::id(),
            'old_status' => 'on_moderation',
            'new_status' => 'rejected',
            'comment'    => $request->reason,
        ]);

        app(NotificationService::class)->send(
            $order->client_id,
            'order_rejected',
            'Ваш заказ «' . $order->title . '» отклонён. Причина: ' . $request->reason,
            ['order_id' => $order->id]
        );

        return redirect()->route('moderator.orders.index')->with('success', 'Заказ отклонён.');
    }

    public function revise(Request $request, Order $order)
    {
        $request->validate(['reason' => 'required|string|max:500']);

        $order->update(['status' => 'draft', 'rejection_reason' => $request->reason]);

        OrderStatusHistory::create([
            'order_id'   => $order->id,
            'changed_by' => Auth::id(),
            'old_status' => 'on_moderation',
            'new_status' => 'draft',
            'comment'    => 'На доработку: ' . $request->reason,
        ]);

        app(NotificationService::class)->send(
            $order->client_id,
            'order_needs_revision',
            'Заказ «' . $order->title . '» отправлен на доработку.',
            ['order_id' => $order->id]
        );

        return redirect()->route('moderator.orders.index')->with('success', 'Отправлен на доработку.');
    }
}