{{-- resources/views/request_service/partials/modal_action.blade.php --}}
@if ($sr->service)

  {{-- PROGRESS (6 -> 7) : tanpa input, langsung konfirmasi --}}
  <div class="modal fade" id="modalProgress{{ $sr->id }}" tabindex="-1" role="dialog" aria-labelledby="modalProgressLabel{{ $sr->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <form method="POST" action="{{ route('request_service.approveProgress', ['id' => $sr->id]) }}">
        @csrf
        @method('PATCH')
        <input type="hidden" name="request_service_id" value="{{ $sr->id }}">
        <input type="hidden" name="service_id" value="{{ $sr->service_id }}">
        <div class="modal-content">
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title" id="modalProgressLabel{{ $sr->id }}">Konfirmasi On Progress</h5>
            <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
          </div>
          <div class="modal-body">
            Lanjutkan status ke <strong>On Progress</strong> sekarang?
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-success">Ya</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  {{-- TOLAK (-> 10) --}}
  <div class="modal fade" id="modalTolak{{ $sr->id }}" tabindex="-1" role="dialog" aria-labelledby="modalTolakLabel{{ $sr->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <form method="POST" action="{{ route('request_service.reject') }}">
        @csrf
        <input type="hidden" name="request_service_id" value="{{ $sr->id }}">
        <input type="hidden" name="service_id" value="{{ $sr->service_id }}">
        <div class="modal-content">
          <div class="modal-header bg-danger text-white">
            <h5 class="modal-title" id="modalTolakLabel{{ $sr->id }}">Tolak Permohonan</h5>
            <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label>Alasan Penolakan</label>
              <textarea name="keterangan" class="form-control" rows="3" required placeholder="Tulis alasan penolakan..."></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin ingin menolak permohonan ini?')">Tolak</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  {{-- SELESAI (7 -> 9) : keterangan WAJIB --}}
  <div class="modal fade" id="modalSelesai{{ $sr->id }}" tabindex="-1" role="dialog" aria-labelledby="modalSelesaiLabel{{ $sr->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <form method="POST" action="{{ route('request_service.approveFinish', ['id' => $sr->id]) }}">
        @csrf
        @method('PATCH')
        <input type="hidden" name="request_service_id" value="{{ $sr->id }}">
        <input type="hidden" name="service_id" value="{{ $sr->service_id }}">
        <div class="modal-content">
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title" id="modalSelesaiLabel{{ $sr->id }}">Tandai Selesai</h5>
            <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
          </div>
          <div class="modal-body">
            <p class="mb-2">Tuliskan ringkasan pekerjaan/perbaikan yang dilakukan (WAJIB).</p>
            <div class="form-group">
              <label>Ringkasan pekerjaan/perbaikan</label>
              <textarea name="keterangan" class="form-control" rows="3" required placeholder="Contoh: Ganti keyboard, update driver VGA, bersihkan fan, tes OK.">{{ old('keterangan') }}</textarea>
              @error('keterangan')
                <small class="text-danger d-block mt-1">{{ $message }}</small>
              @enderror
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-success">Selesai</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  {{-- CLOSED (8) --}}
  <div class="modal fade" id="modalClosed{{ $sr->id }}" tabindex="-1" role="dialog" aria-labelledby="modalClosedLabel{{ $sr->id }}" aria-hidden="true">
    <div class="modal-dialog" role="document">
      {{-- ➜ PERBAIKAN: kirim ID ke route --}}
      <form method="POST" action="{{ route('request_service.closed', ['id' => $sr->id]) }}">
        @csrf
        <input type="hidden" name="request_service_id" value="{{ $sr->id }}">
        <input type="hidden" name="service_id" value="{{ $sr->service_id }}">
        <div class="modal-content">
          <div class="modal-header bg-warning text-white">
            <h5 class="modal-title" id="modalClosedLabel{{ $sr->id }}">Tutup Tiket (Closed)</h5>
            <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
          </div>
          <div class="modal-body">
            Tutup tiket tanpa tindakan lanjutan?
            <div class="form-group mt-3">
              <label>Keterangan (opsional)</label>
              <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan close tiket..."></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-warning" onclick="return confirm('Yakin menutup tiket ini?')">Closed</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          </div>
        </div>
      </form>
    </div>
  </div>

@endif
