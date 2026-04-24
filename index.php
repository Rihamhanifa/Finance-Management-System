<?php
// index.php
require_once 'db.php';
session_start();

$pageTitle = "Megastar Eid Carnival 2026 - Finance Summary";

// Calculate Totals
$stmt = $pdo->query("SELECT SUM(amount) as total FROM donations");
$total_donations = $stmt->fetch()['total'] ?? 0;

$stmt = $pdo->query("SELECT SUM(amount) as total FROM expenses");
$total_expenses = $stmt->fetch()['total'] ?? 0;

$balance = $total_donations - $total_expenses;

$stmt = $pdo->query("SELECT COUNT(*) as total FROM prize_sponsors");
$total_sponsors = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT SUM(prize_count) as total FROM prize_sponsors");
$total_prizes = $stmt->fetch()['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    <style>
        .public-wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .public-header {
            background-color: var(--white);
            height: var(--header-height);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 50;
        }
        .public-header-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        .public-header-title span {
            color: var(--accent-color);
        }
        .public-content {
            flex: 1;
            padding: 3rem 2rem;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }
        .public-dashboard-title {
            text-align: center;
            margin-bottom: 3rem;
            color: var(--primary-color);
            font-weight: 700;
            font-size: 2.2rem;
        }
        
        .finance-cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        
        /* Mobile adjustment for 2 columns */
        @media (max-width: 768px) {
            .finance-cards-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }
            .card {
                padding: 1.2rem;
            }
            .card-value {
                font-size: 1.5rem;
            }
            .public-dashboard-title {
                font-size: 1.5rem;
                margin-bottom: 2rem;
            }
            .public-header {
                padding: 0 1rem;
            }
            .public-header-title {
                font-size: 1.1rem;
            }
        }
        
        @media (max-width: 480px) {
            .finance-cards-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body class="bg-color">
    <div class="public-wrapper">
        <header class="public-header">
            <div class="public-header-title">Megastar <span>Carnival '26</span></div>
            <div>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <a href="dashboard.php" class="btn btn-primary">Admin Dashboard</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary">Admin Login</a>
                <?php endif; ?>
            </div>
        </header>

        <div class="public-content">
            <h1 class="public-dashboard-title">Finance Summary Dashboard</h1>

            <div class="finance-cards-grid">
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
                <div class="card" style="border-left-color: #8b5cf6;">
                    <div class="card-title">Prize Sponsors</div>
                    <div class="card-value"><?php echo $total_sponsors; ?></div>
                </div>
                <div class="card" style="border-left-color: #10b981;">
                    <div class="card-title">Total Prizes Received</div>
                    <div class="card-value"><?php echo $total_prizes; ?></div>
                </div>
            </div>

        </div>
    </div>
</body>
</html>
