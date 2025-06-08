<nav class="navbar navbar-expand-lg bg-primary sticky-top mb-5">
    <div class="container-fluid">
        <a class="navbar-brand" href="/">
            <img src="/assets/logo.jpg" alt="Logo" height="32" class="d-inline-block align-text-top rounded-circle">
        </a>

        <?php if (isset($_SESSION['username'])): ?>
            <ul class="navbar-nav mx-auto flex-row gap-5">
                <li class="nav-item">
                    <a class="nav-link <?= !str_contains($_SERVER['REQUEST_URI'], 'inbox') ? 'active' : '' ?>" href="/">Play</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= str_contains($_SERVER['REQUEST_URI'], 'inbox') ? 'active' : '' ?>" href="/inbox.php">Inbox</a>
                </li>
            </ul>
        <?php endif; ?>

        <div class="dropdown ms-auto">
            <button class="btn  dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <img
                    src="https://api.dicebear.com/9.x/bottts/svg?seed=<?= urlencode($_SESSION['username'] ?? 'guest') ?>"
                    alt="Avatar"
                    height="32" width="32"
                    class="rounded-circle">
            </button>

            <ul class="dropdown-menu dropdown-menu-end">
                <?php if (isset($_SESSION['username'])): ?>
                    <li><a class="dropdown-item disabled">@<?= htmlspecialchars($_SESSION['username']) ?></a></li>
                    <li><a class="dropdown-item" href="/logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a class="dropdown-item" href="/login.php">Login</a></li>
                    <li><a class="dropdown-item" href="/signup.php">Signup</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>