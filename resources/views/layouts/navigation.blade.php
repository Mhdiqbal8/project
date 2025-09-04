<nav class="sidenav navbar navbar-vertical fixed-left navbar-expand-xs navbar-light bg-white" id="sidenav-main">
  <div class="scrollbar-inner">

    {{-- Brand --}}
    <div class="sidenav-header align-items-center d-flex justify-content-between">
      <a class="navbar-brand" href="{{ route('home') }}">
        <img src="{{ asset('assets/img/icons/rskk logo-01.png') }}" class="navbar-brand-img" alt="...">
      </a>
    </div>

    @php
      /** @var \App\Models\User $user */
      $user        = Auth::user();
      $jabatanName = strtolower(optional($user->jabatan)->nama ?? '');
      $isStaff     = $jabatanName === 'staff';

      // Privileges untuk folder Service
      $canRS  = $user && $user->hasPrivilege('access_request_service');
      $canSvc = $user && $user->hasPrivilege('access_service');
      $canLap = $user && $user->hasPrivilege('laporan_service');

      // ✅ Privileges untuk E-Personalia (HR)
      $canHR  = $user && $user->hasPrivilege('access_personalia');

      // Buka folder Service kalau lagi di salah satu halamannya
      $serviceOpen = request()->is('request_service*')
                    || request()->routeIs('service.*')
                    || request()->is('laporan_service*');

      // ✅ Buka folder HR kalau lagi di /hr
      $hrOpen = request()->is('hr*') || request()->routeIs('hr.*');

      // Buka folder Mutu kalau lagi di BAP/Laporan BAP
      $mutuOpen = request()->is('bap*') || request()->routeIs('laporan.bap');

      // Buka submenu Management User (pakai Gate)
      $mgmtOpen = request()->is('management_user*') || request()->is('privileges*')
                || request()->routeIs('management_user.*') || request()->routeIs('privileges.*');
    @endphp

    <div class="navbar-inner">
      <div class="collapse navbar-collapse" id="sidenav-collapse-main">
        <ul class="navbar-nav">

          {{-- =========================
               STAFF ONLY
             ========================= --}}
          @if($isStaff)

            {{-- Dashboard (opsional untuk staff, hapus kalau gak perlu) --}}
            <li class="nav-item">
              <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                <i class="ni ni-tv-2 text-primary"></i>
                <span class="nav-link-text">Dashboard</span>
              </a>
            </li>

          @else
          {{-- =========================
               NON-STAFF
             ========================= --}}

            {{-- Dashboard --}}
            <li class="nav-item">
              <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                <i class="ni ni-tv-2 text-primary"></i>
                <span class="nav-link-text">Dashboard</span>
              </a>
            </li>

            @if (Auth::check())
              {{-- (Tetap) Management Inventaris untuk dept tertentu (contoh: IT id=1) --}}
              @if (optional($user)->department_id == 1)
                <li class="nav-item">
                  <a class="nav-link {{ request()->is('management_inventaris') ? 'active' : '' }}"
                     href="{{ url('management_inventaris') }}">
                    <i class="fas fa-toolbox text-primary"></i>
                    <span class="nav-link-text">Managemen Inventaris</span>
                  </a>
                </li>
              @endif
            @endif
          @endif

          {{-- =========================
               UNIVERSAL: Management User (pakai Gate)
             ========================= --}}
          @can('access_user_management')
            <li class="nav-item">
              <a class="nav-link {{ $mgmtOpen ? '' : 'collapsed' }}"
                 href="#submenuUser" data-toggle="collapse" role="button"
                 aria-expanded="{{ $mgmtOpen ? 'true' : 'false' }}" aria-controls="submenuUser">
                <i class="fas fa-users text-primary"></i>
                <span class="nav-link-text">Management User</span>
              </a>
              <div class="collapse {{ $mgmtOpen ? 'show' : '' }}" id="submenuUser">
                <ul class="nav nav-sm flex-column ml-3">
                  <li class="nav-item">
                    <a href="{{ route('management_user.index') }}"
                       class="nav-link {{ request()->routeIs('management_user.*') ? 'active' : '' }}">
                      <i class="fas fa-user mr-2 text-primary"></i> Data User
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ route('privileges.index') }}"
                       class="nav-link {{ request()->routeIs('privileges.*') ? 'active' : '' }}">
                      <i class="fas fa-user-shield mr-2 text-primary"></i> Akses User
                    </a>
                  </li>
                </ul>
              </div>
            </li>
          @endcan

         {{-- =========================
     E-PERSONALIA (HR)
   ========================= --}}
@if ($canHR)
  <li class="nav-item">
    <a class="nav-link {{ $hrOpen ? '' : 'collapsed' }}"
       href="#submenuHR" data-toggle="collapse" role="button"
       aria-expanded="{{ $hrOpen ? 'true' : 'false' }}" aria-controls="submenuHR">
      <i class="fas fa-users-cog text-primary"></i>
      <span class="nav-link-text">E-Personalia</span>
    </a>
    <div class="collapse {{ $hrOpen ? 'show' : '' }}" id="submenuHR">
      <ul class="nav nav-sm flex-column ml-3">

        {{-- Dashboard HR --}}
        <li class="nav-item">
          <a href="{{ route('hr.dashboard') }}"
             class="nav-link {{ request()->routeIs('hr.dashboard') ? 'active' : '' }}">
            <i class="fas fa-chart-line mr-2 text-primary"></i> Dashboard HR
          </a>
        </li>

        {{-- Master Karyawan --}}
        @if ($user->hasPrivilege('hr_employee_manage'))
          <li class="nav-item">
            <a href="{{ route('hr.employees.index') }}"
               class="nav-link {{ request()->routeIs('hr.employees.*') ? 'active' : '' }}">
              <i class="fas fa-id-badge mr-2 text-primary"></i> Master Karyawan
            </a>
          </li>
        @endif

      </ul>
    </div>
  </li>
