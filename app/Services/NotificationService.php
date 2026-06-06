<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public function send(int $userId, string $type, string $message, array $data = []): void
    {
        Notification::create([
            'user_id' => $userId,
            'type'    => $type,
            'message' => $message,
            'data'    => $data,
        ]);

        $user = User::find($userId);

        if ($user && $user->email_verified_at) {
            try {
                Mail::raw(
                    $message,
                    fn ($m) => $m->to($user->email)
                                 ->subject('Уведомление — Маркетплейс')
                );
            } catch (\Exception $e) {
                \Log::warning('Notification email failed: '.$e->getMessage());
            }
        }
    }

    public function notifyMatchingSavedSearches(int $orderId): void
    {
        $order = \App\Models\Order::find($orderId);

        if (! $order) {
            return;
        }

        \App\Models\SavedSearch::with('user')
            ->chunk(100, function ($searches) use ($order) {
                foreach ($searches as $search) {
                    if ($search->matchesOrder($order)) {
                        $this->send(
                            $search->user_id,
                            'saved_search_match',
                            'Новый заказ по вашему сохранённому поиску «'
                                .$search->name.'»: '.$order->title,
                            [
                                'order_id' => $order->id,
                                'saved_search_id' => $search->id,
                            ]
                        );
                    }
                }
            });
    }

    public function notifyMatchingSavedSearchesExternal(
        string $title,
        array $skills
    ): void {
        \App\Models\SavedSearch::with('user')
            ->where(function ($q) {
                $q->whereNull('source')
                  ->orWhere('source', 'external');
            })
            ->chunk(100, function ($searches) use ($title, $skills) {

                foreach ($searches as $search) {
                    $matched = false;

                    if ($search->keywords) {
                        $matched = str_contains(
                            strtolower($title),
                            strtolower($search->keywords)
                        );
                    }

                    if (! $matched && $search->skills) {
                        $matched = count(array_intersect(
                            array_map('strtolower', $skills),
                            array_map('strtolower', $search->skills)
                        )) > 0;
                    }

                    if ($matched) {
                        $this->send(
                            $search->user_id,
                            'external_order_match',
                            'Краулер нашёл подходящий внешний заказ: «'
                                .$title.'»',
                            [
                                'saved_search_id' => $search->id,
                            ]
                        );
                    }
                }
            });
    }
}