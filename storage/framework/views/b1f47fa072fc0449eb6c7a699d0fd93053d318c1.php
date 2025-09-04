<nav class="sidenav navbar navbar-vertical fixed-left navbar-expand-xs navbar-light bg-white" id="sidenav-main">
  <div class="scrollbar-inner">

    
    <div class="sidenav-header align-items-center d-flex justify-content-between">
      <a class="navbar-brand" href="<?php echo e(route('home')); ?>">
        <img src="<?php echo e(asset('assets/img/icons/rskk logo-01.png')); ?>" class="navbar-brand-img" alt="...">
      </a>
    </div>

    <?php
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
    ?>

    <div class="navbar-inner">
      <div class="collapse navbar-collapse" id="sidenav-collapse-main">
        <ul class="navbar-nav">

          
          <?php if($isStaff): ?>

            
            <li class="nav-item">
              <a class="nav-link <?php echo e(request()->routeIs('home') ? 'active' : ''); ?>" href="<?php echo e(route('home')); ?>">
                <i class="ni ni-tv-2 text-primary"></i>
                <span class="nav-link-text">Dashboard</span>
              </a>
            </li>

          <?php else: ?>
          

            
            <li class="nav-item">
              <a class="nav-link <?php echo e(request()->routeIs('home') ? 'active' : ''); ?>" href="<?php echo e(route('home')); ?>">
                <i class="ni ni-tv-2 text-primary"></i>
                <span class="nav-link-text">Dashboard</span>
              </a>
            </li>

            <?php if(Auth::check()): ?>
              
              <?php if(optional($user)->department_id == 1): ?>
                <li class="nav-item">
                  <a class="nav-link <?php echo e(request()->is('management_inventaris') ? 'active' : ''); ?>"
                     href="<?php echo e(url('management_inventaris')); ?>">
                    <i class="fas fa-toolbox text-primary"></i>
                    <span class="nav-link-text">Managemen Inventaris</span>
                  </a>
                </li>
              <?php endif; ?>
            <?php endif; ?>
          <?php endif; ?>

          
          <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('access_user_management')): ?>
            <li class="nav-item">
              <a class="nav-link <?php echo e($mgmtOpen ? '' : 'collapsed'); ?>"
                 href="#submenuUser" data-toggle="collapse" role="button"
                 aria-expanded="<?php echo e($mgmtOpen ? 'true' : 'false'); ?>" aria-controls="submenuUser">
                <i class="fas fa-users text-primary"></i>
                <span class="nav-link-text">Management User</span>
              </a>
              <div class="collapse <?php echo e($mgmtOpen ? 'show' : ''); ?>" id="submenuUser">
                <ul class="nav nav-sm flex-column ml-3">
                  <li class="nav-item">
                    <a href="<?php echo e(route('management_user.index')); ?>"
                       class="nav-link <?php echo e(request()->routeIs('management_user.*') ? 'active' : ''); ?>">
                      <i class="fas fa-user mr-2 text-primary"></i> Data User
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?php echo e(route('privileges.index')); ?>"
                       class="nav-link <?php echo e(request()->routeIs('privileges.*') ? 'active' : ''); ?>">
                      <i class="fas fa-user-shield mr-2 text-primary"></i> Akses User
                    </a>
                  </li>
                </ul>
              </div>
            </li>
          <?php endif; ?>

         
<?php if($canHR): ?>
  <li class="nav-item">
    <a class="nav-link <?php echo e($hrOpen ? '' : 'collapsed'); ?>"
       href="#submenuHR" data-toggle="collapse" role="button"
       aria-expanded="<?php echo e($hrOpen ? 'true' : 'false'); ?>" aria-controls="submenuHR">
      <i class="fas fa-users-cog text-primary"></i>
      <span class="nav-link-text">E-Personalia</span>
    </a>
    <div class="collapse <?php echo e($hrOpen ? 'show' : ''); ?>" id="submenuHR">
      <ul class="nav nav-sm flex-column ml-3">

        
        <li class="nav-item">
          <a href="<?php echo e(route('hr.dashboard')); ?>"
             class="nav-link <?php echo e(request()->routeIs('hr.dashboard') ? 'active' : ''); ?>">
            <i class="fas fa-chart-line mr-2 text-primary"></i> Dashboard HR
          </a>
        </li>

        
        <?php if($user->hasPrivilege('hr_employee_manage')): ?>
          <li class="nav-item">
            <a href="<?php echo e(route('hr.employees.index')); ?>"
               class="nav-link <?php echo e(request()->routeIs('hr.employees.*') ? 'active' : ''); ?>">
              <i class="fas fa-id-badge mr-2 text-primary"></i> Master Karyawan
            </a>
          </li>
        <?php endif; ?>

      </ul>
    </div>
  </li>
