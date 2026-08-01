<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Login') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: rgba(30, 41, 59, 0.9);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 16px;
            padding: 2.5rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
        }
        .login-logo {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 1.75rem;
            color: white;
        }
        .form-control {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255,255,255,0.1);
            color: #fff;
            padding: 0.75rem 1rem;
            border-radius: 10px;
        }
        .form-control:focus {
            background: rgba(15, 23, 42, 0.8);
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
            color: #fff;
        }
        .btn-login {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            border: none;
            padding: 0.75rem;
            border-radius: 10px;
            font-weight: 600;
            width: 100%;
        }
        .btn-login:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-logo">
            <i class="fa-solid fa-server"></i>
        </div>
        <h3 class="text-center text-white mb-1">BindAdmin</h3>
        <p class="text-center text-muted mb-4" style="font-size:0.9rem;">BIND9 DNS Management</p>
        
        <?php if ($msg = flash('error')): ?>
            <div class="alert alert-danger py-2" style="font-size:0.875rem;">
                <i class="fa-solid fa-circle-exclamation me-1"></i> <?= e($msg) ?>
            </div>
        <?php endif; ?>
        <?php if ($msg = flash('success')): ?>
            <div class="alert alert-success py-2" style="font-size:0.875rem;">
                <?= e($msg) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="/login">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label text-muted small">Username</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-secondary text-muted">
                        <i class="fa-solid fa-user"></i>
                    </span>
                    <input type="text" name="username" class="form-control" value="<?= e(old('username')) ?>" required autofocus placeholder="admin">
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label text-muted small">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-secondary text-muted">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input type="password" name="password" class="form-control" required placeholder="••••••••">
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-login">
                <i class="fa-solid fa-right-to-bracket me-2"></i> Sign In
            </button>
        </form>
        
        <p class="text-center text-muted mt-4 mb-0" style="font-size:0.75rem;">
            Default: <code>admin</code> / <code>admin123</code>
        </p>
    </div>
</body>
</html>
