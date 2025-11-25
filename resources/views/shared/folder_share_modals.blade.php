@foreach($allShared as $share)
    @php $folder = $share->folder; @endphp
    @if(!$folder) @continue @endif

    {{-- ========================= --}}
    {{--       RENAME FOLDER       --}}
    {{-- ========================= --}}
    <div class="modal fade" id="renameModal{{ $folder->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <form action="{{ route('folders.update', $folder->id) }}" method="POST">
                    @csrf @method('PUT')

                    <div class="modal-header">
                        <h5 class="modal-title">Ubah Nama Folder</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <input type="text" name="name" class="form-control"
                               value="{{ $folder->name }}" required>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    {{-- ========================= --}}
    {{--       SHARE FOLDER        --}}
    {{-- ========================= --}}
    {{-- Share Modal --}}
  <div class="modal fade" id="shareFolderModal{{ $folder->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="POST" action="{{ route('folders.share', $folder->id) }}">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Bagikan File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label>Email Pengguna</label>
                    <input type="text" name="email" class="form-control"
                        placeholder="Masukkan satu atau lebih email, pisahkan dengan koma ( , )" required>
                </div>

                <div class="mb-3">
                    <label>Izin Akses</label>
                    <select name="permission" class="form-select" required>
                        <option value="view">Lihat</option>
                        <option value="edit">Edit</option>
                        <option value="download">Unduh</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Pesan (opsional)</label>
                    <textarea name="message" class="form-control" rows="3" placeholder="Tambahkan pesan..."></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Bagikan</button>
            </div>
        </form>
    </div>
  </div>

@endforeach