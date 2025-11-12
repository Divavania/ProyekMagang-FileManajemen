@extends('layouts.app')
@section('title', 'Dasbor - RadarFiles')

@section('content')
<div class="container-fluid mt-3" style="max-width: 1200px;">

  {{-- 🔍 Form Pencarian --}}
  <form class="row g-2 mb-4" method="GET" action="{{ route('dashboard') }}">
    <div class="col-md-4 col-sm-6">
      <input type="text" name="keyword" class="form-control form-control-sm" placeholder="Cari berkas..." value="{{ request('keyword') }}">
    </div>
    <div class="col-md-3 col-sm-4">
      <select name="type" class="form-select form-select-sm">
        <option value="">Semua</option>
        <option value="pdf" {{ request('type')=='pdf'?'selected':'' }}>PDF</option>
        <option value="image" {{ request('type')=='image'?'selected':'' }}>Gambar</option>
      </select>
    </div>
    <div class="col-md-2 col-sm-3">
      <button class="btn btn-primary btn-sm w-100">
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
    @include('folders._folder_cards', ['folders' => $folders])
  </div>

  {{-- 🕒 Berkas Terbaru --}}
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-2 px-3 d-flex align-items-center">
      <h6 class="m-0 fw-semibold"><i class="bi bi-clock-history me-1"></i> Berkas Terbaru</h6>
    </div>
    <div class="table-responsive">
      <table class="table table-sm table-hover align-middle mb-0">
        <thead class="table-light small">
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
@endsection