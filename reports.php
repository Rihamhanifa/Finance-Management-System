<?php
// reports.php
require_once 'db.php';
require_once 'includes/auth.php';
requireAdmin();

$pageTitle = "Financial Reports";

$from_date = $_GET['from_date'] ?? date('Y-m-01'); // Default to 1st of current month
$to_date = $_GET['to_date'] ?? date('Y-m-t'); // Default to last day of current month
$area_filter = $_GET['area'] ?? '';
$sort_by = $_GET['sort_amount'] ?? '';

// Fetch distinct areas for dropdown
$stmt_areas = $pdo->query("
    SELECT DISTINCT area FROM (
        SELECT area FROM donations WHERE area IS NOT NULL AND area != ''
        UNION
        SELECT area FROM prize_sponsors WHERE area IS NOT NULL AND area != ''
    ) as combined_areas ORDER BY area ASC
");
$areas = $stmt_areas->fetchAll(PDO::FETCH_COLUMN);

// Fetch filtered totals
if ($area_filter !== '') {
    $stmt = $pdo->prepare("SELECT SUM(amount) as total FROM donations WHERE date BETWEEN ? AND ? AND area = ?");
    $stmt->execute([$from_date, $to_date, $area_filter]);
}
else {
    $stmt = $pdo->prepare("SELECT SUM(amount) as total FROM donations WHERE date BETWEEN ? AND ?");
    $stmt->execute([$from_date, $to_date]);
}
$total_donations = $stmt->fetch()['total'] ?? 0;

$stmt = $pdo->prepare("SELECT SUM(amount) as total FROM expenses WHERE date BETWEEN ? AND ?");
$stmt->execute([$from_date, $to_date]);
$total_expenses = $stmt->fetch()['total'] ?? 0;

$balance = $total_donations - $total_expenses;

// Fetch filtered lists
$order_clause = "ORDER BY date ASC";
if ($sort_by === 'asc') {
    $order_clause = "ORDER BY amount ASC, date ASC";
}
elseif ($sort_by === 'desc') {
    $order_clause = "ORDER BY amount DESC, date ASC";
}

if ($area_filter !== '') {
    $stmt = $pdo->prepare("SELECT * FROM donations WHERE date BETWEEN ? AND ? AND area = ? $order_clause");
    $stmt->execute([$from_date, $to_date, $area_filter]);
}
else {
    $stmt = $pdo->prepare("SELECT * FROM donations WHERE date BETWEEN ? AND ? $order_clause");
    $stmt->execute([$from_date, $to_date]);
}
$donations = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT * FROM expenses WHERE date BETWEEN ? AND ? ORDER BY date ASC");
$stmt->execute([$from_date, $to_date]);
$expenses = $stmt->fetchAll();

if ($area_filter !== '') {
    $stmt = $pdo->prepare("SELECT * FROM prize_sponsors WHERE date BETWEEN ? AND ? AND area = ? ORDER BY date ASC");
    $stmt->execute([$from_date, $to_date, $area_filter]);
}
else {
    $stmt = $pdo->prepare("SELECT * FROM prize_sponsors WHERE date BETWEEN ? AND ? ORDER BY date ASC");
    $stmt->execute([$from_date, $to_date]);
}
$sponsors = $stmt->fetchAll();

?>

<?php include 'includes/header.php';
include 'includes/sidebar.php'; ?>

<div class="no-print" style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 1.5rem; flex-wrap:wrap; gap:10px;">
    <h2 class="page-title" style="margin:0;">Financial Reports</h2>
    <div style="display:flex; gap:10px; flex-wrap:wrap;">
        <button onclick="window.print()" class="btn btn-accent"><i data-feather="printer"></i> <?php echo $area_filter ? 'Print Report' : 'Export PDF / Print'; ?></button>
        <button id="exportExcelBtn" class="btn btn-success"><i data-feather="download"></i> Export Excel</button>
    </div>
</div>

<!-- Filter Form -->
<div class="section-box no-print">
    <form method="GET" action="reports.php" style="display:flex; gap:15px; align-items:flex-end; flex-wrap:wrap;">
        <div>
            <label style="font-weight:500; font-size:0.9rem; color:var(--primary-color);">From Date</label>
            <input type="date" name="from_date" class="form-control" value="<?php echo htmlspecialchars($from_date); ?>" required>
        </div>
        <div>
            <label style="font-weight:500; font-size:0.9rem; color:var(--primary-color);">To Date</label>
            <input type="date" name="to_date" class="form-control" value="<?php echo htmlspecialchars($to_date); ?>" required>
        </div>
        <div>
            <label style="font-weight:500; font-size:0.9rem; color:var(--primary-color);">Area Filter</label>
            <select name="area" class="form-control" style="min-width: 150px;">
                <option value="">-- All Areas --</option>
                <?php foreach ($areas as $a): ?>
                    <option value="<?php echo htmlspecialchars($a); ?>" <?php echo $area_filter === $a ? 'selected' : ''; ?>><?php echo htmlspecialchars($a); ?></option>
                <?php
endforeach; ?>
            </select>
        </div>
        <div>
            <label style="font-weight:500; font-size:0.9rem; color:var(--primary-color);">Sort Donations By</label>
            <select name="sort_amount" class="form-control" style="min-width: 150px;">
                <option value="">Date (Default)</option>
                <option value="asc" <?php echo $sort_by === 'asc' ? 'selected' : ''; ?>>Payment Fee (Low to High)</option>
                <option value="desc" <?php echo $sort_by === 'desc' ? 'selected' : ''; ?>>Payment Fee (High to Low)</option>
            </select>
        </div>
        <div>
            <button type="submit" class="btn btn-primary"><i data-feather="filter"></i> Generate Report</button>
        </div>
    </form>
</div>

<!-- Print Title (Only visible when printing or in normal view on top of report body) -->
<?php if ($area_filter !== ''): ?>
    <h2 style="text-align:center; display:none;" class="print-title">Megastar Eid Carnival 2026 – Area Donation Report</h2>
    <h3 style="text-align:center; display:none; font-size:1.8rem; margin:10px 0; color:#333;" class="print-title"><?php echo htmlspecialchars($area_filter); ?> Report</h3>
    <p style="text-align:center; display:none;" class="print-title">Period: <?php echo date('d M Y', strtotime($from_date)); ?> to <?php echo date('d M Y', strtotime($to_date)); ?></p>
    <p style="text-align:center; display:none; font-weight:bold; font-size:1.3rem; margin-bottom: 20px; border-bottom: 2px solid #ccc; padding-bottom: 10px;" class="print-title">Total Donations from <?php echo htmlspecialchars($area_filter); ?>: Rs.<?php echo number_format($total_donations, 2); ?></p>
<?php
else: ?>
    <h2 style="text-align:center; display:none;" class="print-title">Megastar Eid Carnival 2026 – Financial Report</h2>
    <p style="text-align:center; display:none;" class="print-title">Period: <?php echo date('d M Y', strtotime($from_date)); ?> to <?php echo date('d M Y', strtotime($to_date)); ?></p>
<?php
endif; ?>

<style>
    @media print {
        @page { margin: 0; }
        body { margin: 1.5cm; }
        .print-title { display: block !important; margin-bottom: 10px; }
        .dashboard-cards { gap: 10px !important; margin-top:20px !important; }
        .card { padding: 10px !important; }
        .card-value { font-size: 1.5rem !important; }
        h3.section-title { font-size: 1.1rem; border-bottom: 1px solid #000; padding-bottom: 5px; margin-bottom: 10px; }
        <?php if ($area_filter !== ''): ?>
        .hide-on-area-print { display: none !important; }
        .dashboard-cards { display: none !important; } /* Hidden for Area Report */
        <?php
endif; ?>
    }
</style>

<!-- Summary Totals -->
<div class="dashboard-cards hide-on-area-print">
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
</div>

<!-- Donations Table -->
<div class="section-box">
    <h3 class="section-title">Donations List <?php echo $area_filter ? '- Area: ' . htmlspecialchars($area_filter) : ''; ?></h3>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th class="no-print">Date</th>
                    <th>Donor Name</th>
                    <th class="no-print">Area</th>
                    <th class="no-print">Country</th>
                    <th>Payment fees</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($donations) > 0): ?>
                    <?php foreach ($donations as $r): ?>
                        <tr>
                            <td class="no-print"><?php echo htmlspecialchars($r['date']); ?></td>
                            <td><?php echo htmlspecialchars($r['name']); ?></td>
                            <td class="no-print"><?php echo htmlspecialchars($r['area']); ?></td>
                            <td class="no-print"><?php echo htmlspecialchars($r['country']); ?></td>
                            <td style="color:var(--success-color); font-weight:600;">Rs.<?php echo number_format($r['amount'], 2); ?></td>
                        </tr>
                    <?php
    endforeach; ?>
                <?php
else: ?>
                    <tr><td colspan="5">No donations in this period.</td></tr>
                <?php
endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Expenses Table -->
<div class="section-box hide-on-area-print">
    <h3 class="section-title">Expenses List</h3>
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
                <?php if (count($expenses) > 0): ?>
                    <?php foreach ($expenses as $r): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($r['date']); ?></td>
                            <td><?php echo htmlspecialchars($r['title']); ?></td>
                            <td style="color:var(--danger-color); font-weight:600;">Rs.<?php echo number_format($r['amount'], 2); ?></td>
                        </tr>
                    <?php
    endforeach; ?>
                <?php
else: ?>
                    <tr><td colspan="3">No expenses in this period.</td></tr>
                <?php
endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Sponsors Table -->
<div class="section-box hide-on-area-print">
    <h3 class="section-title">Prize Sponsors List</h3>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th class="no-print">Date</th>
                    <th>Sponsor Name</th>
                    <th>Area</th>
                    <th>Prize Item</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($sponsors) > 0): ?>
                    <?php foreach ($sponsors as $r): ?>
                        <tr>
                            <td class="no-print"><?php echo htmlspecialchars($r['date']); ?></td>
                            <td><?php echo htmlspecialchars($r['name']); ?></td>
                            <td><?php echo htmlspecialchars($r['area']); ?></td>
                            <td><?php echo htmlspecialchars($r['prize_item'] ?: 'N/A'); ?></td>
                        </tr>
                    <?php
    endforeach; ?>
                <?php
