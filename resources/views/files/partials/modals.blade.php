@foreach($files as $file)
  @php
    $extension = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));
    $fileUrl = asset('storage/' . $file->file_path);
  @endphp

  {{-- =========================
       MODAL PREVIEW FILE
       ========================= --}}
<div class="modal fade" id="previewModal{{ $file->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl" style="max-width:90vw;">
    <div class="modal-content bg-white border-0 rounded-4 shadow-lg overflow-hidden">

      {{-- Header --}}
      <div class="modal-header border-0 bg-white text-dark px-4 py-3">
        <h6 class="modal-title text-truncate" style="max-width: 80%;">
          <i class="bi bi-eye me-2"></i>{{ $file->file_name }}
        </h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      {{-- Body --}}
      <div class="modal-body d-flex justify-content-center align-items-center bg-light" style="max-height: 85vh;">

        {{-- IMAGE --}}
        @if(in_array($extension, ['jpg','jpeg','png','gif','webp','bmp']))
          <img src="{{ $fileUrl }}" alt="{{ $file->file_name }}"
               class="img-fluid rounded shadow"
               style="max-height:80vh; max-width:100%; object-fit:contain; background:white;">

        {{-- VIDEO --}}
        @elseif(in_array($extension, ['mp4','mov','avi','mkv','webm']))
          <video controls playsinline
                 class="rounded shadow w-100"
                 style="max-height:80vh; object-fit:contain; background:white;">
            <source src="{{ $fileUrl }}">
            Browser kamu tidak mendukung video.
          </video>

        {{-- PDF --}}
        @elseif($extension === 'pdf')
          <iframe src="{{ $fileUrl }}"
                  class="w-100 rounded border"
                  style="height:80vh; background:white;">
          </iframe>

        {{-- TXT --}}
        @elseif($extension === 'txt')
          <pre class="text-dark p-3 rounded border w-100 bg-white"
               style="max-height:80vh; overflow:auto; white-space:pre-wrap;">
{{ Storage::disk('public')->get($file->file_path) }}
          </pre>

        {{-- UNKNOWN --}}
        @else
          <div class="text-center text-secondary">
            <i class="bi bi-file-earmark-text fs-1 mb-3"></i>
            <p>File tidak dapat ditampilkan langsung.</p>
          </div>
        @endif
      </div>

      {{-- Footer --}}
      <div class="modal-footer border-0 bg-white justify-content-between px-4 py-2">
        <small class="text-muted">Diupload: {{ $file->created_at->format('d M Y, H:i') }}</small>
        <a href="{{ route('files.download', $file->id) }}"
           class="btn btn-outline-dark btn-sm rounded-pill px-3">
          <i class="bi bi-download me-1"></i> Unduh
        </a>
      </div>

    </div>
  </div>
</div>


  {{-- =========================
       SHARE FILE MODAL
       ========================= --}}
<div class="modal fade" id="shareFileModal{{ $file->id }}" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
      <form class="modal-content bg-white rounded-4" method="POST" action="{{ route('files.share', $file->id) }}">
          @csrf
          <div class="modal-header bg-white">
              <h5 class="modal-title">Bagikan File</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
              <div class="mb-3">
                  <label>Email Pengguna</label>
                  <input type="text" name="email" class="form-control"
                      placeholder="Masukkan email, pisahkan dengan koma (,)" required>
              </div>

              <div class="mb-3">
                  <label>Izin Akses</label>
                  <select name="permission" class="form-select" required>
                      <option value="view">Lihat</option>
                      <option value="edit">Edit</option>
                      <option value="download">Unduh</option>
                  </select>
              </div>

              <div class="mb-3">
                  <label>Pesan (opsional)</label>
                  <textarea name="message" class="form-control" rows="3"></textarea>
              </div>
          </div>

          <div class="modal-footer bg-white">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-primary">Bagikan</button>
          </div>
      </form>
  </div>
</div>


  {{-- =========================
       EDIT FILE NAME MODAL
       ========================= --}}
<div class="modal fade" id="editFileModal{{ $file->id }}" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <form action="{{ route('files.update', $file->id) }}" method="POST" class="modal-content rounded-4 bg-white">
      @csrf
      @method('PUT')

      <div class="modal-header bg-white">
        <h5 class="modal-title">Ubah Nama File</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="mb-3">
          <label>Nama Baru</label>
          <input type="text" name="file_name" class="form-control" value="{{ $file->file_name }}" required>
        </div>
      </div>

      <div class="modal-footer bg-white">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>


  {{-- =========================
       MOVE FILE MODAL
       ========================= --}}
<div class="modal fade" id="moveFileModal{{ $file->id }}" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content rounded-4 bg-white"
          method="POST"
          action="{{ route('files.move', $file->id) }}">
      @csrf
      @method('PUT')

      <div class="modal-header bg-white">
        <h5 class="modal-title">Pindahkan File: {{ $file->file_name }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <div class="mb-3">
          <label>Pilih Folder</label>
          <select name="folder_id" class="form-select" required>
            <option value="">-- Pilih Folder --</option>
            @foreach($folders as $folder)
              <option value="{{ $folder->id }}">{{ $folder->name }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="modal-footer bg-white">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-primary">Pindahkan</button>
      </div>

    </form>
  </div>
</div>

@endforeach


{{-- =========================
     CSS Tambahan
     ========================= --}}
<style>
  .modal-xl {
    width: 90vw !important;
    max-width: 90vw !important;
  }

  .modal-content video,
  .modal-content img {
    background: #fff !important;
  }
</style>

<script>
function copyLink(link){
  navigator.clipboard.writeText(link).then(()=>alert('Link berhasil disalin!'));
}
</script>
