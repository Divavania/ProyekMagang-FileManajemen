@php
  $ext = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));
  $fileUrl = asset('storage/' . $file->file_path);
@endphp

<div class="col-6 col-sm-4 col-md-3 file-item">
  <div class="card h-100 file-card position-relative shadow-sm border-0 rounded-3">

    {{-- Checkbox + Favorite + Dropdown --}}
    <div class="position-absolute top-0 start-0 m-2">
     <input type="checkbox" class="form-check-input select-checkbox d-none" name="selected_files[]" value="{{ $file->id }}">
    </div>

<div class="position-absolute top-0 end-0 m-2">
  <div class="dropdown">
    <button class="btn btn-sm btn-light p-1 rounded-circle" type="button" data-bs-toggle="dropdown">
      <i class="bi bi-three-dots-vertical"></i>
    </button>

    <ul class="dropdown-menu dropdown-menu-end">
      <li>
        <a class="dropdown-item" href="{{ route('files.download', $file->id) }}">
          <i class="bi bi-download me-2"></i>Unduh
        </a>
      </li>

      {{-- FIX: tambahkan type="button" --}}
      <li>
        <button type="button" class="dropdown-item"
                data-bs-toggle="modal"
                data-bs-target="#editFileModal{{ $file->id }}">
          <i class="bi bi-pencil me-2"></i>Ubah Nama
        </button>
      </li>

      <li>
      <button type="button" class="dropdown-item"
              data-bs-toggle="modal"
              data-bs-target="#moveFileModal{{ $file->id }}">
        <i class="bi bi-folder-symlink me-2"></i>Pindahkan
      </button>
    </li>

      <li>
        <button type="button" class="dropdown-item"
                data-bs-toggle="modal"
                data-bs-target="#shareFileModal{{ $file->id }}">
          <i class="bi bi-share me-2"></i>Berbagi
        </button>
      </li>

      <li>
          @if(!is_null($file->folder_id))
              {{-- File berasal dari folder → disabled --}}
              <button class="dropdown-item" disabled
                      title="File ini berasal dari folder '{{ $file->folder->name }}'. Ubah status folder untuk mempengaruhi semua file di dalamnya.">
                  <i class="bi bi-shield-lock me-2"></i>Ubah Status
              </button>
          @else
              {{-- File root → bisa ubah status --}}
              <button type="button"
                      class="dropdown-item toggle-status-btn"
                      data-id="{{ $file->id }}"
                      data-status="{{ $file->status === 'Private' ? 'Public' : 'Private' }}">
                  @if($file->status === 'Private')
                      <i class="bi bi-unlock me-2 text-success"></i>Jadikan Publik
                  @else
                      <i class="bi bi-lock me-2 text-warning"></i>Jadikan Privat
                  @endif
              </button>
          @endif
      </li>

      <li>
        <button type="button"
                class="dropdown-item toggle-favorite-btn"
                data-id="{{ $file->id }}">
          @if($file->isFavoritedBy(auth()->id()))
            <i class="bi bi-star-fill text-warning me-2"></i>Hapus dari Favorit
          @else
            <i class="bi bi-star me-2"></i>Tambah ke Favorit
          @endif
        </button>
      </li>

       <li><hr class="dropdown-divider"></li>

      <li>
        <button type="button" class="dropdown-item text-danger btn-delete" data-id="{{ $file->id }}">
          <i class="bi bi-trash me-2"></i>Hapus
        </button>
      </li>
      </li>
    </ul>
  </div>
