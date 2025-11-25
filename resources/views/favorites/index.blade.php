@extends('layouts.app')
@section('title', 'Favorit - RadarFiles')
@section('page_title', 'File Favorit')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container-fluid mt-3">
  <div class="row g-3">
    @forelse($favorites as $fav)
      @php
        $file   = $fav->file;     // relasi file
        $folder = $fav->folder;   // relasi folder
      @endphp

      @if($file)
        @php 
          $ext = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));
          $fileUrl = asset('storage/' . $file->file_path);
        @endphp

      <div class="col-6 col-sm-4 col-md-3 file-item">
        <div class="card h-100 file-card position-relative shadow-sm border-0 rounded-3">

          {{-- Favorite toggle + dropdown --}}
         <div class="position-absolute top-0 start-0 m-2 d-flex align-items-center gap-1">
  {{-- Tombol favorit di kiri atas --}}
  <button type="button"
          class="btn btn-sm p-0 bg-transparent border-0 toggle-favorite"
          data-id="{{ $file->id }}"
          title="Hapus dari Favorit">
    <i class="bi bi-star-fill text-warning fs-5"></i>
  </button>
</div>

{{-- Dropdown tiga titik tetap di kanan atas --}}
<div class="position-absolute top-0 end-0 m-2">
  <div class="dropdown">
    <button class="btn btn-sm btn-light p-1 rounded-circle" type="button" data-bs-toggle="dropdown">
      <i class="bi bi-three-dots-vertical"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end">
      <li><a class="dropdown-item" href="{{ route('files.download', $file->id) }}"><i class="bi bi-download me-2"></i>Unduh</a></li>
      <li><button type="button" class="dropdown-item btn-unfavorite" data-id="{{ $file->id }}"><i class="bi bi-star me-2"></i>Hapus dari Favorit</button></li>
    </ul>
  </div>
