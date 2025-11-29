@extends('layouts.app')
@section('title','File Publik')
@section('page_title','Semua File Publik')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <!-- Nav tabs -->
  <ul class="nav nav-tabs">
    <li class="nav-item">
      <a class="nav-link" href="{{ route('shared.index') }}">Semua File Dibagikan</a>
    </li>
    <li class="nav-item">
      <a class="nav-link active" href="{{ route('shared.public') }}">Semua File Publik</a>
    </li>
  </ul>

  <!-- Sort + View Switch -->
  <div class="d-flex align-items-center gap-2">
    <form method="GET" class="m-0">
      <select name="sort" class="form-select form-select-sm" onchange="this.form.submit()">
        <option value="latest" {{ request('sort')=='oldest' ? '' : 'selected' }}>Terbaru</option>
        <option value="oldest" {{ request('sort')=='oldest' ? 'selected' : '' }}>Terlama</option>
      </select>
    </form>
    <button id="globalListBtn" class="btn btn-primary btn-sm" title="List View"><i class="bi bi-list-ul"></i></button>
    <button id="globalGridBtn" class="btn btn-outline-primary btn-sm" title="Grid View"><i class="bi bi-grid-fill"></i></button>
  </div>
</div>

{{-- ===== FOLDER PUBLIK ===== --}}
<h5 class="mb-3">📂 Folder Publik</h5>

@if($publicFolders->count()>0)
  {{-- LIST VIEW --}}
  <div id="publicFolderList" class="table-responsive">
    <table class="table align-middle">
      <thead>
        <tr>
          <th>Nama Folder</th>
          <th>Pemilik</th>
          <th>Tanggal</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @foreach($publicFolders as $folder)
          <tr>
            <td>
              <div class="d-flex align-items-center gap-2">
                <i class="bi bi-folder-fill fs-4 text-warning"></i>
                <a href="{{ route('folders.show', $folder->id) }}" class="fw-semibold text-decoration-none text-dark">{{ $folder->name }}</a>
              </div>
            </td>
            <td>{{ $folder->user->name ?? 'Unknown' }}</td>
            <td>{{ $folder->created_at->format('d M Y') }}</td>
            <td class="text-end">
              <div class="dropdown">
                <button class="btn btn-sm btn-light" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li><a class="dropdown-item" href="{{ route('folders.downloadZip', $folder->id) }}"><i class="bi bi-download me-2"></i>Download</a></li>
                  <li>
                    <form action="{{ route('favorites.toggle.folder', $folder->id) }}" method="POST">
                      @csrf
                      <button class="dropdown-item toggle-favorite-folder" data-id="{{ $folder->id }}">
                        <i class="bi {{ $folder->isFavoritedBy(auth()->id()) ? 'bi-star-fill text-warning' : 'bi-star' }} me-2"></i>Favorite
                      </button>
                    </form>
                  </li>
                </ul>
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  {{-- GRID VIEW --}}
  <div id="publicFolderGrid" class="row g-3 d-none">
    @foreach($publicFolders as $folder)
      <div class="col-xl-3 col-lg-4 col-md-6">
        <div class="card border-0 shadow-sm p-3 position-relative h-100">
          <div class="position-absolute top-0 end-0 m-2">
            <div class="dropdown">
              <button class="btn btn-sm btn-light p-1" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('folders.downloadZip', $folder->id) }}"><i class="bi bi-download me-2"></i>Download</a></li>
                <li>
                  <form action="{{ route('favorites.toggle.folder', $folder->id) }}" method="POST">
                    @csrf
                    <button class="dropdown-item toggle-favorite-folder" data-id="{{ $folder->id }}">
                      <i class="bi {{ $folder->isFavoritedBy(auth()->id()) ? 'bi-star-fill text-warning' : 'bi-star' }} me-2"></i>Favorite
                    </button>
                  </form>
                </li>
              </ul>
            </div>
          </div>
          <a href="{{ route('folders.show', $folder->id) }}" class="fw-bold text-decoration-none text-dark">
            <i class="bi bi-folder-fill fs-1 text-warning"></i>
            <div class="mt-2 fw-semibold text-truncate">{{ $folder->name }}</div>
            <div class="text-muted small mt-1">{{ $folder->created_at->format('d M Y') }}</div>
          </a>
        </div>
      </div>
    @endforeach
  </div>
