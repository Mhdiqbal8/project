<?php $__env->startSection('content'); ?>
<!-- Main content -->
<div class="header bg-primary pb-6">
  <div class="container-fluid">
    <div class="header-body">
      <div class="row align-items-center py-4">
        <div class="col-lg-6 col-7">
          <h6 class="h2 text-white d-inline-block mb-0">Tables</h6>
          <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
            <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
              <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i></a></li>
              <li class="breadcrumb-item"><a href="#">Laporan Service</a></li>
              <li class="breadcrumb-item active" aria-current="page">Data</li>
            </ol>
          </nav>
        </div>
        <div class="col-lg-6 col-5"></div>
      </div>
    </div>
  </div>
</div>

<!-- Page content -->
<div class="container-fluid mt--6">
  <div class="row">
    <div class="col">
      <div class="card">
        <?php echo $__env->make('components.alert', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <div class="card-header border-0 d-flex align-items-center justify-content-between">
          <h3 class="mb-0 text-dark">Laporan Service</h3>

          
          <button type="button" class="btn btn-outline-primary btn-sm" onclick="gotoBulanan()">
            Lihat % Bulanan
          </button>
        </div>

        <div class="card-body">
          <div class="row form-group">
            <div class="col-md-3">
              <label for="start_date">Dari Tanggal</label>
              <input id="start_date" name="start_date" type="date" class="form-control"
                     required oninput="handleStartChange(this)">
            </div>
            <div class="col-md-3">
              <label for="end_date">Sampai Tanggal</label>
              <input id="end_date" name="end_date" type="date" class="form-control" required>
            </div>
            <div class="col-md-3 d-flex align-items-end">
              <button type="button" class="btn btn-lg btn-primary" id="btn-submit" onclick="search_report();">
                <span class="spinner-border spinner-border-sm mr-2 d-none" id="spin" role="status" aria-hidden="true"></span>
                Submit
              </button>
            </div>
          </div>

          <div class="form-group row ml-1">
            <button type="button" class="btn btn-warning ml-1" id="btn-reset" style="display:none;" onclick="resetFilters();">
              <i class="fas fa-reply"></i> Reset
            </button>

            <form action="<?php echo e(route('laporan.service.search_excel')); ?>" method="get" class="ml-1">
              <input id="excel_start_date" type="hidden" name="start_date" readonly>
              <input id="excel_end_date"   type="hidden" name="end_date"   readonly>
              <button type="submit" class="btn btn-success" id="btn-excel" style="display:none;">
                <i class="fas fa-file-excel"></i> Export Excel
              </button>
            </form>

            <form action="<?php echo e(route('laporan.service.search_pdf')); ?>" method="get" target="_blank" class="ml-1">
              <input id="pdf_start_date" type="hidden" name="start_date" readonly>
              <input id="pdf_end_date"   type="hidden" name="end_date"   readonly>
              <button type="submit" class="btn btn-danger" id="btn-pdf" style="display:none;">
                <i class="fas fa-file-pdf"></i> PDF
              </button>
            </form>
          </div>

          <div class="table-responsive" id="div_table_value" style="display:none;"></div>
        </div><!-- /card-body -->

      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="footer pt-0">
    <div class="row align-items-center justify-content-lg-between">
      <div class="col-lg-6">
        <div class="copyright text-center text-lg-left text-muted">
          &copy; 2022 <a href="https://www.keluarga-kita.com" class="font-weight-bold ml-1" target="_blank">
            Rumah Sakit Keluarga Kita - Developer IT Team RSKK
          </a>
        </div>
      </div>
    </div>
  </footer>
</div>


<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<script>
  // ====== UTIL ======
  function handleStartChange(el) {
    const start = el.value;
    const end = document.getElementById('end_date');
    end.min = start || '';
    if (end.value && start && end.value < start) {
      end.value = '';
    }
  }

  function esc(v) {
    if (v === null || v === undefined) return '';
    return String(v)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function rupiah(num) {
    const n = Number(num || 0);
    return 'Rp. ' + n.toLocaleString('id-ID');
  }

  function fmtTanggal(iso) {
    if (!iso) return '';
    try {
      return new Date(iso).toLocaleDateString('id-ID', { timeZone: 'Asia/Jakarta' });
    } catch (_) {
      const d = new Date(iso);
      if (isNaN(d)) return '';
      const dd = String(d.getDate()).padStart(2, '0');
      const mm = String(d.getMonth() + 1).padStart(2, '0');
      const yy = d.getFullYear();
      return `${dd}-${mm}-${yy}`;
    }
  }

  function toggleExports(show) {
    document.getElementById('btn-reset').style.display = show ? '' : 'none';
    document.getElementById('btn-excel').style.display = show ? '' : 'none';
    document.getElementById('btn-pdf').style.display   = show ? '' : 'none';
  }

  function setLoading(loading) {
    const btn = document.getElementById('btn-submit');
    const spin = document.getElementById('spin');
    if (loading) {
      btn.setAttribute('disabled', 'disabled');
      spin.classList.remove('d-none');
    } else {
      btn.removeAttribute('disabled');
      spin.classList.add('d-none');
    }
  }

  // ====== SHORTCUT: ke ringkasan bulanan ======
  function gotoBulanan() {
    const sd = document.getElementById('start_date').value;
    const d  = sd ? new Date(sd) : new Date();
    const month = d.getMonth() + 1; // 1..12
    const year  = d.getFullYear();
    window.location.href = `<?php echo e(route('laporan.service.bulanan')); ?>?month=${month}&year=${year}`;
  }

  // ====== ACTIONS ======
  async function search_report() {
    const start_date = document.getElementById('start_date').value;
    const end_date   = document.getElementById('end_date').value;

    if (!start_date) { swal('Info', 'Start Date tidak boleh kosong.', 'info'); return; }
    if (!end_date)   { swal('Info', 'End Date tidak boleh kosong.', 'info');   return; }

    // set hidden untuk export
    document.getElementById('excel_start_date').value = start_date;
    document.getElementById('excel_end_date').value   = end_date;
    document.getElementById('pdf_start_date').value   = start_date;
    document.getElementById('pdf_end_date').value     = end_date;

    setLoading(true);
    try {
      const resp = await fetch('<?php echo e(route('laporan.service.search')); ?>', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        },
        body: JSON.stringify({ start_date, end_date }),
        credentials: 'same-origin'
      });

      if (!resp.ok) {
        let msg = 'Terjadi kesalahan.';
        try {
          const j = await resp.json();
          if (j && j.message) msg = j.message;
        } catch (_) {
          try { msg = await resp.text(); } catch (_) {}
        }
        throw new Error(msg);
      }

      const json = await resp.json();

      if (!json || json.response !== 'success') {
        swal('Info', 'Data Tidak Tersedia', 'info');
        document.getElementById('div_table_value').style.display = 'none';
        toggleExports(false);
        return;
      }

      const data = Array.isArray(json.search) ? json.search : [];
      const hasData = data.length > 0;
      const div = document.getElementById('div_table_value');

      let html = `
        <table class="table table-bordered table-sm" id="table_value" cellspacing="0" style="width:100%">
          <thead class="thead-light text-center">
            <tr>
              <th>No</th>
              <th>No.Tiket</th>
              <th>Nama Pemohon</th>
              <th>Department</th>
              <th>Unit</th>
              <th>Jenis Service</th>
              <th>Inventaris/Tindakan</th>
              <th>Service</th>
              <th>Perkiraan Biaya</th>
              <th>Tanggal</th>
              <th>Keterangan</th>
              <th>Teknisi</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
      `;

      if (hasData) {
        data.forEach((row, i) => {
          const id = row.id_service;
          html += `
            <tr>
              <td class="text-center">${i + 1}</td>
              <td>${esc(row.no_tiket ?? '')}</td>
              <td>${esc(row.pemohon ?? '')}</td>
              <td>${esc(row.department ?? '')}</td>
              <td>${esc(row.nama_unit ?? '')}</td>
              <td>${esc(row.jenis_inventaris ?? '')}</td>
              <td>${esc(row.inventaris ?? '')}</td>
              <td>${esc(row.service ?? '')}</td>
              <td class="text-right">${rupiah(row.biaya_service)}</td>
              <td class="text-nowrap">${fmtTanggal(row.created_at)}</td>
              <td>${esc(row.keterangan ?? '')}</td>
              <td>${esc(row.nama_teknisi ?? '')}</td>
              <td class="text-center">
                <a target="_blank" href="<?php echo e(url('laporan_service/search_pdf_single')); ?>/${encodeURIComponent(id)}" class="btn btn-sm btn-info" title="Cetak Form">
                  <i class="fa fa-print"></i>
                </a>
              </td>
            </tr>
          `;
        });
      } else {
        html += `
          <tr>
            <td colspan="13" class="text-danger text-center">
              <h5 class="m-0"><i>Data Tidak Tersedia</i></h5>
            </td>
          </tr>
        `;
      }

      html += '</tbody></table>';

      div.innerHTML = html;
      div.style.display = '';
      toggleExports(hasData);

    } catch (err) {
      console.error(err);
      swal('Error', err.message || 'Terjadi kesalahan.', 'error');
    } finally {
      setLoading(false);
    }
  }

  function resetFilters() {
    document.getElementById('start_date').value = '';
    document.getElementById('end_date').value   = '';
    document.getElementById('end_date').removeAttribute('min');
    document.getElementById('div_table_value').style.display = 'none';
    document.getElementById('div_table_value').innerHTML = '';
    toggleExports(false);
  }

  // Prefill + auto-submit kalau datang dari bulanan
  document.addEventListener('DOMContentLoaded', () => {
    const qs = new URLSearchParams(location.search);
    const sd = qs.get('start_date');
    const ed = qs.get('end_date');
    if (sd && ed) {
      const s = document.getElementById('start_date');
      const e = document.getElementById('end_date');
      s.value = sd;
      e.value = ed;
      e.min   = sd;
      // otomatis load data
      search_report();
    }
  });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\xampp\htdocs\project_form\resources\views/laporan/laporan_service.blade.php ENDPATH**/ ?>