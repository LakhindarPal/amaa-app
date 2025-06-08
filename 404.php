<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <?php
    http_response_code(404);
    $metaTitle = "Page Not Found | AMAA App";
    $metaDesc = "Oops! The page you're looking for can't be found. Try exploring other parts of the AMAA App.";
    include "partials/head.php";
    ?>
</head>

<body>
    <div class="container text-center mt-5">
        <h1 class="display-4">404</h1>
        <p class="lead">Oops! That page doesn’t exist.</p>
        <a href="/" class="btn btn-primary">Go Home</a>
    </div>
</body>

</html>