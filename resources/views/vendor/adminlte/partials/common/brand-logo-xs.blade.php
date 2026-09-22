@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')

@php( $dashboard_url = View::getSection('dashboard_url') ?? config('adminlte.dashboard_url', 'home') )

@if (config('adminlte.use_route_url', false))
    @php( $dashboard_url = $dashboard_url ? route($dashboard_url) : '' )
@else
    @php( $dashboard_url = $dashboard_url ? url($dashboard_url) : '' )
@endif

<a href="/" class="brand-link" style="text-decoration:none;">
    <span class="brand-text font-weight-bold" style="font-size:1.1rem; font-weight:700; color:#fff;">
        <i class="fas fa-briefcase" style="color:#4e9af1;"></i>
        &nbsp;<b>Фриланс</b>Маркет
    </span>
</a>