else: ?>
                    <tr><td colspan="3">No prize sponsors in this period.</td></tr>
                <?php
endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
// Data arrays for Excel Export
$export_donations = array_map(function ($d) {
    return [
    'Donor Name' => $d['name'],
    'Area' => $d['area'],
    'Country' => $d['country'],
    'Phone' => $d['phone'],
    'Amount' => $d['amount'],
    'Date' => $d['date'],
    'Notes' => $d['notes']
    ];
}, $donations);

$export_expenses = array_map(function ($e) {
    return [
    'Expense Title' => $e['title'],
    'Amount' => $e['amount'],
    'Date' => $e['date'],
    'Description' => $e['description']
    ];
}, $expenses);

$export_sponsors = array_map(function ($s) {
    return [
    'Sponsor Name' => $s['name'],
    'Area' => $s['area'],
    'Prize Item' => $s['prize_item'],
    'Prize Count' => $s['prize_count'],
    'Phone' => $s['phone'],
    'Date' => $s['date'],
    'Notes' => $s['notes']
    ];
}, $sponsors);
?>

<!-- Include SheetJS for Excel export -->
<script src="https://cdn.sheetjs.com/xlsx-0.20.0/package/dist/xlsx.full.min.js"></script>
<script>
document.getElementById('exportExcelBtn').addEventListener('click', function() {
    // Check if SheetJS loaded
    if(typeof XLSX === 'undefined') {
        alert('Could not load Excel exporter library. Please check your internet connection.');
        return;
    }

    const wb = XLSX.utils.book_new();
    
    // Donations Sheet
    const donationsData = <?php echo json_encode($export_donations); ?>;
    const ws1 = XLSX.utils.json_to_sheet(donationsData);
    XLSX.utils.book_append_sheet(wb, ws1, "Donations");
    
    // Expenses Sheet
    const expensesData = <?php echo json_encode($export_expenses); ?>;
    const ws2 = XLSX.utils.json_to_sheet(expensesData);
    XLSX.utils.book_append_sheet(wb, ws2, "Expenses");
    
    // Sponsors Sheet
    const sponsorsData = <?php echo json_encode($export_sponsors); ?>;
    const ws3 = XLSX.utils.json_to_sheet(sponsorsData);
    XLSX.utils.book_append_sheet(wb, ws3, "Prize Sponsors");
    
    // Trigger download
    XLSX.writeFile(wb, "Megastar_Eid_Carnival_Finance_Report.xlsx");
});
</script>

<?php include 'includes/footer.php'; ?>
