@extends('layouts.app')
@section('title', 'Sampah - RadarFiles')
@section('page_title', 'Sampah')

@section('content')
<div class="container py-4">

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
      <button class="nav-link active" id="files-tab" data-bs-toggle="tab" data-bs-target="#files" type="button" role="tab">File</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="folders-tab" data-bs-toggle="tab" data-bs-target="#folders" type="button" role="tab">Folder</button>
    </li>
  </ul>

  <div class="tab-content" id="trashTabContent">
    {{-- Tab File --}}
    <div class="tab-pane fade show active" id="files" role="tabpanel">
      @if($trashedFiles->isEmpty())
        <div class="text-center text-muted py-5">
          <i class="bi bi-trash3 display-4 mb-3"></i>
          <h5>Tidak ada file di sampah</h5>
        </div>
      @else
        {{-- Tombol Pulihkan Semua / Hapus Semua --}}
        <div class="d-flex justify-content-end gap-2 mb-3">
          <form action="{{ route('trash.restoreAll') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-success btn-sm">
              <i class="bi bi-arrow-counterclockwise"></i> Pulihkan Semua
            </button>
          </form>
          <form action="{{ route('trash.empty') }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus permanen semua file?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">
              <i class="bi bi-trash3"></i> Hapus Semua
            </button>
          </form>
        </div>

        {{-- Tabel File --}}
        <div class="card shadow-sm border-0">
          <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>Nama File</th>
                  <th class="text-center" style="width:120px;">Ukuran</th>
                  <th class="text-center" style="width:180px;">Tanggal Dihapus</th>
                  <th class="text-center" style="width:200px;">Aksi</th>
                </tr>
              </thead>
              <tbody>
                @foreach($trashedFiles as $file)
                  <tr>
                    <td class="text-break">{{ $file->file_name }}</td>
                    <td class="text-center">{{ number_format($file->size / 1024, 2) }} KB</td>
                    <td class="text-center">{{ $file->deleted_at->format('d M Y H:i') }}</td>
                    <td class="text-center">
                      <div class="d-flex justify-content-center gap-2 flex-wrap">
                        <form action="{{ route('trash.restore', $file->id) }}" method="POST">
                          @csrf
                          <button type="submit" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-arrow-counterclockwise"></i> Pulihkan
                          </button>
                        </form>
                        <form action="{{ route('trash.forceDelete', $file->id) }}" method="POST" onsubmit="return confirm('Hapus permanen file ini?')">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-x-circle"></i> Hapus
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
      @endif
    </div>

    {{-- Tab Folder --}}
    <div class="tab-pane fade" id="folders" role="tabpanel">
      @if($trashedFolders->isEmpty())
        <div class="text-center text-muted py-5">
          <i class="bi bi-folder2-open display-4 mb-3"></i>
          <h5>Tidak ada folder di sampah</h5>
        </div>
      @else
        {{-- Tombol Pulihkan Semua / Hapus Semua --}}
        <div class="d-flex justify-content-end gap-2 mb-3">
          <form action="{{ route('trash.folders.restoreAll') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-success btn-sm">
              <i class="bi bi-arrow-counterclockwise"></i> Pulihkan Semua
            </button>
          </form>
          <form action="{{ route('trash.folders.empty') }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus permanen semua folder?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">
              <i class="bi bi-trash3"></i> Hapus Semua
            </button>
          </form>
        </div>

        {{-- Tabel Folder --}}
        <div class="card shadow-sm border-0">
          <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>Nama Folder</th>
                  <th class="text-center" style="width:180px;">Tanggal Dihapus</th>
                  <th class="text-center" style="width:200px;">Aksi</th>
                </tr>
              </thead>
              <tbody>
                @foreach($trashedFolders as $folder)
                  <tr>
                    <td class="text-break">{{ $folder->name }}</td>
                    <td class="text-center">{{ $folder->deleted_at->format('d M Y H:i') }}</td>
                    <td class="text-center">
                      <div class="d-flex justify-content-center gap-2 flex-wrap">
                        <form action="{{ route('trash.folders.restore', $folder->id) }}" method="POST">
                          @csrf
                          <button type="submit" class="btn btn-outline-success btn-sm">
                            <i class="bi bi-arrow-counterclockwise"></i> Pulihkan
                          </button>
                        </form>
                        <form action="{{ route('trash.folders.forceDelete', $folder->id) }}" method="POST" onsubmit="return confirm('Hapus permanen folder ini beserta isinya?')">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-x-circle"></i> Hapus
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
      @endif
    </div>
  </div>

</div>
@endsection