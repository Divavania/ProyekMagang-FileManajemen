<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lupa Sandi - Radar Files</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex align-items-center justify-content-center vh-100 bg-light">
  <div class="card shadow p-4" style="width: 350px;">
    <h4 class="text-center mb-3" style="color:#1e3a8a;">Lupa Kata Sandi</h4>
    <p class="text-center text-muted" style="font-size:14px;">Masukkan email akunmu untuk menerima tautan reset kata sandi.</p>
    
    <form method="POST" action="#">
      @csrf
      <input type="email" name="email" placeholder="Masukkan email" class="form-control mb-3" required>
      <button type="submit" class="btn btn-primary w-100">Kirim Link Reset</button>
    </form>

    <div class="text-center mt-3">
      <a href="{{ route('login') }}" class="text-decoration-none" style="color:#2563eb; font-size:14px;">
        ← Kembali ke Login
      </a>
    </div>
  </div>
</body>
</html>
