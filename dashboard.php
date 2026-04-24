<?php
// index.php
require_once 'db.php';
require_once 'includes/auth.php';
requireAdmin();

$pageTitle = "Dashboard";

// Calculate Totals
$stmt = $pdo->query("SELECT SUM(amount) as total FROM donations");
$total_donations = $stmt->fetch()['total'] ?? 0;

$stmt = $pdo->query("SELECT SUM(amount) as total FROM expenses");
$total_expenses = $stmt->fetch()['total'] ?? 0;

$balance = $total_donations - $total_expenses;

$stmt = $pdo->query("SELECT COUNT(*) as total FROM donations");
$total_donors = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM prize_sponsors");
$total_sponsors = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT SUM(prize_count) as total FROM prize_sponsors");
$total_prizes = $stmt->fetch()['total'] ?? 0;

// Fetch Recent 5
$recent_donations = $pdo->query("SELECT * FROM donations ORDER BY date DESC, id DESC LIMIT 5")->fetchAll();
$recent_expenses = $pdo->query("SELECT * FROM expenses ORDER BY date DESC, id DESC LIMIT 5")->fetchAll();
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<h2 class="page-title">Admin Dashboard</h2>

<div class="dashboard-cards">
    <div class="card success-card">
        <div class="card-title">Total Donations</div>
        <div class="card-value">Rs.<?php echo number_format($total_donations, 2); ?></div>
    </div>
    <div class="card card-danger" style="border-left-color: var(--danger-color);">
        <div class="card-title">Total Expenses</div>
        <div class="card-value">Rs.<?php echo number_format($total_expenses, 2); ?></div>
    </div>
    <div class="card accent-card">
        <div class="card-title">Final Balance</div>
        <div class="card-value">Rs.<?php echo number_format($balance, 2); ?></div>
    </div>
    <div class="card" style="border-left-color: #3b82f6;">
        <div class="card-title">Total Donors</div>
        <div class="card-value"><?php echo $total_donors; ?></div>
    </div>
    <div class="card" style="border-left-color: #8b5cf6;">
        <div class="card-title">Prize Sponsors</div>
        <div class="card-value"><?php echo $total_sponsors; ?></div>
    </div>
    <div class="card" style="border-left-color: #10b981;">
        <div class="card-title">Total Prizes Received</div>
        <div class="card-value"><?php echo $total_prizes; ?></div>
    </div>
</div>

<div class="grid-2">
    <!-- Recent Donations -->
    <div class="section-box">
        <div class="section-header">
            <h3 class="section-title">Recent Donations</h3>
            <a href="donations.php" class="btn btn-primary btn-sm">View All</a>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Name</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($recent_donations) > 0): ?>
                        <?php foreach ($recent_donations as $d): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($d['date']); ?></td>
                                <td><?php echo htmlspecialchars($d['name']); ?></td>
                                <td style="color:var(--success-color); font-weight:600;">Rs.<?php echo number_format($d['amount'], 2); ?></td>
                            </tr>
                        <?php
    endforeach; ?>
                    <?php
else: ?>
                        <tr><td colspan="3">No donations yet.</td></tr>
                    <?php
endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Expenses -->
    <div class="section-box">
        <div class="section-header">
            <h3 class="section-title">Recent Expenses</h3>
            <a href="expenses.php" class="btn btn-primary btn-sm">View All</a>
        </div>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Title</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($recent_expenses) > 0): ?>
                        <?php foreach ($recent_expenses as $e): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($e['date']); ?></td>
                                <td><?php echo htmlspecialchars($e['title']); ?></td>
                                <td style="color:var(--danger-color); font-weight:600;">Rs.<?php echo number_format($e['amount'], 2); ?></td>
                            </tr>
                        <?php
    endforeach; ?>
                    <?php
else: ?>
                        <tr><td colspan="3">No expenses yet.</td></tr>
                    <?php
endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
