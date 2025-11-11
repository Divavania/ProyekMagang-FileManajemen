{{-- modals.blade.php --}}

@foreach($files as $file)
  @php
    $extension = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));
    $fileUrl = asset('storage/' . $file->file_path);
  @endphp

  {{-- === MODAL PREVIEW FILE (REVISED CLEAN VERSION) === --}}
<div class="modal fade" id="previewModal{{ $file->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content bg-dark border-0 rounded-4 shadow-lg overflow-hidden">
      {{-- Header --}}
      <div class="modal-header border-0 bg-dark text-white px-4 py-3">
        <h6 class="modal-title text-truncate" style="max-width: 80%;">
          <i class="bi bi-eye me-2"></i>{{ $file->file_name }}
        </h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      {{-- Body --}}
      <div class="modal-body d-flex justify-content-center align-items-center bg-black" style="max-height: 75vh;">
        @php
          $extension = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));
          $fileUrl = asset('storage/' . $file->file_path);
        @endphp

        {{-- === Preview === --}}
        @if(in_array($extension, ['jpg','jpeg','png','gif','webp','bmp']))
          <img src="{{ $fileUrl }}" alt="{{ $file->file_name }}" 
               class="img-fluid rounded-3 shadow"
               style="max-height:70vh; max-width:100%; object-fit:contain;">

        @elseif(in_array($extension, ['mp4','mov','avi','mkv','webm']))
          <video controls playsinline
                 class="rounded-3 shadow"
                 style="max-height:70vh; max-width:100%; object-fit:contain; background:#000;">
            <source src="{{ $fileUrl }}" type="video/mp4">
            Browser kamu tidak mendukung video.
          </video>

        @elseif($extension === 'pdf')
          <iframe src="{{ $fileUrl }}" class="w-100 rounded-3 border-0" style="height:70vh;"></iframe>

        @else
          <div class="text-center text-light opacity-75">
            <i class="bi bi-file-earmark-text fs-1 mb-3"></i>
            <p>File tidak dapat ditampilkan langsung.</p>
          </div>
        @endif
      </div>

      {{-- Footer --}}
      <div class="modal-footer border-0 bg-dark justify-content-between px-4 py-2">
        <small class="text-secondary">Diupload: {{ $file->created_at->format('d M Y, H:i') }}</small>
        <a href="{{ route('files.download', $file->id) }}" 
           class="btn btn-outline-light btn-sm rounded-pill px-3">
          <i class="bi bi-download me-1"></i> Unduh
        </a>
      </div>
    </div>
  </div>
</div>
        <div class="modal-footer border-0 justify-content-between">
          <small class="text-light opacity-75">
            Diupload: {{ $file->created_at->format('d M Y, H:i') }}
          </small>
          <a href="{{ route('files.download', $file->id) }}" class="btn btn-outline-light btn-sm">
            <i class="bi bi-download me-1"></i>Unduh
          </a>
        </div>
      </div>
    </div>
  </div>

{{-- Checkbox Pilih File --}}
{{-- 
<div class="form-check mb-2">
  <input class="form-check-input select-file-checkbox" type="checkbox" name="selected_files[]" value="{{ $file->id }}" id="selectFile{{ $file->id }}">
  <label class="form-check-label" for="selectFile{{ $file->id }}">{{ $file->file_name }}</label>
</div>
--}}

  {{-- Share Modal --}}
  <div class="modal fade" id="shareModal{{ $file->id }}" tabindex="-1" aria-labelledby="shareModalLabel{{ $file->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content rounded-3">
        <div class="modal-header">
          <h5 class="modal-title">Bagikan File: {{ $file->file_name }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Tipe Bagikan</label>
            <select id="shareType{{ $file->id }}" class="form-select share-type" data-target="#shareUserList{{ $file->id }}">
              <option value="public" {{ $file->share_type == 'public' ? 'selected' : '' }}>Publik</option>
              <option value="selective" {{ $file->share_type == 'selective' ? 'selected' : '' }}>Spesifik Pengguna</option>
            </select>
          </div>

          <div class="mb-3" id="shareUserList{{ $file->id }}" style="display: {{ $file->share_type == 'selective' ? 'block' : 'none' }};">
            <label class="form-label">Pilih Pengguna</label>
            <select class="form-select" multiple>
              @foreach($users as $user)
                <option value="{{ $user->id }}" {{ $file->shared_with && in_array($user->id, json_decode($file->shared_with)) ? 'selected' : '' }}>
                  {{ $user->name }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Link Share</label>
            <input type="text" class="form-control" value="{{ $fileUrl }}" readonly onclick="this.select()">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          <button type="button" class="btn btn-primary" onclick="copyLink('{{ $fileUrl }}')">Salin & Bagikan</button>
        </div>
      </div>
    </div>
  </div>

  {{-- Edit File Name Modal --}}
  <div class="modal fade" id="editFileModal{{ $file->id }}" tabindex="-1" aria-labelledby="editFileModalLabel{{ $file->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <form action="{{ route('files.update', $file->id) }}" method="POST" class="modal-content rounded-3">
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title">Ubah Nama File</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nama Baru</label>
            <input type="text" name="file_name" class="form-control" value="{{ $file->file_name }}" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  {{-- Move File Modal --}}
  <div class="modal fade" id="moveFileModal{{ $file->id }}" tabindex="-1" aria-labelledby="moveFileModalLabel{{ $file->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <form class="modal-content rounded-3 move-file-form" data-file-id="{{ $file->id }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Pindahkan File: {{ $file->file_name }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Pilih Folder</label>
            <select name="folder_id" class="form-select" required>
              <option value="">-- Pilih Folder --</option>
              @foreach($folders as $folder)
                <option value="{{ $folder->id }}">{{ $folder->name }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Pindahkan</button>
        </div>
      </form>
    </div>
  </div>
@endforeach

<style>
.modal-content video, .modal-content img { background:#000; }
.modal-dialog.modal-xl { max-width:90vw; }
</style>

<script>
function copyLink(link){
  navigator.clipboard.writeText(link).then(()=>alert('Link berhasil disalin!'));
}
document.querySelectorAll('.share-type').forEach(sel=>{
  sel.addEventListener('change',function(){
    const target=document.querySelector(this.dataset.target);
    target.style.display=(this.value==='selective')?'block':'none';
  });
});
</script>