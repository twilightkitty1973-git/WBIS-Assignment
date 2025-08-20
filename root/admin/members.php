<?php

// db config - active non-active user
session_start();
require_once '../includes/db.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: admin_login.php');
    exit();
}

try {

    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($page < 1) $page = 1;

    $itemLimit = 10;
    // skip a row
    $offset = ($page - 1) * $itemLimit;

    // find number of page
    $totalStmt = $pdo->query("SELECT COUNT(*) FROM user WHERE role = 'member'");
    $totalRows = $totalStmt->fetchColumn();
    $totalPages = ceil($totalRows / $itemLimit);

    // fetch row by page
    $stmt = $pdo->prepare("SELECT * FROM user WHERE role = 'member' LIMIT :itemLimit OFFSET :offset");
    $stmt->bindValue(':itemLimit', $itemLimit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $userData = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "No data: " . $e->getMessage();
}

include '../includes/header.php';
?>

<section class="admin-section membership">
    <div class="container" style="padding: 30px 20px 25px 20px;">
        <h2>Membership</h2>
        <div class="container2">
            <table class="rows">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Password</th>
                        <th>Role</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($userData as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['user_id']) ?></td>
                            <td><?= htmlspecialchars($item['username']) ?></td>
                            <td><?= htmlspecialchars($item['email']) ?></td>
                            <td><?= htmlspecialchars($item['password']) ?></td>
                            <td><?= htmlspecialchars($item['role']) ?></td>
                            <td><?= htmlspecialchars($item['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

                <!-- page button -->
                <div class="pagination" style="text-align:center; margin-top:20px;">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>" 
                            style="display:inline-block; padding:8px 12px; margin:0 5px; text-decoration:none; border:1px solid #ccc; border-radius:4px; color:#333;">
                            Prev
                        </a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a 
                            href="?page=<?= $i ?>" 
                            style="display:inline-block; padding:8px 12px; margin:0 5px; text-decoration:none; border:1px solid #ccc; border-radius:4px; color:#333; <?= $i === $page ? 'font-weight:bold; background-color:#ddd;' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <a 
                            href="?page=<?= $page + 1 ?>" 
                            style="display:inline-block; padding:8px 12px; margin:0 5px; text-decoration:none; border:1px solid #ccc; border-radius:4px; color:#333;">
                            Next
                        </a>
                    <?php endif; ?>
                </div>
        </div>
    </div>
</section>


