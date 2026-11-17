<?php

if (!isset($pageTitle)) {
    $pageTitle = 'Beszélgetéselemző';
}

?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?> – Beszélgetéselemző</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="app">
        <?php require __DIR__ . '/sidebar.php'; ?>
        <div class="main">
            <header class="topbar">
                <h1><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></h1>
            </header>
            <main class="content">
