<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index(Order $order)
    {
        $this->authorizeAccess($order);

        $messages = $order->messages()->with('sender')->get();

        // Mark received messages as read
        $order->messages()
            ->where('sender_id', '!=', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('messages.index', compact('order', 'messages'));
    }

    public function store(Request $request, Order $order)
    {
        $this->authorizeAccess($order);

        $data = $request->validate([
            'body'        => 'required|string|max:5000',
            'attachments' => 'nullable|array',
        ]);

        OrderMessage::create([
            'order_id'  => $order->id,
            'sender_id' => Auth::id(),
            'body'      => $data['body'],
        ]);

        return back()->with('success', 'Сообщение отправлено.');
    }

    private function authorizeAccess(Order $order): void
    {
        $work = $order->work;
        $isClient     = Auth::id() === $order->client_id;
        $isFreelancer = $work && Auth::id() === $work->freelancer_id;

        abort_if(!$isClient && !$isFreelancer, 403);
    }
}