@else
  <p class="text-muted">Tidak ada folder publik.</p>
@endif

<hr>

{{-- ===== FILE PUBLIK ===== --}}
<h5 class="mb-3">📄 File Publik</h5>

@if($publicFiles->count()>0)
  {{-- LIST VIEW --}}
  <div id="publicFileList" class="table-responsive">
    <table class="table align-middle">
      <thead>
        <tr>
          <th>Nama File</th>
          <th>Pemilik</th>
          <th>Tanggal</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        @foreach($publicFiles as $file)
          @php $ext = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION)); @endphp
          <tr>
            <td>
              <div class="d-flex align-items-center gap-2">
                @if(in_array($ext, ['jpg','jpeg','png','gif','bmp','webp']))
                  <img src="{{ asset('storage/'.$file->file_path) }}" style="width:45px;height:45px;object-fit:cover;border-radius:6px;" data-bs-toggle="modal" data-bs-target="#previewPublicModal{{ $file->id }}">
                @else
                  <i class="bi bi-file-earmark fs-4" data-bs-toggle="modal" data-bs-target="#previewPublicModal{{ $file->id }}"></i>
                @endif
                <span class="fw-semibold text-dark" style="cursor:pointer;" data-bs-toggle="modal" data-bs-target="#previewPublicModal{{ $file->id }}">
                  {{ $file->file_name }}
                </span>
              </div>
            </td>
            <td>{{ $file->uploader->name ?? 'Unknown' }}</td>
            <td>{{ $file->created_at->format('d M Y') }}</td>
            <td class="text-end">
              <div class="dropdown">
                <button class="btn btn-sm btn-light" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li><a class="dropdown-item" href="{{ route('files.download', $file->id) }}"><i class="bi bi-download me-2"></i>Download</a></li>
                  <li>
                    <button type="button" class="dropdown-item toggle-favorite" data-id="{{ $file->id }}">
                      <i class="bi {{ $file->isFavoritedBy(auth()->id()) ? 'bi-star-fill text-warning' : 'bi-star' }} me-2"></i>Favorite
                    </button>
                  </li>
                </ul>
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  {{-- GRID VIEW --}}
  <div id="publicFileGrid" class="row g-3 d-none">
    @foreach($publicFiles as $file)
      @php $ext = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION)); @endphp
      <div class="col-xl-3 col-lg-4 col-md-6">
        <div class="card border-0 shadow-sm position-relative h-100 p-3 text-center">
          <div class="position-absolute top-0 end-0 m-2">
            <div class="dropdown">
              <button class="btn btn-sm btn-light p-1" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="{{ route('files.download', $file->id) }}"><i class="bi bi-download me-2"></i>Download</a></li>
                <li>
                  <button type="button" class="dropdown-item toggle-favorite" data-id="{{ $file->id }}">
                    <i class="bi {{ $file->isFavoritedBy(auth()->id()) ? 'bi-star-fill text-warning' : 'bi-star' }} me-2"></i>Favorite
                  </button>
                </li>
              </ul>
            </div>
          </div>

          {{-- Thumbnail/Preview Trigger --}}
          @if(in_array($ext, ['jpg','jpeg','png','gif','bmp','webp']))
            <div class="file-thumb mb-2" data-bs-toggle="modal" data-bs-target="#previewPublicModal{{ $file->id }}">
              <img src="{{ asset('storage/' . $file->file_path) }}" class="img-fluid">
            </div>
          @elseif($ext == 'pdf')
            <i class="bi bi-file-earmark-pdf fs-1 text-danger mb-2" data-bs-toggle="modal" data-bs-target="#previewPublicModal{{ $file->id }}"></i>
          @elseif($ext == 'txt')
            <i class="bi bi-file-earmark-text fs-1 text-muted mb-2" data-bs-toggle="modal" data-bs-target="#previewPublicModal{{ $file->id }}"></i>
          @else
            <i class="bi bi-file-earmark fs-1 text-secondary mb-2" data-bs-toggle="modal" data-bs-target="#previewPublicModal{{ $file->id }}"></i>
          @endif

          <div class="fw-semibold text-truncate" data-bs-toggle="modal" data-bs-target="#previewPublicModal{{ $file->id }}">
            {{ $file->file_name }}
          </div>
          <div class="text-muted small">{{ $file->created_at->format('d M Y') }}</div>
        </div>
      </div>
    @endforeach
  </div>
