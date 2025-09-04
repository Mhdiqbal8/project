@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 mt-4">
  <h3 class="text-success mb-4 fw-bold">
    <i class="fas fa-file-alt me-2"></i> Formulir BAP
  </h3>

  <div class="card shadow-sm border-0">
    <div class="card-body">

      <form action="{{ route('bap.store_bap') }}" method="POST" id="form-bap">
        @csrf

        {{-- JUDUL --}}
        <div class="mb-4">
          <label for="judul" class="form-label fw-bold">
            Judul Formulir <span class="text-muted small">(Opsional)</span>
          </label>
          <input type="text" name="judul" id="judul"
                 class="form-control rounded-pill shadow-sm"
                 placeholder="Misalnya: Kesalahan Input Obat Pasien"
                 value="{{ old('judul') }}">
          <div class="form-text">Kosongkan jika ingin pakai judul otomatis.</div>
        </div>

        {{-- DIVISI VERIFIKASI --}}
        <div class="mb-4">
          <label for="divisi_verifikasi" class="form-label fw-bold">
            Divisi Verifikasi <span class="text-danger">*</span>
          </label>
          <select name="divisi_verifikasi" id="divisi_verifikasi"
                  class="form-select rounded-pill shadow-sm" required>
            <option value="">-- Pilih Divisi --</option>
            <option value="IT"          {{ old('divisi_verifikasi') === 'IT' ? 'selected' : '' }}>IT</option>
            <option value="Maintenance" {{ old('divisi_verifikasi') === 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
            <option value="Lainnya"     {{ old('divisi_verifikasi') === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
          </select>
        </div>

        {{-- PERBAIKAN SISTEM (Dynamic) --}}
        <div class="mb-4">
          <label class="form-label fw-bold mb-2">
            1️⃣ Perbaikan Sistem <span class="text-danger">*</span>
          </label>

          {{-- quick hint --}}
          <div class="text-muted small mb-2">
            Pilih minimal satu perbaikan yang sesuai.
          </div>

          <div id="checkbox-perbaikan" class="row row-cols-1 row-cols-md-2 gy-2">
            {{-- Dirender via JS sesuai divisi --}}
          </div>

          {{-- pesan error client-side --}}
          <div id="perbaikan-error" class="invalid-feedback d-block" style="display:none;">
            Minimal pilih satu perbaikan sistem.
          </div>
        </div>

        {{-- TINDAKAN MEDIS --}}
        <div class="mb-4">
          <label for="tindakan_medis" class="form-label fw-bold">Tindakan Medis</label>
          <input type="text" name="tindakan_medis"
                 class="form-control rounded-pill shadow-sm"
                 placeholder="Isi jika ada tindakan medis"
                 value="{{ old('tindakan_medis') }}">
        </div>

        {{-- LAIN-LAIN --}}
        <div class="mb-4">
          <label for="lain_lain" class="form-label fw-bold">Lain-lain</label>
          <input type="text" name="lain_lain"
                 class="form-control rounded-pill shadow-sm"
                 placeholder="Isi jika ada keterangan lain"
                 value="{{ old('lain_lain') }}">
        </div>

        {{-- PERMASALAHAN LAIN --}}
        <div class="mb-4">
          <label for="permasalahan_lain" class="form-label fw-bold">Permasalahan Lain</label>
          <textarea name="permasalahan_lain" rows="3"
                    class="form-control rounded shadow-sm"
                    placeholder="Isi jika ada masalah lain">{{ old('permasalahan_lain') }}</textarea>
        </div>

        {{-- BUTTON SUBMIT --}}
        <div class="d-grid">
          <button type="submit" class="btn btn-success rounded-pill py-2 fw-bold">
            <i class="fas fa-save me-1"></i> Simpan Form BAP
          </button>
        </div>
      </form>

    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
  /* Full width layout rapi */
  .card { border-radius: 14px; }
  .form-control, .form-select { min-height: 44px; }
  /* Checkbox list lebih padat */
  #checkbox-perbaikan .form-check { padding: .35rem .75rem; border-radius: 10px; }
  #checkbox-perbaikan .form-check:hover { background: #f7f9fc; }
</style>
@endpush

@push('scripts')
<script>
  // Opsi per divisi
  const perbaikanOptions = {
    IT: [
      'Dokumentasi/penulisan SOAP',
      'Laporan Operasi',
      'Input Obat Pasien',
      'Tidak Menulis SOAP',
      'Input Penunjang (Laboratorium / Radiologi)'
    ],
    Maintenance: [
      'Kerusakan AC',
      'Kebocoran Air',
      'Listrik Mati',
      'Pipa Mampet',
      'Kerusakan Pintu/Jendela'
    ],
    Lainnya: [
      'Kesalahan Data Entry',
      'Permintaan Edit Data',
      'Laporan Masalah Umum'
    ]
  };

  // Old selections dari server (agar centang balik kalau validation fail)
  const oldPerbaikan = @json(old('perbaikan', []));

  function renderCheckboxes(divisi) {
    const container = document.getElementById('checkbox-perbaikan');
    container.innerHTML = '';

    const list = perbaikanOptions[divisi] || [];
    list.forEach((item, i) => {
      const id = `perbaikan_${divisi}_${i}`;
      const col = document.createElement('div');
      col.className = 'col';

      const checked = oldPerbaikan.includes(item) ? 'checked' : '';

      col.innerHTML = `
        <div class="form-check border">
          <input class="form-check-input" type="checkbox" ${checked}
                 name="perbaikan[]" value="${item}" id="${id}">
          <label class="form-check-label" for="${id}">
            ${item}
          </label>
        </div>
      `;

      container.appendChild(col);
    });

    // reset pesan error ketika render ulang
    hidePerbaikanError();
  }

  function showPerbaikanError() {
    const el = document.getElementById('perbaikan-error');
    el.style.display = 'block';
  }
  function hidePerbaikanError() {
    const el = document.getElementById('perbaikan-error');
    el.style.display = 'none';
  }

  document.addEventListener('DOMContentLoaded', () => {
    const selectDivisi = document.getElementById('divisi_verifikasi');
    const form = document.getElementById('form-bap');

    // Render awal sesuai pilihan (termasuk old())
    if (selectDivisi.value) renderCheckboxes(selectDivisi.value);

    // Render ulang saat divisi diubah
    selectDivisi.addEventListener('change', function () {
      renderCheckboxes(this.value);
    });

    // Validasi minimal 1 checkbox
    form.addEventListener('submit', (e) => {
      const anyChecked = [...document.querySelectorAll('#checkbox-perbaikan input[type="checkbox"]')]
        .some(cb => cb.checked);

      if (!anyChecked) {
        e.preventDefault();
        showPerbaikanError();
        // fokuskan ke area perbaikan
        document.getElementById('checkbox-perbaikan').scrollIntoView({behavior: 'smooth', block: 'center'});
      }
    });
  });
</script>
@endpush