</div>


          {{-- Thumbnail / Preview --}}
          <div class="card-body text-center p-3">
            <a href="#" data-bs-toggle="modal" data-bs-target="#previewFileModal{{ $file->id }}">
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

            <h6 class="text-truncate mt-2 mb-0" title="{{ $file->file_name }}">{{ $file->file_name }}</h6>
            <small class="text-muted d-block">{{ $file->uploader->name ?? 'Unknown' }}</small>
            <small class="text-muted">{{ $file->created_at->format('d M Y') }}</small>
          </div>

          {{-- Modal Preview (versi disamakan dengan File Saya + tombol bintang kiri) --}}
          <div class="modal fade" id="previewFileModal{{ $file->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
              <div class="modal-content bg-dark text-white rounded-4 border-0 overflow-hidden">
                
                {{-- Header Preview --}}
                <div class="modal-header border-0 bg-dark d-flex align-items-center justify-content-between">
                  <div class="d-flex align-items-center gap-2">
                    {{-- Tombol bintang di kiri --}}
                    <button class="btn btn-sm btn-outline-warning rounded-circle toggle-favorite" data-id="{{ $file->id }}">
                      <i class="bi bi-star-fill text-warning"></i>
                    </button>
                    <h6 class="mb-0 text-truncate" style="max-width: 400px;">{{ $file->file_name }}</h6>
                  </div>

                  {{-- Dropdown kanan atas --}}
                  <div class="dropdown">
                    <button class="btn btn-sm btn-outline-light rounded-circle" type="button" data-bs-toggle="dropdown">
                      <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                      <li><a class="dropdown-item" href="{{ route('files.download', $file->id) }}"><i class="bi bi-download me-2"></i>Unduh</a></li>
                      <li><button type="button" class="dropdown-item btn-unfavorite" data-id="{{ $file->id }}"><i class="bi bi-star me-2"></i>Hapus dari Favorit</button></li>
                    </ul>
                  </div>
                </div>

                {{-- Body Preview --}}
                <div class="modal-body bg-black text-center d-flex justify-content-center align-items-center" style="max-height:75vh;">
                  @if(in_array($ext, ['jpg','jpeg','png','gif','webp','bmp']))
                    <img src="{{ $fileUrl }}" class="img-fluid rounded shadow" style="max-height:70vh; object-fit:contain;">
                  @elseif(in_array($ext, ['mp3','wav','ogg','flac']))
                    <audio controls style="width:100%;"><source src="{{ $fileUrl }}" type="audio/{{ $ext }}">Browser tidak mendukung audio.</audio>
                  @elseif(in_array($ext, ['mp4','mov','avi','mkv','webm']))
                    <video class="rounded shadow w-100" style="max-height:70vh; background:#000;" controls autoplay muted playsinline>
                      <source src="{{ $fileUrl }}" type="video/mp4">
                    </video>
                  @elseif($ext == 'pdf')
                    <iframe src="{{ $fileUrl }}" class="w-100 border-0 rounded" style="height:70vh;"></iframe>
                  @else
                    <p class="text-light opacity-75">Preview tidak tersedia untuk tipe file ini.</p>
                  @endif
                </div>

                {{-- Footer --}}
                <div class="modal-footer border-0 bg-dark justify-content-between">
                  <small class="text-secondary">Diupload: {{ $file->created_at->format('d M Y, H:i') }}</small>
                  <a href="{{ route('files.download', $file->id) }}" class="btn btn-outline-light btn-sm rounded-pill">
                    <i class="bi bi-download me-1"></i> Unduh
                  </a>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    @endif
      {{-- FAVORIT: FOLDER --}}
        @if($folder)
          <div class="col-6 col-sm-4 col-md-3 file-item">
            <div class="card h-100 shadow-sm border-0 rounded-3 bg-warning-subtle p-3 position-relative">

              <div class="position-absolute top-0 start-0 m-2">
                <button type="button"
                        class="btn btn-sm p-0 bg-transparent border-0 toggle-favorite-folder"
                        data-id="{{ $folder->id }}">
                  <i class="bi bi-star-fill text-warning fs-5"></i>
                </button>
              </div>

              <a href="{{ route('folders.show', $folder->id) }}" 
                class="text-decoration-none text-dark">

                <div class="text-center mt-4">
                  <i class="bi bi-folder-fill fs-1 text-warning"></i>
                </div>

                <h6 class="text-center text-truncate mt-2" title="{{ $folder->name }}">
                  {{ $folder->name }}
                </h6>

              </a>

            </div>
          </div>
        @endif

    @empty
      <div class="col-12 text-center py-5 text-muted">
        <i class="bi bi-star text-secondary fs-1 d-block mb-2"></i>
        Belum ada file atau folder favorit.
      </div>
    @endforelse
  </div>
</div>

<style>
.file-card .position-absolute {
  z-index: 5 !important;
}
.file-card video {
  z-index: 1;
  position: relative;
}

.file-card .position-absolute {
  z-index: 5 !important;
}

.file-card video {
  z-index: 1;
  position: relative;
}

</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.toggle-favorite, .btn-unfavorite').forEach(btn => {
    btn.addEventListener('click', async (e) => {
      e.preventDefault();
      const fileId = btn.dataset.id;
      const card = btn.closest('.col-6, .col-sm-4, .col-md-3');
      try {
        const res = await fetch(`/favorites/file/${fileId}`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
          }
        });
        const data = await res.json();
        if (data.favorited === false) {
          card.style.transition = 'all 0.3s ease';
          card.style.opacity = '0';
          setTimeout(() => card.remove(), 300);
        }
      } catch (err) {
        console.error(err);
        Swal.fire('Error', 'Terjadi kesalahan koneksi ke server.', 'error');
      }
    });
  });

  document.querySelectorAll('.toggle-favorite-folder').forEach(btn => {
    btn.addEventListener('click', async (e) => {
      e.preventDefault();
      const id = btn.dataset.id;
      const card = btn.closest('.col-6, .col-sm-4, .col-md-3');
      try {
        const res = await fetch(`/favorites/folder/${id}`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
          }
        });
        const data = await res.json();
        if (data.favorited === false) {
          card.style.transition = 'all 0.3s ease';
          card.style.opacity = '0';
          setTimeout(() => card.remove(), 300);
        }
      } catch (err) {
        console.error(err);
        Swal.fire('Error', 'Terjadi kesalahan koneksi ke server.', 'error');
      }
    });
  });  
});
</script>
@endsection
