@php
  $ext = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));
  $fileUrl = asset('storage/' . $file->file_path);
@endphp

<div class="col-6 col-sm-4 col-md-3 file-item">
  <div class="card h-100 file-card position-relative shadow-sm border-0 rounded-3">

    {{-- Checkbox + Favorite + Dropdown --}}
    <div class="position-absolute top-0 start-0 m-2">
      <input type="checkbox" class="form-check-input select-checkbox d-none" name="selected[]" value="{{ $file->id }}">
    </div>
  {{-- Tombol Favorite di kiri atas, menu di kanan atas --}}
<div class="position-absolute top-0 start-0 m-2">
  <button type="button"
          class="btn btn-sm p-0 bg-transparent border-0 toggle-favorite"
          data-id="{{ $file->id }}"
          title="Favoritkan">
    @if(method_exists($file, 'isFavoritedBy') && $file->isFavoritedBy(auth()->id()))
      <i class="bi bi-star-fill text-warning fs-5"></i>
    @else
      <i class="bi bi-star text-secondary fs-5"></i>
    @endif
  </button>
</div>

<div class="position-absolute top-0 end-0 m-2">
  <div class="dropdown">
    <button class="btn btn-sm btn-light p-1 rounded-circle" type="button" data-bs-toggle="dropdown">
      <i class="bi bi-three-dots-vertical"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end">
      <li><a class="dropdown-item" href="{{ route('files.download', $file->id) }}"><i class="bi bi-download me-2"></i>Unduh</a></li>
      <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editFileModal{{ $file->id }}"><i class="bi bi-pencil me-2"></i>Ubah Nama</button></li>
      <li><button class="dropdown-item text-danger btn-delete" data-id="{{ $file->id }}"><i class="bi bi-trash me-2"></i>Hapus</button></li>
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
          <div class="position-relative bg-dark rounded overflow-hidden" style="height:110px;">
            <video class="w-100 h-100" style="object-fit:cover;" muted preload="metadata" playsinline
                   onmouseenter="this.play()" onmouseleave="this.pause(); this.currentTime=0;">
              <source src="{{ $fileUrl }}" type="video/mp4">
            </video>
          </div>
        @elseif($ext == 'pdf')
          <div class="d-flex align-items-center justify-content-center bg-light rounded" style="height:110px;">
            <i class="bi bi-file-earmark-pdf fs-1 text-danger"></i>
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


</style>