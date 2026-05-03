<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

$stmt = $pdo->query("SELECT * FROM gallery ORDER BY uploaded_at DESC");
$images = $stmt->fetchAll();

include 'includes/header.php';
?>

<div class="container mt-5 pt-5 mb-5">
    <h1 class="text-center text-accent decorative-heading mb-4">Cosmic Gallery</h1>

    <div class="text-center mb-5">
        <button class="btn btn-outline-info mx-1 mb-2" onclick="filterGallery('all')">All</button>
        <button class="btn btn-outline-info mx-1 mb-2" onclick="filterGallery('Planets')">Planets</button>
        <button class="btn btn-outline-info mx-1 mb-2" onclick="filterGallery('Rockets')">Rockets</button>
        <button class="btn btn-outline-info mx-1 mb-2" onclick="filterGallery('Nebulae')">Nebulae</button>
        <button class="btn btn-outline-info mx-1 mb-2" onclick="filterGallery('ISS')">ISS</button>
    </div>

    <!-- CSS Grid Area -->
    <div class="gallery-grid" id="gallery-container">
        <?php foreach ($images as $img): ?>
            <div class="gallery-item" data-category="<?php echo htmlspecialchars($img['category']); ?>">
                <a href="#lightbox-<?php echo $img['id']; ?>">
                    <img src="<?php echo BASE_URL . htmlspecialchars($img['image_path']); ?>" alt="<?php echo htmlspecialchars($img['title']); ?>" loading="lazy">
                </a>
            </div>

            <!-- Pure CSS Lightbox Target -->
            <div id="lightbox-<?php echo $img['id']; ?>" class="lightbox">
                <a href="#_" class="lightbox-close">&times;</a>
                <img src="<?php echo BASE_URL . htmlspecialchars($img['image_path']); ?>" alt="<?php echo htmlspecialchars($img['title']); ?>">
                <div class="position-absolute bottom-0 text-white p-3 text-center w-100 bg-dark bg-opacity-75">
                    <h5><?php echo htmlspecialchars($img['title']); ?></h5>
                    <span class="badge bg-info"><?php echo htmlspecialchars($img['category']); ?></span>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if(empty($images)): ?>
            <p class="text-center text-muted">No images found in the gallery.</p>
        <?php endif; ?>
    </div>
</div>

<script>
// Filter gallery items by category client-side
function filterGallery(category) {
    const items = document.querySelectorAll('.gallery-item');
    items.forEach(item => {
        if (category === 'all' || item.dataset.category === category) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}
</script>

<?php include 'includes/footer.php'; ?>
