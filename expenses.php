<?php
// expenses.php
require_once 'db.php';
require_once 'includes/auth.php';
requireAdmin();

$pageTitle = "Expenses Management";
$action = $_GET['action'] ?? 'list';
$msg = '';
$err = '';

// Handle Delete
if ($action === 'delete') {
    $id = $_GET['id'] ?? 0;
    if ($id) {
        $stmt = $pdo->prepare("DELETE FROM expenses WHERE id = ?");
        $stmt->execute([$id]);
        $msg = "Expense deleted successfully.";
    }
    $action = 'list';
}

// Handle Form Submission (Add/Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $title = trim($_POST['title'] ?? '');
    $amount = $_POST['amount'] ?? 0;
    $date = $_POST['date'] ?? date('Y-m-d');
    $description = trim($_POST['description'] ?? '');

    if (empty($title) || empty($amount) || empty($date)) {
        $err = "Title, Amount, and Date are required fields.";
        $action = $id ? 'edit' : 'add';
        $edit_data = $_POST;
    }
    else {
        if ($id) {
            // Update
            $stmt = $pdo->prepare("UPDATE expenses SET title=?, amount=?, date=?, description=? WHERE id=?");
            $stmt->execute([$title, $amount, $date, $description, $id]);
            $msg = "Expense updated successfully.";
        }
        else {
            // Insert
            $stmt = $pdo->prepare("INSERT INTO expenses (title, amount, date, description) VALUES (?, ?, ?, ?)");
            $stmt->execute([$title, $amount, $date, $description]);
            $msg = "Expense added successfully.";
        }
        $action = 'list';
    }
}

// Fetch single record for edit if not already set by error handler
if ($action === 'edit' && !isset($edit_data)) {
    $id = $_GET['id'] ?? 0;
    $stmt = $pdo->prepare("SELECT * FROM expenses WHERE id = ?");
    $stmt->execute([$id]);
    $edit_data = $stmt->fetch();
    if (!$edit_data)
        $action = 'list';
}

// Fetch all for list
if ($action === 'list') {
    $expenses = $pdo->query("SELECT * FROM expenses ORDER BY date DESC, id DESC")->fetchAll();
}
?>

<?php include 'includes/header.php';
include 'includes/sidebar.php'; ?>

<h2 class="page-title">Manage Expenses</h2>

<?php if ($msg): ?><div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div><?php
endif; ?>
<?php if ($err): ?><div class="alert alert-danger"><?php echo htmlspecialchars($err); ?></div><?php
endif; ?>

<?php if ($action === 'add' || $action === 'edit'): ?>
    <!-- Form View -->
    <div class="section-box">
        <h3 class="section-title"><?php echo $action === 'edit' ? 'Edit Expense' : 'Add New Expense'; ?></h3>
        <form action="expenses.php?action=<?php echo $action; ?>" method="POST">
            <?php if (!empty($edit_data['id'])): ?>
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($edit_data['id']); ?>">
            <?php
    endif; ?>

            <div class="grid-2">
                <div class="form-group">
                    <label>Expense Title *</label>
                    <input type="text" name="title" class="form-control" required value="<?php echo htmlspecialchars($edit_data['title'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Amount (Rs.) *</label>
                    <input type="number" step="0.01" name="amount" class="form-control" required value="<?php echo htmlspecialchars($edit_data['amount'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Date *</label>
                    <input type="date" name="date" class="form-control" required value="<?php echo htmlspecialchars($edit_data['date'] ?? date('Y-m-d')); ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($edit_data['description'] ?? ''); ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary"><i data-feather="save"></i> Save</button>
            <a href="expenses.php" class="btn" style="background:#e2e8f0; color:#334155;">Cancel</a>
        </form>
    </div>

<?php
else: ?>
    <!-- List View -->
    <div class="section-box">
        <div class="section-header">
            <input type="text" id="tableSearch" class="form-control" placeholder="Search expenses..." style="max-width:300px;">
            <a href="expenses.php?action=add" class="btn btn-danger"><i data-feather="plus"></i> Add Expense</a>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($expenses) > 0): ?>
                        <?php foreach ($expenses as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['date']); ?></td>
                                <td><strong><?php echo htmlspecialchars($row['title']); ?></strong></td>
                                <td><?php echo htmlspecialchars(substr($row['description'], 0, 50)); ?><?php echo strlen($row['description']) > 50 ? '...' : ''; ?></td>
                                <td style="color:var(--danger-color); font-weight:600;">Rs.<?php echo number_format($row['amount'], 2); ?></td>
                                <td>
                                    <div class="action-links">
                                        <a href="expenses.php?action=edit&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary" title="Edit"><i data-feather="edit-2" style="width:16px;height:16px;"></i></a>
                                        <a href="expenses.php?action=delete&id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger delete-btn" title="Delete"><i data-feather="trash-2" style="width:16px;height:16px;"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php
        endforeach; ?>
                    <?php
    else: ?>
                        <tr><td colspan="5" style="text-align:center;">No expenses found.</td></tr>
                    <?php
    endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php
endif; ?>

<?php include 'includes/footer.php'; ?>
