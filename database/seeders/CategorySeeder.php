<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Разработка',
                'slug' => 'development',
                'description' => 'Веб, мобильная и десктопная разработка',
                'children' => [
                    ['name' => 'Веб-разработка',      'slug' => 'web-development'],
                    ['name' => 'Мобильные приложения', 'slug' => 'mobile'],
                    ['name' => 'Бэкенд',               'slug' => 'backend'],
                    ['name' => 'Фронтенд',             'slug' => 'frontend'],
                    ['name' => 'Базы данных',          'slug' => 'databases'],
                ],
            ],
            [
                'name' => 'Дизайн',
                'slug' => 'design',
                'description' => 'UI/UX, графика, брендинг',
                'children' => [
                    ['name' => 'UI/UX дизайн',    'slug' => 'ui-ux'],
                    ['name' => 'Графический дизайн', 'slug' => 'graphic-design'],
                    ['name' => 'Логотипы',         'slug' => 'logos'],
                ],
            ],
            [
                'name' => 'Контент и тексты',
                'slug' => 'content',
                'description' => 'Копирайтинг, переводы, SEO-тексты',
                'children' => [
                    ['name' => 'Копирайтинг', 'slug' => 'copywriting'],
                    ['name' => 'Переводы',    'slug' => 'translation'],
                    ['name' => 'SEO-тексты',  'slug' => 'seo-content'],
                ],
            ],
            [
                'name' => 'Маркетинг',
                'slug' => 'marketing',
                'description' => 'SMM, реклама, продвижение',
                'children' => [
                    ['name' => 'SMM',              'slug' => 'smm'],
                    ['name' => 'Контекстная реклама', 'slug' => 'ppc'],
                    ['name' => 'SEO',              'slug' => 'seo'],
                ],
            ],
            [
                'name' => 'Аналитика и данные',
                'slug' => 'analytics',
                'description' => 'Data science, BI, аналитика',
                'children' => [
                    ['name' => 'Data Science',  'slug' => 'data-science'],
                    ['name' => 'BI и отчёты',   'slug' => 'bi-reports'],
                ],
            ],
        ];

        foreach ($categories as $data) {
            $children = $data['children'] ?? [];
            unset($data['children']);

            $parent = Category::create($data);

            foreach ($children as $child) {
                Category::create(array_merge($child, ['parent_id' => $parent->id]));
            }
        }
    }
}