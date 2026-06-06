<?php
// config/adminlte.php
return [
    'title'          => 'Администратор',
    'title_prefix'   => '',
    'title_postfix'  => ' | ФрилансМаркет',
    'logo'           => '<b>Фриланс</b>Маркет',
    'logo_img'       => false,
    'logo_img_class' => 'brand-image',
    'skin'           => 'dark',
    'layout_topnav'  => null,
    'layout_boxed'   => null,
    'layout_fixed_sidebar'   => true,
    'layout_fixed_navbar'    => true,
    'layout_fixed_footer'    => false,
    'classes_body'   => '',
    'classes_brand'  => 'bg-dark',
    'classes_sidebar'=> 'sidebar-dark-primary elevation-4',
    'classes_topnav' => 'navbar-dark bg-dark',

    'menu' => [
        ['header' => 'УПРАВЛЕНИЕ'],
        [
            'text'    => 'Главная',
            'url'     => 'admin/dashboard',
            'icon'    => 'fas fa-tachometer-alt',
            'active'  => ['admin/dashboard'],
        ],
        [
            'text'    => 'Пользователи',
            'url'     => 'admin/users',
            'icon'    => 'fas fa-users',
            'active'  => ['admin/users*'],
        ],
        [
            'text'    => 'Категории',
            'url'     => 'admin/categories',
            'icon'    => 'fas fa-tags',
        ],
        [
            'text'    => 'Навыки',
            'url'     => 'admin/skills',
            'icon'    => 'fas fa-code',
        ],
        ['header' => 'КРАУЛЕР'],
        [
            'text'    => 'Источники',
            'url'     => 'admin/sources',
            'icon'    => 'fas fa-globe',
        ],
        [
            'text'    => 'Логи краулера',
            'url'     => 'admin/crawler/logs',
            'icon'    => 'fas fa-history',
        ],
        ['header' => 'ОТЧЁТЫ'],
        [
            'text'    => 'Статистика',
            'url'     => 'admin/reports',
            'icon'    => 'fas fa-chart-bar',
        ],
        ['header' => 'МОДЕРАЦИЯ'],
        [
            'text'    => 'На модерации',
            'url'     => 'moderator/orders',
            'icon'    => 'fas fa-shield-alt',
        ],
    ],
];