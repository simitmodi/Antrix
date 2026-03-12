<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

// Initial fetch
$stmt = $pdo->query("SELECT * FROM news ORDER BY published_at DESC");
$all_news = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="container mt-5 pt-5 mb-5">
    <h1 class="text-center text-accent decorative-heading mb-4">Space News</h1>

    <div class="row mb-4 justify-content-center">
        <div class="col-md-6">
            <input type="text" id="news-search" class="form-control" placeholder="Live search news topics...">
        </div>
    </div>

    <!-- Container for AJAX results and initial render -->
    <div class="row" id="news-container">
        <?php foreach ($all_news as $news): ?>
            <div class="col-md-6 col-lg-4 mb-4 news-item">
                <div class="card h-100 bg-surface border-secondary">
                    <img src="<?php echo BASE_URL . htmlspecialchars($news['image_path']); ?>" class="card-img-top" alt="News Image" style="height:200px; object-fit:cover;">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-white"><?php echo htmlspecialchars($news['title']); ?></h5>
                        <p class="card-text text-muted small"><i class="bi bi-clock"></i> <?php echo date('M d, Y', strtotime($news['published_at'])); ?></p>
                        <p class="card-text flex-grow-1 text-light">
                            <?php echo htmlspecialchars(substr($news['content'], 0, 100)) . '...'; ?>
                        </p>
                        <a href="<?php echo htmlspecialchars($news['source_url']); ?>" class="btn btn-outline-info mt-auto" target="_blank">Read Source</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if(empty($all_news)): ?>
            <p class="text-center text-muted w-100">No news articles found.</p>
        <?php endif; ?>
    </div>
</div>

<script>
// Callback for Feature 6 (defined in main.js jQuery ajax call)
function renderNews(data) {
    const container = document.getElementById('news-container');
    container.innerHTML = '';

    if (data.length === 0) {
        container.innerHTML = '<p class="text-center text-muted w-100">No news found matching your search.</p>';
        return;
    }

    data.forEach(item => {
        let excerpt = item.excerpt.length > 100 ? item.excerpt.substring(0, 100) + '...' : item.excerpt;
        const cardHtml = `
            <div class="col-md-6 col-lg-4 mb-4 news-item">
                <div class="card h-100 bg-surface border-secondary">
                    <img src="${BASE_URL}${item.image_path}" class="card-img-top" alt="News Image" style="height:200px; object-fit:cover;">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title text-white">${item.title}</h5>
                        <p class="card-text text-muted small"><i class="bi bi-clock"></i> ${item.date}</p>
                        <p class="card-text flex-grow-1 text-light">${excerpt}</p>
                        <a href="${item.source_url}" class="btn btn-outline-info mt-auto" target="_blank">Read Source</a>
                    </div>
                </div>
            </div>
        `;
        container.innerHTML += cardHtml;
    });
}
</script>

<?php include 'includes/footer.php'; ?>
