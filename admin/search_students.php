<?php
require_once __DIR__ . '/../includes/functions.php';
$pdo = getPDO();

$q = trim($_GET['q'] ?? '');

if ($q == '') {
    $stmt = $pdo->query("SELECT * FROM students ORDER BY updated_at DESC LIMIT 200");
    $rows = $stmt->fetchAll();
} else {
    $sql = "SELECT * FROM students 
            WHERE 
                name LIKE ? OR 
                phone LIKE ? OR
                email LIKE ? OR
                batch LIKE ? OR
                faculty_name LIKE ? OR
                student_code LIKE ?
            ORDER BY updated_at DESC LIMIT 200";

    $like = '%' . $q . '%';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$like,$like,$like,$like,$like,$like]);

    $rows = $stmt->fetchAll();
}

foreach ($rows as $r):
?>

<tr>
    <td><?= htmlspecialchars($r['student_code']) ?></td>
    <td><?= htmlspecialchars($r['name']) ?></td>
    <td><?= htmlspecialchars($r['phone']) ?></td>
    <td><?= htmlspecialchars($r['email']) ?></td>
    <td><?= htmlspecialchars($r['batch']) ?></td>
    <td><?= htmlspecialchars($r['faculty_name']) ?></td>
    <td><?= htmlspecialchars($r['rm']) ?></td>

    <td>
       <?php
$status = trim($r['status']);

if ($status === "Prep On") {
    echo '<span class="status-badge status-prep">Prep On</span>';
}
elseif ($status === "Date Booked") {
    echo '<span class="status-badge status-booked">Date Booked</span>';
}
elseif ($status === "Plan Drop") {
    echo '<span class="status-badge status-hold">Plan Drop</span>';
}
elseif ($status === "Extended") {
    echo '<span class="status-badge status-proc">Extended</span>';
}
else {
    echo '<span class="status-badge">'.$status.'</span>';
}
?>

    </td>

    <td><?= htmlspecialchars($r['updated_at']) ?></td>

    <td>
        <a href="view_student.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-primary">View</a>
        <a href="edit_student.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-info">Edit</a>
        <a href="delete_student.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-danger"
           onclick="return confirm('Delete student?');">Delete</a>
    </td>
</tr>

<?php endforeach; ?>
