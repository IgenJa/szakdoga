<?php

require __DIR__ . '/data/mock.php';

$requestedId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$conversation = null;

if ($requestedId !== false && $requestedId !== null) {
    foreach ($conversations as $item) {
        if ((int) $item['id'] === $requestedId) {
            $conversation = $item;
            break;
        }
    }
}

if ($conversation === null) {
    $pageTitle = 'Beszélgetés nem található';
} else {
    $pageTitle = $conversation['title'];
}

require __DIR__ . '/includes/header.php';

if ($conversation === null) {
    ?>
    <section class="card">
        <h2>A beszélgetés nem található</h2>
        <p>Nincs ilyen azonosítójú bemutató beszélgetés.</p>
        <p><a href="conversations.php">Vissza a beszélgetésekhez</a></p>
    </section>
    <?php
    require __DIR__ . '/includes/footer.php';
    return;
}

$speakerList = $conversation['speaker_list'] ?? [];
$segments = $conversation['segments'] ?? [];
$speakerNames = [];
$speakerColors = [];
$colorNumber = 1;

foreach ($speakerList as $speaker) {
    $speakerNames[$speaker['id']] = $speaker['name'];
    $speakerColors[$speaker['id']] = (($colorNumber - 1) % 3) + 1;
    $colorNumber++;
}

$audioRelative = $conversation['audio'] ?? '';
$audioAvailable = false;

if (is_string($audioRelative) && $audioRelative !== '' && !str_contains($audioRelative, '..')) {
    $audioAvailable = is_file(__DIR__ . '/' . $audioRelative);
}

?>

<p class="demo-note">Demonstrációs leirat. Nem valós beszédfelismerésből készült.</p>

<section class="card">
    <h2>Adatok</h2>
    <?php if (!empty($conversation['description'])): ?>
        <p><?php echo htmlspecialchars($conversation['description'], ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>
    <p><strong>Dátum:</strong> <?php echo htmlspecialchars(formatDate($conversation['date']), ENT_QUOTES, 'UTF-8'); ?></p>
    <p><strong>Hossz:</strong> <?php echo htmlspecialchars(formatDuration($conversation['duration']), ENT_QUOTES, 'UTF-8'); ?></p>
    <p><strong>Állapot:</strong> <?php echo htmlspecialchars(statusLabel($conversation['status']), ENT_QUOTES, 'UTF-8'); ?></p>
    <p><strong>Beszélők száma:</strong> <?php echo (int) $conversation['speakers']; ?></p>
</section>

<section class="card">
    <h2>Beszélők</h2>
    <?php if (count($speakerList) === 0): ?>
        <p>A beszélők listája még nem áll rendelkezésre.</p>
    <?php else: ?>
        <ul class="speaker-list">
            <?php foreach ($speakerList as $speaker): ?>
                <?php $colorClass = 'speaker-color-' . (int) $speakerColors[$speaker['id']]; ?>
                <li>
                    <span class="speaker-swatch <?php echo $colorClass; ?>" aria-hidden="true"></span>
                    <span class="<?php echo $colorClass; ?>"><?php echo htmlspecialchars($speaker['name'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <span class="speaker-role"><?php echo htmlspecialchars($speaker['role'], ENT_QUOTES, 'UTF-8'); ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>

<section class="card">
    <h2>Hang</h2>
    <?php if ($audioAvailable): ?>
        <audio id="conversation-audio" controls src="<?php echo htmlspecialchars($audioRelative, ENT_QUOTES, 'UTF-8'); ?>"></audio>
    <?php else: ?>
        <p>Nincs csatolt hangfájl, ezért a lejátszás nem elérhető. Az időbélyeg nem teker hangot.</p>
    <?php endif; ?>
</section>

<section class="card">
    <h2>Leirat</h2>
    <?php if (count($segments) === 0): ?>
        <p>Ehhez a beszélgetéshez még nincs leirat.</p>
    <?php else: ?>
        <div class="form-field">
            <label for="transcript-search">Keresés a leiratban</label>
            <input type="text" id="transcript-search" placeholder="Szó vagy beszélő neve">
        </div>
        <p id="search-empty" class="note" hidden>Nincs a keresésnek megfelelő sor.</p>
        <div id="transcript">
            <?php foreach ($segments as $segment): ?>
                <?php
                    $speakerId = $segment['speaker'];
                    $speakerName = $speakerNames[$speakerId] ?? $speakerId;
                    $colorClass = 'speaker-color-' . (int) ($speakerColors[$speakerId] ?? 1);
                    $start = (int) $segment['start'];
                ?>
                <article class="segment">
                    <?php if ($audioAvailable): ?>
                        <button type="button" class="timestamp-button" data-start="<?php echo $start; ?>">
                            <?php echo htmlspecialchars(formatTimestamp($start), ENT_QUOTES, 'UTF-8'); ?>
                        </button>
                    <?php else: ?>
                        <span class="timestamp"><?php echo htmlspecialchars(formatTimestamp($start), ENT_QUOTES, 'UTF-8'); ?></span>
                    <?php endif; ?>
                    <div class="segment-speaker <?php echo $colorClass; ?>"><?php echo htmlspecialchars($speakerName, ENT_QUOTES, 'UTF-8'); ?></div>
                    <p class="segment-text"><?php echo htmlspecialchars($segment['text'], ENT_QUOTES, 'UTF-8'); ?></p>
                </article>
            <?php endforeach; ?>
        </div>
        <script src="js/transcript.js"></script>
    <?php endif; ?>
</section>

<p class="page-links">
    <a href="conversations.php">Vissza a beszélgetésekhez</a>
    <a href="statistics.php?id=<?php echo (int) $conversation['id']; ?>">Statisztika</a>
</p>

<?php require __DIR__ . '/includes/footer.php'; ?>
