<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    if (empty($username) || empty($password)) {
        $error = "Please enter username and password.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                if ($remember) {
                    setcookie('remember_user', $user['username'], time() + (30 * 24 * 60 * 60), "/");
                }

                $_SESSION['flash_success'] = "Welcome back, " . htmlspecialchars($user['username']) . "!";
                header('Location: index.php');
                exit;
            } else {
                $error = "Invalid username or password.";
            }
        } catch (\PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
} else {
    // Auto-fill username if 'remember_user' cookie is set
    $username = $_COOKIE['remember_user'] ?? '';
}
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-5 pt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card p-4">
                <h3 class="text-center mb-4 text-accent">Login</h3>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <form method="post" action="login.php">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember" <?php echo isset($_COOKIE['remember_user']) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="remember">Remember Me (30 days)</label>
                    </div>
                    <button type="submit" class="btn btn-outline-info w-100">Login</button>
                    <div class="mt-3 text-center">
                        <small>Don't have an account? <a href="register.php" class="text-accent">Register here</a></small>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
