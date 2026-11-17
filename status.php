<?php

require __DIR__ . '/data/options.php';

$pageTitle = 'Feldolgozás állapota';

$requestedTitle = trim($_GET['title'] ?? '');

if ($requestedTitle !== '' && strlen($requestedTitle) <= 200) {
    $conversationTitle = $requestedTitle;
} else {
    $conversationTitle = 'Szakmai interjú';
}

$model = $_GET['model'] ?? 'small';
$language = $_GET['language'] ?? 'hu';
$device = $_GET['device'] ?? 'cpu';

if (!isset($models[$model])) {
    $model = 'small';
}

if (!isset($languages[$language])) {
    $language = 'hu';
}

if (!isset($devices[$device])) {
    $device = 'cpu';
}

if (isset($_GET['diarization'])) {
    $diarization = $_GET['diarization'] === '1';
} else {
    $diarization = true;
}

$minSpeakers = filter_var($_GET['min_speakers'] ?? 1, FILTER_VALIDATE_INT);
$maxSpeakers = filter_var($_GET['max_speakers'] ?? 4, FILTER_VALIDATE_INT);

if ($minSpeakers === false || $minSpeakers < 1) {
    $minSpeakers = 1;
}

if ($maxSpeakers === false || $maxSpeakers < $minSpeakers) {
    $maxSpeakers = max($minSpeakers, 4);
}

require __DIR__ . '/includes/header.php';

?>

<p class="demo-note">
    Ez időzített bemutató. Hangfelismerés nem fut, a sáv csak a képernyőn halad.
</p>

<section class="card settings-card">
    <h2><?php echo htmlspecialchars($conversationTitle, ENT_QUOTES, 'UTF-8'); ?></h2>
    <p>Állapot: <strong id="status-label">Várakozás</strong></p>

    <div
        class="progress"
        id="progress"
        role="progressbar"
        aria-valuemin="0"
        aria-valuemax="100"
        aria-valuenow="0"
        aria-labelledby="status-label"
    >
        <div class="progress-bar" id="progress-bar"></div>
    </div>
    <p class="progress-value" id="progress-value">0%</p>
    <p id="status-message" class="status-message">A bemutató a sorban vár.</p>

    <div class="form-field">
        <label class="check-label" for="simulate-failure">
            <input type="checkbox" id="simulate-failure">
            Sikertelen kimenet szimulálása
        </label>
    </div>

    <div class="actions">
        <button class="button" type="button" id="start-demo">Bemutató indítása</button>
        <button class="button button-secondary" type="button" id="reset-demo">Visszaállítás</button>
    </div>
</section>

<section class="card settings-card">
    <h2>Kiválasztott beállítások</h2>
    <p><strong>Modell:</strong> <?php echo htmlspecialchars($models[$model], ENT_QUOTES, 'UTF-8'); ?></p>
    <p><strong>Nyelv:</strong> <?php echo htmlspecialchars($languages[$language], ENT_QUOTES, 'UTF-8'); ?></p>
    <p><strong>Beszélők szétválasztása:</strong> <?php echo $diarization ? 'Bekapcsolva' : 'Kikapcsolva'; ?></p>
    <p><strong>Beszélőszám:</strong> <?php echo (int) $minSpeakers; ?>–<?php echo (int) $maxSpeakers; ?></p>
    <p><strong>Feldolgozás helye:</strong> <?php echo htmlspecialchars($devices[$device], ENT_QUOTES, 'UTF-8'); ?></p>
    <?php if (!$diarization): ?>
        <p>A beszélőszám most nem számít, mert a szétválasztás ki van kapcsolva.</p>
    <?php endif; ?>
</section>

<p><a href="processing.php">Vissza a beállításokhoz</a></p>

<script src="js/status.js"></script>

<?php require __DIR__ . '/includes/footer.php'; ?>