@endif


          {{-- =========================
               FOLDER: SERVICE (Universal)
               berisi Request Service, Service, Laporan Service
             ========================= --}}
          @if($canRS || $canSvc || $canLap)
            <li class="nav-item">
              <a class="nav-link {{ $serviceOpen ? '' : 'collapsed' }}"
                 href="#submenuService" data-toggle="collapse"
                 role="button" aria-expanded="{{ $serviceOpen ? 'true' : 'false' }}"
                 aria-controls="submenuService">
                <i class="fas fa-toolbox text-primary"></i>
                <span class="nav-link-text">Service</span>
              </a>
              <div class="collapse {{ $serviceOpen ? 'show' : '' }}" id="submenuService">
                <ul class="nav nav-sm flex-column ml-3">

                  {{-- Request Service --}}
                  @if ($canRS)
                    <li class="nav-item">
                      <a class="nav-link {{ request()->routeIs('request_service.*') ? 'active' : '' }}"
                         href="{{ url('request_service') }}">
                        <i class="fas fa-hand-holding mr-2 text-primary"></i>
                        Request Service
                        @if(!empty($totalPendingRequestService) && $totalPendingRequestService > 0)
                          <span class="badge badge-danger ml-2">{{ $totalPendingRequestService }}</span>
                        @endif
                      </a>
                    </li>
                  @endif

                  {{-- Service --}}
                  @if ($canSvc)
                    <li class="nav-item">
                      <a class="nav-link {{ request()->routeIs('service.index') ? 'active' : '' }}"
                         href="{{ route('service.index') }}">
                        <i class="fas fa-tools mr-2 text-primary"></i>
                        Service
                        @if(!empty($totalPendingService) && $totalPendingService > 0)
                          <span class="badge badge-danger ml-2">{{ $totalPendingService }}</span>
                        @endif
                      </a>
                    </li>
                  @endif

                  {{-- Laporan Service --}}
                  @if ($canLap)
                    <li class="nav-item">
                      <a class="nav-link {{ request()->is('laporan_service*') ? 'active' : '' }}"
                         href="{{ url('laporan_service') }}">
                        <i class="fas fa-file mr-2 text-primary"></i>
                        Laporan Service
                      </a>
                    </li>
                  @endif

                </ul>
              </div>
            </li>
          @endif

          {{-- =========================
               UNIVERSAL: MUTU (submenu BAP + Laporan BAP)
             ========================= --}}
          @if(($user && $user->hasPrivilege('access_bap')) || $isStaff)
            <li class="nav-item">
              <a class="nav-link {{ $mutuOpen ? '' : 'collapsed' }}"
                 href="#submenuMutu" data-toggle="collapse"
                 role="button" aria-expanded="{{ $mutuOpen ? 'true' : 'false' }}"
                 aria-controls="submenuMutu">
                <i class="fas fa-user-check text-primary"></i>
                <span class="nav-link-text">Mutu</span>
              </a>

              <div class="collapse {{ $mutuOpen ? 'show' : '' }}" id="submenuMutu">
                <ul class="nav nav-sm flex-column ml-3">
                  {{-- BAP --}}
                  <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('bap.index') ? 'active' : '' }}"
                       href="{{ route('bap.index') }}">
                      <i class="fas fa-file-alt text-success"></i>
                      <span class="nav-link-text">
                        BAP
                        @if(!empty($totalPendingBap) && $totalPendingBap > 0)
                          <span class="badge badge-danger ml-2">{{ $totalPendingBap }}</span>
                        @endif
                      </span>
                    </a>
                  </li>

                  {{-- Laporan BAP (hanya kalau punya privilege) --}}
                  @if ($user && $user->hasPrivilege('laporan_bap'))
                    <li class="nav-item">
                      <a class="nav-link {{ request()->routeIs('laporan.bap') ? 'active' : '' }}"
                         href="{{ route('laporan.bap', ['month' => now()->month, 'year' => now()->year]) }}">
                        <i class="fas fa-chart-bar text-primary"></i>
                        <span class="nav-link-text">Laporan BAP</span>
                      </a>
                    </li>
                  @endif
                </ul>
              </div>
            </li>
          @endif

          {{-- =========================
               Activity Logs (akses via privilege)
             ========================= --}}
          @if ($user && $user->hasPrivilege('view_activity_logs'))
            <li class="nav-item">
              <a class="nav-link {{ request()->is('activity-logs*') ? 'active' : '' }}"
                 href="{{ route('activity.index') }}">
                <i class="fas fa-history text-primary"></i>
                <span class="nav-link-text">Activity Logs</span>
              </a>
            </li>
          @endif

          {{-- =========================
               Laporan Harian IT (siapa pun yang punya akses)
             ========================= --}}
          @if ($user && $user->hasAccess('laporan_it'))
            <li class="nav-item">
              <a class="nav-link {{ request()->is('laporan-kerja*') ? 'active' : '' }}"
                 href="{{ url('laporan-kerja?status_id=1&tanggal=&user_name=') }}">
                <i class="fas fa-calendar-check text-success"></i>
                <span class="nav-link-text">Laporan Harian IT</span>
              </a>
            </li>
          @endif

        </ul>
      </div>
    </div>
  </div>
</nav>
