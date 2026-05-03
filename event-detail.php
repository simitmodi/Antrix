<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header("Location: events.php");
    exit;
}

// Fetch event
$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ? AND is_approved = 1");
$stmt->execute([$id]);
$event = $stmt->fetch();

if (!$event) {
    echo "<div class='container mt-5 pt-5'><div class='alert alert-danger'>Event not found or pending approval.</div></div>";
    exit;
}

// Ensure timezone is set for display
date_default_timezone_set('Asia/Kolkata');
$event_date_formatted = date('l, F j, Y \a\t g:i A \I\S\T', strtotime($event['event_date']));

// Fetch related events (same type)
$related_stmt = $pdo->prepare("SELECT * FROM events WHERE event_type = ? AND id != ? AND is_approved = 1 LIMIT 3");
$related_stmt->execute([$event['event_type'], $id]);
$related_events = $related_stmt->fetchAll();

include 'includes/header.php';
?>

<div class="container mt-5 pt-5 mb-5">
    <div class="row">
        <!-- Main Event Details -->
        <div class="col-lg-8 mb-4">
            <div class="card bg-surface border-0 shadow-lg">
                <img src="<?php echo BASE_URL . htmlspecialchars($event['image_path']); ?>" class="card-img-top rounded-top" alt="<?php echo htmlspecialchars($event['title']); ?>" style="max-height: 400px; object-fit: cover;">
                <div class="card-body p-5">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="badge badge-<?php echo htmlspecialchars($event['event_type']); ?> fs-6 p-2 text-uppercase"><?php echo htmlspecialchars(str_replace('_', ' ', $event['event_type'])); ?></span>
                        <div class="d-flex align-items-center">
                            <span class="text-accent me-2"><i class="bi bi-heart-fill"></i> <span id="interest-count"><?php echo $event['interest_count']; ?></span> interested</span>
                            <button id="interested-btn" class="btn btn-outline-info btn-sm" data-id="<?php echo $event['id']; ?>">Mark Interested</button>
                        </div>
                    </div>
                    
                    <h1 class="card-title text-accent mb-4 fw-bold"><?php echo htmlspecialchars($event['title']); ?></h1>
                    
                    <div class="row mb-4">
                        <div class="col-md-6 text-muted">
                            <p class="mb-2"><i class="bi bi-calendar-event me-2"></i> <?php echo $event_date_formatted; ?></p>
                            <p class="mb-0"><i class="bi bi-geo-alt me-2"></i> <?php echo htmlspecialchars($event['location']); ?></p>
                        </div>
                    </div>

                    <h5 class="text-white border-bottom border-secondary pb-2 mb-3">About the Event</h5>
                    <div class="card-text text-light lh-lg" style="white-space: pre-wrap;">
                        <?php echo htmlspecialchars($event['description']); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar: Related Events -->
        <div class="col-lg-4">
            <h4 class="text-accent mb-4 decorative-heading">Related Events</h4>
            <div class="row">
                <?php foreach($related_events as $rel): ?>
                    <div class="col-12 mb-3">
                        <div class="card bg-surface border-secondary h-100">
                            <div class="row g-0">
                                <div class="col-4">
                                    <img src="<?php echo BASE_URL . htmlspecialchars($rel['image_path']); ?>" class="img-fluid rounded-start h-100" alt="Related" style="object-fit:cover;">
                                </div>
                                <div class="col-8">
                                    <div class="card-body py-2 pe-2">
                                        <h6 class="card-title mb-1 text-white text-truncate"><?php echo htmlspecialchars($rel['title']); ?></h6>
                                        <p class="card-text small text-muted mb-1"><?php echo date('M d, Y', strtotime($rel['event_date'])); ?></p>
                                        <a href="event-detail.php?id=<?php echo $rel['id']; ?>" class="text-info small text-decoration-none">View Details</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if(empty($related_events)): ?>
                    <p class="text-muted">No related events found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
