<?php
// prize_sponsors.php
require_once 'db.php';
require_once 'includes/auth.php';
requireAdmin();

$pageTitle = "Prize Sponsors Management";
$action = $_GET['action'] ?? 'list';
$msg = '';
$err = '';

// Handle Delete
if ($action === 'delete') {
    $id = $_GET['id'] ?? 0;
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM prize_sponsors WHERE id = ?");
        $stmt->execute([$id]);
        $msg = "Prize sponsor deleted successfully.";
    }
    $action = 'list';
}

// Handle Form Submission (Add/Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $prize_item = trim($_POST['prize_item'] ?? '');
    $prize_count = (int)($_POST['prize_count'] ?? 1);
    $area = trim($_POST['area'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $date = $_POST['date'] ?? date('Y-m-d');
    $notes = trim($_POST['notes'] ?? '');

    if (empty($name) || empty($date)) {
        $err = "Name and Date are required fields.";
        $action = $id ? 'edit' : 'add';
        $edit_data = $_POST;
    } else {
        if ($id) {
            // Update
            $stmt = $pdo->prepare("UPDATE prize_sponsors SET name=?, area=?, prize_item=?, prize_count=?, phone=?, date=?, notes=? WHERE id=?");
            $stmt->execute([$name, $area, $prize_item, $prize_count, $phone, $date, $notes, $id]);
            $msg = "Prize sponsor updated successfully.";
        } else {
            // Insert
            $stmt = $pdo->prepare("INSERT INTO prize_sponsors (name, area, prize_item, prize_count, phone, date, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $area, $prize_item, $prize_count, $phone, $date, $notes]);
            $msg = "Prize sponsor added successfully.";
        }
        $action = 'list';
    }
}

// Fetch single record for edit if not already set by error handler
if ($action === 'edit' && !isset($edit_data)) {
    $id = $_GET['id'] ?? 0;
    $stmt = $pdo->prepare("SELECT * FROM prize_sponsors WHERE id = ?");
    $stmt->execute([$id]);
    $edit_data = $stmt->fetch();
    if (!$edit_data) $action = 'list';
}

// Fetch all for list
if ($action === 'list') {
    $sponsors = $pdo->query("SELECT * FROM prize_sponsors ORDER BY date DESC, id DESC")->fetchAll();
}
?>

<?php include 'includes/header.php'; include 'includes/sidebar.php'; ?>

<h2 class="page-title">Manage Prize Sponsors</h2>

<?php if ($msg): ?><div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-danger"><?php echo htmlspecialchars($err); ?></div><?php endif; ?>

<?php if ($action === 'add' || $action === 'edit'): ?>
    <!-- Form View -->
    <div class="section-box">
        <h3 class="section-title"><?php echo $action === 'edit' ? 'Edit Sponsor' : 'Add New Sponsor'; ?></h3>
        <form action="prize_sponsors.php?action=<?php echo $action; ?>" method="POST">
            <?php if (!empty($edit_data['id'])): ?>
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($edit_data['id']); ?>">
            <?php endif; ?>

            <div class="grid-2">
                <div class="form-group">
                    <label>Sponsor Name *</label>
                    <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($edit_data['name'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Prize Item</label>
                    <input type="text" name="prize_item" class="form-control" value="<?php echo htmlspecialchars($edit_data['prize_item'] ?? ''); ?>" placeholder="e.g. 50 T-Shirts">
                </div>
                <div class="form-group">
                    <label>Number of Prizes *</label>
                    <input type="number" name="prize_count" class="form-control" min="1" required value="<?php echo htmlspecialchars($edit_data['prize_count'] ?? '1'); ?>">
                </div>
                <div class="form-group">
                    <label>Date *</label>
                    <input type="date" name="date" class="form-control" required value="<?php echo htmlspecialchars($edit_data['date'] ?? date('Y-m-d')); ?>">
                </div>
                <div class="form-group">
                    <label>Area</label>
                    <input type="text" name="area" class="form-control" value="<?php echo htmlspecialchars($edit_data['area'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($edit_data['phone'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label>Notes</label>
                <textarea name="notes" class="form-control" rows="3"><?php echo htmlspecialchars($edit_data['notes'] ?? ''); ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary"><i data-feather="save"></i> Save</button>
            <a href="prize_sponsors.php" class="btn" style="background:#e2e8f0; color:#334155;">Cancel</a>
        </form>
    </div>

<?php else: ?>
    <!-- List View -->
    <div class="section-box">
        <div class="section-header">
            <input type="text" id="tableSearch" class="form-control" placeholder="Search sponsors..." style="max-width:300px;">
            <a href="prize_sponsors.php?action=add" class="btn btn-accent" style="background-color:#8b5cf6;"><i data-feather="plus"></i> Add Sponsor</a>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Sponsor Name</th>
                        <th>Area</th>
                        <th>Prize Item</th>
                        <th>Prize Count</th>
                        <th>Phone</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($sponsors) > 0): ?>
                        <?php foreach($sponsors as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['date']); ?></td>
                                <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($row['area']); ?></td>
                                <td><span style="background:#e0e7ff; color:#3730a3; padding:4px 8px; border-radius:4px; font-size:0.85rem;"><?php echo htmlspecialchars($row['prize_item'] ?: 'N/A'); ?></span></td>
                                <td><strong><?php echo htmlspecialchars($row['prize_count'] ?? '1'); ?></strong></td>
                                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                <td>
                                    <div class="action-links">
                                        <a href="prize_sponsors.php?action=edit&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary" title="Edit"><i data-feather="edit-2" style="width:16px;height:16px;"></i></a>
                                        <a href="prize_sponsors.php?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger delete-btn" title="Delete"><i data-feather="trash-2" style="width:16px;height:16px;"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" style="text-align:center;">No sponsors found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
