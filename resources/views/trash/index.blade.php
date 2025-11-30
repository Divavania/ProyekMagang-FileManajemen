@extends('layouts.app')
@section('title', 'Sampah - RadarFiles')
@section('page_title', 'Sampah')

@section('content')
<div class="container py-4">

  {{-- Info Banner Auto Delete --}}
  <div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
    <i class="bi bi-info-circle-fill me-2"></i>
    <strong>Info:</strong> File dan folder di sampah akan dihapus secara otomatis setelah <strong>14 hari</strong>.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>

  {{-- Pesan sukses / error --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @elseif(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  {{-- Tabs File & Folder --}}
  <ul class="nav nav-tabs mb-4" id="trashTab" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="files-tab" data-bs-toggle="tab" data-bs-target="#files" type="button" role="tab">
        <i class="bi bi-file-earmark"></i> File
        @if($trashedFiles->count() > 0)
          <span class="badge bg-danger ms-1">{{ $trashedFiles->count() }}</span>
        @endif
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="folders-tab" data-bs-toggle="tab" data-bs-target="#folders" type="button" role="tab">
        <i class="bi bi-folder"></i> Folder
        @if($trashedFolders->count() > 0)
          <span class="badge bg-danger ms-1">{{ $trashedFolders->count() }}</span>
        @endif
      </button>
    </li>
  </ul>

  <div class="tab-content" id="trashTabContent">
    {{-- Tab File --}}
    <div class="tab-pane fade show active" id="files" role="tabpanel">
      @if($trashedFiles->isEmpty())
        <div class="text-center text-muted py-5">
          <i class="bi bi-trash3 display-4 mb-3"></i>
          <h5>Tidak ada file di sampah</h5>
          <p class="text-muted">File yang dihapus akan muncul di sini</p>
        </div>
      @else
        {{-- Tombol Pulihkan Semua / Hapus Semua --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div class="text-muted">
            <i class="bi bi-info-circle"></i> Total: <strong>{{ $trashedFiles->count() }}</strong> file
          </div>
          <div class="d-flex gap-2">
            <form action="{{ route('trash.restoreAll') }}" method="POST">
              @csrf
              <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-arrow-counterclockwise"></i> Pulihkan Semua</button>
            </form>
            <form action="{{ route('trash.empty') }}" method="POST" onsubmit="return confirm('⚠️ PERHATIAN!\n\nSemua file akan dihapus PERMANEN dan tidak dapat dipulihkan kembali.\n\nYakin ingin melanjutkan?')">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash3"></i> Hapus Semua</button>
            </form>
          </div>
        </div>

        {{-- Tabel File --}}
        <div class="card shadow-sm border-0">
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Nama File</th>
                    <th class="text-center" style="width:120px;">Ukuran</th>
                    <th class="text-center" style="width:180px;">Tanggal Dihapus</th>
                    <th class="text-center" style="width:220px;">Status</th>
                    <th class="text-center" style="width:200px;">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($trashedFiles as $file)
                    <tr class="{{ $file->days_until_deletion <= 3 ? 'table-danger' : ($file->days_until_deletion <= 7 ? 'table-warning' : '') }}">
                      <td class="text-break">
                        <i class="bi bi-file-earmark me-2"></i>
                        {{ $file->file_name }}
                      </td>
                      <td class="text-center">
                        @php
                          $size = $file->file_size ?? 0;
                          if ($size >= 1073741824) {
                            echo number_format($size / 1073741824, 2) . ' GB';
                          } elseif ($size >= 1048576) {
                            echo number_format($size / 1048576, 2) . ' MB';
                          } elseif ($size >= 1024) {
                            echo number_format($size / 1024, 2) . ' KB';
                          } else {
                            echo $size . ' B';
                          }
                        @endphp
                      </td>
                      <td class="text-center">
                        <small class="text-muted">{{ $file->deleted_at->format('d M Y') }}</small><br>
                        <small class="text-muted">{{ $file->deleted_at->format('H:i') }}</small>
                      </td>
                      <td class="text-center">
                        @if($file->days_until_deletion <= 0)
                          <span class="badge bg-danger"><i class="bi bi-exclamation-triangle-fill"></i> Akan segera dihapus</span>
                        @elseif($file->days_until_deletion <= 3)
                          <span class="badge bg-danger"><i class="bi bi-exclamation-triangle-fill"></i> {{ $file->days_until_deletion }} hari lagi</span>
                        @elseif($file->days_until_deletion <= 7)
                          <span class="badge bg-warning text-dark"><i class="bi bi-clock-fill"></i> {{ $file->days_until_deletion }} hari lagi</span>
                        @else
                          <span class="badge bg-info"><i class="bi bi-info-circle-fill"></i> {{ $file->days_until_deletion }} hari lagi</span>
                        @endif
                        <br>
                        <small class="text-muted">
                          {{ $file->will_be_deleted_at->format('d M Y') }}
                        </small>
                      </td>
                      <td class="text-center">
                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                          <form action="{{ route('trash.restore', $file->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-success btn-sm" title="Pulihkan file">
                              <i class="bi bi-arrow-counterclockwise"></i>
                            </button>
                          </form>
                          <form action="{{ route('trash.forceDelete', $file->id) }}" method="POST" onsubmit="return confirm('⚠️ File akan dihapus PERMANEN!\n\nFile: {{ $file->file_name }}\n\nTidak dapat dipulihkan kembali. Lanjutkan?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Hapus permanen">
                              <i class="bi bi-x-circle"></i>
                            </button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      @endif
    </div>

    {{-- Tab Folder --}}
    <div class="tab-pane fade" id="folders" role="tabpanel">
      @if($trashedFolders->isEmpty())
        <div class="text-center text-muted py-5">
          <i class="bi bi-folder2-open display-4 mb-3"></i>
          <h5>Tidak ada folder di sampah</h5>
          <p class="text-muted">Folder yang dihapus akan muncul di sini</p>
        </div>
      @else
        {{-- Tombol Pulihkan Semua / Hapus Semua --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div class="text-muted"> <i class="bi bi-info-circle"></i> Total: <strong>{{ $trashedFolders->count() }}</strong> folder</div>
          <div class="d-flex gap-2">
            <form action="{{ route('trash.folders.restoreAll') }}" method="POST">
              @csrf
              <button type="submit" class="btn btn-success btn-sm"><i class="bi bi-arrow-counterclockwise"></i> Pulihkan Semua</button>
            </form>
            <form action="{{ route('trash.folders.empty') }}" method="POST" onsubmit="return confirm('⚠️ PERHATIAN!\n\nSemua folder beserta isinya akan dihapus PERMANEN dan tidak dapat dipulihkan kembali.\n\nYakin ingin melanjutkan?')">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-trash3"></i> Hapus Semua</button>
            </form>
          </div>
        </div>

        {{-- Tabel Folder --}}
        <div class="card shadow-sm border-0">
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Nama Folder</th>
                    <th class="text-center" style="width:180px;">Tanggal Dihapus</th>
                    <th class="text-center" style="width:220px;">Status</th>
                    <th class="text-center" style="width:200px;">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($trashedFolders as $folder)
                    <tr class="{{ $folder->days_until_deletion <= 3 ? 'table-danger' : ($folder->days_until_deletion <= 7 ? 'table-warning' : '') }}">
                      <td class="text-break">
                        <i class="bi bi-folder-fill text-warning me-2"></i>
                        {{ $folder->name }}
                      </td>
                      <td class="text-center">
                        <small class="text-muted">{{ $folder->deleted_at->format('d M Y') }}</small><br>
                        <small class="text-muted">{{ $folder->deleted_at->format('H:i') }}</small>
                      </td>
                      <td class="text-center">
                        @if($folder->days_until_deletion <= 0)
                          <span class="badge bg-danger"><i class="bi bi-exclamation-triangle-fill"></i> Akan segera dihapus</span>
                        @elseif($folder->days_until_deletion <= 3)
                          <span class="badge bg-danger"><i class="bi bi-exclamation-triangle-fill"></i> {{ $folder->days_until_deletion }} hari lagi</span>
                        @elseif($folder->days_until_deletion <= 7)
                          <span class="badge bg-warning text-dark"><i class="bi bi-clock-fill"></i> {{ $folder->days_until_deletion }} hari lagi</span>
                        @else
                          <span class="badge bg-info"><i class="bi bi-info-circle-fill"></i> {{ $folder->days_until_deletion }} hari lagi</span>
                        @endif
                        <br>
                        <small class="text-muted">
                          {{ $folder->will_be_deleted_at->format('d M Y') }}
                        </small>
                      </td>
                      <td class="text-center">
                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                          <form action="{{ route('trash.folders.restore', $folder->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-success btn-sm" title="Pulihkan folder">
                              <i class="bi bi-arrow-counterclockwise"></i>
                            </button>
                          </form>
                          <form action="{{ route('trash.folders.forceDelete', $folder->id) }}" method="POST" onsubmit="return confirm('⚠️ Folder akan dihapus PERMANEN!\n\nFolder: {{ $folder->name }}\nBeserta seluruh isinya!\n\nTidak dapat dipulihkan kembali. Lanjutkan?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm" title="Hapus permanen">
                              <i class="bi bi-x-circle"></i>
                            </button>
                          </form>
                        </div>
                      </td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      @endif
    </div>
  </div>

</div>

<style>
/* Animasi untuk row yang hampir terhapus */
@keyframes pulse-danger {
  0%, 100% { background-color: transparent; }
  50% { background-color: rgba(220, 53, 69, 0.1); }
}
.table-danger {
  animation: pulse-danger 2s ease-in-out infinite;
}
.table-warning {
  background-color: rgba(255, 193, 7, 0.1);
}
/* Badge styling */
.badge {
  font-size: 0.75rem;
  padding: 0.35em 0.65em;
}
/* Responsive table */
@media (max-width: 768px) {
  .table-responsive {
    font-size: 0.875rem;
  }
  .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
  }
}
</style>
@endsection