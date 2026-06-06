<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\FreelancerProfile;
use App\Models\ClientProfile;
use App\Models\Skill;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // --- ADMIN ---
        $admin = User::create([
            'name'              => 'Администратор',
            'email'             => 'admin@marketplace.ru',
            'password'          => Hash::make('password'),
            'role'              => 'admin',
            'email_verified_at' => now(),
        ]);

        // --- MODERATOR ---
        $moderator = User::create([
            'name'              => 'Модератор',
            'email'             => 'moderator@marketplace.ru',
            'password'          => Hash::make('password'),
            'role'              => 'moderator',
            'email_verified_at' => now(),
        ]);

        // --- CLIENT ---
        $client = User::create([
            'name'              => 'Иван Заказчиков',
            'email'             => 'client@marketplace.ru',
            'password'          => Hash::make('password'),
            'role'              => 'client',
            'email_verified_at' => now(),
        ]);

        ClientProfile::create([
            'user_id'          => $client->id,
            'company_name'     => 'ООО «ТехПроект»',
            'description'      => 'Разрабатываем цифровые продукты для бизнеса',
            'phone'            => '+7 900 000-00-01',
            'telegram'         => '@client_test',
            'contact_verified' => true,
            'rating'           => 4.80,
            'reviews_count'    => 12,
        ]);

        // --- FREELANCER 1 ---
        $freelancer1 = User::create([
            'name'              => 'Алексей Разработчиков',
            'email'             => 'freelancer@marketplace.ru',
            'password'          => Hash::make('password'),
            'role'              => 'freelancer',
            'email_verified_at' => now(),
        ]);

        $fp1 = FreelancerProfile::create([
            'user_id'        => $freelancer1->id,
            'display_name'   => 'Алексей — Laravel разработчик',
            'specialization' => 'Веб-разработка',
            'experience'     => '5 лет коммерческой разработки на Laravel и Vue.js. Реализовал более 30 проектов.',
            'portfolio'      => 'https://github.com/alexdev',
            'hourly_rate'    => 2500.00,
            'phone'          => '+7 900 000-00-02',
            'telegram'       => '@freelancer_test',
            'rating'         => 4.90,
            'reviews_count'  => 24,
            'is_available'   => true,
        ]);

        $skills = Skill::whereIn('slug', ['laravel', 'vue-js', 'mysql', 'php', 'rest-api'])->get();
        $fp1->skills()->attach($skills->pluck('id'));

        // --- FREELANCER 2 ---
        $freelancer2 = User::create([
            'name'              => 'Мария Дизайнерова',
            'email'             => 'designer@marketplace.ru',
            'password'          => Hash::make('password'),
            'role'              => 'freelancer',
            'email_verified_at' => now(),
        ]);

        $fp2 = FreelancerProfile::create([
            'user_id'        => $freelancer2->id,
            'display_name'   => 'Мария — UI/UX дизайнер',
            'specialization' => 'UI/UX дизайн',
            'experience'     => '3 года работы с Figma и Adobe XD. Специализация — мобильные приложения.',
            'portfolio'      => 'https://behance.net/mariadesign',
            'hourly_rate'    => 1800.00,
            'phone'          => '+7 900 000-00-03',
            'telegram'       => '@designer_test',
            'rating'         => 4.70,
            'reviews_count'  => 15,
            'is_available'   => true,
        ]);

        $skills2 = Skill::whereIn('slug', ['figma', 'adobe-xd', 'wireframing', 'prototyping'])->get();
        $fp2->skills()->attach($skills2->pluck('id'));
    }
}