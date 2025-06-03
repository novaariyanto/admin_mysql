<?php
$dsn = 'mysql:host=cmbfvsh2n0004kwad8vrc64yy;port=3306;dbname=cmbfvsh2l0003adkwa1pi3ms8';
$username = 'cmbfvsh2k0001adkw6uycfpn9';
$password = 'nwAj3h6OROILtbYEJG6pvaAj';

try {
    $pdo = new PDO($dsn, $username, $password);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
$currentTable = $_GET['table'] ?? null;
$action = $_GET['action'] ?? null;
$id = $_GET['id'] ?? null;

// Handle create database
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_db'])) {
    $newDb = preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['new_db']);
    if ($newDb) {
        $pdo->exec("CREATE DATABASE `$newDb`");
        header("Location: ?success=create_db");
        exit;
    }
}

// Insert
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'insert') {
    $cols = array_keys($_POST);
    $sql = "INSERT INTO `$currentTable` (`" . implode('`,`', $cols) . "`) VALUES (:" . implode(',:', $cols) . ")";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($_POST);
    header("Location: ?table=$currentTable");
    exit;
}

// Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'edit') {
    $cols = array_keys($_POST);
    $set = implode(', ', array_map(fn($col) => "`$col` = :$col", $cols));
    $sql = "UPDATE `$currentTable` SET $set WHERE id = :id";
    $_POST['id'] = $id;
    $stmt = $pdo->prepare($sql);
    $stmt->execute($_POST);
    header("Location: ?table=$currentTable");
    exit;
}

// Delete
if ($action === 'delete' && $id) {
    $stmt = $pdo->prepare("DELETE FROM `$currentTable` WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: ?table=$currentTable");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>DB Manager Sederhana</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f0f0f0; }
        table { border-collapse: collapse; width: 100%; background: white; }
        th, td { border: 1px solid #ccc; padding: 8px; }
        th { background: #eee; }
        a, button { text-decoration: none; color: blue; }
    </style>
</head>
<body>
<h2>📦 Database Viewer</h2>

<form method="POST" style="margin-bottom: 20px;">
    <input type="text" name="new_db" placeholder="Nama Database Baru" required>
    <button type="submit" name="create_db">Buat Database</button>
</form>

<h3>Tabel</h3>
<?php foreach ($tables as $table): ?>
    <a href="?table=<?= $table ?>"><?= $table ?></a>
<?php endforeach; ?>

<?php if ($currentTable && in_array($currentTable, $tables)): ?>
    <h3>📄 Tabel: <?= $currentTable ?></h3>
    <a href="?table=<?= $currentTable ?>&action=insert">➕ Tambah Data</a>
    <br><br>
    <?php
    $rows = $pdo->query("SELECT * FROM `$currentTable` LIMIT 100")->fetchAll(PDO::FETCH_ASSOC);
    if ($rows):
    ?>
    <table>
        <thead>
        <tr>
            <?php foreach (array_keys($rows[0]) as $col): ?>
                <th><?= $col ?></th>
            <?php endforeach; ?>
            <th>Aksi</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $row): ?>
            <tr>
                <?php foreach ($row as $val): ?>
                    <td><?= htmlspecialchars((string)$val) ?></td>
                <?php endforeach; ?>
                <td>
                    <a href="?table=<?= $currentTable ?>&action=edit&id=<?= $row['id'] ?>">✏️</a>
                    <a href="?table=<?= $currentTable ?>&action=delete&id=<?= $row['id'] ?>" onclick="return confirm('Hapus data ini?')">🗑️</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p><em>Tidak ada data.</em></p>
    <?php endif; ?>
<?php endif; ?>

<?php if (in_array($action, ['insert', 'edit']) && $currentTable): ?>
    <?php
    $columns = $pdo->query("DESCRIBE `$currentTable`")->fetchAll(PDO::FETCH_COLUMN);
    $values = [];
    if ($action === 'edit' && $id) {
        $stmt = $pdo->prepare("SELECT * FROM `$currentTable` WHERE id = ?");
        $stmt->execute([$id]);
        $values = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    ?>
    <h3><?= $action === 'insert' ? 'Tambah' : 'Edit' ?> Data di <?= $currentTable ?></h3>
    <form method="POST">
        <?php foreach ($columns as $col): ?>
            <div>
                <label><?= $col ?>:</label><br>
                <input type="text" name="<?= $col ?>" value="<?= htmlspecialchars($values[$col] ?? '') ?>">
            </div>
        <?php endforeach; ?>
        <br>
        <button type="submit">Simpan</button>
        <a href="?table=<?= $currentTable ?>">Batal</a>
    </form>
<?php endif; ?>
</body>
</html>
