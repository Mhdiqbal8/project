<?php
    $user = auth()->user();
    $notifCount = $unreadNotifications->count(); // dari View Composer
?>

<nav class="navbar navbar-top navbar-expand navbar-dark bg-primary border-bottom" style="padding-left: 60px;">

  <!-- ✅ Hamburger -->
  <div class="navbar-toggler sidenav-toggler"
       style="cursor:pointer; margin:0; position:absolute; top:0; left:0; height:56px; width:56px; display:flex; align-items:center; justify-content:center;">
    <span class="navbar-toggler-icon"></span>
  </div>

  <div class="d-flex justify-content-between align-items-center w-100 pe-5">
    <!-- Brand -->
    <h1 class="title-text mb-0 text-white" style="font-size: 1.25rem;">
      E Report Form RSKK
    </h1>

    <!-- Navbar Right -->
    <ul class="navbar-nav align-items-center ml-auto">

      <!-- 🔔 Notifikasi Laravel (Bootstrap 4 compatible) -->
      <li class="nav-item dropdown">
        <a class="nav-link position-relative" href="#" id="notifDropdown" role="button"
           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fas fa-bell text-white"></i>
          <span
            id="notif-badge"
            class="badge badge-danger badge-pill"
            style="position:absolute; top:-4px; right:-6px; <?php echo e($notifCount == 0 ? 'display:none;' : ''); ?>">
            <?php echo e($notifCount); ?>

          </span>
        </a>

        <ul class="dropdown-menu dropdown-menu-right shadow p-2"
            aria-labelledby="notifDropdown"
            style="min-width: 300px; max-height: 300px; overflow-y: auto; border-radius: 1rem;">
          <?php if($notifCount > 0): ?>
            <li class="px-2 pb-1">
              <form action="<?php echo e(route('notif.read_all')); ?>" method="post">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-sm btn-link p-0">Tandai semua sudah dibaca</button>
              </form>
            </li>
            <li><hr class="dropdown-divider"></li>
          <?php endif; ?>

          <?php $__empty_1 = true; $__currentLoopData = $unreadNotifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notif): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
              $unitName = strtolower($notif->data['unit'] ?? '');
              $emoji = match(true) {
                  str_contains($unitName, 'it') => '🛠',
                  str_contains($unitName, 'farmasi') => '💊',
                  str_contains($unitName, 'cs'), str_contains($unitName, 'cleaning') => '🧼',
                  str_contains($unitName, 'keuangan') => '💰',
                  str_contains($unitName, 'dokter') => '🩺',
                  str_contains($unitName, 'rekam medis') => '📄',
                  default => '🔔',
              };
            ?>

            <li class="mb-1">
              <a class="dropdown-item small border rounded p-2" href="<?php echo e(route('notif.go', $notif->id)); ?>">
                <div class="d-flex flex-column">
                  <strong class="text-dark">
                    <span class="mr-1"><?php echo e($emoji); ?></span><?php echo e($notif->data['title'] ?? '📌 Notifikasi'); ?>

                  </strong>
                  <span class="text-muted" style="font-size: 13px;">
                    <?php echo e($notif->data['message'] ?? 'Ada update baru.'); ?>

                  </span>
                  <small class="text-muted"><?php echo e($notif->created_at->diffForHumans()); ?></small>
                </div>
              </a>
            </li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <li><span class="dropdown-item small text-muted">Tidak ada notifikasi baru</span></li>
          <?php endif; ?>
        </ul>
      </li>

      <!-- 👤 Avatar + Nama -->
      <li class="nav-item dropdown position-relative">
        <a class="nav-link pr-0" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <div class="media align-items-center">
            <span class="avatar avatar-sm rounded-circle position-relative">
              <?php if($user->gender_id == 1): ?>
                  <img alt="Image placeholder" src="<?php echo e(asset('assets/img/theme/male.png')); ?>">
              <?php else: ?>
                  <img alt="Image placeholder" src="<?php echo e(asset('assets/img/theme/female.png')); ?>">
              <?php endif; ?>
              <span id="avatar-badge" style="display: none;"></span>
            </span>
            <div class="media-body ml-2 d-none d-lg-block">
              <span class="mb-0 text-sm font-weight-bold text-white">
                <?php echo e($user->nama); ?>

              </span>
            </div>
          </div>
        </a>

        <div class="dropdown-menu dropdown-menu-right">
          <div class="dropdown-header noti-title">
            <h6 class="text-overflow m-0 text-primary">Selamat Datang!</h6>
          </div>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item text-primary" data-toggle="modal" data-target="#ModalUbahPassword">
            Ubah Password
          </a>
          <a href="#" class="dropdown-item text-danger"
             onclick="event.preventDefault(); document.getElementById('user-form').submit()">
             Log Out
          </a>
          <form id="user-form" action="<?php echo e(route('logout')); ?>" method="post" style="display: none;">
            <?php echo csrf_field(); ?>
          </form>
        </div>

        
      </li>
    </ul>
  </div>
