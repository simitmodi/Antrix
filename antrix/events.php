<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

// Pagination setup
$limit = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Fetch total approved events for pagination
$total_stmt = $pdo->query("SELECT COUNT(*) FROM events WHERE is_approved = 1");
$total_records = $total_stmt->fetchColumn();
$total_pages = ceil($total_records / $limit);

// Fetch events for current page
$stmt = $pdo->prepare("SELECT * FROM events WHERE is_approved = 1 ORDER BY event_date ASC LIMIT ? OFFSET ?");
$stmt->bindValue(1, $limit, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$events = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="container mt-5 pt-5 mb-5">
    <h1 class="text-center text-accent decorative-heading mb-4">Space Events Directory</h1>

    <!-- Filters & Search -->
    <div class="row mb-4 bg-surface p-3 rounded align-items-center">
        <div class="col-md-8 mb-3 mb-md-0">
            <button class="btn btn-outline-info btn-sm me-2 mb-2" onclick="filterEvents('all')">All</button>
            <button class="btn btn-outline-info btn-sm me-2 mb-2 badge-launch" onclick="filterEvents('launch')">Launch</button>
            <button class="btn btn-outline-info btn-sm me-2 mb-2 badge-eclipse" onclick="filterEvents('eclipse')">Eclipse</button>
            <button class="btn btn-outline-info btn-sm me-2 mb-2 badge-meteor_shower" onclick="filterEvents('meteor_shower')">Meteor Shower</button>
            <button class="btn btn-outline-info btn-sm me-2 mb-2 badge-iss_pass" onclick="filterEvents('iss_pass')">ISS Pass</button>
        </div>
        <div class="col-md-4">
            <input type="text" id="event-search" class="form-control" placeholder="Search events...">
        </div>
    </div>

    <!-- Events Grid -->
    <div class="row event-container" id="events-grid">
        <?php foreach ($events as $event): ?>
            <div class="col-md-4 mb-4 event-card-item" data-type="<?php echo htmlspecialchars($event['event_type']); ?>">
                <div class="card h-100 bg-surface">
                    <img src="<?php echo BASE_URL . htmlspecialchars($event['image_path']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($event['title']); ?>" style="height:200px; object-fit:cover;">
                    <div class="card-body">
                        <span class="badge badge-<?php echo htmlspecialchars($event['event_type']); ?> mb-2" data-tooltip="<?php echo htmlspecialchars($event['event_type']); ?>"><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $event['event_type']))); ?></span>
                        <h5 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h5>
                        <p class="card-text text-muted small"><i class="bi bi-calendar"></i> <?php echo date('F j, Y, g:i a', strtotime($event['event_date'])); ?></p>
                        <p class="card-text text-muted small"><i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($event['location']); ?></p>
                        <a href="event-detail.php?id=<?php echo $event['id']; ?>" class="btn btn-outline-info btn-sm mt-2">View Details</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if(empty($events)): ?>
            <p class="text-center text-muted">No events found.</p>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <nav aria-label="Events pagination" class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link bg-surface text-accent border-secondary" href="?page=<?php echo $page - 1; ?>">Previous</a>
                </li>
                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                        <a class="page-link border-secondary <?php echo $page == $i ? 'bg-info border-info text-dark' : 'bg-surface text-accent'; ?>" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                    <a class="page-link bg-surface text-accent border-secondary" href="?page=<?php echo $page + 1; ?>">Next</a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
