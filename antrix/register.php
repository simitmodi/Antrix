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
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Server-side validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($username) < 3) {
        $error = "Username must be at least 3 characters.";
    } else {
        try {
            // Check for existing username/email
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            if ($stmt->fetch()) {
                $error = "Username or email already exists.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
                if ($stmt->execute([$username, $email, $hashed_password])) {
                    // Auto login on register
                    $_SESSION['user_id'] = $pdo->lastInsertId();
                    $_SESSION['username'] = $username;
                    $_SESSION['role'] = 'user';
                    $_SESSION['flash_success'] = "Registration successful! Welcome, $username.";
                    header('Location: index.php');
                    exit;
                } else {
                    $error = "Registration failed. Please try again.";
                }
            }
        } catch (\PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-5 pt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card p-4">
                <h3 class="text-center mb-4 text-accent">Register</h3>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <!-- JS validation attached via form onsubmit or inline on the button. 
                     We use the function defined in main.js -->
                <form method="post" action="register.php" id="register-form" onsubmit="return validateRegisterForm('register-form');">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required minlength="3">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                        <div id="email-error" class="text-danger small mt-1"></div>
                    </div>
                    <div class="mb-3">
                        <label for="reg_password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="reg_password" name="password" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label for="reg_confirm_password" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="reg_confirm_password" name="confirm_password" required minlength="6">
                        <div id="pw-match-msg" class="mt-1"></div>
                        <div id="pw-error" class="text-danger small mt-1"></div>
                    </div>
                    <button type="submit" class="btn btn-outline-info w-100">Register</button>
                    <div class="mt-3 text-center">
                        <small>Already have an account? <a href="login.php" class="text-accent">Login here</a></small>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
