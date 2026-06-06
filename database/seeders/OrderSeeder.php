<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;
use App\Models\Category;
use App\Models\Skill;
use App\Models\OrderStatusHistory;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $client   = User::where('email', 'client@marketplace.ru')->first();
        $webCat   = Category::where('slug', 'web-development')->first();
        $uiCat    = Category::where('slug', 'ui-ux')->first();

        // --- ORDER 1: Published ---
        $order1 = Order::create([
            'client_id'      => $client->id,
            'category_id'    => $webCat->id,
            'title'          => 'Разработка корпоративного сайта на Laravel',
            'description'    => 'Нужен корпоративный сайт с CMS, личным кабинетом сотрудника и интеграцией с 1С. Дизайн предоставим.',
            'budget_min'     => 80000,
            'budget_max'     => 150000,
            'payment_format' => 'fixed',
            'deadline'       => now()->addDays(45),
            'status'         => 'published',
            'published_at'   => now(),
        ]);

        $order1->skills()->attach(
            Skill::whereIn('slug', ['laravel', 'php', 'mysql'])->pluck('id')
        );

        OrderStatusHistory::create([
            'order_id'   => $order1->id,
            'changed_by' => $client->id,
            'old_status' => 'draft',
            'new_status' => 'on_moderation',
            'comment'    => 'Отправлен на модерацию',
        ]);

        OrderStatusHistory::create([
            'order_id'   => $order1->id,
            'changed_by' => User::where('role', 'moderator')->first()->id,
            'old_status' => 'on_moderation',
            'new_status' => 'published',
            'comment'    => 'Заказ одобрен',
        ]);

        // --- ORDER 2: Published ---
        $order2 = Order::create([
            'client_id'      => $client->id,
            'category_id'    => $uiCat->id,
            'title'          => 'Дизайн мобильного приложения для доставки еды',
            'description'    => 'Нужен полный дизайн в Figma: онбординг, каталог, корзина, профиль, история заказов. 15–20 экранов.',
            'budget_min'     => 30000,
            'budget_max'     => 60000,
            'payment_format' => 'fixed',
            'deadline'       => now()->addDays(21),
            'status'         => 'published',
            'published_at'   => now()->subDays(2),
        ]);

        $order2->skills()->attach(
            Skill::whereIn('slug', ['figma', 'wireframing', 'prototyping'])->pluck('id')
        );

        // --- ORDER 3: Draft ---
        Order::create([
            'client_id'      => $client->id,
            'category_id'    => $webCat->id,
            'title'          => 'Парсер данных с маркетплейсов',
            'description'    => 'Нужен парсер для сбора данных о ценах конкурентов с Wildberries и Ozon.',
            'budget_min'     => 15000,
            'budget_max'     => 25000,
            'payment_format' => 'fixed',
            'deadline'       => now()->addDays(14),
            'status'         => 'draft',
        ]);

        // --- ORDER 4: On moderation ---
        Order::create([
            'client_id'      => $client->id,
            'category_id'    => $webCat->id,
            'title'          => 'Разработка Telegram-бота для записи клиентов',
            'description'    => 'Бот для салона красоты: запись на услугу, выбор мастера, напоминания.',
            'budget_min'     => 20000,
            'budget_max'     => 40000,
            'payment_format' => 'fixed',
            'deadline'       => now()->addDays(30),
            'status'         => 'on_moderation',
        ]);
    }
}