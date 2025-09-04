{{-- Modal Approve --}}
<div class="modal fade" id="modalApprove{{ $service->id }}" tabindex="-1" role="dialog" aria-labelledby="modalApproveLabel{{ $service->id }}" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <form action="{{ route('approve.modal.service') }}" method="POST">
      @csrf
      <input type="hidden" name="service_id" value="{{ $service->id }}">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="modalApproveLabel{{ $service->id }}">Approve Permohonan</h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Jenis Permohonan</label><br>
            <label><input type="radio" name="type_permohonan" value="0" required> Not Urgent</label>
            <label class="ml-3"><input type="radio" name="type_permohonan" value="1" required> Urgent</label>
          </div>
          <div class="form-group">
            <label>Keterangan Tambahan</label>
            <textarea name="keterangan" class="form-control" rows="3" placeholder="Opsional..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Approve</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        </div>
      </div>
    </form>
  </div>
</div>
