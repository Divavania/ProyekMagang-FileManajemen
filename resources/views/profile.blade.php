@extends('layouts.app')

@section('title', 'Profil Saya - RadarFiles')
@section('content')
<link href="https://unpkg.com/cropperjs/dist/cropper.min.css" rel="stylesheet"/>
<script src="https://unpkg.com/cropperjs/dist/cropper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="mt-4">
  @if(session('success'))
    <div class="alert alert-success d-flex align-items-center">
      <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    </div>
   @endif
  @if(session('error'))
    <div class="alert alert-danger d-flex align-items-center">
      <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
    </div>
  @endif

<div class="card border-0 rounded-4 shadow-sm bg-white p-4">
    <div class="row">
      <!-- Kolom Kiri: Foto Profil -->
      <div class="col-md-4 text-center mb-4">
        <div class="position-relative d-inline-block mb-3">
          @php $initial = strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)); @endphp

          @if(!empty(Auth::user()->photo) && Storage::disk('public')->exists(Auth::user()->photo))
            <img id="profileImage"
                 src="{{ asset('storage/' . Auth::user()->photo) }}"
                 alt="Foto Profil"
                 class="rounded-circle border shadow-sm"
                 width="150" height="150"
                 style="object-fit:cover;cursor:pointer;"
                 onclick="showFullImage(this)">
          @else
            <div id="defaultAvatar"
                 class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center shadow-sm"
                 style="width:150px;height:150px;font-size:50px;font-weight:bold;cursor:pointer;"
                 onclick="showFullImage(this)">
              {{ $initial }}
            </div>
          @endif

          <!-- Tombol hapus -->
          <form method="POST" action="{{ route('profile.deletePhoto') }}"
                class="position-absolute delete-photo-form"
                style="bottom:0;right:0;">
            @csrf @method('DELETE')
            <button type="button"
                    class="btn btn-danger btn-sm rounded-circle d-flex align-items-center justify-content-center shadow"
                    style="width:32px;height:32px;">
              <i class="bi bi-trash3-fill" style="font-size:14px;"></i>
            </button>
          </form>
        </div>

        <!-- Upload Foto -->
        <form method="POST" action="{{ route('profile.updatePhoto') }}" enctype="multipart/form-data">
          @csrf
          <div class="input-group mb-2">
            <input type="file" name="photo" accept="image/*" class="form-control form-control-sm" onchange="previewProfile(event)">
          </div>
          <button class="btn btn-outline-primary btn-sm w-100">
            <i class="bi bi-upload me-1"></i> Unggah Foto
          </button>
        </form>
      </div>

      <!-- Kolom Kanan: Info Akun -->
      <div class="col-md-8">
        <!-- Update Info -->
        <h5 class="fw-bold mb-3"><i class="bi bi-person-circle me-2"></i>Informasi Akun</h5>
        <form id="updateInfoForm" method="POST" action="{{ route('profile.updateInfo') }}">
          @csrf
          <div class="mb-3">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" name="name" value="{{ Auth::user()->name }}" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Alamat Email</label>
            <input type="email" name="email" value="{{ Auth::user()->email }}" class="form-control" required>
          </div>
          <button class="btn btn-success px-4 me-auto" style="width: 40%;">
            <i class="bi bi-save2 me-1"></i> Simpan Perubahan
          </button>
        </form>

        <hr class="my-4">

        <!-- Update Password -->
        <h5 class="fw-bold mb-3"><i class="bi bi-shield-lock me-2"></i>Ganti Kata Sandi</h5>
        <form id="updatePasswordForm" method="POST" action="{{ route('profile.updatePassword') }}">
          @csrf
          <div class="mb-3">
            <label class="form-label">Kata Sandi Saat Ini</label>
            <input type="password" name="current_password" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Kata Sandi Baru</label>
            <input type="password" name="new_password" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Konfirmasi Kata Sandi Baru</label>
            <input type="password" name="new_password_confirmation" class="form-control" required>
          </div>
          <button class="btn btn-warning px-4 text-dark" style="width: 40%;">
            <i class="bi bi-key-fill me-1"></i> Perbarui Kata Sandi
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal Gambar -->
<div id="imageModal" class="d-none position-fixed top-0 start-0 w-100 h-100"
     style="z-index:1050;background-color:rgba(0,0,0,0.85);" onclick="closeImageModal()">
  <span class="position-absolute top-0 end-0 text-white fs-1 fw-bold m-3" style="cursor:pointer;">&times;</span>
  <div class="d-flex justify-content-center align-items-center h-100">
    <img id="fullImage" style="max-width:90%;max-height:90%;border-radius:12px;">
  </div>
