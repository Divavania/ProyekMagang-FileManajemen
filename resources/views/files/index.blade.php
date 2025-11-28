@extends('layouts.app')

@section('title', 'File Saya - RadarFiles')
@section('page_title', 'File Saya')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container-fluid mt-3">

  {{-- === Toolbar === --}}
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <form id="searchForm" class="d-flex gap-2 align-items-center flex-grow-1 flex-wrap" method="GET" action="{{ route('files.index') }}">
      <input type="text" name="keyword" class="form-control" placeholder="Cari file..." value="{{ request('keyword') }}" style="min-width:180px; max-width:420px;">
      <select name="type" class="form-select" style="width:150px;">
        <option value="semua" {{ request('type')=='semua' ? 'selected' : '' }}>Semua</option>
        <option value="dokumen" {{ request('type')=='dokumen' ? 'selected' : '' }}>Dokumen</option>
        <option value="gambar" {{ request('type')=='gambar' ? 'selected' : '' }}>Gambar</option>
        <option value="video" {{ request('type')=='video' ? 'selected' : '' }}>Video</option>
        <option value="audio" {{ request('type')=='audio' ? 'selected' : '' }}>Audio</option>
      </select>
      <select name="sort" class="form-select" style="width:180px;">
        <option value="created_at_desc" {{ request('sort')=='created_at_desc' ? 'selected' : '' }}>Terbaru</option>
        <option value="created_at_asc" {{ request('sort')=='created_at_asc' ? 'selected' : '' }}>Terlama</option>
        <option value="name_asc" {{ request('sort')=='name_asc' ? 'selected' : '' }}>Nama A–Z</option>
        <option value="name_desc" {{ request('sort')=='name_desc' ? 'selected' : '' }}>Nama Z–A</option>
        <option value="size_desc" {{ request('sort')=='size_desc' ? 'selected' : '' }}>Ukuran Terbesar</option>
        <option value="size_asc" {{ request('sort')=='size_asc' ? 'selected' : '' }}>Ukuran Terkecil</option>
      </select>
      <button type="submit" class="btn btn-primary px-3"><i class="bi bi-search me-1"></i>Cari</button>
    </form>

   <div class="d-flex align-items-center gap-2 toolbar-actions">
      <button id="toggleSelectMode" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-check2-square me-1"></i>Pilih File
      </button>
      <button id="deleteSelected" class="btn btn-danger btn-sm d-none">
        <i class="bi bi-trash me-1"></i>Hapus Terpilih
      </button>
      <button id="toggleView" class="btn btn-outline-primary btn-sm">
        <i class="bi bi-list"></i> Tampilan Daftar
      </button>
    </div>
  </div>

    {{-- === File Container === --}}

    <div id="fileContainer" class="position-relative">

       {{-- === Bulk Delete Form === --}}
  <form id="bulkDeleteForm" action="{{ route('files.bulkDelete') }}" method="POST">
    @csrf
    @method('DELETE')
    <input type="hidden" name="selected_files" id="selectedFilesInput">

    {{-- === GRID VIEW === --}}
    <div id="gridView" class="row g-3">
      @forelse($files as $file)
        @include('files.partials.file-grid', ['file' => $file])
      @empty
        <div class="col-12 text-center py-5 text-muted">Tidak ada file ditemukan.</div>
      @endforelse
    </div>

    {{-- === LIST VIEW === --}}
    <div id="listView" class="d-none">
      <div class="d-flex border-bottom py-2 px-3 bg-light fw-semibold">
        <div class="flex-grow-1">Nama File</div>
        <div style="width:150px;">Pemilik</div>
        <div style="width:150px;">Tanggal</div>
        <div style="width:100px;">Ukuran</div>
        <div style="width:50px;"></div>
      </div>
      @foreach($files as $file)
        @include('files.partials.file-list', ['file' => $file])
      @endforeach
    </div>
  </form>

  @include('files.partials.modals', ['files' => $files])
</div>

  {{-- Hidden form single delete --}}
  <form id="singleDeleteForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
  </form>

</div>

{{-- === CSS === --}}
<style>
#listView > div.d-flex:hover {
  background-color: #f8f9fa;
}

.toolbar-actions {
  min-height: 38px; /* tinggi minimal agar tidak turun */
  display: flex;
  align-items: center;
}
</style>

