<?php
// includes/sidebar.php
$currentPage = basename($_SERVER['PHP_SELF']);
$role = $_SESSION['role'] ?? 'viewer';
?>
<!-- Sidebar -->
<div class="sidebar-overlay" id="sidebar-overlay"></div>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        Megastar Sports Club 
    </div>
    <ul class="sidebar-menu">
        <?php if ($role === 'admin'): ?>
            <li class="<?php echo($currentPage == 'dashboard.php') ? 'active' : ''; ?>">
                <a href="dashboard.php"><i data-feather="grid"></i> Dashboard</a>
            </li>
            <li class="<?php echo($currentPage == 'donations.php') ? 'active' : ''; ?>">
                <a href="donations.php"><i data-feather="dollar-sign"></i> Donations</a>
            </li>
            <li class="<?php echo($currentPage == 'expenses.php') ? 'active' : ''; ?>">
                <a href="expenses.php"><i data-feather="shopping-cart"></i> Expenses</a>
            </li>
            <li class="<?php echo($currentPage == 'prize_sponsors.php') ? 'active' : ''; ?>">
                <a href="prize_sponsors.php"><i data-feather="gift"></i> Prize Sponsors</a>
            </li>
            <li class="<?php echo($currentPage == 'reports.php') ? 'active' : ''; ?>">
                <a href="reports.php"><i data-feather="file-text"></i> Reports</a>
            </li>
        <?php endif; ?>
        <li>
            <a href="logout.php"><i data-feather="log-out"></i> Logout</a>
        </li>
    </ul>
</aside>

<!-- Main Content Area -->
<div class="main-content">
    <!-- Top Header -->
    <header>
        <button class="menu-toggle" id="menu-toggle"><i data-feather="menu"></i></button>
        <div class="header-title" style="flex:1; margin-left:15px; font-weight: 600; color: var(--primary-color);">
            <?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Overview'; ?>
        </div>
        <div class="user-info">
            <button id="theme-toggle" class="btn btn-sm" style="background-color: var(--bg-color); color: var(--heading-color); border: 1px solid var(--border-color); display:flex; align-items:center; gap:5px; margin-right:10px;">
                <span id="theme-icon">🌙</span> <span id="theme-text" class="hide-on-mobile">Dark Mode</span>
            </button>
            <span><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></span>
        </div>
    </header>

    <div class="content-area">
