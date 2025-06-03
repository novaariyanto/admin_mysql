<?php
// Database credentials dari koneksi DSN
$dsn = 'mysql:host=cmbfvsh2n0004kwad8vrc64yy;port=3306;dbname=cmbfvsh2l0003adkwa1pi3ms8';
$username = 'cmbfvsh2k0001adkw6uycfpn9';
$password = 'nwAj3h6OROILtbYEJG6pvaAj';

try {
    $pdo = new PDO($dsn, $username, $password);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

// Tampilkan semua tabel
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
$currentTable = $_GET['table'] ?? null;

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Simple DB Viewer</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        h1 { font-size: 24px; }
        a { color: #007bff; text-decoration: none; margin-right: 10px; }
        table { border-collapse: collapse; width: 100%; margin-top: 10px; background: white; }
        th, td { border: 1px solid #ccc; padding: 8px; font-size: 14px; }
        th { background-color: #eee; }
        .container { max-width: 1000px; margin: auto; }
    </style>
</head>
<body>
<div class="container">
    <h1>📦 Simple DB Viewer</h1>
    <p><strong>Daftar Tabel:</strong></p>
    <?php foreach ($tables as $table): ?>
        <a href="?table=<?= htmlspecialchars($table) ?>"><?= htmlspecialchars($table) ?></a>
    <?php endforeach; ?>

    <?php if ($currentTable && in_array($currentTable, $tables)): ?>
        <h2>📄 Data dari tabel: <code><?= htmlspecialchars($currentTable) ?></code></h2>
        <?php
        $stmt = $pdo->prepare("SELECT * FROM `$currentTable` LIMIT 100");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($rows)):
        ?>
        <table>
            <thead>
                <tr>
                    <?php foreach (array_keys($rows[0]) as $col): ?>
                        <th><?= htmlspecialchars($col) ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <?php foreach ($row as $val): ?>
                            <td><?= htmlspecialchars((string)$val) ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p><em>Tidak ada data.</em></p>
        <?php endif; ?>
    <?php endif; ?>
</div>
</body>
</html>