@else
  <p class="text-muted">Belum ada file publik.</p>
@endif

{{-- ===== MODAL PREVIEW FILE PUBLIK ===== --}}
@foreach($publicFiles as $file)
  @php $ext = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION)); $fileUrl = asset('storage/' . $file->file_path); @endphp
  <div class="modal fade" id="previewPublicModal{{ $file->id }}" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ $file->file_name }}</h5>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-center">
          @if(in_array($ext, ['jpg','jpeg','png','gif','webp','bmp']))
            <img src="{{ $fileUrl }}" class="img-fluid">
          @elseif(in_array($ext, ['mp4','webm','ogg','mov','mkv']))
            <video controls width="100%" style="max-height:600px;"><source src="{{ $fileUrl }}"></video>
          @elseif($ext === 'pdf')
            <iframe src="{{ $fileUrl }}" width="100%" height="600px"></iframe>
          @elseif($ext === 'txt' && file_exists(storage_path('app/public/'.$file->file_path)))
            <textarea class="form-control" rows="20" readonly>{{ file_get_contents(storage_path('app/public/'.$file->file_path)) }}</textarea>
          @else
            <p class="text-muted">Preview tidak tersedia untuk tipe file ini.</p>
          @endif
        </div>
      </div>
    </div>
  </div>
@endforeach
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const globalListBtn = document.getElementById('globalListBtn');
  const globalGridBtn = document.getElementById('globalGridBtn');
  const folderList = document.getElementById('publicFolderList');
  const folderGrid = document.getElementById('publicFolderGrid');
  const fileList = document.getElementById('publicFileList');
  const fileGrid = document.getElementById('publicFileGrid');

  function showList(){
    folderList.classList.remove('d-none'); folderGrid.classList.add('d-none');
    fileList.classList.remove('d-none'); fileGrid.classList.add('d-none');
    globalListBtn.classList.add('btn-primary'); globalListBtn.classList.remove('btn-outline-primary');
    globalGridBtn.classList.add('btn-outline-primary'); globalGridBtn.classList.remove('btn-primary');
  }

  function showGrid(){
    folderGrid.classList.remove('d-none'); folderList.classList.add('d-none');
    fileGrid.classList.remove('d-none'); fileList.classList.add('d-none');
    globalGridBtn.classList.add('btn-primary'); globalGridBtn.classList.remove('btn-outline-primary');
    globalListBtn.classList.add('btn-outline-primary'); globalListBtn.classList.remove('btn-primary');
  }

  showList(); // default
  globalListBtn.addEventListener('click', showList);
  globalGridBtn.addEventListener('click', showGrid);

  // Toggle favorite file
  document.querySelectorAll('.toggle-favorite').forEach(btn=>{
    btn.addEventListener('click', async e=>{
      e.preventDefault();
      const fileId = btn.dataset.id;
      try {
        const res = await fetch(`/favorites/file/${fileId}`,{
          method:'POST',
          headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'Accept':'application/json'}
        });
        const data = await res.json();
        btn.innerHTML = data.favorited ? '<i class="bi bi-star-fill text-warning me-2"></i>Favorited':'<i class="bi bi-star me-2"></i>Favorite';
      } catch(err){ console.error(err); alert('Gagal mengubah favorit'); }
    });
  });

  // Toggle favorite folder
  document.querySelectorAll('.toggle-favorite-folder').forEach(btn=>{
    btn.addEventListener('click', async e=>{
      e.preventDefault();
      const id = btn.dataset.id;
      try{
        const res = await fetch(`/favorites/folder/${id}`,{
          method:'POST',
          headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content,'Accept':'application/json'}
        });
        const data = await res.json();
        btn.innerHTML = data.favorited ? '<i class="bi bi-star-fill text-warning me-2"></i>Favorited':'<i class="bi bi-star me-2"></i>Favorite';
      } catch(err){ console.error(err); alert('Gagal mengubah favorit'); }
    });
  });
});
</script>
@endpush

<style>
.file-thumb { width:100%; height:140px; border-radius:10px; overflow:hidden; background:#f8f9fa; display:flex; align-items:center; justify-content:center; cursor:pointer; }
.file-thumb img { width:85%; height:100%; object-fit:cover; }
</style>