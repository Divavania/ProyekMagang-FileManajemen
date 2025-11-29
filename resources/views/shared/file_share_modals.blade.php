@foreach($allShared as $share)
  @php $file = $share->file; @endphp
  @if(!$file) @continue @endif

  @php
    $ext = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));
    $fileUrl = asset('storage/' . $file->file_path);
    $txtPath = storage_path('app/public/' . $file->file_path);
  @endphp


  {{-- ========================= --}}
  {{--      PREVIEW MODAL        --}}
  {{-- ========================= --}}
  <div class="modal fade" id="previewModal{{ $file->id }}" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content">

        <div class="modal-header">
          <h5 class="modal-title">{{ $file->file_name }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body text-center">

          @if(in_array($ext, ['jpg','jpeg','png','gif','webp']))
            <img src="{{ $fileUrl }}" class="img-fluid">

          @elseif($ext === 'pdf')
            <iframe src="{{ $fileUrl }}" width="100%" height="600px"></iframe>

          @elseif($ext === 'txt' && file_exists($txtPath))
            <textarea class="form-control" rows="20" readonly>{{ file_get_contents($txtPath) }}</textarea>

          @elseif(in_array($ext, ['mp4','webm','ogg']))
            <video controls width="100%" style="max-height:600px;">
              <source src="{{ $fileUrl }}" type="video/{{ $ext }}">
            </video>

          @else
            <p class="text-muted">Preview tidak tersedia untuk tipe file ini.</p>
          @endif

        </div>

      </div>
    </div>
  </div>



  {{-- ========================= --}}
  {{--       RENAME MODAL        --}}
  {{-- ========================= --}}
  <div class="modal fade" id="renameModal{{ $file->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">

        <form action="{{ route('files.update', $file->id) }}" method="POST">
          @csrf @method('PUT')

          <div class="modal-header">
            <h5 class="modal-title">Rename File</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
            <input type="text" name="file_name" class="form-control"
                   value="{{ $file->file_name }}" required>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
          </div>

        </form>

      </div>
    </div>
  </div>



  {{-- ========================= --}}
  {{--        SHARE MODAL        --}}
  {{-- ========================= --}}
  <div class="modal fade" id="shareFileModal{{ $file->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="POST" action="{{ route('files.share', $file->id) }}">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Bagikan File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label>Email Pengguna</label>
                    <input type="text" name="email" class="form-control"
                        placeholder="Masukkan satu atau lebih email, pisahkan dengan koma ( , )" required>
                </div>

                <div class="mb-3">
                    <label>Pesan (opsional)</label>
                    <textarea name="message" class="form-control" rows="3" placeholder="Tambahkan pesan..."></textarea>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Bagikan</button>
            </div>
        </form>
    </div>
  </div>

@endforeach