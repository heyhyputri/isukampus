<?php
session_start();

if (isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Username dan Password sederhana
    if ($username == "admin" && $password == "admin123") {
        $_SESSION['login'] = true;
        $_SESSION['username'] = $username;
        header("Location: index.php");
        exit;
    } else {
        $error = "Username atau Password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Isu Kampus</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom Style -->
    <link href="assets/style.css?v=<?= filemtime('assets/style.css'); ?>" rel="stylesheet">
</head>
<body>

    <div class="login-container">
        <div class="login-card">
            <div class="text-center">
                <div class="login-logo" style="background: rgba(230, 220, 207, 0.1); border: 1px solid rgba(230, 220, 207, 0.3); box-shadow: none;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#e6dccf" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                        <path d="M6 12v5c0 2 2 3 6 3s6-1 6-3v-5"/>
                    </svg>
                </div>
                <h2 style="font-weight: 500; letter-spacing: 1px;">LAPORAN ISU KAMPUS</h2>
                <p>Silakan login untuk mengelola laporan isu</p>
            </div>

            <?php if (isset($error)) : ?>
                <div class="alert alert-danger d-flex align-items-center" role="alert" style="border-radius: 4px;">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>
                    <div>
                        <?= $error; ?>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label fw-medium text-secondary">Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 4px 0 0 4px;">
                            <i class="fa-regular fa-user"></i>
                        </span>
                        <input type="text"
                               name="username"
                               class="form-control border-start-0 ps-0"
                               placeholder="admin"
                               style="border-radius: 0 4px 4px 0;"
                               required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-medium text-secondary">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted" style="border-radius: 4px 0 0 4px;">
                            <i class="fa-regular fa-lock"></i>
                        </span>
                        <input type="password"
                               name="password"
                               class="form-control border-start-0 ps-0"
                               placeholder="Password: admin123"
                               style="border-radius: 0 4px 4px 0;"
                               required>
                    </div>
                </div>

                <button type="submit"
                        name="login"
                        class="btn btn-primary-custom w-100 py-2.5 fw-semibold"
                        style="border-radius: 4px;">
                    <i class="fa-regular fa-circle-check me-2"></i>Login
                </button>
            </form>
        </div>
    </div>

</body>
</html>
