<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>@yield('title', 'RadarFiles')</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- CSRF token untuk AJAX -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Bootstrap & Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<style>
  body {
    background-color: #f5f8ff;
    margin: 0;
    overflow-x: hidden;
    transition: padding-left 0.3s ease;
    padding-left: 230px;
  }

/* === SIDEBAR === */
.sidebar {
  height: 100vh;
  background: #fff;
  border-right: 1px solid #ddd;
  padding: 20px;
  width: 230px;
  position: fixed;
  top: 0;
  left: 0;
  overflow-y: auto;
  transition: transform 0.3s ease;
  z-index: 1050;
  transform: translateX(0); /* default desktop = terbuka */
}

/* Link & button di dalam sidebar */
.sidebar a,
.sidebar button.dropdown-toggle {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px;
  color: #333;
  border-radius: 8px;
  text-decoration: none;
  margin-bottom: 5px;
  transition: background 0.2s ease, color 0.2s ease;
  width: 100%;
  background: none;
  border: none;
  text-align: left;
}

.sidebar a i,
.sidebar button i {
  font-size: 1.2rem;
}

.sidebar a:hover,
.sidebar button.dropdown-toggle:hover {
  background-color: #007bff;
  color: #fff;
}

/* === OVERLAY (mobile) === */
.sidebar-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.4);
  z-index: 1500;
  display: none;
}

/* === MOBILE === */
@media (max-width: 991px) {
  .sidebar {
    transform: translateX(-230px); /* default tertutup */
  }

  .sidebar.active {
    transform: translateX(0); /* buka */
  }

  .sidebar.active + .sidebar-overlay {
    display: block;
  }
}

/* === DESKTOP === */
@media (min-width: 992px) {
  body.sidebar-open {
    padding-left: 230px;
  }

  body.sidebar-closed {
    padding-left: 0 !important;
  }

  .sidebar.closed {
    transform: translateX(-230px); /* tutup */
  }
}
  .sidebar.closed + .sidebar-overlay {
    display: none;
  }

  /* === MAIN CONTENT === */
  .main-content {
    padding: 20px;
    min-height: 100vh;
    transition: all 0.3s ease;
  }

  /* === TOPBAR === */
  .topbar {
    position: sticky;
    top: 0;
    background-color: #f5f8ff;
    padding: 15px 25px;
    border-bottom: 1px solid #ddd;
    z-index: 100;
    transition: all 0.3s ease;
  }

  /* === PROFILE CIRCLE === */
  .profile-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #007bff;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    cursor: pointer;
  }

  /* === RESPONSIVE === */
  @media (max-width: 992px) {
    body {
      padding-left: 0;
    }

    .sidebar {
      transform: translateX(-250px);
      width: 250px;
      z-index: 2000;
      box-shadow: 2px 0 10px rgba(0,0,0,0.1);
    }

    .sidebar.active {
      transform: translateX(0);
    }

    .sidebar-overlay {
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.3);
      z-index: 1500;
      display: none;
    }

    .sidebar.active + .sidebar-overlay {
      display: block;
    }
  }
</style>
</head>

