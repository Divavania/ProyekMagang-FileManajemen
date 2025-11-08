@extends('layouts.app')
@section('title', 'Dashboard - RadarFiles')

@section('content')
<div class="container-fluid mt-3">

  {{-- 🔍 Form Pencarian --}}
  <form class="row g-2 mb-4" method="GET" action="{{ route('dashboard') }}">
    <div class="col-lg-4 col-md-6 col-sm-8 col-12">
      <input type="text" name="keyword" class="form-control" placeholder="Search file..." value="{{ request('keyword') }}">
    </div>
    <div class="col-lg-3 col-md-4 col-sm-6 col-12">
      <select name="type" class="form-select">
        <option value="">Semua</option>
        <option value="pdf" {{ request('type')=='pdf'?'selected':'' }}>PDF</option>
        <option value="image" {{ request('type')=='image'?'selected':'' }}>Image</option>
      </select>
    </div>
    <div class="col-lg-2 col-md-2 col-sm-4 col-12">
      <button class="btn btn-primary w-100">
        <i class="bi bi-search me-1"></i> Cari
      </button>
    </div>
  </form>

  {{-- 📊 Statistik --}}
  <div class="row text-center mb-4">
    @php
      $stats = [
        ['icon' => 'bi-file-earmark-text', 'label' => 'Dokumen', 'value' => $totalDocuments, 'color' => 'success'],
        ['icon' => 'bi-image', 'label' => 'Gambar', 'value' => $totalImages, 'color' => 'info'],
        ['icon' => 'bi-camera-reels', 'label' => 'Video', 'value' => $totalVideos, 'color' => 'warning'],
        ['icon' => 'bi-music-note-beamed', 'label' => 'Audio', 'value' => $totalAudios, 'color' => 'danger'],
      ];
    @endphp

     @foreach($stats as $stat)
    <div class="col-xl-3 col-md-6 col-sm-6 mb-3">
      <div class="card p-3 border-0 shadow-sm h-100 small-card">
        <h6 class="text-muted mb-1">
          <i class="bi {{ $stat['icon'] }} me-1 text-secondary"></i> {{ $stat['label'] }}
        </h6>
        <h5 class="fw-bold text-{{ $stat['color'] }}">{{ $stat['value'] }}</h5>
      </div>
    </div>
    @endforeach
  </div>

  {{-- 📁 Daftar Folder --}}
  <div class="row g-3 mb-5">
    @forelse($folders as $folder)
      <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 col-12">
        <div class="card shadow-sm border-0 folder-card p-3 h-100" style="cursor: pointer;" onclick="window.location='{{ route('folders.show', $folder->id) }}'">
          <div class="d-flex justify-content-between align-items-start">
            <span class="fw-semibold text-dark text-truncate" style="max-width: 120px;">
              <i class="bi bi-folder-fill me-1 text-warning"></i>{{ $folder->name }}
            </span>
            <div class="dropdown">
              <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" onclick="event.stopPropagation()">
                <i class="bi bi-three-dots-vertical"></i>
              </button>
              <ul class="dropdown-menu dropdown-menu-end" onclick="event.stopPropagation()">
                <li>
                  <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editFolderModal{{ $folder->id }}">
                    <i class="bi bi-pencil-square me-1 text-primary"></i>Ubah
                  </button>
                </li>
                <li>
                  <form action="{{ route('folders.destroy', $folder->id) }}" method="POST" onsubmit="return confirm('Yakin hapus folder ini?')">
                    @csrf @method('DELETE')
                     <button type="submit" class="dropdown-item text-danger">
                      <i class="bi bi-trash me-1"></i>Hapus
                    </button>
                  </form>
                </li>
              </ul>
            </div>
          </div>
          <p class="text-muted small mb-0 mt-2">
             <i class="bi bi-calendar3 me-1"></i> {{ $folder->created_at->format('d M Y') }}
        </div>
      </div>

      {{-- Modal Edit Folder --}}
      <div class="modal fade" id="editFolderModal{{ $folder->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
          <form class="modal-content" method="POST" action="{{ route('folders.update', $folder->id) }}">
            @csrf @method('PUT')
            <div class="modal-header">
              <h5 class="modal-title">Edit Folder</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
             <div class="modal-body">
              <input type="text" name="name" class="form-control form-control-sm" value="{{ $folder->name }}" required>
            </div>
            <div class="modal-footer p-2">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
          </form>
        </div>
      </div>
    @empty
      <p class="text-muted text-center">Belum ada folder.</p>
    @endforelse
  </div>

  {{-- 🗂 Recent Files --}}
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
       <h6 class="m-0 fw-semibold"><i class="bi bi-clock-history me-1"></i> Berkas Terbaru</h6>
    </div>
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>Nama Berkas</th>
            <th>Kategori</th>
            <th>Pengunggah</th>
            <th>Tanggal</th>
          </tr>
        </thead>
        <tbody class="small">
          @forelse($files as $file)
          <tr>
            <td class="text-truncate" style="max-width: 160px;">
             <i class="bi bi-file-earmark me-1 text-secondary"></i>{{ $file->file_name }}
            </td>
            <td>{{ ucfirst($file->file_type) }}</td>
             <td class="d-flex align-items-center">
              @if(isset($file->uploader) && $file->uploader->photo && file_exists(public_path('storage/' . $file->uploader->photo)))
                <img src="{{ asset('storage/' . $file->uploader->photo) }}" class="rounded-circle me-2" style="width:24px;height:24px;object-fit:cover;">
              @else
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width:24px;height:24px;font-size:0.7rem;font-weight:600;">
                  {{ strtoupper(substr($file->uploader->name ?? 'A', 0, 1)) }}
                </div>
              @endif
              {{ $file->uploader->name ?? 'Tidak diketahui' }}
            </td>
            <td>{{ $file->created_at->format('d M Y') }}</td>
          </tr>
          @empty
          <tr>
            <td colspan="4" class="text-center text-muted py-3">Tidak ada berkas ditemukan.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

{{--  CSS  --}}
<style>
  .small-card { font-size: 0.9rem; }
  .folder-card:hover { transform: scale(1.02); transition: 0.2s; }
  .table td, .table th { vertical-align: middle !important; }
</style>
@endsection