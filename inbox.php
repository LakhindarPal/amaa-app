<?php
session_start();
require "database/connect.php";

// Redirect if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: /login.php");
    exit();
}

// CSRF token (optional here, but still good practice)
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$username = $_SESSION['username'];

// Fetch messages
$stmt = $conn->prepare("SELECT message, category, timestamp FROM inbox WHERE username = ? ORDER BY timestamp DESC");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$messages = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <?php
    $metaTitle = "Your Inbox | AMAA App";
    $metaDesc = "View all the anonymous messages you've received on AMAA. See what your friends really think!";
    include "partials/head.php";
    ?>
</head>

<body>
    <?php include "partials/navbar.php"; ?>

    <div class="container my-5">
        <h2 class="text-center mb-4">Your Inbox</h2>

        <?php if (empty($messages)): ?>
            <div class="alert alert-info text-center">No messages yet. Share your link to receive anonymous messages!</div>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-md-2 g-4">
                <?php require "data/categories.php"; ?>
                <?php foreach ($messages as $msg): ?>
                    <div class="col">
                        <div class="card shadow-sm border-light">
                            <div class="card-header">
                                <?= htmlspecialchars($CATEGORIES[$msg['category']]['title']) ?>
                            </div>
                            <div class="card-body">
                                <p class="card-text"><?= nl2br(htmlspecialchars($msg['message'])) ?></p>
                            </div>
                            <div class="card-footer text-muted small text-end">
                                <?= date('M d, Y H:i', strtotime($msg['timestamp'])) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>