<body>
<!-- Sidebar -->
<div class="sidebar" id="sidebar">
  <h4 class="text-primary mb-4 text-center">
      <img src="{{ asset('images/RadarMadiun.jpg') }}" alt="Logo" class="me-2" style="width: 40px; height: 40px; object-fit: cover; vertical-align: middle;">
      Radar Files
  </h4>

  <a href="{{ route('dashboard') }}" class="{{ request()->is('dashboard') ? 'active' : '' }}">
    <i class="bi bi-speedometer2"></i> Dashboard
  </a>

  <!-- === MENU NEW (dropdown) === -->
  <div class="dropdown">
    <button class="dropdown-toggle" type="button" id="newMenu" data-bs-toggle="dropdown" aria-expanded="false">
      <i class="bi bi-plus-circle"></i> Baru
    </button>
    <ul class="dropdown-menu w-100" aria-labelledby="newMenu">
      <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#createFolderModal">
        <i class="bi bi-folder-plus me-2"></i> Buat Folder
      </a></li>
      <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#uploadFileModal">
        <i class="bi bi-file-earmark-arrow-up me-2"></i> Unggah File
      </a></li>
      <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#uploadFolderModal">
        <i class="bi bi-folder-symlink me-2"></i> Unggah Folder
      </a></li>
    </ul>
  </div>

  <a href="{{ route('files.index') }}" class="{{ request()->is('files*') ? 'active' : '' }}">
    <i class="bi bi-file-earmark-text"></i> File Saya
  </a>

  <a href="{{ route('folders.index') }}" class="{{ request()->is('folders*') ? 'active' : '' }}">
    <i class="bi bi-folder"></i> Folder Saya
  </a>

  <a href="{{ route('favorites.index') }}" class="{{ request()->is('favorites*') ? 'active' : '' }}">
    <i class="bi bi-star"></i> Favorit
  </a>

  <a href="{{ route('shared.index') }}" class="{{ request()->is('shared*') ? 'active' : '' }}">
    <i class="bi bi-people"></i> Berbagi & Publik
  </a>

  <a href="{{ route('trash.index') }}" class="{{ request()->is('trash*') ? 'active' : '' }}">
    <i class="bi bi-trash"></i> Sampah
  </a>

   {{-- Menu tambah user & log aktivitas untuk admin & superadmin --}}
  @if(Auth::check())

    {{-- ADMIN & SUPERADMIN boleh tambah user --}}
    @if(Auth::check() && in_array(Auth::user()->role, ['admin', 'superadmin']))
    <a href="{{ route('users.index') }}" class="{{ request()->is('users*') ? 'active' : '' }}">
        <i class="bi bi-person-plus"></i> Daftar Pengguna
    </a>
    @endif

    {{-- Hanya SUPERADMIN boleh lihat log aktivitas --}}
    @if(Auth::check() && Auth::user()->role === 'superadmin')
    <a href="{{ route('activity.logs') }}" class="{{ request()->is('activity-logs') ? 'active' : '' }}">
        <i class="bi bi-clipboard-data"></i> Log Aktivitas
    </a>
    @endif

@endif

  <form action="{{ route('logout') }}" method="POST" class="mt-3">
    @csrf
    <button type="submit" class="btn btn-danger w-100">
      <i class="bi bi-box-arrow-right me-2"></i> Keluar
    </button>
  </form>
</div>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Topbar -->
<div class="topbar d-flex justify-content-between align-items-center flex-wrap gap-3">
  <div class="d-flex align-items-center gap-3">
    <button id="sidebarToggle" class="btn border-0 p-0 bg-transparent">
      <i class="bi bi-list fs-3"></i>
    </button>
    <h4 class="m-0">@yield('page_title', 'Dashboard')</h4>
  </div>

  <div class="d-flex align-items-center gap-3">
    {{-- Ambil notifikasi dari model custom agar tidak memanggil notifiable_type --}}
    @php
  if (Auth::check()) {
      // ambil semua notifikasi via relasi bawaan Laravel
      $user = Auth::user();
      $unreadCount = $user->unreadNotifications->count();
      $latestNotifs = $user->notifications->take(5);
  } else {
      $unreadCount = 0;
      $latestNotifs = collect();
  }
