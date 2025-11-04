<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>@yield('title', 'RadarFiles')</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<style>
  body {
    background-color: #f5f8ff;
    margin: 0;
    overflow-x: hidden;
    transition: padding-left 0.3s ease;
    padding-left: 230px; /* default posisi sidebar */
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
    transition: all 0.3s ease;
    z-index: 1050;
  }

  .sidebar a {
    display: block;
    padding: 10px;
    color: #333;
    border-radius: 8px;
    text-decoration: none;
    margin-bottom: 5px;
    transition: background 0.2s ease;
  }

  .sidebar a.active,
  .sidebar a:hover {
    background-color: #007bff;
    color: #fff;
  }

  .sidebar.closed {
    left: -230px;
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
    text-decoration: none;
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
  <h4 class="text-primary mb-4 text-center">📁 Radar Files</h4>

  <a href="{{ route('dashboard') }}" class="{{ request()->is('dashboard') ? 'active' : '' }}">Dashboard</a>

  <a href="#" class="dropdown-toggle" id="newMenu" data-bs-toggle="dropdown" aria-expanded="false">New</a>
  <ul class="dropdown-menu" aria-labelledby="newMenu">
    <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#createFolderModal">➕ Create Folder</a></li>
    <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#uploadFileModal">📤 Upload File</a></li>
    <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#uploadFolderModal">📁 Upload Folder</a></li>
  </ul>

  <a href="{{ route('folders.index') }}" class="{{ request()->is('folders*') ? 'active' : '' }}">My Folders</a>
  <a href="{{ route('files.index') }}" class="{{ request()->is('files*') ? 'active' : '' }}">My Files</a>

  <form action="{{ route('logout') }}" method="POST" class="mt-3">
    @csrf
    <button type="submit" class="btn btn-danger w-100">Logout</button>
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

  <div class="d-flex align-items-center">
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

  toggle.addEventListener('click', (e) => {
    e.stopPropagation();
    if (window.innerWidth > 992) {
      sidebar.classList.toggle('closed');
      document.body.style.paddingLeft = sidebar.classList.contains('closed') ? '0' : '230px';
    } else {
      sidebar.classList.toggle('active');
      overlay.style.display = sidebar.classList.contains('active') ? 'block' : 'none';
    }
  });

  overlay.addEventListener('click', () => {
    sidebar.classList.remove('active');
    overlay.style.display = 'none';
  });
});
</script>
</body>
</html>