</nav>

<!-- 🔁 ====== OVERRIDE WARNA (Navbar = Header) ====== -->
<style>
  /* Navbar (1) disamain dengan Header (2) – hijau soft nyaman */
  nav.navbar.bg-primary {
    background: linear-gradient(135deg, #6ee7b7 0%, #34d399 50%, #22c55e 100%) !important;
    border-bottom: 1px solid rgba(255,255,255,.08) !important;
  }

  /* Header Dashboard (2) – sudah pas, biar senada dengan navbar */
  .header.bg-primary {
    background: linear-gradient(135deg, #6ee7b7 0%, #34d399 50%, #22c55e 100%) !important;
    border-radius: 0 0 1rem 1rem;
    box-shadow: 0 4px 12px rgba(0,0,0,.08);
  }

  /* Judul Dashboard tetap crisp */
  .header h2, .header h1, .header h3 {
    color: #fff !important;
    text-shadow: 0 1px 2px rgba(0,0,0,.12);
  }

  /* Breadcrumb serasi */
  .breadcrumb.breadcrumb-dark .breadcrumb-item a { color: #ecfdf5 !important; }
  .breadcrumb.breadcrumb-dark .breadcrumb-item.active { color: #f0fdf4 !important; }

  /* Card halus */
  .card {
    border: none;
    border-radius: 1rem;
    box-shadow: 0 4px 14px rgba(0,0,0,.05);
    transition: transform .15s ease, box-shadow .15s ease;
  }
  .card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(0,0,0,.08);
  }
</style>

<?php $__env->startPush('modals'); ?>
  <!-- Modal Ubah Password (sekarang ditaruh di akhir <body>) -->
  <div class="modal fade" id="ModalUbahPassword" tabindex="-1" role="dialog" aria-labelledby="ModalUbahPasswordTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="ModalUbahPasswordTitle">Ubah Password, <?php echo e($user->nama); ?></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="<?php echo e(url('ubah_password')); ?>" method="post">
          <?php echo csrf_field(); ?>
          <div class="modal-body">
            <div class="form-group">
              <label>Password Lama Anda</label>
              <input type="password" name="password_old" class="form-control" placeholder="Password Lama Anda" required>
            </div>
            <div class="form-group">
              <label>Password Baru Anda</label>
              <input type="password" name="password_new" class="form-control" placeholder="Password Baru Anda" required>
            </div>
            <div class="form-group">
              <label>Konfirmasi Password Baru</label>
              <input type="password" name="confirm_password_new" class="form-control" placeholder="Konfirmasi Password Baru Anda" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary" onclick="return confirm('Apakah Anda Yakin?')">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
<?php $__env->stopPush(); ?>
<?php /**PATH C:\xampp\htdocs\project_form\resources\views/components/navbar-top.blade.php ENDPATH**/ ?>