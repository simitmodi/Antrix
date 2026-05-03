<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

requireLogin(); // Must be logged in to submit

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $event_type = $_POST['event_type'] ?? 'other';
    $event_date = $_POST['event_date'] ?? '';
    $location = trim($_POST['location'] ?? '');
    
    // Server-side validation matching JS
    if (!preg_match('/^[a-zA-Z0-9\s\-]{5,150}$/', $title)) {
        $error = "Title must be 5-150 chars and contain only letters, numbers, spaces, and hyphens.";
    } elseif (strtotime($event_date) <= time()) {
        $error = "Event date must be in the future.";
    } elseif (empty($_FILES['image']['name'])) {
        $error = "Image is required.";
    } else {
        // Handle File Upload
        $file = $_FILES['image'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        
        if ($file['size'] > 2 * 1024 * 1024) {
            $error = "Image size exceeds 2MB limit.";
        } elseif (!in_array($file['type'], $allowed_types) || $file['error'] !== UPLOAD_ERR_OK) {
            $error = "Invalid image file. Only JPG, PNG, or GIF allowed.";
        } else {
            // Processing valid file
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $new_filename = time() . '_' . preg_replace('/[^a-zA-Z0-9]/', '', basename($file['name'], ".$ext")) . '.' . $ext;
            $upload_dir = __DIR__ . '/uploads/events/';
            
            // Ensure directory exists
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $upload_path = $upload_dir . $new_filename;
            $db_path = 'uploads/events/' . $new_filename;
            
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                // Insert to DB as unapproved
                try {
                    $stmt = $pdo->prepare("INSERT INTO events (title, description, event_type, event_date, location, image_path, submitted_by, is_approved) VALUES (?, ?, ?, ?, ?, ?, ?, 0)");
                    $stmt->execute([
                        $title,
                        $description,
                        $event_type,
                        $event_date,
                        $location,
                        $db_path,
                        $_SESSION['user_id']
                    ]);
                    $_SESSION['flash_success'] = "Event submitted successfully! It is pending admin approval.";
                    header("Location: events.php");
                    exit;
                } catch (\PDOException $e) {
                    $error = "Database error: " . $e->getMessage();
                }
            } else {
                $error = "Failed to move uploaded file.";
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="container mt-5 pt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card p-4 bg-surface border-secondary shadow-lg">
                <h2 class="text-accent text-center mb-4">Submit a Space Event</h2>
                <p class="text-muted text-center mb-4">Contribute to the cosmic calendar! Your submission will be reviewed by our team.</p>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="post" action="submit-event.php" id="submit-event-form" enctype="multipart/form-data" onsubmit="return validateEventSubmit('submit-event-form');">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="title" class="form-label text-white">Event Title</label>
                            <input type="text" class="form-control" id="title" name="title" required value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
                            <div id="title-error" class="text-danger small mt-1"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="event_type" class="form-label text-white">Event Type</label>
                            <select class="form-select" id="event_type" name="event_type" required>
                                <option value="launch">Launch</option>
                                <option value="eclipse">Eclipse</option>
                                <option value="meteor_shower">Meteor Shower</option>
                                <option value="conjunction">Conjunction</option>
                                <option value="iss_pass">ISS Pass</option>
                                <option value="other" selected>Other</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="event_date" class="form-label text-white">Date and Time</label>
                            <input type="datetime-local" class="form-control" id="event_date" name="event_date" required value="<?php echo htmlspecialchars($_POST['event_date'] ?? ''); ?>">
                            <div id="date-error" class="text-danger small mt-1"></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="location" class="form-label text-white">Location / Viewpoint</label>
                            <input type="text" class="form-control" id="location" name="location" required value="<?php echo htmlspecialchars($_POST['location'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label text-white">Event Description</label>
                        <textarea class="form-control" id="description" name="description" rows="4" required><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label for="image" class="form-label text-white">Upload Event Image</label>
                        <input class="form-control text-muted" type="file" id="image" name="image" accept="image/jpeg, image/png, image/gif" required>
                        <div class="form-text text-muted">Max size: 2MB. Formats: JPG, PNG, GIF.</div>
                        <div id="image-error" class="text-danger small mt-1"></div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-info btn-lg text-dark fw-bold">Submit Event</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
