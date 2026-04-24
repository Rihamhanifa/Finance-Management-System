<?php
// viewer.php
require_once 'db.php';
require_once 'includes/auth.php';
requireLogin();

// Explicitly let viewer see this, and if admin lands here, that's fine too.
$pageTitle = "Public Transparency Dashboard";

// Calculate Totals
$stmt = $pdo->query("SELECT SUM(amount) as total FROM donations");
$total_donations = $stmt->fetch()['total'] ?? 0;

$stmt = $pdo->query("SELECT SUM(amount) as total FROM expenses");
$total_expenses = $stmt->fetch()['total'] ?? 0;

$balance = $total_donations - $total_expenses;

$stmt = $pdo->query("SELECT COUNT(*) as total FROM prize_sponsors");
$total_sponsors = $stmt->fetch()['total'];

?>

<?php include 'includes/header.php'; include 'includes/sidebar.php'; ?>

<h2 class="page-title">Carnival Finance Overview</h2>

<div class="dashboard-cards">
    <div class="card success-card">
        <div class="card-title">Total Donations</div>
        <div class="card-value">Rs.<?php echo number_format($total_donations, 2); ?></div>
    </div>
    <div class="card" style="border-left-color: var(--danger-color);">
        <div class="card-title">Total Expenses</div>
        <div class="card-value">Rs.<?php echo number_format($total_expenses, 2); ?></div>
    </div>
    <div class="card accent-card">
        <div class="card-title">Final Balance</div>
        <div class="card-value">Rs.<?php echo number_format($balance, 2); ?></div>
    </div>
    <div class="card" style="border-left-color: #8b5cf6;">
        <div class="card-title">Prize Sponsors</div>
        <div class="card-value"><?php echo $total_sponsors; ?></div>
    </div>
</div>

<div class="section-box" style="max-width: 600px; margin: 2rem auto;">
    <h3 class="section-title" style="text-align: center;">Donations vs Expenses Overview</h3>
    <div style="position: relative; height:300px; width:400px; margin: 0 auto; max-width:100%;">
        <canvas id="financeChart"></canvas>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('financeChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Total Donations', 'Total Expenses'],
                datasets: [{
                    data: [<?php echo (float)$total_donations; ?>, <?php echo (float)$total_expenses; ?>],
                    backgroundColor: [
                        '#22C55E', // success
                        '#EF4444'  // danger
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>
