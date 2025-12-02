<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/header.php';

$pdo = getPDO();

// Read filters
$search = trim($_GET['q'] ?? '');
$batch  = trim($_GET['batch'] ?? '');
$rm     = trim($_GET['rm'] ?? '');
$fac    = trim($_GET['fac'] ?? '');

$sql = "SELECT * FROM students WHERE 1 ";
$params = [];

// Dynamic WHERE conditions
if ($search !== "") {
    $sql .= " AND (
            LOWER(name) LIKE ? OR 
            LOWER(phone) LIKE ? OR 
            LOWER(email) LIKE ? OR
            LOWER(student_code) LIKE ? OR
            LOWER(faculty_name) LIKE ? OR
            LOWER(rm) LIKE ?
        )";
    $like = '%' . strtolower($search) . '%';
    array_push($params, $like, $like, $like, $like, $like, $like);
}

if ($batch !== "") {
    $sql .= " AND batch = ?";
    $params[] = $batch;
}

if ($rm !== "") {
    $sql .= " AND rm = ?";
    $params[] = $rm;
}

if ($fac !== "") {
    $sql .= " AND faculty_name = ?";
    $params[] = $fac;
}

$sql .= " ORDER BY updated_at DESC LIMIT 300";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

// Fetch dropdown values
$batchList = $pdo->query("SELECT DISTINCT batch FROM students ORDER BY batch")->fetchAll();
$rmList    = $pdo->query("SELECT DISTINCT rm FROM students ORDER BY rm")->fetchAll();
$facList   = $pdo->query("SELECT DISTINCT faculty_name FROM students ORDER BY faculty_name")->fetchAll();
?>
<div class="container my-4">

<h3>Students (Synced from Google Sheet)</h3>
<a href="dashboard.php" class="btn btn-link mb-3">Back to Dashboard</a>

<!-- Filters -->
<form class="row g-2 mb-3" method="GET" id="filterForm">
    <div class="col-md-3">
        <input type="text" name="q" id="searchBox" value="<?php echo htmlspecialchars($search); ?>" class="form-control" placeholder="Search anything...">
    </div>

    <div class="col-md-2">
        <select name="batch" class="form-select" onchange="this.form.submit()">
            <option value="">All Batches</option>
            <?php foreach ($batchList as $b): ?>
                <option <?php if($batch==$b['batch']) echo "selected"; ?>>
                    <?php echo $b['batch']; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-2">
        <select name="rm" class="form-select" onchange="this.form.submit()">
            <option value="">All RM</option>
            <?php foreach ($rmList as $r): ?>
                <option <?php if($rm==$r['rm']) echo "selected"; ?>>
                    <?php echo $r['rm']; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-2">
        <select name="fac" class="form-select" onchange="this.form.submit()">
            <option value="">All Faculty</option>
            <?php foreach ($facList as $f): ?>
                <option <?php if($fac==$f['faculty_name']) echo "selected"; ?>>
                    <?php echo $f['faculty_name']; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-2">
        <a href="export_students.php?<?php echo http_build_query($_GET); ?>" class="btn btn-success w-100">Export CSV</a>
    </div>
</form>

<!-- Table -->
<div class="table-responsive">
    <table class="table table-striped table-bordered table-sm align-middle" id="studentTable">
        <thead class="table-dark">
            <tr>
                <th onclick="sortTable(0)">ID</th>
                <th onclick="sortTable(1)">Name</th>
                <th onclick="sortTable(2)">Phone</th>
                <th onclick="sortTable(3)">Email</th>
                <th onclick="sortTable(4)">Student Code</th>
                <th onclick="sortTable(5)">Batch</th>
                <th onclick="sortTable(6)">Faculty</th>
                <th onclick="sortTable(7)">RM</th>
                <th onclick="sortTable(8)">Status</th>
                <th onclick="sortTable(9)">Updated</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
            <tr>
                <td><?php echo $r['id']; ?></td>
                <td><?php echo htmlspecialchars($r['name']); ?></td>
                <td><?php echo htmlspecialchars($r['phone']); ?></td>
                <td><?php echo htmlspecialchars($r['email']); ?></td>
                <td><?php echo htmlspecialchars($r['student_code']); ?></td>
                <td><?php echo htmlspecialchars($r['batch']); ?></td>
                <td><?php echo htmlspecialchars($r['faculty_name']); ?></td>
                <td><?php echo htmlspecialchars($r['rm']); ?></td>
                <td><?php echo htmlspecialchars($r['status']); ?></td>
                <td><?php echo htmlspecialchars($r['updated_at']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

</div>

<script>
// Live dynamic search (AJAX-like without backend changes)
document.getElementById("searchBox").addEventListener("keyup", function(){
    clearTimeout(window.delayTimer);
    window.delayTimer = setTimeout(() => {
        document.getElementById("filterForm").submit();
    }, 400);
});

// Table sorting
function sortTable(colIndex) {
    let table = document.getElementById("studentTable");
    let rows = [...table.rows].slice(1);
    let asc = table.getAttribute("data-sort") !== "asc";

    rows.sort((a,b) => {
        let x = a.cells[colIndex].innerText.toLowerCase();
        let y = b.cells[colIndex].innerText.toLowerCase();
        return asc ? x.localeCompare(y) : y.localeCompare(x);
    });

    table.setAttribute("data-sort", asc ? "asc" : "desc");

    rows.forEach(r => table.tBodies[0].appendChild(r));
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
