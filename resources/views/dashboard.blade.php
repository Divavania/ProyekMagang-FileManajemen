<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Radar Files</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f5f5f5;
        }
        .dashboard-container {
            max-width: 900px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .header h2 {
            color: #1e3a8a;
        }
        .card-stat {
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            background-color: #2563eb;
            color: white;
        }
        .btn-logout {
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 500;
        }
    </style>
</head>
<body>

    <div class="dashboard-container">
        <div class="header">
            <h2>Selamat datang, {{ auth()->user()?->name ?? 'Guest' }}</h2>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-danger btn-logout">Logout</button>
            </form>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card-stat">
                    <h5>Total Users</h5>
                    <h2>{{ $totalUsers }}</h2>
                </div>
            </div>
            <!-- Bisa tambah card lain di sini -->
        </div>

        <div class="mt-4">
            <p>Ini adalah halaman dashboard utama untuk mengelola Radar Files.</p>
        </div>
    </div>

</body>
</html>
