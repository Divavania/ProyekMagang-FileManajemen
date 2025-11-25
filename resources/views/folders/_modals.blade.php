@php
use App\Models\Folder;
use Illuminate\Support\Facades\Auth;

$allFolders = $allFolders ?? Folder::with('children')->where('created_by', Auth::id())->get();

$getSubfolderIds = function($folder) use (&$getSubfolderIds) {
    $ids = [];
    foreach ($folder->children as $child) {
        $ids[] = $child->id;
        $ids = array_merge($ids, $getSubfolderIds($child));
    }
    return $ids;
};

// Ambil semua ID folder yang tidak boleh dijadikan parent (folder itu sendiri + semua subfolder)
$invalidFolderIds = array_merge([$folder->id], $getSubfolderIds($folder));
@endphp

{{-- Modal Edit Folder --}}
<div class="modal fade" id="editFolderModal{{ $folder->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('folders.update', $folder->id) }}">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <h5 class="modal-title">Rename Folder</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="text" name="name" class="form-control" value="{{ $folder->name }}" required>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </form>
  </div>
</div>

{{-- Modal Move Folder --}}
<div class="modal fade" id="moveFolderModal{{ $folder->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('folders.move') }}">
      @csrf
      <input type="hidden" name="folder_id" value="{{ $folder->id }}">
      <div class="modal-header">
        <h5 class="modal-title">Move Folder</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <label class="form-label">Pilih Folder Tujuan:</label>
        <select name="parent_id" class="form-select">
          <option value="">Root</option>

          {{-- tampilkan semua folder kecuali folder itu sendiri --}}
          @foreach ($allFolders as $f)
            @if($f->id != $folder->id)
              <option value="{{ $f->id }}">{{ $f->name }}</option>
            @endif
          @endforeach
        </select>
        <small class="text-muted d-block mt-2">Pindahkan folder ini ke folder tujuan. Pilih "Root" untuk meletakkan di level atas.</small>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Move</button>
      </div>
    </form>
  </div>
</div>

<div class="modal fade" id="shareFolderModal{{ $folder->id }}" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content" method="POST" action="{{ route('folders.share', $folder->id) }}">
      @csrf

      <div class="modal-header">
        <h5 class="modal-title">Bagikan Folder</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        <div class="mb-3">
          <label>Email Pengguna</label>
          <input type="text" name="email" class="form-control"
                 placeholder="Masukkan satu atau lebih email, pisahkan dengan koma (,)"
                 required>
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
          <label>Pesan (Opsional)</label>
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