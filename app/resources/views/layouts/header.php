<?php
header("Content-Type: text/html");
global $seoMeta;
$title = $seoMeta['title'] ?? APP_NAME;
$description = $seoMeta['description'] ?? '';
$image = $seoMeta['image'] ?? '';
$url = BASE_URL . $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">

    <title><?php echo htmlspecialchars($title); ?></title>

    <!-- Basic Meta -->
    <meta name="description" content="<?php echo htmlspecialchars($description); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Open Graph -->
    <meta property="og:title" content="<?php echo htmlspecialchars($title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($description); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($image); ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($url); ?>">
    <meta property="og:type" content="website">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($title); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($description); ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($image); ?>">
    <link rel="icon" href="<?php get_assets('images/icon.jpg'); ?>" type="image/x-icon">

    <!-- style and scripts -->
    <link rel="stylesheet" href="<?php get_assets('css/style.css'); ?>">
    <script src="<?php get_assets('js/jquery.min.js'); ?>"></script>
</head>

<body>
    <main>
        <header class="site-header">
            <div class="logo-title">
                <a href="/">
                    <img src="<?php echo get_assets('images/icon.jpg'); ?>" alt="Logo" class="logo">
                    <h1 class="site-title">Fifi App</h1>
                </a>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="/">Home</a></li>
                    <li><a href="/about">About</a></li>
                    <li><a href="/contact">Contact</a></li>
                </ul>
            </nav>
        </header>