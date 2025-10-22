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
}

.sidebar a.active,
.sidebar a:hover {
  background-color: #007bff;
  color: #fff;
}

/* Sidebar desktop toggle */
.sidebar.closed {
  margin-left: -230px;
}

/* === MAIN CONTENT === */
.main-content {
  margin-left: 230px;
  padding: 20px;
  min-height: 100vh;
  transition: all 0.3s ease;
}

.main-content.fullwidth {
  margin-left: 0;
}

/* === TOPBAR === */
.topbar {
  position: sticky;
  top: 0;
  background-color: #f5f8ff;
  padding: 15px 25px;
  border-bottom: 1px solid #ddd;
  z-index: 100;
  margin-left: 230px;
  transition: all 0.3s ease;
}

.topbar.fullwidth {
  margin-left: 0;
}

/* === HAMBURGER MODE === */
#sidebarToggle {
  color: #007bff;
  cursor: pointer;
  transition: color 0.2s;
}
#sidebarToggle:hover {
  color: #0056b3;
}

/* Mobile responsive */
@media (max-width: 992px) {
  .sidebar {
    transform: translateX(-250px);
  }

  .sidebar.active {
    transform: translateX(0);
  }

  #sidebarToggle {
    display: block;
  }

  .main-content {
    margin-left: 0;
    padding: 20px;
  }

  .topbar {
    margin-left: 0;
  }
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
</style>
</head>

<body>
<!-- Sidebar -->
<div class="sidebar" id="sidebar">
  <h4 class="text-primary mb-4 w-100 text-center">📁 Radar Files</h4>
  <a href="{{ route('dashboard') }}" class="{{ request()->is('dashboard') ? 'active' : '' }}">Dashboard</a>

  <a href="#" class="dropdown-toggle" id="newMenu" data-bs-toggle="dropdown" aria-expanded="false">New</a>
  <ul class="dropdown-menu" aria-labelledby="newMenu">
    <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#createFolderModal">➕ Create Folder</a></li>
    <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#uploadFileModal">📤 Upload File</a></li>
    <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#uploadFolderModal">📁 Upload Folder</a></li>
  </ul>

  <a href="{{ route('folders.index') }}" class="{{ request()->is('folders*') ? 'active' : '' }}">My Folders</a>
  <a href="#">My Files</a>

  <form action="{{ route('logout') }}" method="POST" class="mt-3">
    @csrf
    <button type="submit" class="btn btn-danger w-100">Logout</button>
  </form>
</div>

<!-- Topbar -->
<div class="topbar d-flex justify-content-between align-items-center flex-wrap gap-3">
  <div class="d-flex align-items-center gap-3">
    <button id="sidebarToggle" class="btn border-0 p-0 bg-transparent d-flex align-items-center">
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
<div class="main-content w-100">
  @yield('content')
</div>

<!-- ====== Modals ====== -->
<!-- Create Folder Modal -->
<div class="modal fade" id="createFolderModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('store.folder') }}">
      @csrf
      <div class="modal-header">
        <h5>Create New Folder</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="text" name="name" class="form-control" placeholder="Enter folder name" required>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Create</button>
      </div>
    </form>
  </div>
</div>

<!-- Upload File Modal -->
<div class="modal fade" id="uploadFileModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('store.file') }}" enctype="multipart/form-data">
      @csrf
      <div class="modal-header"><h5>Upload File</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body"><input type="file" name="file" class="form-control" required></div>
      <div class="modal-footer"><button type="submit" class="btn btn-primary">Upload</button></div>
    </form>
  </div>
</div>

<!-- Upload Folder Modal -->
<div class="modal fade" id="uploadFolderModal" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" method="POST" action="{{ route('store.upload.folder') }}" enctype="multipart/form-data">
      @csrf
      <div class="modal-header"><h5>Upload Folder (ZIP)</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body"><input type="file" name="folder_zip" accept=".zip" class="form-control" required></div>
      <div class="modal-footer"><button type="submit" class="btn btn-primary">Upload</button></div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const sidebar = document.getElementById('sidebar');
  const toggle = document.getElementById('sidebarToggle');
  const mainContent = document.querySelector('.main-content');
  const topbar = document.querySelector('.topbar');

  toggle.addEventListener('click', (e) => {
    e.stopPropagation();
    
    if (window.innerWidth > 992) {
      // Desktop toggle
      sidebar.classList.toggle('closed');
      mainContent.classList.toggle('fullwidth');
      topbar.classList.toggle('fullwidth');
    } else {
      // Mobile toggle
      sidebar.classList.toggle('active');
    }
  });

  document.addEventListener('click', (e) => {
    if (window.innerWidth <= 992 && sidebar.classList.contains('active')) {
      if (!sidebar.contains(e.target) && e.target !== toggle) {
        sidebar.classList.remove('active');
      }
    }
  });
});
</script>
</body>
</html>