@endphp

 <!-- Notification Dropdown -->
  <div class="dropdown">
    <button class="btn border-0 bg-transparent position-relative" 
        type="button" id="notificationDropdown"
        data-bs-toggle="dropdown" aria-expanded="false">
      <i class="bi bi-bell fs-5"></i>
      @if($unreadCount > 0)
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
          {{ $unreadCount }}
        </span>
      @endif
    </button>

    <div class="dropdown-menu dropdown-menu-end shadow border-0 p-0 rounded-3 animate-fade"
        aria-labelledby="notificationDropdown" style="min-width: 340px;">
      <div class="p-3 border-bottom bg-light fw-semibold">Notifikasi</div>

      {{-- Scroll area untuk daftar notifikasi --}}
      <div class="list-group list-group-flush" style="max-height: 300px; overflow-y: auto;">
        @forelse($latestNotifs as $notification)
          <a href="{{ route('notifications.read', $notification->id) }}" 
            class="list-group-item list-group-item-action d-flex justify-content-between align-items-start">
            <div>
              <div class="fw-semibold">
                {{ Str::limit($notification->data['title'] ?? 'Notifikasi', 50) }}
              </div>
              <small class="text-muted d-block">
                {{ Str::limit($notification->data['message'] ?? '', 80) }}
              </small>
              <div class="text-muted small">{{ $notification->created_at->diffForHumans() }}</div>
            </div>
            @if(is_null($notification->read_at))
              <span class="badge bg-primary rounded-circle p-2 ms-2"></span>
            @endif
          </a>
        @empty
          <div class="p-3 text-center text-muted">Belum ada notifikasi</div>
        @endforelse
      </div>

      <div class="border-top">
        <a href="{{ route('notifications.index') }}" class="dropdown-item text-center text-primary py-2">
          Lihat Semua
        </a>
      </div>
    </div>
  </div>

    <!-- Role & Profil -->
    <span class="text-muted me-2">{{ Auth::user()->role ?? 'User' }}</span>
    @if(Auth::user()->photo && file_exists(public_path('storage/' . Auth::user()->photo)))
      <img src="{{ asset('storage/' . Auth::user()->photo) }}"
           class="rounded-circle"
           style="width:40px;height:40px;object-fit:cover;cursor:pointer;"
           onclick="window.location.href='{{ route('profile') }}'">
    @else
      <div class="profile-circle" onclick="window.location.href='{{ route('profile') }}'">
        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
      </div>
    @endif
  </div>
</div>

<!-- Main Content -->
<div class="main-content container-fluid">
  @yield('content')
</div>

<!-- ===== Modals ===== -->
@include('partials.modals')

<script>
document.addEventListener('DOMContentLoaded', () => {
  const sidebar = document.getElementById('sidebar');
  const toggle = document.getElementById('sidebarToggle');
  const overlay = document.getElementById('sidebarOverlay');

  // Default desktop = sidebar terbuka
  if (window.innerWidth >= 992) {
    document.body.classList.add('sidebar-open');
  }

  toggle.addEventListener('click', () => {
    // === MOBILE ===
    if (window.innerWidth < 992) {
      sidebar.classList.toggle('active');
      overlay.style.display = sidebar.classList.contains('active') ? 'block' : 'none';
      return;
    }

    // === DESKTOP ===
    const isClosed = sidebar.classList.contains('closed');

    sidebar.classList.toggle('closed'); // toggle geser sidebar

    document.body.classList.toggle('sidebar-open', isClosed);
    document.body.classList.toggle('sidebar-closed', !isClosed);
  });

  // Klik overlay untuk menutup sidebar di mobile
  overlay.addEventListener('click', () => {
    sidebar.classList.remove('active');
    overlay.style.display = 'none';
  });
});
document.addEventListener('DOMContentLoaded', () => {s

// Notifikasi: klik item -> tandai dibaca via AJAX
  const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  document.querySelectorAll('.notification-item').forEach(item => {
    item.addEventListener('click', function (e) {
      // jika link valid, biarkan navigasi setelah request (optional)
      const id = this.dataset.id;
      if (!id) return;

      // kirim request POST untuk mark as read
      fetch(`/notifications/${id}/read`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': token,
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({})
      }).then(res => {
        if (res.ok) {
          // hapus badge kecil yang menandai belum dibaca
          const badge = this.querySelector('.badge');
          if (badge) badge.remove();

          // kurangi angka lencana global (jika ada)
          const badgeGlobal = document.querySelector('#notificationDropdown .badge');
          if (badgeGlobal) {
            let val = parseInt(badgeGlobal.textContent || 0);
            val = Math.max(0, val - 1);
            if (val <= 0) badgeGlobal.remove();
            else badgeGlobal.textContent = val;
          }
        }
      }).catch(err => {
        console.error('Mark as read failed', err);
      });

      // jika anchor punya href target, lanjutkan navigasi
      const href = this.getAttribute('href');
      if (href && href !== '#') {
        // gunakan timeout kecil agar request sempat dikirim
        setTimeout(() => { window.location = href; }, 150);
      } else {
        e.preventDefault();
      }
    });
  });
});

</script>
@stack('scripts')
</body>
</html>