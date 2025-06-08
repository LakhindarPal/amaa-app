<?php
session_start();
require "database/connect.php";

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$login_error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $login_error = "Invalid request.";
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        // Basic empty check
        if ($username === '' || $password === '') {
            $login_error = "Please fill in all fields.";
        } else {
            // Fetch user by username
            $stmt = $conn->prepare("SELECT username, password FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            $user = $result->fetch_assoc();
            $stmt->close();

            if ($user && password_verify($password, $user['password'])) {
                // Success: Set session and redirect
                $_SESSION['username'] = $user['username'];
                session_regenerate_id(true);
                unset($_SESSION['csrf_token']);
                header("Location: /");
                exit;
            } else {
                $login_error = "Username or password is incorrect.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <?php
    $metaTitle = "Login to AMAA App";
    $metaDesc = "Log in to your AMAA account to read messages, explore categories, and manage your profile.";
    include "partials/head.php";
    ?>
</head>

<body>
    <?php include "partials/navbar.php"; ?>

    <form action="login.php" method="POST" class="container p-4 rounded border bg-body-tertiary shadow">
        <h1 class="text-center">AMAA - Login</h1>

        <?php if (!empty($login_error)): ?>
            <div class="my-3 alert alert-danger text-center"><?= htmlspecialchars($login_error) ?></div>
        <?php endif; ?>

        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" minlength="3" required>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>

        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <div class="text-center">
            <button type="submit" class="btn btn-primary">Login</button>
        </div>
    </form>

    <div class="text-center mt-4">
        Don't have an account? <a href="/signup.php">Signup now</a>
    </div>
</body>

</html>