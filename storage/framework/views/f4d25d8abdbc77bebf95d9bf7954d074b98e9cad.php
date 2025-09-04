<?php $__env->startSection('content'); ?>
<div class="header bg-primary pb-6">
  <div class="px-4">
    <div class="header-body">
      <div class="row align-items-center py-4">
        <div class="col-lg-6 col-7">
          <h6 class="h2 text-white d-inline-block mb-0">Manajemen User</h6>
          <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
            <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
              <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i></a></li>
              <li class="breadcrumb-item active" aria-current="page">Management User</li>
            </ol>
          </nav>

          <!-- ==== Smooth UI Overrides (no markup changes) ==== -->
<style>
  /* ====== Palette hijau adem ====== */
  :root{
    --brand-50:#ecfdf5;
    --brand-100:#d1fae5;
    --brand-200:#a7f3d0;
    --brand-300:#6ee7b7;
    --brand-400:#34d399; /* mint */
    --brand-500:#22c55e; /* primary */
    --brand-600:#16a34a;
    --brand-700:#15803d;
    --ink-700:#0f172a;
    --ink-500:#334155;
    --ink-300:#94a3b8;
    --card-shadow: 0 6px 18px rgba(0,0,0,.08);
    --soft-shadow: 0 4px 12px rgba(0,0,0,.06);
  }

  /* ====== Navbar: samain tone dengan header & “makin tebal dikit2” ====== */
  nav.navbar.bg-primary{
    position: relative;
    background: linear-gradient(135deg, var(--brand-300) 0%, var(--brand-400) 50%, var(--brand-500) 100%) !important;
    border-bottom: 1px solid rgba(255,255,255,.08) !important;
    box-shadow: 0 2px 8px rgba(0,0,0,.04);
    transition: box-shadow .2s ease;
  }
  /* Accent bar di bawah navbar yang pelan2 “menebal” */
  nav.navbar.bg-primary::after{
    content:"";
    position:absolute; left:0; right:0; bottom:-1px;
    height:0;
    background: linear-gradient(90deg, rgba(255,255,255,.18), rgba(255,255,255,0));
    border-radius: 0 0 12px 12px;
    animation: navThicken 6s ease-out forwards; /* sekali, pelan */
  }
  @keyframes  navThicken{
    0%   { height:0;    opacity:.0; }
    35%  { height:4px;  opacity:.7; }
    100% { height:8px;  opacity:1;  }
  }
  /* Saat discroll, kasih elevasi dikit biar terasa “tebal”/berbobot */
  .nav-scrolled{
    box-shadow: 0 8px 22px rgba(0,0,0,.10) !important;
  }

  /* ====== Header hijau smooth (yang kotak besar “Manajemen User”) ====== */
  .header.bg-primary{
    background: linear-gradient(135deg, var(--brand-300) 0%, var(--brand-400) 50%, var(--brand-500) 100%) !important;
    border-radius: 0 0 18px 18px;
    box-shadow: var(--soft-shadow);
  }
  .header h1,.header h2,.header h3,.header h6{
    color:#fff !important;
    text-shadow: 0 1px 2px rgba(0,0,0,.12);
    letter-spacing:.2px;
  }
  .breadcrumb.breadcrumb-dark .breadcrumb-item a{ color: var(--brand-50)!important; }
  .breadcrumb.breadcrumb-dark .breadcrumb-item.active{ color: #fff !important; opacity:.8; }

  /* ====== Card & container ====== */
  .card{
    border:none;
    border-radius: 16px;
    box-shadow: var(--card-shadow);
    overflow:hidden;
    transition: transform .15s ease, box-shadow .15s ease;
  }
  .card:hover{ transform: translateY(-2px); box-shadow: 0 10px 26px rgba(0,0,0,.10); }
  .card-header.bg-white{ background:#fff !important; border-bottom:1px solid rgba(0,0,0,.05) !important; }

  /* ====== Tombol ====== */
  .btn-primary{
    background-color: var(--brand-500) !important;
    border-color: var(--brand-500) !important;
    box-shadow: 0 2px 6px rgba(34,197,94,.18);
    transition: all .18s ease;
  }
  .btn-primary:hover,.btn-primary:focus{
    background-color: var(--brand-600) !important;
    border-color: var(--brand-600) !important;
    box-shadow: 0 6px 14px rgba(22,163,74,.22);
  }
  .btn-outline-secondary{
    color: var(--ink-500) !important;
    border-color: rgba(0,0,0,.1) !important;
  }
  .btn-outline-secondary:hover{
    background: rgba(15,23,42,.04) !important;
    color: var(--ink-700) !important;
  }
  .btn-secondary{
    background: linear-gradient(135deg, #a5b4fc, #818cf8) !important;
    border: none !important;
  }

  /* ====== Input & Select ====== */
  .form-control{
    border-radius: 10px;
    border:1px solid rgba(0,0,0,.12);
    transition: box-shadow .15s ease, border-color .15s ease;
  }
  .form-control:focus{
    border-color: var(--brand-300);
    box-shadow: 0 0 0 .2rem rgba(52,211,153,.18);
  }
  select.form-control{
    background-image: linear-gradient(135deg, var(--brand-50), transparent);
  }

  /* ====== Tabel ====== */
  .table{
    border-collapse: separate;
    border-spacing: 0 10px; /* baris ‘kartu’ */
  }
  .table thead.thead-dark th{
    background: linear-gradient(180deg, #0f172a, #111827) !important;
    border: none !important; color:#fff;
    position: sticky; top:0; z-index:1;
  }
  .table tbody tr{
    background:#fff;
    box-shadow: 0 2px 10px rgba(0,0,0,.04);
  }
  .table tbody td{
    border-top: none !important;
    padding-top: 16px; padding-bottom: 16px;
    color: var(--ink-700);
  }
  .table-hover tbody tr:hover{
    background: linear-gradient(90deg, #ffffff, #f8fffb);
  }

  /* ====== Badge status ====== */
  .badge-success{
    background: linear-gradient(135deg, var(--brand-400), var(--brand-500)) !important;
    color:#fff; box-shadow: 0 2px 6px rgba(34,197,94,.25);
  }

  /* ====== Notif badge biar tetep ‘stand out’ ====== */
  #notif-badge{
    box-shadow: 0 2px 6px rgba(0,0,0,.18);
  }
</style>

<script>
  // kecil & aman: tambahin elevasi navbar saat discroll
  document.addEventListener('scroll', function () {
    var nav = document.querySelector('nav.navbar.bg-primary');
    if (!nav) return;
    if (window.scrollY > 10) nav.classList.add('nav-scrolled');
    else nav.classList.remove('nav-scrolled');
  });
</script>

        </div>
        <div class="col-lg-6 col-5 text-right">
          <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#tambah">
            Tambah Data
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<?php echo $__env->make('management_user.modal_tambah', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="container-fluid mt--6">
  <div class="row">
    <div class="col">
      <div class="card shadow">
        <?php echo $__env->make('components.alert', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <div class="card-header border-0 bg-white">
          <h3 class="mb-0 text-dark">Data User</h3>
        </div>
        <div class="card-body">

          
         <form method="GET" action="<?php echo e(route('management_user.index')); ?>" class="mb-4">
  <div class="row">
    <div class="col-md-3 mb-2">
      <input type="text" name="search" class="form-control border-success" placeholder="Cari nama / NIK / username / unit..." value="<?php echo e(request('search')); ?>">
    </div>
    
    <div class="col-md-3 mb-2">
      <select name="unit" class="form-control border-info">
        <option value="">-- Filter Unit --</option>
        <?php $__currentLoopData = $units; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $unit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($unit->id); ?>" <?php echo e(request('unit') == $unit->id ? 'selected' : ''); ?>>
            <?php echo e($unit->nama_unit); ?>

          </option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>

    <div class="col-md-2 mb-2">
      <button type="submit" class="btn btn-primary w-100">
        <i class="fas fa-search"></i> Cari
      </button>
    </div>

    <div class="col-md-2 mb-2">
      <a href="<?php echo e(route('management_user.index')); ?>"
         class="btn btn-outline-secondary w-100"
         style="padding: 8px 16px; font-size: 14px;"
         title="Hapus semua filter pencarian">
        <i class="fas fa-sync-alt mr-1"></i> Reset
      </a>
    </div>
  </div>
</form>


          
          <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped text-center align-middle">
              <thead class="thead-dark">
                <tr>
                  <th>No</th>
                  <th>Nama</th>
                  <th>NIK</th>
                  <th>Username</th>
                  <th>Gender</th>
                  <th>Department</th>
                  <th>Jabatan</th>
                  <th>Unit</th>
                  <th>Kepala Unit</th>
                  <th>Supervisor Unit</th>
                  <th>Manager Unit</th>
                  <th>TTD</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                  <td><?php echo e($loop->iteration); ?></td>
                  <td><?php echo e($user->nama); ?></td>
                  <td><?php echo e($user->nik); ?></td>
                  <td><?php echo e($user->username); ?></td>
                  <td><?php echo e($user->gender->gender ?? '-'); ?></td>
                  <td><?php echo e($user->department->nama ?? '-'); ?></td>
                  <td><?php echo e($user->jabatan->nama ?? '-'); ?></td>
                  <td><?php echo e($user->unit?->nama_unit ?? '-'); ?></td>
                  <td><?php echo e($user->unit?->kepalaUnit?->nama ?? '-'); ?></td>
                  <td><?php echo e($user->unit?->supervisorUnit?->nama ?? '-'); ?></td>
                  <td><?php echo e($user->unit?->managerUnit?->nama ?? '-'); ?></td>
                  <td>
                    <?php if($user->ttd_path): ?>
                      <img src="<?php echo e(asset('storage/' . $user->ttd_path)); ?>"
                           alt="TTD <?php echo e($user->nama); ?>"
                           width="70"
                           class="rounded shadow-sm border">
                    <?php else: ?>
                      <span class="text-muted">-</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <span class="badge badge-<?php echo e($user->status_id == 1 ? 'success' : 'secondary'); ?>">
                      <?php echo e($user->status->status ?? '-'); ?>

                    </span>
                  </td>
                  <td>
                    <a href="<?php echo e(url('management_user/edit/'.$user->id)); ?>" class="btn btn-sm btn-warning mb-1">
                      <i class="fas fa-edit"></i> Edit
                    </a>
                    <form action="<?php echo e(route('management_user.destroy', $user->id)); ?>" method="POST" class="d-inline"
                          onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                      <?php echo csrf_field(); ?>
                      <?php echo method_field('DELETE'); ?>
                      <button type="submit" class="btn btn-sm btn-danger">
                        <i class="fas fa-trash-alt"></i> Hapus
                      </button>
                    </form>
                  </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>
  </div>

  <footer class="footer pt-0">
    <div class="row align-items-center justify-content-lg-between">
      <div class="col-lg-6">
        <div class="copyright text-center text-lg-left text-muted">
          &copy; 2025 <a href="https://keluarga-kita.com" class="font-weight-bold ml-1" target="_blank">RSKK IT Team</a>
        </div>
      </div>
    </div>
  </footer>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\project_form\resources\views/management_user/index.blade.php ENDPATH**/ ?>