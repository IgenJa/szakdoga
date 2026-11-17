<?php

$pageTitle = 'Új elemzés';
$today = date('Y-m-d');

require __DIR__ . '/includes/header.php';

?>

<p class="demo-note">
    Demonstráció. A kiválasztott fájl nem kerül mentésre, és elemzés nem indul.
    A feldolgozás beállításai a
    <a href="processing.php">feldolgozási beállítások</a>
    oldalon adhatók meg.
</p>

<section class="card upload-card">
    <form id="upload-form" novalidate>
        <div class="form-field">
            <label for="title">A beszélgetés címe</label>
            <input type="text" id="title" name="title">
        </div>

        <div class="form-field">
            <label for="recorded-on">Dátum</label>
            <input type="date" id="recorded-on" name="recorded_on" value="<?php echo htmlspecialchars($today, ENT_QUOTES, 'UTF-8'); ?>">
        </div>

        <div class="form-field">
            <label for="description">Leírás <span class="optional">(nem kötelező)</span></label>
            <textarea id="description" name="description"></textarea>
        </div>

        <div class="form-field">
            <label for="audio-file">Hangfájl</label>
            <div class="drop-zone" id="drop-zone">
                <p>Húzd ide az MP3 vagy WAV fájlt, vagy válaszd ki tallózással.</p>
                <input type="file" id="audio-file" name="audio" accept=".mp3,.wav,audio/mpeg,audio/wav,audio/x-wav">
            </div>
            <p id="file-info" class="file-info" hidden></p>
        </div>

        <div id="form-errors" class="form-error" hidden></div>

        <div class="actions">
            <button class="button" type="submit">Feltöltés</button>
            <button class="button button-secondary" type="reset">Mezők törlése</button>
        </div>
    </form>
</section>

<section class="card upload-card" id="upload-result" hidden>
    <h2>Ellenőrzés kész</h2>
    <p>A bemutató sikeres. A fájl nem lett elmentve.</p>
    <p><strong>Cím:</strong> <span id="result-title"></span></p>
    <p><strong>Dátum:</strong> <span id="result-date"></span></p>
    <p><strong>Leírás:</strong> <span id="result-description"></span></p>
    <p><strong>Fájl:</strong> <span id="result-file"></span></p>
    <p><a href="processing.php">Tovább a feldolgozási beállításokhoz</a></p>
</section>

<script src="js/upload.js"></script>

<?php require __DIR__ . '/includes/footer.php'; ?>
