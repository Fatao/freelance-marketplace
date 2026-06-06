<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderApplication;
use App\Models\OrderWork;
use App\Models\OrderStatusHistory;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    public function index(Order $order)
    {
        abort_if($order->client_id !== Auth::id(), 403);

        $applications = $order->applications()
            ->with('freelancer.freelancerProfile')
            ->orderByDesc('created_at')
            ->get();

        // Mark as viewed
        $order->applications()->where('status', 'sent')->update(['status' => 'viewed']);

        return view('applications.index', compact('order', 'applications'));
    }

    public function store(Request $request, Order $order)
    {
        abort_if(!$order->isPublished(), 403);
        abort_if($order->applications()->where('freelancer_id', Auth::id())->exists(), 422);

        $data = $request->validate([
            'cover_letter'   => 'required|string|min:50',
            'proposed_price' => 'nullable|numeric|min:0',
            'proposed_days'  => 'nullable|integer|min:1',
        ]);

        $application = OrderApplication::create(array_merge($data, [
            'order_id'      => $order->id,
            'freelancer_id' => Auth::id(),
            'status'        => 'sent',
        ]));

        // Notify client
        app(NotificationService::class)->send(
            $order->client_id,
            'new_application',
            'Новый отклик на ваш заказ «' . $order->title . '»',
            ['order_id' => $order->id, 'application_id' => $application->id]
        );

        return redirect()->route('orders.show', $order)
            ->with('success', 'Отклик отправлен!');
    }

    public function accept(OrderApplication $application)
    {
        $order = $application->order;
        abort_if($order->client_id !== Auth::id(), 403);

        // Reject all others
        $order->applications()
            ->where('id', '!=', $application->id)
            ->update(['status' => 'rejected']);

        $application->update(['status' => 'accepted']);

        // Create work record
        OrderWork::create([
            'order_id'      => $order->id,
            'freelancer_id' => $application->freelancer_id,
            'status'        => 'in_progress',
            'started_at'    => now(),
        ]);

        $old = $order->status;
        $order->update(['status' => 'in_progress']);

        OrderStatusHistory::create([
            'order_id'   => $order->id,
            'changed_by' => Auth::id(),
            'old_status' => $old,
            'new_status' => 'in_progress',
            'comment'    => 'Принят отклик фрилансера ID ' . $application->freelancer_id,
        ]);

        // Notify freelancer
        app(NotificationService::class)->send(
            $application->freelancer_id,
            'application_accepted',
            'Ваш отклик принят на заказ «' . $order->title . '»',
            ['order_id' => $order->id]
        );

        return redirect()->route('applications.index', $order)
            ->with('success', 'Отклик принят, заказ в работе.');
    }

    public function reject(OrderApplication $application)
    {
        $order = $application->order;
        abort_if($order->client_id !== Auth::id(), 403);

        $application->update(['status' => 'rejected']);

        app(NotificationService::class)->send(
            $application->freelancer_id,
            'application_rejected',
            'Ваш отклик отклонён на заказ «' . $order->title . '»',
            ['order_id' => $order->id]
        );

        return back()->with('success', 'Отклик отклонён.');
    }

    public function withdraw(OrderApplication $application)
    {
        abort_if($application->freelancer_id !== Auth::id(), 403);
        abort_if($application->isAccepted(), 403);

        $application->update(['status' => 'withdrawn']);
        return back()->with('success', 'Отклик отозван.');
    }

    public function myApplications()
    {
        $applications = OrderApplication::where('freelancer_id', Auth::id())
            ->with('order.client.clientProfile')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('applications.my', compact('applications'));
    }
}