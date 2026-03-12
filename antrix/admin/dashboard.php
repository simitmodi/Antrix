<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';

requireAdmin();

// Fetch stats
$stat_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$stat_events = $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
$stat_pending = $pdo->query("SELECT COUNT(*) FROM events WHERE is_approved = 0")->fetchColumn();
$stat_news = $pdo->query("SELECT COUNT(*) FROM news")->fetchColumn();

include '../includes/header.php';
?>

<div class="container mt-5 pt-5 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom border-secondary pb-3">
        <h2 class="text-accent decorative-heading m-0">Admin Dashboard</h2>
        <a href="../index.php" class="btn btn-outline-light btn-sm">Back to Main Site</a>
    </div>

    <div class="row text-center mb-5">
        <div class="col-md-3 mb-3">
            <div class="card bg-surface border-info h-100 p-4">
                <h1 class="display-4 text-white"><?php echo $stat_pending; ?></h1>
                <p class="text-info text-uppercase mb-0">Pending Events</p>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-surface border-secondary h-100 p-4">
                <h1 class="display-4 text-white"><?php echo $stat_events; ?></h1>
                <p class="text-muted text-uppercase mb-0">Total Events</p>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-surface border-secondary h-100 p-4">
                <h1 class="display-4 text-white"><?php echo $stat_users; ?></h1>
                <p class="text-muted text-uppercase mb-0">Registered Users</p>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-surface border-secondary h-100 p-4">
                <h1 class="display-4 text-white"><?php echo $stat_news; ?></h1>
                <p class="text-muted text-uppercase mb-0">News Articles</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card bg-surface border-0 h-100">
                <div class="card-body">
                    <h4 class="card-title text-white mb-3"><i class="bi bi-calendar-event me-2 text-accent"></i> Manage Events</h4>
                    <p class="text-muted">Review pending submissions, edit existing events, or remove outdated entries from the database.</p>
                    <a href="manage-events.php" class="btn btn-info w-100 text-dark fw-bold mt-auto">Go to Events Management</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card bg-surface border-0 h-100">
                <div class="card-body">
                    <h4 class="card-title text-white mb-3"><i class="bi bi-people me-2 text-accent"></i> Manage Users</h4>
                    <p class="text-muted">View the list of registered explorers and their roles.</p>
                    <a href="manage-users.php" class="btn btn-outline-info w-100 mt-auto">Go to User Management</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