</div>

<script>
  setTimeout(function() {
    let alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
      alert.classList.add('fade');
      setTimeout(() => alert.remove(), 500);
    });
  }, 3000);

  function previewProfile(event) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = e => {
      const img = document.getElementById('profileImage');
      const avatar = document.getElementById('defaultAvatar');

      if (avatar) avatar.style.display = 'none';

      if (img) {
        img.src = e.target.result;
        img.style.display = 'block';
      } else {
        const newImg = document.createElement('img');
        Object.assign(newImg, {
          id: 'profileImage',
          src: e.target.result,
          alt: 'Foto Profil',
          width: 150, height: 150,
          className: 'rounded-circle border shadow-sm',
        });
        newImg.style.objectFit = 'cover';
        newImg.style.cursor = 'pointer';
        newImg.onclick = () => showFullImage(newImg);
        event.target.closest('.position-relative').prepend(newImg);
      }
    };
    reader.readAsDataURL(file);
  }

  // Modal foto profil
  function showFullImage(element) {
    const modal = document.getElementById('imageModal');
    const img = document.getElementById('fullImage');

    if (element.tagName === 'IMG') {
      img.src = element.src;
    } else {
      const canvas = document.createElement('canvas');
      canvas.width = 400;
      canvas.height = 400;
      const ctx = canvas.getContext('2d');
      ctx.fillStyle = getComputedStyle(element).backgroundColor;
      ctx.fillRect(0, 0, canvas.width, canvas.height);
      ctx.font = "bold 200px Arial";
      ctx.fillStyle = "white";
      ctx.textAlign = "center";
      ctx.textBaseline = "middle";
      ctx.fillText(element.textContent.trim(), 200, 210);
      img.src = canvas.toDataURL('image/png');
    }

    modal.classList.remove('d-none');
  }

  function closeImageModal() {
    document.getElementById('imageModal').classList.add('d-none');
  }

  // Konfirmasi hapus foto profil dengan SweetAlert2
   document.querySelectorAll('.delete-photo-form').forEach(form => {
    const button = form.querySelector('button');
    button.addEventListener('click', function(e) {
      e.preventDefault();
      Swal.fire({
        icon: 'warning',
        title: 'Hapus foto profil?',
        text: 'Foto akan dihapus permanen.',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          form.submit();
        }
      });
    });
  });

  const updateInfoForm = document.querySelector('form[action="{{ route('profile.updateInfo') }}"]');
  if (updateInfoForm) {
    updateInfoForm.addEventListener("submit", function(e) {
      e.preventDefault();
      Swal.fire({
        icon: 'question',
        title: 'Simpan perubahan?',
        text: 'Nama dan email akan diperbarui.',
        showCancelButton: true,
        confirmButtonText: 'Ya, simpan',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          this.submit();
        }
      });
    });
  }

  const updatePasswordForm = document.querySelector('form[action="{{ route('profile.updatePassword') }}"]');
  if (updatePasswordForm) {
    updatePasswordForm.addEventListener("submit", function(e) {
      e.preventDefault();
      Swal.fire({
        icon: 'warning',
        title: 'Ganti kata sandi?',
        text: 'Pastikan password baru sudah benar.',
        showCancelButton: true,
        confirmButtonText: 'Ya, ganti',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          this.submit();
        }
      });
    });
  }

</script>

<style>
  .alert.fade {
    opacity: 0;
    transition: opacity .5s ease-in-out;
  }
</style>
@endsection