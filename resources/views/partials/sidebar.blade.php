<style>
    .sidebar .menu a {
        transition: all 0.2s ease-in-out;
    }
    .sidebar .menu a:hover {
        transform: translateX(5px);
        background-color: rgba(255,255,255,0.05);
        border-radius: 8px;
    }
</style>
<div class="sidebar d-flex flex-column h-100">
    <!-- Logo Section -->
    <div class="logo py-4 text-center border-bottom border-secondary" style="border-bottom: 1px solid rgba(255,255,255,0.1) !important;">
        <h4 class="fw-bold text-white mb-1" style="letter-spacing: 1.5px;">
            🌍 SIMRPG
        </h4>
        <small class="text-muted d-block" style="font-size: 0.7rem; color: #94a3b8 !important;">
            Sistem Monitoring Risiko Global
        </small>
    </div>

    <!-- Navigation Menu Section -->
    <ul class="menu flex-grow-1 overflow-y-auto mb-0 list-unstyled" style="padding: 15px 12px; overflow-y: auto;">
        
        @if(auth()->check() && auth()->user()->isAdmin())
            <!-- Admin Menu -->
            <li class="menu-title mb-2" style="font-size: 0.68rem; font-weight: 700; color: #64748b; letter-spacing: 1px; padding: 10px 15px 5px;">ADMINISTRASI SISTEM</li>

            <li>
                <a href="{{ route('admin.index') }}" class="{{ Route::is('admin.index') ? 'active' : '' }}">
                    <i class="bi bi-person-gear"></i>
                    <span>Dashboard Admin</span>
                </a>
            </li>
            
            <li>
                <a href="{{ route('admin.users') }}" class="{{ Route::is('admin.users') || Route::is('admin.users.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i>
                    <span>Kelola User</span>
                </a>
            </li>

            <li>
                <a href="{{ route('admin.articles') }}" class="{{ Route::is('admin.articles') || Route::is('admin.articles.*') ? 'active' : '' }}">
                    <i class="bi bi-journal-text"></i>
                    <span>Kelola Artikel</span>
                </a>
            </li>
            
            <li>
                <a href="{{ route('admin.ports') }}" class="{{ Route::is('admin.ports') || Route::is('admin.ports.*') ? 'active' : '' }}">
                    <i class="bi bi-database-check"></i>
                    <span>Kelola Pelabuhan</span>
                </a>
            </li>
            
            <li class="menu-title mt-3 mb-2" style="font-size: 0.68rem; font-weight: 700; color: #64748b; letter-spacing: 1px; padding: 10px 15px 5px;">SISTEM & API</li>

            <li>
                <a href="{{ route('admin.api.index') }}" class="{{ Route::is('admin.api.index') ? 'active' : '' }}">
                    <i class="bi bi-cloud-arrow-down"></i>
                    <span>Monitoring Integrasi API</span>
                </a>
            </li>
        @else
            <!-- User Menu -->
            <li class="menu-title mb-2" style="font-size: 0.68rem; font-weight: 700; color: #64748b; letter-spacing: 1px; padding: 10px 15px 5px;">MENU UTAMA</li>

            <li>
                <a href="{{ route('dashboard') }}" class="{{ Route::is('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Beranda</span>
                </a>
            </li>

            <li>
                <a href="{{ route('countries.index') }}" class="{{ Route::is('countries.*') ? 'active' : '' }}">
                    <i class="bi bi-globe2"></i>
                    <span>Negara Global</span>
                </a>
            </li>

            <li>
                <a href="{{ route('risk.index') }}" class="{{ Route::is('risk.index') ? 'active' : '' }}">
                    <i class="bi bi-exclamation-triangle"></i>
                    <span>Penilaian Risiko</span>
                </a>
            </li>

            <li>
                <a href="{{ route('cuaca.index') }}" class="{{ Route::is('cuaca.index') ? 'active' : '' }}">
                    <i class="bi bi-cloud-sun"></i>
                    <span>Cuaca Global</span>
                </a>
            </li>

            <li>
               <a href="{{ route('nilai-tukar.index') }}" class="{{ Route::is('nilai-tukar.index') ? 'active' : '' }}">
                    <i class="bi bi-currency-exchange"></i>
                    <span>Nilai Tukar</span>
                </a>
            </li>

            <li>
                <a href="{{ route('berita.index') }}" class="{{ Route::is('berita.index') ? 'active' : '' }}">
                    <i class="bi bi-newspaper"></i>
                    <span>Berita Global</span>
                </a>
            </li>

            <li>
                <a href="{{ route('pelabuhan.index') }}" class="{{ Route::is('pelabuhan.index') ? 'active' : '' }}">
                    <i class="bi bi-geo-alt"></i>
                    <span>Lokasi Pelabuhan</span>
                </a>
            </li>

            <li>
                <a href="/visualisasi" class="{{ Request::is('visualisasi') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart"></i>
                    <span>Visualisasi Data</span>
                </a>
            </li>

            <li>
                <a href="{{ route('compare.index') }}" class="{{ Route::is('compare.*') ? 'active' : '' }}">
                    <i class="bi bi-columns-gap"></i>
                    <span>Bandingkan Negara</span>
                </a>
            </li>

            <li>
                <a href="{{ route('watchlist.index') }}" class="{{ Route::is('watchlist.index') ? 'active' : '' }}">
                    <i class="bi bi-star"></i>
                    <span>Pantauan Favorit</span>
                </a>
            </li>
        @endif
    </ul>

    <!-- Admin Profile Section at the Bottom -->
    <!-- User Profile Section at the Bottom -->
    <div class="sidebar-profile p-3 mt-auto" style="background: #090f1e; border-top: 1px solid rgba(255,255,255,0.08);">
        @auth
        <div class="d-flex align-items-center gap-3">
            @php
                $initials = collect(explode(' ', auth()->user()->name))->map(function($segment) { return strtoupper(substr($segment, 0, 1)); })->take(2)->join('');
            @endphp
            <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" 
                 style="width: 38px; height: 38px; font-size: 0.95rem; background: linear-gradient(135deg, #3b82f6, #1d4ed8); flex-shrink: 0;">
                {{ $initials }}
            </div>
            <div class="min-w-0 flex-grow-1">
                <div class="fw-bold text-white text-truncate" style="font-size: 0.82rem; letter-spacing: 0.3px;" title="{{ auth()->user()->name }}">
                    {{ auth()->user()->name }}
                </div>
                <small class="d-block text-truncate mb-1" style="font-size: 0.72rem; color: #64748b;" title="{{ auth()->user()->email }}">
                    {{ auth()->user()->email }}
                </small>
                @if(auth()->user()->isAdmin())
                    <span class="badge bg-primary" style="font-size: 0.6rem; padding: 3px 6px;">Administrator</span>
                @else
                    <span class="badge bg-secondary" style="font-size: 0.6rem; padding: 3px 6px;">User</span>
                @endif
            </div>
        </div>
        @endauth
    </div>
</div>

<!-- Scroll Persistence Script -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const sidebarMenu = document.querySelector('.sidebar .menu');
    if (sidebarMenu) {
        // Restore scroll position
        const scrollPos = sessionStorage.getItem('sidebar-scroll-position');
        if (scrollPos) {
            sidebarMenu.scrollTop = parseInt(scrollPos, 10);
        }

        // Save scroll position before leaving page or on scroll
        sidebarMenu.addEventListener('scroll', function () {
            sessionStorage.setItem('sidebar-scroll-position', sidebarMenu.scrollTop);
        });

        // Backup for click events
        const menuLinks = sidebarMenu.querySelectorAll('a');
        menuLinks.forEach(link => {
            link.addEventListener('click', function () {
                sessionStorage.setItem('sidebar-scroll-position', sidebarMenu.scrollTop);
            });
        });
    }
});
</script>