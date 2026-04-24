<?php
// donations.php
require_once 'db.php';
require_once 'includes/auth.php';
requireAdmin();

$pageTitle = "Donations Management";
$action = $_GET['action'] ?? 'list';
$msg = '';
$err = '';

// Handle Delete
if ($action === 'delete') {
    $id = $_GET['id'] ?? 0;
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM donations WHERE id = ?");
        $stmt->execute([$id]);
        $msg = "Donation deleted successfully.";
    }
    $action = 'list';
}

// Handle Form Submission (Add/Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $name = trim($_POST['name'] ?? '');
    $area = trim($_POST['area'] ?? '');
    $country = trim($_POST['country'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $amount = $_POST['amount'] ?? 0;
    $date = $_POST['date'] ?? date('Y-m-d');
    $notes = trim($_POST['notes'] ?? '');

    if (empty($name) || empty($amount) || empty($date)) {
        $err = "Name, Amount, and Date are required fields.";
    } else {
        if ($id) {
            // Update
            $stmt = $pdo->prepare("UPDATE donations SET name=?, area=?, country=?, phone=?, amount=?, date=?, notes=? WHERE id=?");
            $stmt->execute([$name, $area, $country, $phone, $amount, $date, $notes, $id]);
            $msg = "Donation updated successfully.";
        } else {
            // Insert
            $stmt = $pdo->prepare("INSERT INTO donations (name, area, country, phone, amount, date, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $area, $country, $phone, $amount, $date, $notes]);
            $msg = "Donation added successfully.";
        }
        $action = 'list';
    }
}

// Fetch single record for edit
$edit_data = null;
if ($action === 'edit') {
    $id = $_GET['id'] ?? 0;
    $stmt = $pdo->prepare("SELECT * FROM donations WHERE id = ?");
    $stmt->execute([$id]);
    $edit_data = $stmt->fetch();
    if (!$edit_data)
        $action = 'list';
}

// Fetch all for list
if ($action === 'list') {
    $donations = $pdo->query("SELECT * FROM donations ORDER BY date DESC, id DESC")->fetchAll();
}
?>

<?php include 'includes/header.php';
include 'includes/sidebar.php'; ?>

<h2 class="page-title">Manage Donations</h2>

<?php if ($msg): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div><?php
endif; ?>
<?php if ($err): ?>
    <div class="alert alert-danger"><?php echo htmlspecialchars($err); ?></div><?php
endif; ?>

<?php if ($action === 'add' || $action === 'edit'): ?>
    <!-- Form View -->
    <div class="section-box">
        <h3 class="section-title"><?php echo $action === 'edit' ? 'Edit Donation' : 'Add New Donation'; ?></h3>
        <form action="donations.php?action=<?php echo $action; ?>" method="POST">
            <?php if ($edit_data): ?>
                <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                <?php
            endif; ?>

            <div class="grid-2">
                <div class="form-group">
                    <label>Donor Name *</label>
                    <input type="text" name="name" class="form-control" required
                        value="<?php echo htmlspecialchars($edit_data['name'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Amount (Rs.) *</label>
                    <input type="number" step="0.01" name="amount" class="form-control" required
                        value="<?php echo htmlspecialchars($edit_data['amount'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Date *</label>
                    <input type="date" name="date" class="form-control" required
                        value="<?php echo htmlspecialchars($edit_data['date'] ?? date('Y-m-d')); ?>">
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" class="form-control"
                        value="<?php echo htmlspecialchars($edit_data['phone'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Area</label>
                    <input type="text" name="area" class="form-control"
                        value="<?php echo htmlspecialchars($edit_data['area'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Country</label>
                    <input type="text" name="country" class="form-control"
                        value="<?php echo htmlspecialchars($edit_data['country'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-group">
                <label>Notes</label>
                <textarea name="notes" class="form-control"
                    rows="3"><?php echo htmlspecialchars($edit_data['notes'] ?? ''); ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary"><i data-feather="save"></i> Save</button>
            <a href="donations.php" class="btn" style="background:#e2e8f0; color:#334155;">Cancel</a>
        </form>
    </div>

    <?php
else: ?>
    <!-- List View -->
    <div class="section-box">
        <div class="section-header">
            <input type="text" id="tableSearch" class="form-control" placeholder="Search donors..."
                style="max-width:300px;">
            <a href="donations.php?action=add" class="btn btn-success"><i data-feather="plus"></i> Add Donation</a>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Donor Name</th>
                        <th>Location</th>
                        <th>Amount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($donations) > 0): ?>
                        <?php foreach ($donations as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['date']); ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($row['name']); ?></strong><br>
                                    <small style="color:#64748b"><?php echo htmlspecialchars($row['phone']); ?></small>
                                </td>
                                <td>
                                    <?php
                                    $loc = array_filter([$row['area'], $row['country']]);
                                    echo htmlspecialchars(implode(', ', $loc));
                                    ?>
                                </td>
                                <td style="color:var(--success-color); font-weight:600;">
                                    Rs. <?php echo number_format($row['amount'], 2); ?></td>
                                <td>
                                    <div class="action-links">
                                        <a href="donations.php?action=edit&id=<?php echo $row['id']; ?>"
                                            class="btn btn-sm btn-primary" title="Edit"><i data-feather="edit-2"
                                                style="width:16px;height:16px;"></i></a>
                                        <a href="donations.php?action=delete&id=<?php echo $row['id']; ?>"
                                            class="btn btn-sm btn-danger delete-btn" title="Delete"><i data-feather="trash-2"
                                                style="width:16px;height:16px;"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <?php
                        endforeach; ?>
                        <?php
                    else: ?>
                        <tr>
                            <td colspan="5" style="text-align:center;">No donations found.</td>
                        </tr>
                        <?php
                    endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
endif; ?>

<?php include 'includes/footer.php'; ?>