{{-- === SCRIPT === --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
  const toggleViewBtn = document.getElementById('toggleView');
  const toggleSelectBtn = document.getElementById('toggleSelectMode');
  const deleteSelectedBtn = document.getElementById('deleteSelected');
  const singleDeleteForm = document.getElementById('singleDeleteForm');
  const gridView = document.getElementById('gridView');
  const listView = document.getElementById('listView');

  // === Toggle grid/list view ===
  toggleViewBtn.addEventListener('click', () => {
    const isGridVisible = !gridView.classList.contains('d-none');

    if (isGridVisible) {
      // switch ke daftar
      gridView.classList.add('d-none');
      listView.classList.remove('d-none');
      toggleViewBtn.innerHTML = '<i class="bi bi-grid"></i> Tampilan Petak';
    } else {
      // switch ke petak
      listView.classList.add('d-none');
      gridView.classList.remove('d-none');
      toggleViewBtn.innerHTML = '<i class="bi bi-list"></i> Tampilan Daftar';
    }
  });

  // Toggle mode pilih file
toggleSelectBtn.addEventListener('click', () => {
  const checkboxes = document.querySelectorAll('.select-checkbox');
  const show = checkboxes.length && checkboxes[0].classList.contains('d-none');

  checkboxes.forEach(cb => cb.classList.toggle('d-none', !show));
  deleteSelectedBtn.classList.toggle('d-none', !show);

  // Ubah label tombol agar jelas
  if (show) {
    toggleSelectBtn.innerHTML = '<i class="bi bi-x-circle me-1"></i>Batal';
  } else {
    toggleSelectBtn.innerHTML = '<i class="bi bi-check2-square me-1"></i>Pilih File';
  }
});


  // === Delete Selected ===
deleteSelectedBtn.addEventListener('click', (e) => {
  e.preventDefault();
  const selected = document.querySelectorAll('.select-checkbox:checked');
  if (selected.length === 0) {
    return Swal.fire('Info', 'Pilih file terlebih dahulu', 'info');
  }

  Swal.fire({
    title: 'Hapus File Terpilih?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Hapus',
    cancelButtonText: 'Batal'
  }).then(result => {
    if (result.isConfirmed) {
      const form = document.getElementById('bulkDeleteForm');

      // Hapus input lama biar gak dobel
      form.querySelectorAll('input[name="selected_files[]"]').forEach(el => el.remove());

      // Tambahkan input hidden untuk tiap file yang dicentang
      selected.forEach(cb => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'selected_files[]';
        input.value = cb.value; // pastikan value di checkbox = ID file
        form.appendChild(input);
      });

      form.submit();
    }
  });
});

  // === Single Delete ===
  document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', () => {
      const fileId = btn.dataset.id;
      Swal.fire({
        title: 'Hapus File?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Hapus',
        cancelButtonText: 'Batal'
      }).then(result => {
        if (result.isConfirmed) {
          singleDeleteForm.action = `/files/${fileId}`;
          singleDeleteForm.submit();
        }
      });
    });
  });

  // === Toggle Favorite ===
  document.querySelectorAll('.toggle-favorite-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
      const fileId = btn.dataset.id;

      try {
        const res = await fetch(`/favorites/file/${fileId}`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
          }
        });

        const data = await res.json();

        if (data.status === 'ok') {
          // Update icon di dropdown
          const icon = btn.querySelector('i');

          if (data.favorited) {
            icon.classList.remove('bi-star', 'text-secondary');
            icon.classList.add('bi-star-fill', 'text-warning');
            btn.innerHTML = `<i class="bi bi-star-fill text-warning me-2"></i>Hapus dari Favorit`;
          } else {
            icon.classList.remove('bi-star-fill', 'text-warning');
            icon.classList.add('bi-star', 'text-secondary');
            btn.innerHTML = `<i class="bi bi-star me-2"></i>Tambah ke Favorit`;

            // Jika sedang di halaman favorit → hapus kartu dari tampilan
            const isFavoritesPage = window.location.pathname.includes('/favorites');
            if (isFavoritesPage) {
              const card = btn.closest('.file-item') || btn.closest('.d-flex');
              if (card) card.remove();
            }
          }
        }
      } catch (err) {
        console.error(err);
        Swal.fire('Error', 'Gagal terhubung ke server.', 'error');
      }
    });
  });
});
</script>


@endsection
