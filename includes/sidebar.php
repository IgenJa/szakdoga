<?php

$navItems = [
    'index.php' => 'Áttekintés',
    'upload.php' => 'Új elemzés',
    'conversations.php' => 'Beszélgetések',
    'statistics.php' => 'Statisztikák',
];

$currentScript = basename($_SERVER['SCRIPT_NAME'] ?? 'index.php');

?>
<aside class="sidebar">
    <a class="brand" href="index.php">Beszélgetéselemző</a>
    <nav class="nav" aria-label="Főmenü">
        <?php foreach ($navItems as $file => $label): ?>
            <?php $isActive = $currentScript === $file; ?>
            <a
                class="nav-link<?php echo $isActive ? ' is-active' : ''; ?>"
                href="<?php echo htmlspecialchars($file, ENT_QUOTES, 'UTF-8'); ?>"
                <?php echo $isActive ? 'aria-current="page"' : ''; ?>
            >
                <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
            </a>
        <?php endforeach; ?>
    </nav>
</aside>
