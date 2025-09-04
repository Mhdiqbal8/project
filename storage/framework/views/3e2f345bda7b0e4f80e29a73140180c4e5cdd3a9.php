<?php $__env->startSection('content'); ?>

<style>
  :root{
    --fresh-bg: #f6fbf9;               /* soft mint background */
    --fresh-grad-start:#16a085;        /* teal/green */
    --fresh-grad-end:#2ecc71;          /* fresh green */
    --fresh-text:#2f3e46;
    --fresh-muted:#7b8a8b;
    --fresh-card:#ffffff;
    --fresh-ring: rgba(46, 204, 113, .25);
  }

  /* Page background */
  body{ background: var(--fresh-bg); }

  /* Header with smooth gradient */
  .header.fresh{
    background: linear-gradient(135deg, var(--fresh-grad-start), var(--fresh-grad-end));
    border-bottom-left-radius: 1.25rem;
    border-bottom-right-radius: 1.25rem;
    padding: 2.25rem 1.25rem !important;
    box-shadow: 0 8px 24px rgba(0,0,0,.08);
  }
  .header .breadcrumb .breadcrumb-item + .breadcrumb-item::before{ color: rgba(255,255,255,.65); }
  .header .breadcrumb .breadcrumb-item a,
  .header .breadcrumb .breadcrumb-item,
  .header h2{ color:#fff !important; }
  .header .breadcrumb .active{ color: rgba(255,255,255,.9) !important; }

  /* Card style: airy, rounded, subtle hover */
  .card.fresh{
    background: var(--fresh-card);
    border: 1px solid rgba(0,0,0,.04);
    border-radius: 1rem;
    box-shadow: 0 6px 18px rgba(0,0,0,.06);
    transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
  }
  .card.fresh:hover{
    transform: translateY(-4px);
    box-shadow: 0 14px 30px rgba(0,0,0,.10);
    border-color: rgba(46, 204, 113, .25);
  }

  /* Icon ring */
  .icon-ring{
    position: relative;
    display: inline-flex; align-items:center; justify-content:center;
    width: 52px; height: 52px; border-radius: 50%;
    background: #fff; color: var(--fresh-grad-start);
    box-shadow: 0 10px 18px var(--fresh-ring);
  }
  .icon-ring .ni, .icon-ring i{ font-size: 20px; }

  /* Titles & text */
  .card-title.small-muted{ color: var(--fresh-muted); letter-spacing:.06em; font-weight: 700; font-size:.72rem; text-transform: uppercase; }
  .metric{ color: var(--fresh-text); font-weight: 800; }

  /* Subtle reveal animation */
  .reveal{ opacity: 0; transform: translateY(10px); animation: reveal .55s ease forwards; }
  @keyframes  reveal{ to{ opacity:1; transform:none; } }
  .reveal.d1{ animation-delay:.05s } .reveal.d2{ animation-delay:.10s } .reveal.d3{ animation-delay:.15s } .reveal.d4{ animation-delay:.20s }

  /* Divider */
  .fresh-hr{ border:0; height:1px; background: linear-gradient(90deg, transparent, rgba(0,0,0,.08), transparent); }

  /* Footer */
  .fresh-footer{ color: var(--fresh-muted); }
  .fresh-footer a{ color: #16a085; text-decoration: none; }
  .fresh-footer a:hover{ text-decoration: underline; }

  /* Responsive tweaks */
  @media (max-width: 991.98px){ .header.fresh{ border-radius: 0 0 1rem 1rem; } }
</style>

<div class="header fresh pb-4">
  <div class="d-flex justify-content-between align-items-center">
      <h2 class="mb-0 fw-bold">Dashboard</h2>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb breadcrumb-links breadcrumb-dark mb-0">
          <li class="breadcrumb-item"><a href="<?php echo e(route('home')); ?>"><i class="fas fa-home"></i></a></li>
          <li class="breadcrumb-item">Dashboards</li>
          <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
      </nav>
  </div>
</div>

<div class="container-fluid px-0 px-lg-3">
  <div class="row mt-4">

    
    <div class="col-xl-3 col-md-6 mb-4 reveal d1">
      <div class="card card-stats h-100 shadow-sm fresh">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col">
              <h5 class="card-title small-muted mb-2">Total User Aktif</h5>
              <div class="h2 metric mb-0"><?php echo e($data_account); ?></div>
            </div>
            <div class="col-auto">
              <span class="icon-ring"><i class="ni ni-single-02"></i></span>
            </div>
          </div>
        </div>
      </div>
    </div>

    
    <div class="col-xl-3 col-md-6 mb-4 reveal d2">
      <div class="card card-stats h-100 shadow-sm fresh">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col">
              <h5 class="card-title small-muted mb-2">Total Permohonan Service</h5>
              <div class="h2 metric mb-0"><?php echo e($total_service); ?></div>
            </div>
            <div class="col-auto">
              <span class="icon-ring"><i class="ni ni-chart-pie-35"></i></span>
            </div>
          </div>
        </div>
      </div>
    </div>

    
    <div class="col-xl-3 col-md-6 mb-4 reveal d3">
      <div class="card card-stats h-100 shadow-sm fresh">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col">
              <h5 class="card-title small-muted mb-2">Service Berhasil Diselesaikan</h5>
              <div class="h2 metric mb-0"><?php echo e($service_selesai); ?></div>
            </div>
            <div class="col-auto">
              <span class="icon-ring"><i class="ni ni-check-bold"></i></span>
            </div>
          </div>
        </div>
      </div>
    </div>

    
    <div class="col-xl-3 col-md-6 mb-4 reveal d4">
      <div class="card card-stats h-100 shadow-sm fresh">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col">
              <h5 class="card-title small-muted mb-2">Service Ditolak / Batal</h5>
              <div class="h2 metric mb-0"><?php echo e($service_reject); ?></div>
            </div>
            <div class="col-auto">
              <span class="icon-ring"><i class="ni ni-fat-remove"></i></span>
            </div>
          </div>
        </div>
      </div>
    </div>

    
    <div class="col-xl-3 col-md-6 mb-4 reveal d1">
      <div class="card card-stats h-100 shadow-sm fresh">
        <div class="card-body">
          <div class="row align-items-center">
            <div class="col">
              <h5 class="card-title small-muted mb-2">Total BAP Masuk</h5>
              <div class="h2 metric mb-0"><?php echo e($total_bap); ?></div>
            </div>
            <div class="col-auto">
              <span class="icon-ring"><i class="ni ni-collection"></i></span>
            </div>
          </div>
        </div>
      </div>
    </div>

    
    
  </div>

  <hr class="fresh-hr my-4">

  <footer class="footer pt-0 mt-4 fresh-footer">
    <div class="row align-items-center justify-content-lg-between">
        <div class="col-lg-6 mb-2 mb-lg-0">
            <div class="copyright text-center text-lg-left">
                &copy; 2025 
                <a href="https://www.keluarga-kita.com/" class="font-weight-bold ms-1" target="_blank">
                    Rumah Sakit Keluarga Kita - Developer TIM IT RSKK
                </a>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="text-center text-lg-right small">
                Dibangun dengan sepenuh hati untuk pelayanan terbaik bagi pasien dan keluarga.
            </div>
        </div>
    </div>
  </footer>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\project_form\resources\views/dashboard.blade.php ENDPATH**/ ?>