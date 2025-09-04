<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="Start your development with a Dashboard for Bootstrap 4.">
  <meta name="author" content="Creative Tim">
  <title>Project Form</title>

  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

  
  <link rel="icon" href="<?php echo e(url('/assets/img/brand/favicon-32x32.png')); ?>" type="image/png">

  
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700">

  
  <link rel="stylesheet" href="<?php echo e(url('/assets/vendor/nucleo/css/nucleo.css')); ?>" type="text/css">
  <link rel="stylesheet" href="<?php echo e(url('/assets/vendor/@fortawesome/fontawesome-free/css/all.min.css')); ?>" type="text/css">

  
  <link rel="stylesheet" href="<?php echo e(url('/assets/css/argon.css')); ?>" type="text/css">

  
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.4/css/dataTables.bootstrap4.min.css">

  
  <link rel="stylesheet" href="<?php echo e(url('/assets/css/main.css')); ?>">

  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <?php echo $__env->yieldPushContent('styles'); ?>

  <style>
    /* ========== SIDEBAR ========== */
    #sidenav-main{
      position: fixed; top:0; left:0; height:100%; z-index:999; width:250px;
      transform: translateX(-100%); transition: transform .3s ease;
      background-color:#fff; overflow-y:auto;
    }
    #sidenav-main.show-sidenav{ transform: translateX(0); }

    /* ========== NAVBAR FIXED ========== */
    .navbar-top{
      position: fixed; top:0; left:0; width:100%; z-index:1030;
      transition: left .3s ease, width .3s ease;
    }
    body.sidebar-open .navbar-top{ left:250px; width:calc(100% - 250px); }

    /* ========== MAIN CONTENT ========== */
    .main-wrapper{
      margin-left:0; transition: margin-left .3s ease;
      /* kecilkan padding samping supaya maksimal lebar layar */
      padding: 80px 12px 24px; /* top buat turun dari navbar */
    }
    body.sidebar-open .main-wrapper{ margin-left:250px; }

    /* Hapus max-width & padding dari container di dalam main-wrapper */
    .main-wrapper .container,
    .main-wrapper .container-sm,
    .main-wrapper .container-md,
    .main-wrapper .container-lg,
    .main-wrapper .container-xl,
    .main-wrapper .container-xxl{
      max-width: 100% !important;
      width: 100% !important;
      padding-left: 0 !important;
      padding-right: 0 !important;
    }

    /* Utilitas full-bleed kalau mau section benar2 nempel kaca */
    .full-bleed{
      width:100vw; position:relative; left:50%; right:50%;
      margin-left:-50vw; margin-right:-50vw;
      padding-left:12px; padding-right:12px;
    }

    /* Hamburger putih */
    .navbar-toggler{ border:none; outline:none; }
    .navbar-toggler-icon{
      background-image:url("data:image/svg+xml;charset=utf8,%3Csvg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke='white' stroke-width='2' stroke-linecap='round' stroke-miterlimit='10' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
    }

    /* ========== MODAL LAYERING FIX ========== */
    .modal         { z-index: 1065 !important; }  /* di atas backdrop */
    .modal-backdrop{ z-index: 1060 !important; }  /* di bawah modal */
  </style>
</head>

<body>
  
  <?php echo $__env->make('layouts.navigation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

  
  <div class="navbar-top">
    <?php echo $__env->make('components.navbar-top', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
  </div>

  <div class="main-wrapper">
    <?php echo $__env->yieldContent('content'); ?>
  </div>

  
  <script src="<?php echo e(url('/assets/vendor/jquery/dist/jquery.min.js')); ?>"></script>
  <script src="<?php echo e(url('/assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js')); ?>"></script>
  <script src="<?php echo e(url('/assets/vendor/js-cookie/js.cookie.js')); ?>"></script>
  <script src="<?php echo e(url('/assets/vendor/jquery.scrollbar/jquery.scrollbar.min.js')); ?>"></script>
  <script src="<?php echo e(url('/assets/js/argon.js?v=1.2.0')); ?>"></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="https://cdn.datatables.net/1.11.4/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.4/js/dataTables.bootstrap4.min.js"></script>

  
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

  
  <script>
    <?php if(session('success')): ?> toastr.success(<?php echo json_encode(session('success'), 15, 512) ?>, "Sukses"); <?php endif; ?>
    <?php if(session('failed')): ?>  toastr.error(<?php echo json_encode(session('failed'), 15, 512) ?>, "Gagal");   <?php endif; ?>
  </script>

  
  <script>
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') } });
  </script>

  
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const toggler = document.querySelector('.sidenav-toggler');
      const sidebar = document.getElementById('sidenav-main');
      const body    = document.body;
      if (toggler && sidebar) {
        toggler.addEventListener('click', function () {
          sidebar.classList.toggle('show-sidenav');
          body.classList.toggle('sidebar-open');
        });
      }
    });
  </script>

  <?php echo $__env->yieldPushContent('scripts'); ?>

  
  <?php echo $__env->yieldPushContent('modals'); ?>

  
  
</body>
</html>
<?php /**PATH C:\xampp\htdocs\project_form\resources\views/layouts/app.blade.php ENDPATH**/ ?>