@extends('layouts.app')
@section('title', 'Sampah - RadarFiles')
@section('page_title', 'Sampah')

@section('content')
<div class="container py-4">

  {{-- Pesan sukses / error --}}
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @elseif(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  @if($trashedFiles->isEmpty())
    <div class="text-center text-muted py-5">
      <i class="bi bi-trash3 display-4 mb-3"></i>
      <h5>Tidak ada file di sampah</h5>
    </div>
  @else
    {{-- Header halaman --}}
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
     
      <div class="d-flex gap-2">
        <form action="{{ route('trash.restoreAll') }}" method="POST" class="d-inline">
          @csrf
          <button type="submit" class="btn btn-success btn-sm px-3">
            <i class="bi bi-arrow-counterclockwise me-1"></i> Pulihkan Semua
          </button>
        </form>
        <form action="{{ route('trash.empty') }}" method="POST" class="d-inline"
              onsubmit="return confirm('Yakin ingin menghapus permanen semua file di sampah?')">
          @csrf
          @method('DELETE')
          <button type="submit" class="btn btn-danger btn-sm px-3">
            <i class="bi bi-trash3 me-1"></i> Hapus Semua
          </button>
        </form>
      </div>
    </div>

    {{-- Card tabel file --}}
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
                  <div class="d-flex justify-content-center gap-2">
                    <form action="{{ route('trash.restore', $file->id) }}" method="POST">
                      @csrf
                      <button type="submit" class="btn btn-outline-success btn-sm">
                        <i class="bi bi-arrow-counterclockwise"></i> Pulihkan
                      </button>
                    </form>
                    <form action="{{ route('trash.forceDelete', $file->id) }}" method="POST"
                          onsubmit="return confirm('Hapus permanen file ini?')">
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
@endsection
