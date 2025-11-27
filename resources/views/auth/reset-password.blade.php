<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Kata Sandi - Radar Files</title>

    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>

<body class="bg-light d-flex justify-content-center align-items-center vh-100">

    <div class="card shadow p-4" style="width: 380px;">

        <h4 class="text-center mb-3" style="color:#1e3a8a;">Buat Kata Sandi Baru</h4>

        <p class="text-muted mb-3" style="font-size: 14px;">
            Silakan buat kata sandi baru yang aman dan memenuhi ketentuan keamanan.
        </p>

        {{-- Pesan error --}}
        @if ($errors->any())
            <div class="alert alert-danger py-2">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li style="font-size: 14px;">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Pesan sukses --}}
        @if (session('success'))
            <div class="alert alert-success py-2" style="font-size: 14px;">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <!-- Token -->
            <input type="hidden" name="token" value="{{ $token }}">

            <!-- Email -->
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email"
                       name="email"
                       class="form-control"
                       value="{{ $email }}"
                       readonly>
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label class="form-label">Kata Sandi Baru</label>

                <div class="input-group">
                    <input type="password"
                           name="password"
                           id="password"
                           class="form-control"
                           placeholder="Masukkan kata sandi baru"
                           required>

                    <span class="input-group-text" id="togglePassword" style="cursor:pointer;">
                        <i class="bi bi-eye-slash" id="iconPassword"></i>
                    </span>
                </div>
            </div>

            <!-- Konfirmasi -->
            <div class="mb-3">
                <label class="form-label">Konfirmasi Kata Sandi</label>

                <div class="input-group">
                    <input type="password"
                           name="password_confirmation"
                           id="confirmPassword"
                           class="form-control"
                           placeholder="Masukkan ulang kata sandi"
                           required>

                    <span class="input-group-text" id="toggleConfirm" style="cursor:pointer;">
                        <i class="bi bi-eye-slash" id="iconConfirm"></i>
                    </span>
                </div>
            </div>

            {{-- Ketentuan Password --}}
            <div class="alert alert-info py-2" style="font-size: 13px;">
                <strong>Kriteria kata sandi:</strong>
                <ul class="mb-0 ps-3">
                    <li>Minimal 8 karakter</li>
                    <li>Mengandung huruf besar dan kecil</li>
                    <li>Mengandung angka</li>
                    <li>Mengandung simbol (misal: ! @ # $ % & *)</li>
                </ul>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                Reset Kata Sandi
            </button>
        </form>

        <div class="text-center mt-3">
            <a href="{{ route('login') }}"
               class="text-decoration-none"
               style="font-size: 14px; color:#2563eb;">
               ← Kembali ke Login
            </a>
        </div>

    </div>

    <!-- Show/Hide Password Script -->
    <script>
        const togglePassword = document.getElementById("togglePassword");
        const toggleConfirm = document.getElementById("toggleConfirm");
        const pwd = document.getElementById("password");
        const cpwd = document.getElementById("confirmPassword");
        const iconPwd = document.getElementById("iconPassword");
        const iconCpwd = document.getElementById("iconConfirm");

        togglePassword.onclick = () => {
            pwd.type = pwd.type === "password" ? "text" : "password";
            iconPwd.classList.toggle("bi-eye");
            iconPwd.classList.toggle("bi-eye-slash");
        };

        toggleConfirm.onclick = () => {
            cpwd.type = cpwd.type === "password" ? "text" : "password";
            iconCpwd.classList.toggle("bi-eye");
            iconCpwd.classList.toggle("bi-eye-slash");
        };
    </script>

</body>
</html>
