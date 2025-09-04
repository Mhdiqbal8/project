{{-- resources/views/request_service/partials/_modal_tolak.blade.php --}}
<div class="modal fade" id="modalTolak{{ $sr->id }}" tabindex="-1" role="dialog" aria-labelledby="modalTolakLabel{{ $sr->id }}" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <form action="{{ route('request_service.reject') }}" method="POST">
      @csrf
      {{-- Kirim dua-duanya biar controller bebas ambil yang ada --}}
      <input type="hidden" name="request_service_id" value="{{ $sr->id }}">
      <input type="hidden" name="service_id" value="{{ $sr->service_id }}">

      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="modalTolakLabel{{ $sr->id }}">Tolak Permohonan</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="form-group">
            <label>Alasan Penolakan</label>
            <textarea name="keterangan" class="form-control" rows="3" required placeholder="Tulis alasan penolakan..."></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin ingin menolak permohonan ini?')">
            Tolak
          </button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            Batal
          </button>
        </div>
      </div>
    </form>
  </div>
</div>
