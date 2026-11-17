<?php

require __DIR__ . '/data/options.php';

$pageTitle = 'Feldolgozási beállítások';

$settings = [
    'model' => 'small',
    'language' => 'hu',
    'diarization' => true,
    'min_speakers' => 1,
    'max_speakers' => 4,
    'device' => 'cpu',
];

$errors = [];
$submitted = $_SERVER['REQUEST_METHOD'] === 'POST';

if ($submitted) {
    $settings['model'] = $_POST['model'] ?? '';
    $settings['language'] = $_POST['language'] ?? '';
    $settings['diarization'] = isset($_POST['diarization']);
    $settings['min_speakers'] = $_POST['min_speakers'] ?? '';
    $settings['max_speakers'] = $_POST['max_speakers'] ?? '';
    $settings['device'] = $_POST['device'] ?? '';

    if (!isset($models[$settings['model']])) {
        $errors[] = 'Válassz egy modellméretet.';
    }

    if (!isset($languages[$settings['language']])) {
        $errors[] = 'Válassz egy nyelvet.';
    }

    if (!isset($devices[$settings['device']])) {
        $errors[] = 'Válassz CPU vagy GPU feldolgozást.';
    }

    $minSpeakers = filter_var($settings['min_speakers'], FILTER_VALIDATE_INT);
    $maxSpeakers = filter_var($settings['max_speakers'], FILTER_VALIDATE_INT);

    if ($minSpeakers === false || $minSpeakers < 1) {
        $errors[] = 'A beszélők minimális száma legalább 1 legyen.';
    }

    if ($maxSpeakers === false || $maxSpeakers < 1) {
        $errors[] = 'A beszélők maximális száma legalább 1 legyen.';
    }

    if ($minSpeakers !== false && $maxSpeakers !== false && $maxSpeakers < $minSpeakers) {
        $errors[] = 'A maximális beszélőszám nem lehet kisebb a minimálisnál.';
    }

    if (count($errors) === 0) {
        $settings['min_speakers'] = $minSpeakers;
        $settings['max_speakers'] = $maxSpeakers;
    }
}

$showSummary = $submitted && count($errors) === 0;

require __DIR__ . '/includes/header.php';

?>

<p class="demo-note">
    Demonstrációs beállítások a későbbi WhisperX-feldolgozáshoz.
    Az értékek nem kerülnek mentésre, és feldolgozás nem indul.
</p>

<section class="card settings-card">
    <form method="post" action="processing.php" novalidate>
        <div class="form-field">
            <label for="model">Modellméret</label>
            <select id="model" name="model">
                <?php foreach ($models as $value => $label): ?>
                    <option value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $settings['model'] === $value ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <p class="field-help">A nagyobb modell pontosabban ismeri fel a beszédet, de több időt és memóriát kér.</p>
        </div>

        <div class="form-field">
            <label for="language">A hang nyelve</label>
            <select id="language" name="language">
                <?php foreach ($languages as $value => $label): ?>
                    <option value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $settings['language'] === $value ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <p class="field-help">Ha a nyelv ismert, a felismerés megbízhatóbb. Az automatikus felismerés lassabb lehet.</p>
        </div>

        <div class="form-field">
            <label class="check-label" for="diarization">
                <input type="checkbox" id="diarization" name="diarization" value="1" <?php echo $settings['diarization'] ? 'checked' : ''; ?>>
                Beszélők szétválasztása
            </label>
            <p class="field-help">Bekapcsolva a későbbi feldolgozás megpróbálja külön jelölni a beszélőket.</p>
        </div>

        <div class="form-field">
            <label for="min-speakers">Minimális beszélőszám</label>
            <input type="number" id="min-speakers" name="min_speakers" min="1" value="<?php echo htmlspecialchars((string) $settings['min_speakers'], ENT_QUOTES, 'UTF-8'); ?>">
        </div>

        <div class="form-field">
            <label for="max-speakers">Maximális beszélőszám</label>
            <input type="number" id="max-speakers" name="max_speakers" min="1" value="<?php echo htmlspecialchars((string) $settings['max_speakers'], ENT_QUOTES, 'UTF-8'); ?>">
            <p class="field-help">Ezek az értékek csak akkor számítanak, ha a beszélők szétválasztása be van kapcsolva.</p>
        </div>

        <fieldset class="form-field">
            <legend>Feldolgozás helye</legend>
            <div class="choice-list">
                <?php foreach ($devices as $value => $label): ?>
                    <label class="check-label">
                        <input type="radio" name="device" value="<?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $settings['device'] === $value ? 'checked' : ''; ?>>
                        <?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?>
                    </label>
                <?php endforeach; ?>
            </div>
            <p class="field-help">A CPU minden gépen használható. A GPU gyorsabb, ha van hozzá megfelelő eszköz.</p>
        </fieldset>

        <?php if (count($errors) > 0): ?>
            <div class="form-error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="actions">
            <button class="button" type="submit">Beállítások ellenőrzése</button>
        </div>
    </form>
</section>

<?php if ($showSummary): ?>
    <section class="card settings-card">
        <h2>Kiválasztott beállítások</h2>
        <p>A bemutató ellenőrzés sikeres. Ezek az értékek nincsenek elmentve.</p>
        <p><strong>Modell:</strong> <?php echo htmlspecialchars($models[$settings['model']], ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>Nyelv:</strong> <?php echo htmlspecialchars($languages[$settings['language']], ENT_QUOTES, 'UTF-8'); ?></p>
        <p><strong>Beszélők szétválasztása:</strong> <?php echo $settings['diarization'] ? 'Bekapcsolva' : 'Kikapcsolva'; ?></p>
        <p><strong>Beszélőszám:</strong> <?php echo (int) $settings['min_speakers']; ?>–<?php echo (int) $settings['max_speakers']; ?></p>
        <p><strong>Feldolgozás helye:</strong> <?php echo htmlspecialchars($devices[$settings['device']], ENT_QUOTES, 'UTF-8'); ?></p>
        <?php if (!$settings['diarization']): ?>
            <p>A beszélőszám most nem számít, mert a szétválasztás ki van kapcsolva.</p>
        <?php endif; ?>
        <?php
            $statusUrl = 'status.php?' . http_build_query([
                'title' => 'Szakmai interjú',
                'model' => $settings['model'],
                'language' => $settings['language'],
                'diarization' => $settings['diarization'] ? '1' : '0',
                'min_speakers' => $settings['min_speakers'],
                'max_speakers' => $settings['max_speakers'],
                'device' => $settings['device'],
            ]);
        ?>
        <p><a href="<?php echo htmlspecialchars($statusUrl, ENT_QUOTES, 'UTF-8'); ?>">Feldolgozás állapotának bemutatása</a></p>
    </section>
<?php endif; ?>

<p><a href="upload.php">Vissza az új elemzéshez</a></p>

<?php require __DIR__ . '/includes/footer.php'; ?>
