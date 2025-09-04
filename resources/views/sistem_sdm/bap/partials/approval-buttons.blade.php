@php
    $user  = $user  ?? auth()->user();
    $units = $units ?? collect();
@endphp

{{-- ACC Kepala Unit / Supervision --}}
@if (
    in_array(strtolower(optional($user->jabatan)->nama), ['kepala unit', 'supervision']) &&
    !$form->kepala_unit_approved_at &&
    !$form->supervision_approved_at
)
    @if(strtolower(optional($user->jabatan)->nama) === 'kepala unit')
        <form method="POST" action="{{ route('bap.approve_kepala_unit', $form->id) }}" class="mb-3">
            @csrf
            <button type="submit" class="btn btn-success">ACC Kepala Unit</button>
        </form>
    @elseif(strtolower(optional($user->jabatan)->nama) === 'supervision')
        <form method="POST" action="{{ route('bap.approve_supervision', $form->id) }}" class="mb-3">
            @csrf
            <button type="submit" class="btn btn-primary">ACC Supervision</button>
        </form>
    @endif
@endif

{{-- ACC Manager --}}
@if (user_can('acc_manager_bap') && !$form->manager_approved_at)
    <form method="POST" action="{{ route('bap.approve', $form->id) }}" class="mb-3">
        @csrf
        <button type="submit" class="btn btn-success">Approve sebagai Manager</button>
    </form>
@endif

{{-- ACC Mutu --}}
@if (user_can('acc_mutu_bap') && $form->manager_approved_at && !$form->mutu_approved_at)
    <form method="POST" action="{{ route('bap.accMutu', $form->id) }}" class="mb-3">
        @csrf
        <button type="submit" class="btn btn-warning">✔ ACC MUTU & Tag Unit</button>
    </form>
@endif

{{-- Tombol & Modal Tag Unit --}}
@if(user_can('acc_mutu_bap') && $form->mutu_approved_at)
    <button class="btn btn-info mb-3" data-bs-toggle="modal" data-bs-target="#modalTagUnit">
        📌 Tag Unit Terkait
    </button>

    <!-- Modal Tag Unit -->
    <div class="modal fade" id="modalTagUnit" tabindex="-1" aria-labelledby="modalTagUnitLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('bap.tag_unit', $form->id) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalTagUnitLabel">Pilih Unit yang Terlibat</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            @foreach($units as $unit)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="unit_ids[]"
                                           value="{{ $unit->id }}"
                                           id="unit{{ $unit->id }}"
                                           {{ $form->taggedUnits->contains($unit->id) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="unit{{ $unit->id }}">
                                        {{ $unit->nama }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Simpan Tag Unit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endif

{{-- ACC Final Unit --}}
@if (
    user_can('acc_final_bap') &&
    !$form->final_approved_at &&
    $form->manager_approved_at &&
    $form->mutu_approved_at
)
    <!-- <form method="POST" action="{{ route('bap.approve', $form->id) }}" class="mb-3">
        @csrf
        <button type="submit" class="btn btn-dark">Finalisasi Form</button>
    </form> -->

    {{-- Form Kendala (hanya muncul setelah mutu_approved_at) --}}
    <form method="POST" action="{{ route('bap.kendala_update', $form->id) }}" class="mb-3">
        @csrf
        <div class="mb-3">
            <label for="kendala" class="form-label fw-bold">
                Isi Kendala / Tindakan Unit Terkait:
            </label>
            <textarea name="kendala" id="kendala" class="form-control" rows="3" required>{{ $form->kendala }}</textarea>
        </div>
        <button type="submit" class="btn btn-warning">Simpan Kendala</button>
    </form>
@endif
