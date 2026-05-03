<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $event_id = (int)($_POST['event_id'] ?? 0);

    if ($event_id > 0) {
        if ($action === 'approve') {
            $stmt = $pdo->prepare("UPDATE events SET is_approved = 1 WHERE id = ?");
            $stmt->execute([$event_id]);
            $_SESSION['flash_success'] = "Event ID $event_id approved.";
        } elseif ($action === 'delete') {
            $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
            $stmt->execute([$event_id]);
            $_SESSION['flash_success'] = "Event ID $event_id deleted.";
        } elseif ($action === 'edit') {
            $title = trim($_POST['title'] ?? '');
            $type = $_POST['event_type'] ?? '';
            $date = $_POST['event_date'] ?? '';
            $location = trim($_POST['location'] ?? '');
            
            $stmt = $pdo->prepare("UPDATE events SET title=?, event_type=?, event_date=?, location=? WHERE id=?");
            $stmt->execute([$title, $type, $date, $location, $event_id]);
            $_SESSION['flash_success'] = "Event ID $event_id updated.";
        }
    }
    header("Location: manage-events.php");
    exit;
}

$stmt = $pdo->query("SELECT e.*, u.username FROM events e LEFT JOIN users u ON e.submitted_by = u.id ORDER BY is_approved ASC, event_date ASC");
$events = $stmt->fetchAll();

include '../includes/header.php';
?>

<div class="container mt-5 pt-5 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom border-secondary pb-3">
        <h2 class="text-accent decorative-heading m-0">Manage Events</h2>
        <a href="dashboard.php" class="btn btn-outline-light btn-sm">Back to Dashboard</a>
    </div>

    <div class="table-responsive">
        <table class="table table-dark table-hover align-middle">
            <thead class="text-accent">
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Title & Type</th>
                    <th>Date & Location</th>
                    <th>Submitted By</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($events as $ev): ?>
                    <tr>
                        <td><?php echo $ev['id']; ?></td>
                        <td><img src="<?php echo BASE_URL . htmlspecialchars($ev['image_path']); ?>" alt="img" width="60" class="rounded"></td>
                        <td>
                            <strong><?php echo htmlspecialchars($ev['title']); ?></strong><br>
                            <span class="badge badge-<?php echo htmlspecialchars($ev['event_type']); ?>"><?php echo htmlspecialchars($ev['event_type']); ?></span>
                        </td>
                        <td>
                            <?php echo date('M d, Y H:i', strtotime($ev['event_date'])); ?><br>
                            <small class="text-muted"><?php echo htmlspecialchars($ev['location']); ?></small>
                        </td>
                        <td><?php echo htmlspecialchars($ev['username'] ?? 'System/Unknown'); ?></td>
                        <td>
                            <?php if($ev['is_approved']): ?>
                                <span class="badge bg-success">Approved</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <!-- Action Buttons -->
                            <div class="d-flex gap-2">
                                <?php if(!$ev['is_approved']): ?>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="action" value="approve">
                                    <input type="hidden" name="event_id" value="<?php echo $ev['id']; ?>">
                                    <button type="submit" class="btn btn-success btn-sm" title="Approve"><i class="bi bi-check-lg"></i></button>
                                </form>
                                <?php endif; ?>
                                
                                <button type="button" class="btn btn-primary btn-sm" onclick="toggleEdit(<?php echo $ev['id']; ?>)" title="Quick Edit"><i class="bi bi-pencil"></i></button>
                                
                                <form method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this event?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="event_id" value="<?php echo $ev['id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" title="Delete"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <!-- Hidden Edit Row for toggling via JS -->
                    <tr id="edit-row-<?php echo $ev['id']; ?>" style="display:none;" class="bg-secondary">
                        <td colspan="7">
                            <form method="post" class="row g-2 align-items-center p-2">
                                <input type="hidden" name="action" value="edit">
                                <input type="hidden" name="event_id" value="<?php echo $ev['id']; ?>">
                                
                                <div class="col-auto">
                                    <input type="text" name="title" class="form-control form-control-sm" value="<?php echo htmlspecialchars($ev['title']); ?>" required>
                                </div>
                                <div class="col-auto">
                                    <select name="event_type" class="form-select form-select-sm" required>
                                        <option value="launch" <?php echo $ev['event_type']=='launch'?'selected':'';?>>Launch</option>
                                        <option value="eclipse" <?php echo $ev['event_type']=='eclipse'?'selected':'';?>>Eclipse</option>
                                        <option value="meteor_shower" <?php echo $ev['event_type']=='meteor_shower'?'selected':'';?>>Meteor Shower</option>
                                        <option value="conjunction" <?php echo $ev['event_type']=='conjunction'?'selected':'';?>>Conjunction</option>
                                        <option value="iss_pass" <?php echo $ev['event_type']=='iss_pass'?'selected':'';?>>ISS Pass</option>
                                        <option value="other" <?php echo $ev['event_type']=='other'?'selected':'';?>>Other</option>
                                    </select>
                                </div>
                                <div class="col-auto">
                                    <input type="datetime-local" name="event_date" class="form-control form-control-sm" value="<?php echo date('Y-m-d\TH:i', strtotime($ev['event_date'])); ?>" required>
                                </div>
                                <div class="col-auto">
                                    <input type="text" name="location" class="form-control form-control-sm" value="<?php echo htmlspecialchars($ev['location']); ?>" required>
                                </div>
                                <div class="col-auto">
                                    <button type="submit" class="btn btn-success btn-sm">Save</button>
                                    <button type="button" class="btn btn-outline-light btn-sm" onclick="toggleEdit(<?php echo $ev['id']; ?>)">Cancel</button>
                                </div>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if(empty($events)): ?>
                    <tr><td colspan="7" class="text-center text-muted">No events found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function toggleEdit(id) {
    const row = document.getElementById('edit-row-' + id);
    if (row.style.display === 'none') {
        row.style.display = 'table-row';
    } else {
        row.style.display = 'none';
    }
}
</script>

<?php include '../includes/footer.php'; ?>
