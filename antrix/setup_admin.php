<?php
require_once 'includes/db.php';

try {
    // Hash passwords
    $admin_pw = password_hash('admin123', PASSWORD_DEFAULT);
    $user_pw = password_hash('space123', PASSWORD_DEFAULT);

    // Update admin
    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = 'admin'");
    $stmt->execute([$admin_pw]);

    // Update stargazer
    $stmt2 = $pdo->prepare("UPDATE users SET password = ? WHERE username = 'stargazer'");
    $stmt2->execute([$user_pw]);

    echo "<h3>Success!</h3>";
    echo "<p>Admin and User passwords have been securely hashed and updated in the database.</p>";
    echo "<p><a href='index.php'>Go to Homepage</a> | <a href='login.php'>Go to Login</a></p>";
    echo "<p><em>Note: You can delete this setup_admin.php file now for security.</em></p>";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
