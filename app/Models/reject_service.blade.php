<!-- Modal Tolak Permohonan -->
<div class="modal fade" id="modalReject{{ $service_request->id }}" tabindex="-1" role="dialog" aria-labelledby="modalRejectLabel{{ $service_request->id }}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <form action="{{ route('request_service.reject') }}" method="POST">
      @csrf
      <input type="hidden" name="service_id" value="{{ $service_request->id }}">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="modalRejectLabel{{ $service_request->id }}">Tolak Permohonan</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <div class="form-group">
            <label for="alasan_penolakan">Alasan Penolakan</label>
            <textarea name="keterangan" class="form-control" rows="3" required placeholder="Tuliskan alasan penolakan..."></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">Tolak Permohonan</button>
        </div>
      </div>
    </form>
  </div>
</div>
