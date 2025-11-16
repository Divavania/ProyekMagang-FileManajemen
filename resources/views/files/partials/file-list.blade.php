@php
  $ext = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));
  $fileUrl = asset('storage/' . $file->file_path);
@endphp

<div class="d-flex align-items-center justify-content-between border-bottom py-2 px-3 bg-white rounded hover-shadow-sm">

  <div class="d-flex align-items-center flex-grow-1 gap-3">

    {{-- Checkbox --}}
    <input type="checkbox" class="form-check-input select-checkbox d-none me-2" name="selected[]" value="{{ $file->id }}">

    {{-- Thumbnail --}}
    @if(in_array($ext, ['jpg','jpeg','png','gif','webp']))
      <img src="{{ $fileUrl }}" class="rounded" style="width:40px;height:40px;object-fit:cover;">
    @elseif(in_array($ext, ['mp4','mov','avi','mkv']))
      <i class="bi bi-camera-video text-secondary fs-4"></i>
    @elseif(in_array($ext, ['mp3','wav','ogg']))
      <i class="bi bi-music-note text-primary fs-4"></i>
    @else
      <i class="bi bi-file-earmark fs-4 text-secondary"></i>
    @endif

    {{-- Nama File --}}
    <div class="flex-grow-1 text-truncate">
      <span class="d-block text-truncate" title="{{ $file->file_name }}">
        {{ $file->file_name }}
      </span>
    </div>

    {{-- Pemilik --}}
    <small class="text-muted text-truncate ps-2" style="width:150px;">
      {{ $file->uploader->name ?? 'Admin' }}
    </small>

    {{-- Tanggal --}}
    <small class="text-muted text-truncate" style="width:150px;">
      {{ $file->created_at->format('d M Y') }}
    </small>

    {{-- Ukuran --}}
    <small class="text-muted text-truncate" style="width:100px;">
      {{ number_format($file->file_size / 1024, 1) }} KB
    </small>

  </div>

  {{-- Menu 3 Titik --}}
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

      {{-- FIX: Tambahkan type="button" supaya tidak submit ke bulk delete --}}
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

    </ul>
  </div>

</div>
