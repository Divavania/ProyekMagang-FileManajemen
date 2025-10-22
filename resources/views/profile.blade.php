@extends('layouts.app')

@section('title', 'My Profile - RadarFiles')
@section('content')
<div class="mt-4">
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
  @endif

  <div class="border-0 rounded-4 p-4 shadow-sm bg-white">
    <div class="row">
      <div class="col-md-4 text-center mb-4">
        <div class="position-relative d-inline-block mb-3">
          @php $initial = strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)); @endphp

          @if(!empty(Auth::user()->photo) && Storage::disk('public')->exists(Auth::user()->photo))
            <img id="profileImage"
                 src="{{ asset('storage/' . Auth::user()->photo) }}"
                 alt="Profile Photo"
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
                onsubmit="return confirm('Yakin ingin menghapus foto profil?')"
                class="position-absolute" style="bottom:0;right:0;">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm rounded-circle d-flex align-items-center justify-content-center shadow" style="width:30px;height:30px;padding:0;">
              <i class="bi bi-trash3" style="font-size:14px;"></i>
            </button>
          </form>
        </div>

        <!-- Upload Foto -->
        <form method="POST" action="{{ route('profile.updatePhoto') }}" enctype="multipart/form-data">
          @csrf
          <input type="file" name="photo" accept="image/*" class="form-control mb-2" onchange="previewProfile(event)">
          <button class="btn btn-primary btn-sm w-100">Update Photo</button>
        </form>
      </div>

      <div class="col-md-8">
        <!-- Update Info -->
        <form method="POST" action="{{ route('profile.updateInfo') }}">
          @csrf
          <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" value="{{ Auth::user()->name }}" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" value="{{ Auth::user()->email }}" class="form-control" required>
          </div>
          <button class="btn btn-success w-100">Update Info</button>
        </form>

        <hr class="my-4">

        <!-- Update Password -->
        <form method="POST" action="{{ route('profile.updatePassword') }}">
          @csrf
          <div class="mb-3">
            <label class="form-label">Current Password</label>
            <input type="password" name="current_password" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">New Password</label>
            <input type="password" name="new_password" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Confirm New Password</label>
            <input type="password" name="new_password_confirmation" class="form-control" required>
          </div>
          <button class="btn btn-warning w-100">Update Password</button>
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
          alt: 'Profile Photo',
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

  function showFullImage(element) {
    const modal = document.getElementById('imageModal');
    const img = document.getElementById('fullImage');
    
    if (element.tagName === 'IMG') {
      img.src = element.src;
      img.style.backgroundColor = 'transparent';
      img.style.width = 'auto';
      img.style.height = 'auto';
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
</script>
@endsection