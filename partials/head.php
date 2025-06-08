<?php
// fallback title
$metaTitle = $metaTitle ?? 'AMAA App';
$metaDesc = $metaDesc ?? "";
$metaImage = "/assets/logo.jpg"
?>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($metaTitle) ?></title>
<meta name="description" content="<?= htmlspecialchars($metaDesc) ?>" />
<link rel="icon" href="/assets/favicon.jpg" sizes="32x32" type="image/jpeg">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website" />
<meta property="og:title" content="<?= htmlspecialchars($metaTitle) ?>" />
<meta property="og:description" content="<?= htmlspecialchars($metaDesc) ?>" />
<meta property="og:image" content="<?= htmlspecialchars($metaImage) ?>" />

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="<?= htmlspecialchars($metaTitle) ?>" />
<meta name="twitter:description" content="<?= htmlspecialchars($metaDesc) ?>" />
<meta name="twitter:image" content="<?= htmlspecialchars($metaImage) ?>" />

<!-- Bootstrap CDN -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>