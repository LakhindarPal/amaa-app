<?php
session_start();
require "database/connect.php";
require "data/categories.php";

$username = "";
$category = "";
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $username = trim($_GET['username'] ?? '');
    $category = trim($_GET['category']);
    if (!isset($CATEGORIES[$category])) $category = "ama";

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows < 1) {
        die("User does not exists.");
    }
}

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Invalid request.";
    } else {
        $username = trim($_POST['username'] ?? '');
        $category = trim($_POST['category']);
        $message = trim($_POST['message'] ?? '');

        // Basic empty check
        if (empty($message)) {
            $error = "Write a message first to send";
        } else {
            // insert into inbox
            $stmt = $conn->prepare("INSERT INTO inbox (username, message, category) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $message, $category);
            if ($stmt->execute()) {
                session_regenerate_id(true);
                unset($_SESSION['csrf_token']);
                header("Location: /success.php");
                exit;
            } else {
                $general_error = "Error: " . $stmt->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <?php
    $metaTitle = $CATEGORIES[$category]["text"] . " | AMAA App";
    $metaDesc = "Send an anonymous message or confession to your friend. No account required, just express yourself freely!";
    include "partials/head.php";
    ?>
</head>

<body>
    <form action="send.php" method="POST" class="container p-4 ">
        <div class="card">
            <div class="card-header">
                <img src="https://api.dicebear.com/9.x/bottts/svg?seed=guest" class="rounded-circle me-2" style="width: 40px;" alt="avatar">
                <?= "@$username"; ?>
            </div>
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($CATEGORIES["$category"]["text"]) ?></h5>
                <div class="mb-3 position-relative">
                    <label for="message" class="form-label visually-hidden">Write your message here:</label>
                    <textarea class="form-control" id="message" name="message" rows="7" maxlength="999" minlength="2" required></textarea>
                    <button type="button" id="dice-btn" class="btn position-absolute"
                        style="bottom: 10px; right: 10px; font-size: 1.5rem; line-height: 1; padding: 0.2rem 0.4rem;"
                        title="Load a sample question">🎲</button>
                </div>
                <div class="d-grid col-6 mx-auto">
                    <button type="submit" class="btn btn-primary">Send</button>
                </div>
            </div>
        </div>
        <input type="hidden" name="username" value="<?= htmlspecialchars($username) ?>">
        <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
    </form>

    <div class="text-center mt-5 mx-auto">
        <a href="/signup.php" class="btn btn-info link-dark link-underline h5">Get your own messages!</a>
    </div>

    <?php require "data/suggestions/$category.php"; ?>
    <script>
        const suggestions = <?= json_encode($SUGGESTIONS, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;

        document.getElementById('dice-btn').addEventListener('click', () => {
            if (suggestions.length === 0) return;
            const randomQuestion = suggestions[Math.floor(Math.random() * suggestions.length)];
            const textarea = document.getElementById('message');
            textarea.value = randomQuestion;
            textarea.focus();
        });
    </script>
</body>

</html>