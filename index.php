<?php
session_start();
require "database/connect.php";

//Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: /login.php");
    exit();
}

// Ensure CSRF token is set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <?php
    $metaTitle = "Ask Me Anything | AMAA App";
    $metaDesc = "Send me anonymous messages and play fun games like AMAA, TBH, NHIE, and more. Let’s keep it real and fun!";
    include "partials/head.php";
    ?>
</head>

<body>
    <?php include "partials/navbar.php"; ?>

    <div id="cardsCarousel" class="carousel slide mb-5 text-center mx-3">
        <?php require "data/categories.php"; ?>
        <div class="carousel-inner">
            <?php foreach ($CATEGORIES as $slug => $item): ?>
                <div class="carousel-item <?= $slug === 'ama' ? 'active' : '' ?>" data-slug="<?= htmlspecialchars($slug) ?>">
                    <div class="card">
                        <!-- <img src="..." class="card-img-top" alt="..."> -->
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($item['title']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars($item['text']) ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#cardsCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#cardsCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>

    <div class="row px-5">
        <div class="col-sm-6 mb-3 mb-sm-0">
            <div class="card text-center">
                <div class="card-header">Step 1</div>
                <div class="card-body">
                    <h5 class="card-title">Copy your link</h5>
                    <p class="card-text" id="current-link"><?= "https://" . $_SERVER['HTTP_HOST'] . "/" . $_SESSION['username'] ?></p>
                    <button type="button" class="btn btn-outline-primary" onclick="copyLink()">Copy</button>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="card text-center">
                <div class="card-header">Step 2</div>
                <div class="card-body">
                    <h5 class="card-title">Share your link</h5>
                    <p class="card-text">Post on story</p>
                    <button type="button" class="btn btn-primary" onclick="shareLink()">Share</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        const user = "<?= $_SESSION['username'] ?>";
        const base = `${window.location.origin}/${user}`;
        const linkEl = document.getElementById("current-link");
        const slider = document.getElementById("cardsCarousel");

        const slug = () =>
            document.querySelector(".carousel-item.active")?.dataset.slug || "";

        const url = () => `${base}/${slug()}`;

        const updateLink = () => {
            linkEl.textContent = url();
        };

        const copyLink = () => {
            navigator.clipboard.writeText(url())
                .then(() => alert("Link copied!"))
                .catch(() => alert("Copy failed."));
        };

        const shareLink = () => {
            if (navigator.share) {
                navigator.share({
                    title: "Send me something!",
                    text: "Drop your message anonymously",
                    url: url()
                }).catch(() => alert("Sharing cancelled."));
            } else {
                alert("Sharing not supported.");
            }
        };

        slider.addEventListener("slid.bs.carousel", updateLink);
        updateLink();
    </script>

</body>

</html>