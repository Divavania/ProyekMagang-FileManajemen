<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Radar Files</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body, html {
      width: 100%;
      height: 100%;
      overflow: hidden;
      background-color: #ffffff;
    }

    .login-container {
      display: flex;
      flex-direction: row;
      height: 100vh;
    }

    /* === Bagian kiri (gambar) === */
    .login-left {
      flex: 1;
      background: url("{{ asset('images/login-illustration.png') }}") center center / cover no-repeat;
    }

    /* === Bagian kanan (form login putih) === */
    .login-right {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      background-color: #ffffff;
      padding: 2rem;
    }

    .login-content {
      max-width: 350px;
      width: 100%;
      text-align: center;
    }

    .login-content h2 {
      font-family: 'Plus Jakarta Sans', sans-serif;
      font-weight: 600;
      color: #1e3a8a;
      margin-bottom: 20px;
      letter-spacing: -0.5px;
    }

    .form-control {
      border-radius: 8px;
      padding: 10px;
      margin-bottom: 15px;
    }

    .btn-login {
      background-color: #2563eb;
      color: white;
      border: none;
      border-radius: 8px;
      width: 100%;
      padding: 10px;
      font-weight: 500;
      transition: 0.2s;
    }

    .btn-login:hover {
      background-color: #1d4ed8;
    }

    .password-wrapper {
      position: relative;
    }

    .password-wrapper .toggle-password {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: #6b7280;
    }

    /* === Responsif === */
    @media (max-width: 992px) {
      body, html { overflow: auto; }
      .login-container { flex-direction: column; height: auto; }
      .login-left, .login-right { width: 100%; height: auto; }
      .login-left {
        height: 250px;
        background-position: center;
        background-size: cover;
      }
      .login-right { padding: 2rem 1.5rem; }
      .login-content { max-width: 90%; }
    }

    @media (max-width: 576px) {
      .login-content { max-width: 100%; }
    }
  </style>
</head>

<body>
  <div class="login-container">
    <!-- Bagian kiri (gambar separuh layar) -->
    <div class="login-left"></div>

    <!-- Bagian kanan (form login putih) -->
    <div class="login-right">
      <div class="login-content text-center">
        <img src="{{ asset('images/radar-madiun-logo.png') }}" alt="Radar Madiun" width="120" class="mb-3">
        <h2>Radar Files</h2>

        <form method="POST" action="{{ route('login') }}">
          @csrf
          <input type="email" name="email" placeholder="Email" class="form-control mb-3" required>

          <div class="password-wrapper mb-3">
            <input type="password" name="password" id="password" placeholder="Kata Sandi" class="form-control" required>
            <i class="bi bi-eye toggle-password" id="togglePassword"></i>
          </div>

          <button type="submit" class="btn-login">Masuk</button>
        </form>

        <div class="mt-3">
          <a href="{{ route('password.request') }}" class="text-decoration-none" style="color:#2563eb; font-size: 14px;">
            Lupa sandi?
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- JS Bootstrap dan SweetAlert -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Toggle Password -->
  <script>
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    togglePassword.addEventListener('click', function () {
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      this.classList.toggle('bi-eye');
      this.classList.toggle('bi-eye-slash');
    });
  </script>

  <!-- SweetAlert Notifikasi -->
  @if (session('success'))
  <script>
    Swal.fire({
      icon: 'success',
      title: 'Berhasil Masuk',
      text: '{{ session('success') }}',
      showConfirmButton: false,
      timer: 2000
    });
  </script>
  @endif

  @if (session('error'))
  <script>
    Swal.fire({
      icon: 'error',
      title: 'Gagal Masuk',
      text: '{{ session('error') }}',
      showConfirmButton: true,
    });
  </script>
  @endif
</body>
</html>
