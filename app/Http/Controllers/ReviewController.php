<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use App\Models\FreelancerProfile;
use App\Models\ClientProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Order $order)
    {
        abort_if(!$order->isCompleted(), 403);

        $data = $request->validate([
            'rating'    => 'required|integer|min:1|max:5',
            'comment'   => 'nullable|string|max:1000',
            'recipient_id' => 'required|exists:users,id',
        ]);

        // Check not already reviewed
        $exists = Review::where('order_id', $order->id)
            ->where('author_id', Auth::id())
            ->exists();
        abort_if($exists, 422);

        Review::create([
            'order_id'     => $order->id,
            'author_id'    => Auth::id(),
            'recipient_id' => $data['recipient_id'],
            'rating'       => $data['rating'],
            'comment'      => $data['comment'] ?? null,
        ]);

        // Recalculate recipient rating
        $this->recalcRating($data['recipient_id']);

        return back()->with('success', 'Отзыв оставлен.');
    }

    private function recalcRating(int $userId): void
    {
        $avg   = Review::where('recipient_id', $userId)->avg('rating');
        $count = Review::where('recipient_id', $userId)->count();

        FreelancerProfile::where('user_id', $userId)
            ->update(['rating' => round($avg, 2), 'reviews_count' => $count]);

        ClientProfile::where('user_id', $userId)
            ->update(['rating' => round($avg, 2), 'reviews_count' => $count]);
    }
}