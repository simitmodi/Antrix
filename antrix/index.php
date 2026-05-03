<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

// Fetch next 3 upcoming events
$stmt = $pdo->query("SELECT * FROM events WHERE is_approved = 1 AND event_date > NOW() ORDER BY event_date ASC LIMIT 3");
$upcoming_events = $stmt->fetchAll();

// Fetch latest 3 news
$news_stmt = $pdo->query("SELECT * FROM news ORDER BY published_at DESC LIMIT 3");
$latest_news = $news_stmt->fetchAll();

// Fetch stats
$stat_events = $pdo->query("SELECT COUNT(*) FROM events WHERE is_approved = 1")->fetchColumn();
$stat_launches = $pdo->query("SELECT COUNT(*) FROM events WHERE event_type = 'launch' AND is_approved = 1")->fetchColumn();
$stat_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section text-center text-white">
    <div class="stars"></div>
    <div class="container position-relative" style="z-index: 2;">
        <h1 class="display-1 fw-bold mb-3 decorative-heading" style="text-shadow: 0 0 20px rgba(0, 229, 255, 0.5);">ANTRIX</h1>
        <p class="lead mb-5 text-uppercase letter-spacing-2">Explore the Universe One Event at a Time</p>
        
        <?php if (!empty($upcoming_events)): ?>
            <div class="mb-5">
                <p class="text-accent mb-2">Next Major Event Starts In:</p>
                <h2 id="countdown-timer" class="display-4 fw-mono text-white" style="text-shadow: 0 0 10px rgba(255,255,255,0.8);">Loading...</h2>
                <!-- We pass the first upcoming event's date to JS -->
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        startCountdown('<?php echo $upcoming_events[0]['event_date']; ?>');
                    });
                </script>
            </div>
            <a href="events.php" class="btn btn-outline-info btn-lg px-5 rounded-pill">View All Events</a>
        <?php else: ?>
            <a href="events.php" class="btn btn-outline-info btn-lg px-5 rounded-pill">Explore Past Events</a>
        <?php endif; ?>
    </div>
</section>

<!-- Stats Bar -->
<section class="bg-surface py-4 border-top border-bottom border-dark">
    <div class="container">
        <div class="row text-center text-accent">
            <div class="col-md-4 mb-3 mb-md-0">
                <h3 class="display-6 fw-bold"><?php echo $stat_events; ?></h3>
                <span class="text-muted text-uppercase small">Total Events</span>
            </div>
            <div class="col-md-4 mb-3 mb-md-0">
                <h3 class="display-6 fw-bold"><?php echo $stat_launches; ?></h3>
                <span class="text-muted text-uppercase small">Rocket Launches</span>
            </div>
            <div class="col-md-4">
                <h3 class="display-6 fw-bold"><?php echo $stat_users; ?></h3>
                <span class="text-muted text-uppercase small">Registered Explorers</span>
            </div>
        </div>
    </div>
</section>

<!-- Upcoming Events -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5 text-white decorative-heading">Upcoming Space Events</h2>
        <div class="row">
            <?php foreach($upcoming_events as $event): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 bg-surface">
                        <img src="<?php echo BASE_URL . htmlspecialchars($event['image_path']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($event['title']); ?>" style="height:200px; object-fit:cover;">
                        <div class="card-body">
                            <span class="badge badge-<?php echo htmlspecialchars($event['event_type']); ?> mb-2"><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $event['event_type']))); ?></span>
                            <h5 class="card-title"><?php echo htmlspecialchars($event['title']); ?></h5>
                            <p class="card-text text-muted small"><i class="bi bi-calendar"></i> <?php echo date('F j, Y, g:i a', strtotime($event['event_date'])); ?></p>
                            <a href="event-detail.php?id=<?php echo $event['id']; ?>" class="btn btn-outline-info btn-sm mt-3">Read More</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if(empty($upcoming_events)): ?>
                <p class="text-center text-muted">No upcoming events scheduled at the moment.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Latest News Strip -->
<section class="py-5 bg-surface border-top border-dark">
    <div class="container">
        <h2 class="text-center mb-5 text-white decorative-heading">Latest Orbit News</h2>
        <div class="row">
            <?php foreach($latest_news as $news): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 border-0 bg-transparent text-white">
                        <div class="row g-0">
                            <div class="col-4">
                                <img src="<?php echo BASE_URL . htmlspecialchars($news['image_path']); ?>" class="img-fluid rounded-start h-100" alt="News Image" style="object-fit:cover;">
                            </div>
                            <div class="col-8">
                                <div class="card-body py-1 pr-1">
                                    <h6 class="card-title text-accent mb-1"><?php echo htmlspecialchars($news['title']); ?></h6>
                                    <p class="card-text small text-muted mb-1"><?php echo htmlspecialchars(substr($news['content'], 0, 50)) . '...'; ?></p>
                                    <a href="<?php echo htmlspecialchars($news['source_url']); ?>" class="text-info small text-decoration-none" target="_blank">Read Full <i class="bi bi-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
