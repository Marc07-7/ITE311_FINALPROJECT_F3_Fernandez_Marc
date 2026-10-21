<?php
require_once __DIR__ . '/store.php';

// A nav link shows up automatically once its page file exists
$menu = [
    'index.php'    => 'Home',
    'wildlife.php' => 'Wildlife',
    'packages.php' => 'Tour Packages',
    'listings.php' => 'Local Businesses',
];
$here = basename($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($pageTitle ?? 'Naujan Lake Eco-Tourism') ?> | Naujan Lake Eco-Tourism</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="topbar">
    <div class="brand">Naujan Lake <span>Eco-Tourism</span></div>
    <nav>
        <?php foreach ($menu as $file => $label): ?>
            <?php if (file_exists(__DIR__ . '/../' . $file)): ?>
                <a href="<?= $file ?>" class="<?= $here === $file ? 'active' : '' ?>"><?= $label ?></a>
            <?php endif; ?>
        <?php endforeach; ?>
    </nav>
</header>
<main class="container">
