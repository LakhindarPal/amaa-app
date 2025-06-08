<?php
session_start();
require "database/connect.php";

$fullname_error = "";
$username_error = "";
$password_error = "";
$confirm_password_error = "";
$general_error = "";
$success = "";

// CSRF token generator
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $general_error = "Invalid CSRF token.";
    } else {
        $fullname = trim($_POST['fullname']);
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $cnf_password = $_POST['cnf-password'];
        $avatar = "";

        // Name validation
        if (!preg_match('/^[A-Za-z ]{4,20}$/', $fullname)) {
            $fullname_error = "Full name must be 4–20 characters long and contain only letters and spaces.";
        }

        // Username validation
        if (!preg_match('/^[a-zA-Z0-9]{4,10}$/', $username)) {
            $username_error = "Username must be 4–10 characters long and contain only letters and numbers (no spaces or special characters).";
        }

        // Password validation
        $passwordPattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,15}$/';
        if (!preg_match($passwordPattern, $password)) {
            $password_error = "Password must be 6-15 characters long, include uppercase, lowercase, numbers, and special characters (@$!%*?&), with no spaces or emojis.";
        }

        // Confirm password validation
        if ($password !== $cnf_password) {
            $confirm_password_error = "Passwords do not match.";
        }

        // Check if there are no errors before proceeding with user registration
        if (empty($username_error) && empty($fullname_error) && empty($password_error) && empty($confirm_password_error)) {
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $general_error = "Username already exists.";
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (username, fullname, password, avatar) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $username, $fullname, $hashed_password, $avatar);

                if ($stmt->execute()) {
                    session_regenerate_id(true);
                    unset($_SESSION['csrf_token']);
                    $_SESSION['username'] = $username;
                    header("Location: /");
                    exit;
                } else {
                    $general_error = "Error: " . $stmt->error;
                }
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <?php
    $metaTitle = "Sign Up for AMAA App";
    $metaDesc = "Create your free AMAA profile and start receiving anonymous messages and confessions from your friends.";
    include "partials/head.php";
    ?>
</head>