<?php endif; ?>


          
          <?php if($canRS || $canSvc || $canLap): ?>
            <li class="nav-item">
              <a class="nav-link <?php echo e($serviceOpen ? '' : 'collapsed'); ?>"
                 href="#submenuService" data-toggle="collapse"
                 role="button" aria-expanded="<?php echo e($serviceOpen ? 'true' : 'false'); ?>"
                 aria-controls="submenuService">
                <i class="fas fa-toolbox text-primary"></i>
                <span class="nav-link-text">Service</span>
              </a>
              <div class="collapse <?php echo e($serviceOpen ? 'show' : ''); ?>" id="submenuService">
                <ul class="nav nav-sm flex-column ml-3">

                  
                  <?php if($canRS): ?>
                    <li class="nav-item">
                      <a class="nav-link <?php echo e(request()->routeIs('request_service.*') ? 'active' : ''); ?>"
                         href="<?php echo e(url('request_service')); ?>">
                        <i class="fas fa-hand-holding mr-2 text-primary"></i>
                        Request Service
                        <?php if(!empty($totalPendingRequestService) && $totalPendingRequestService > 0): ?>
                          <span class="badge badge-danger ml-2"><?php echo e($totalPendingRequestService); ?></span>
                        <?php endif; ?>
                      </a>
                    </li>
                  <?php endif; ?>

                  
                  <?php if($canSvc): ?>
                    <li class="nav-item">
                      <a class="nav-link <?php echo e(request()->routeIs('service.index') ? 'active' : ''); ?>"
                         href="<?php echo e(route('service.index')); ?>">
                        <i class="fas fa-tools mr-2 text-primary"></i>
                        Service
                        <?php if(!empty($totalPendingService) && $totalPendingService > 0): ?>
                          <span class="badge badge-danger ml-2"><?php echo e($totalPendingService); ?></span>
                        <?php endif; ?>
                      </a>
                    </li>
                  <?php endif; ?>

                  
                  <?php if($canLap): ?>
                    <li class="nav-item">
                      <a class="nav-link <?php echo e(request()->is('laporan_service*') ? 'active' : ''); ?>"
                         href="<?php echo e(url('laporan_service')); ?>">
                        <i class="fas fa-file mr-2 text-primary"></i>
                        Laporan Service
                      </a>
                    </li>
                  <?php endif; ?>

                </ul>
              </div>
            </li>
          <?php endif; ?>

          
          <?php if(($user && $user->hasPrivilege('access_bap')) || $isStaff): ?>
            <li class="nav-item">
              <a class="nav-link <?php echo e($mutuOpen ? '' : 'collapsed'); ?>"
                 href="#submenuMutu" data-toggle="collapse"
                 role="button" aria-expanded="<?php echo e($mutuOpen ? 'true' : 'false'); ?>"
                 aria-controls="submenuMutu">
                <i class="fas fa-user-check text-primary"></i>
                <span class="nav-link-text">Mutu</span>
              </a>

              <div class="collapse <?php echo e($mutuOpen ? 'show' : ''); ?>" id="submenuMutu">
                <ul class="nav nav-sm flex-column ml-3">
                  
                  <li class="nav-item">
                    <a class="nav-link <?php echo e(request()->routeIs('bap.index') ? 'active' : ''); ?>"
                       href="<?php echo e(route('bap.index')); ?>">
                      <i class="fas fa-file-alt text-success"></i>
                      <span class="nav-link-text">
                        BAP
                        <?php if(!empty($totalPendingBap) && $totalPendingBap > 0): ?>
                          <span class="badge badge-danger ml-2"><?php echo e($totalPendingBap); ?></span>
                        <?php endif; ?>
                      </span>
                    </a>
                  </li>

                  
                  <?php if($user && $user->hasPrivilege('laporan_bap')): ?>
                    <li class="nav-item">
                      <a class="nav-link <?php echo e(request()->routeIs('laporan.bap') ? 'active' : ''); ?>"
                         href="<?php echo e(route('laporan.bap', ['month' => now()->month, 'year' => now()->year])); ?>">
                        <i class="fas fa-chart-bar text-primary"></i>
                        <span class="nav-link-text">Laporan BAP</span>
                      </a>
                    </li>
                  <?php endif; ?>
                </ul>
              </div>
            </li>
          <?php endif; ?>

          
          <?php if($user && $user->hasPrivilege('view_activity_logs')): ?>
            <li class="nav-item">
              <a class="nav-link <?php echo e(request()->is('activity-logs*') ? 'active' : ''); ?>"
                 href="<?php echo e(route('activity.index')); ?>">
                <i class="fas fa-history text-primary"></i>
                <span class="nav-link-text">Activity Logs</span>
              </a>
            </li>
          <?php endif; ?>

          
          <?php if($user && $user->hasAccess('laporan_it')): ?>
            <li class="nav-item">
              <a class="nav-link <?php echo e(request()->is('laporan-kerja*') ? 'active' : ''); ?>"
                 href="<?php echo e(url('laporan-kerja?status_id=1&tanggal=&user_name=')); ?>">
                <i class="fas fa-calendar-check text-success"></i>
                <span class="nav-link-text">Laporan Harian IT</span>
              </a>
            </li>
          <?php endif; ?>

        </ul>
      </div>
    </div>
  </div>
</nav>
<?php /**PATH C:\xampp\htdocs\project_form\resources\views/layouts/navigation.blade.php ENDPATH**/ ?>