</div>

    {{-- Thumbnail / Preview --}}
    <div class="card-body text-center p-3">
      <a href="#" data-bs-toggle="modal" data-bs-target="#previewModal{{ $file->id }}">
        @if(in_array($ext, ['jpg','jpeg','png','gif','webp','bmp']))
          <img src="{{ $fileUrl }}" class="img-fluid rounded" style="max-height:110px; object-fit:cover; width:100%;">
        @elseif(in_array($ext, ['mp3','wav','ogg','flac']))
          <div class="d-flex align-items-center justify-content-center bg-light rounded" style="height:110px;">
            <i class="bi bi-file-earmark-music fs-1 text-primary"></i>
          </div>
        @elseif(in_array($ext, ['mp4','mov','avi','mkv','webm']))
          <div class="position-relative bg-white rounded overflow-hidden" style="height:110px;">
            <video class="w-100 h-100" style="object-fit:cover;" muted preload="metadata" playsinline
                   onmouseenter="this.play()" onmouseleave="this.pause(); this.currentTime=0;">
              <source src="{{ $fileUrl }}" type="video/mp4">
            </video>
          </div>
        @elseif($ext == 'pdf')
         {{-- PDF --}}
           <div class="d-flex align-items-center justify-content-center bg-light rounded" style="height:110px;">
              <i class="bi bi-file-earmark-pdf fs-1 text-danger"></i>
          </div>
          @elseif(in_array($ext, ['doc','docx']))
          {{-- WORD --}}
          <div class="d-flex align-items-center justify-content-center bg-light rounded" style="height:110px;">
              <i class="bi bi-file-earmark-word fs-1 text-primary"></i>
          </div>
          @elseif(in_array($ext, ['ppt','pptx']))
          {{-- POWERPOINT --}}
          <div class="d-flex align-items-center justify-content-center bg-light rounded" style="height:110px;">
              <i class="bi bi-file-earmark-ppt fs-1 text-warning"></i>
          </div>
          @elseif(in_array($ext, ['xls','xlsx','csv']))
          {{-- EXCEL --}}
          <div class="d-flex align-items-center justify-content-center bg-light rounded" style="height:110px;">
              <i class="bi bi-file-earmark-excel fs-1 text-success"></i>
          </div>
          @elseif(in_array($ext, ['zip','rar','7z']))
          {{-- ZIP / RAR --}}
          <div class="d-flex align-items-center justify-content-center bg-light rounded" style="height:110px;">
              <i class="bi bi-file-earmark-zip fs-1 text-dark"></i>
          </div>
        @else
          <div class="d-flex align-items-center justify-content-center bg-light rounded" style="height:110px;">
            <i class="bi bi-file-earmark fs-1 text-secondary"></i>
          </div>
        @endif
      </a>

      <p class="text-truncate mt-2 mb-0" title="{{ $file->file_name }}">{{ $file->file_name }}</p>
      <small class="text-muted d-block">{{ $file->created_at->format('d M Y') }}</small>
    </div>
  </div>
</div>

<style>
  .file-card {
  position: relative;
}

.file-card video {
  pointer-events: none; /* agar video tidak menutupi tombol */
  background-color: #000; /* biar tetap kelihatan area video */
}

.file-card .position-absolute {
  z-index: 10;
}

.file-card video {
  pointer-events: none;
}

.file-card .toggle-favorite i {
  transition: transform 0.2s ease;
}

.file-card .toggle-favorite:hover i {
  transform: scale(1.1);
}

.file-card .toggle-favorite {
  z-index: 10;
}

.file-card .toggle-favorite i {
  transition: transform 0.2s ease;
}

.file-card .toggle-favorite:hover i {
  transform: scale(1.1);
}

.file-card .dropdown {
    opacity: 0;
    visibility: hidden;
    transition: 0.2s ease;
}

/* saat card di-hover: tampilkan */
.file-card:hover .dropdown {
    opacity: 1;
    visibility: visible;
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.dropdown-item.toggle-status-btn').forEach(btn => {
        btn.addEventListener('click', function(e){
            e.preventDefault();
            let fileId = this.dataset.id;
            let status = this.dataset.status;

            fetch(`/files/${fileId}/status`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({status})
            }).then(res => location.reload()); // reload setelah update
        });
    });
});
</script>