<body>
    <?php include "partials/navbar.php"; ?>

    <form action="/signup.php" method="POST" class="container p-4 rounded border bg-body-tertiary shadow">
        <h1 class="text-center">AMAA - Signup</h1>

        <?php if (!empty($general_error)): ?>
            <div class="my-3 alert alert-danger" role="alert"><?= htmlspecialchars($general_error) ?></div>
        <?php elseif (!empty($success)): ?>
            <div class="my-3 alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <div class="mb-3">
            <label for="fullname" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="fullname" name="fullname"
                value="<?= htmlspecialchars($_POST['fullname'] ?? '') ?>"
                minlength="4" maxlength="20" aria-describedby="fullnameHelp" required>
            <div id="fullnameHelp" class="form-text visually-hidden">
                Full name must be 4–20 characters long and contain only letters and spaces.
            </div>
            <?php if (!empty($fullname_error)): ?>
                <div class="mt-2 alert alert-danger" role="alert"><?= htmlspecialchars($fullname_error) ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <div class="input-group">
                <span class="input-group-text" id="usernamePrepend">@</span>
                <input type="text" class="form-control" id="username" name="username" minlength="4" maxlength="10"
                    value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                    aria-labelledby="usernamePrepend" aria-describedby="usernameHelp" required>
            </div>
            <div id="usernameHelp" class="form-text visually-hidden">
                Username must be 4–10 characters long and contain only letters and numbers. No spaces or special characters.
            </div>
            <?php if (!empty($username_error)): ?>
                <div class="mt-2 alert alert-danger" role="alert"><?= htmlspecialchars($username_error) ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" minlength="6" maxlength="15"
                autocomplete="off" aria-describedby="passwordHelp" required>
            <div id="passwordHelp" class="form-text visually-hidden">
                Password must be 6–15 characters long, include uppercase, lowercase, numbers, and special characters (@$!%*?&), with no spaces or emojis.
            </div>
            <?php if (!empty($password_error)): ?>
                <div class="mt-2 alert alert-danger" role="alert"><?= htmlspecialchars($password_error) ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="cnf-password" class="form-label">Confirm Password</label>
            <input type="password" class="form-control" id="cnf-password" name="cnf-password"
                autocomplete="off" aria-describedby="confirmPasswordHelp" required>
            <div id="confirmPasswordHelp" class="form-text visually-hidden">
                Both passwords must match.
            </div>
            <?php if (!empty($confirm_password_error)): ?>
                <div class="mt-2 alert alert-danger" role="alert"><?= htmlspecialchars($confirm_password_error) ?></div>
            <?php endif; ?>
        </div>

        <!-- CSRF token -->
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <div class="d-flex justify-content-center gap-5">
            <button type="submit" class="btn btn-primary">Submit</button>
            <button type="reset" class="btn btn-danger">Clear</button>
        </div>
    </form>
    <div class="text-center mt-4">Already have an account? <a href="/login.php">Login here</a></div>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const fields = {
                fullname: {
                    input: document.querySelector("#fullname"),
                    help: document.querySelector("#fullnameHelp"),
                    rules: [{
                            test: v => v.length >= 4 && v.length <= 20,
                            msg: "Full name must be 4–20 characters long."
                        },
                        {
                            test: v => /^[A-Za-z ]+$/.test(v),
                            msg: "Full name must contain only letters and spaces."
                        },
                    ],
                },
                username: {
                    input: document.querySelector("#username"),
                    help: document.querySelector("#usernameHelp"),
                    rules: [{
                            test: v => v.length >= 4 && v.length <= 10,
                            msg: "Username must be 4–10 characters long."
                        },
                        {
                            test: v => /^[a-zA-Z0-9]+$/.test(v),
                            msg: "Username must contain only letters and numbers."
                        },
                    ],
                },
                password: {
                    input: document.querySelector("#password"),
                    help: document.querySelector("#passwordHelp"),
                    rules: [{
                            test: v => v.length >= 6 && v.length <= 15,
                            msg: "Password must be 6–15 characters long."
                        },
                        {
                            test: v => /[A-Z]/.test(v),
                            msg: "Password must include at least one uppercase letter."
                        },
                        {
                            test: v => /[a-z]/.test(v),
                            msg: "Password must include at least one lowercase letter."
                        },
                        {
                            test: v => /\d/.test(v),
                            msg: "Password must include at least one number."
                        },
                        {
                            test: v => /[@$!%*?&]/.test(v),
                            msg: "Password must include at least one special character (@$!%*?&)."
                        },
                    ],
                },
                confirmPassword: {
                    input: document.querySelector("#cnf-password"),
                    help: document.querySelector("#confirmPasswordHelp"),
                },
            };

            // Store original help texts
            for (const key in fields) {
                fields[key].originalHelp = fields[key].help.textContent;
            }

            // Generic validator for fields with rules
            function validateField({
                input,
                help,
                rules,
                originalHelp
            }) {
                const val = input.value;
                for (const rule of rules) {
                    if (!rule.test(val)) {
                        help.textContent = rule.msg;
                        help.className = "text-danger";
                        input.classList.add("is-invalid");
                        return false;
                    }
                }
                help.textContent = originalHelp;
                help.className = "visually-hidden";
                input.classList.remove("is-invalid");
                return true;
            }

            // Confirm password validator
            function validateConfirmPassword() {
                const {
                    input,
                    help,
                    originalHelp
                } = fields.confirmPassword;
                const passwordVal = fields.password.input.value;
                if (input.value !== passwordVal) {
                    help.textContent = "Both passwords do not match.";
                    help.className = "text-danger";
                    input.classList.add("is-invalid");
                    return false;
                }
                help.textContent = originalHelp;
                help.className = "visually-hidden";
                input.classList.remove("is-invalid");
                return true;
            }

            // Attach event listeners
            ["fullname", "username", "password"].forEach(key => {
                fields[key].input.addEventListener("input", () => {
                    validateField(fields[key]);
                    if (fields.confirmPassword.input.value) validateConfirmPassword();
                });
            });

            fields.confirmPassword.input.addEventListener("input", validateConfirmPassword);

            // Stop submission if invalid
            document.querySelector("form").addEventListener("submit", function(e) {
                let valid = true;
                valid &= validateField(fields.fullname);
                valid &= validateField(fields.username);
                valid &= validateField(fields.password);
                valid &= validateConfirmPassword();

                if (!valid) e.preventDefault();
            });
        });
    </script>
</body>

</html>