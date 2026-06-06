<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Skill;
use App\Models\Category;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $map = [
            'web-development' => ['HTML', 'CSS', 'JavaScript', 'PHP', 'Laravel', 'WordPress'],
            'mobile'          => ['Flutter', 'React Native', 'Swift', 'Kotlin'],
            'backend'         => ['Python', 'Node.js', 'Java', 'Go', 'REST API'],
            'frontend'        => ['React', 'Vue.js', 'TypeScript', 'Tailwind CSS', 'Bootstrap'],
            'databases'       => ['MySQL', 'PostgreSQL', 'MongoDB', 'Redis'],
            'ui-ux'           => ['Figma', 'Adobe XD', 'Wireframing', 'Prototyping'],
            'graphic-design'  => ['Photoshop', 'Illustrator', 'Canva'],
            'logos'           => ['Брендинг', 'Векторная графика'],
            'copywriting'     => ['Продающие тексты', 'Лендинги', 'Email-рассылки'],
            'translation'     => ['Английский', 'Немецкий', 'Китайский'],
            'seo-content'     => ['SEO-оптимизация', 'Ключевые слова', 'Метатеги'],
            'smm'             => ['Instagram', 'ВКонтакте', 'Telegram', 'TikTok'],
            'ppc'             => ['Яндекс.Директ', 'Google Ads'],
            'seo'             => ['Аудит сайта', 'Ссылочное продвижение', 'Семантическое ядро'],
            'data-science'    => ['Python', 'Pandas', 'Machine Learning', 'TensorFlow'],
            'bi-reports'      => ['Power BI', 'Tableau', 'Excel', '1С'],
        ];

        foreach ($map as $slug => $skills) {
            $category = Category::where('slug', $slug)->first();

            foreach ($skills as $skillName) {
                $skillSlug = \Str::slug($skillName);

                Skill::firstOrCreate(
                    ['slug' => $skillSlug],
                    ['name' => $skillName, 'category_id' => $category?->id]
                );
            }
        }
    }
}