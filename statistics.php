<?php

require __DIR__ . '/data/mock.php';
require __DIR__ . '/includes/stats.php';

$pageTitle = 'Statisztikák';
$bucketSeconds = 60;

$requestedId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$selected = null;
$firstWithTranscript = null;

foreach ($conversations as $conversation) {
    if ($firstWithTranscript === null && !empty($conversation['segments'])) {
        $firstWithTranscript = $conversation;
    }

    if ($requestedId !== false && $requestedId !== null && (int) $conversation['id'] === $requestedId) {
        $selected = $conversation;
    }
}

if ($selected === null) {
    $selected = $firstWithTranscript;
}

$statistics = null;

if ($selected !== null && !empty($selected['segments'])) {
    $statistics = calculateStatistics($selected, $bucketSeconds);
}

require __DIR__ . '/includes/header.php';

?>

<p class="demo-note">
    A számok a bemutató leiratból készülnek, nem valós feldolgozásból.
    Két megszólalás közötti szünet az előző beszélő idejébe számít.
    Az első megszólalás előtti idő csend. Átfedő beszéd nincs a bemutató adatokban.
</p>

<section class="card stats-picker">
    <form method="get" action="statistics.php" class="stats-form">
        <div class="form-field">
            <label for="conversation">Beszélgetés</label>
            <select id="conversation" name="id">
                <?php foreach ($conversations as $conversation): ?>
                    <option value="<?php echo (int) $conversation['id']; ?>" <?php echo $selected !== null && (int) $selected['id'] === (int) $conversation['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($conversation['title'], ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button class="button" type="submit">Megjelenítés</button>
    </form>
</section>

<?php if ($selected === null): ?>
    <section class="card">
        <p>Nincs megjeleníthető beszélgetés.</p>
    </section>
<?php elseif ($statistics === null): ?>
    <section class="card">
        <h2><?php echo htmlspecialchars($selected['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
        <p>Ehhez a beszélgetéshez nincs leirat, ezért nem készül statisztika.</p>
    </section>
<?php else: ?>
    <section class="summary-grid">
        <article class="card summary-card">
            <p class="summary-label">Teljes hossz</p>
            <p class="summary-value"><?php echo htmlspecialchars(formatDuration($statistics['duration']), ENT_QUOTES, 'UTF-8'); ?></p>
        </article>
        <article class="card summary-card">
            <p class="summary-label">Beszédidő</p>
            <p class="summary-value"><?php echo htmlspecialchars(formatDuration($statistics['speakingSeconds']), ENT_QUOTES, 'UTF-8'); ?></p>
        </article>
        <article class="card summary-card">
            <p class="summary-label">Csend az elején</p>
            <p class="summary-value"><?php echo htmlspecialchars(formatDuration($statistics['leadingSilence']), ENT_QUOTES, 'UTF-8'); ?></p>
        </article>
        <article class="card summary-card">
            <p class="summary-label">Megszólalások</p>
            <p class="summary-value"><?php echo (int) $statistics['turnCount']; ?></p>
        </article>
        <article class="card summary-card">
            <p class="summary-label">Beszélőváltások</p>
            <p class="summary-value"><?php echo (int) $statistics['transitions']; ?></p>
        </article>
        <article class="card summary-card">
            <p class="summary-label">Szavak</p>
            <p class="summary-value"><?php echo (int) $statistics['wordCount']; ?></p>
        </article>
    </section>

    <section class="card">
        <div class="section-heading">
            <h2><?php echo htmlspecialchars($selected['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
            <a href="conversation.php?id=<?php echo (int) $selected['id']; ?>">Leirat megnyitása</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Beszélő</th>
                        <th>Beszédidő</th>
                        <th>Megszólalások</th>
                        <th>Átlagos megszólalás</th>
                        <th>Szavak</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($statistics['speakers'] as $speaker): ?>
                        <?php
                            $averageSeconds = 0;
                            if ($speaker['turns'] > 0) {
                                $averageSeconds = (int) round($speaker['seconds'] / $speaker['turns']);
                            }
                        ?>
                        <tr>
                            <td class="<?php echo htmlspecialchars($speaker['colorClass'], ENT_QUOTES, 'UTF-8'); ?>">
                                <?php echo htmlspecialchars($speaker['name'], ENT_QUOTES, 'UTF-8'); ?>
                            </td>
                            <td><?php echo htmlspecialchars(formatDuration($speaker['seconds']), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo (int) $speaker['turns']; ?></td>
                            <td><?php echo htmlspecialchars(formatDuration($averageSeconds), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo (int) $speaker['words']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="card">
        <h2>Idővonal</h2>
        <div class="timeline">
            <?php foreach ($statistics['blocks'] as $block): ?>
                <?php
                    $width = 0;
                    if ($statistics['duration'] > 0) {
                        $width = ($block['seconds'] / $statistics['duration']) * 100;
                    }
                    $widthText = number_format($width, 2, '.', '');
                ?>
                <?php if ($block['type'] === 'silence'): ?>
                    <div
                        class="timeline-block timeline-silence"
                        style="width: <?php echo $widthText; ?>%"
                        title="Csend, <?php echo htmlspecialchars(formatTimestamp($block['start']), ENT_QUOTES, 'UTF-8'); ?>–<?php echo htmlspecialchars(formatTimestamp($block['end']), ENT_QUOTES, 'UTF-8'); ?>"
                    ></div>
                <?php else: ?>
                    <div
                        class="timeline-block <?php echo htmlspecialchars($block['colorClass'], ENT_QUOTES, 'UTF-8'); ?>"
                        style="width: <?php echo $widthText; ?>%"
                        title="<?php echo htmlspecialchars($block['name'] . ', ' . formatTimestamp($block['start']) . '–' . formatTimestamp($block['end']), ENT_QUOTES, 'UTF-8'); ?>"
                    ></div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <ul class="speaker-list timeline-legend">
            <li>
                <span class="speaker-swatch timeline-silence" aria-hidden="true"></span>
                <span>Csend az elején</span>
            </li>
            <?php foreach ($statistics['speakers'] as $speaker): ?>
                <li>
                    <span class="speaker-swatch <?php echo htmlspecialchars($speaker['colorClass'], ENT_QUOTES, 'UTF-8'); ?>" aria-hidden="true"></span>
                    <span class="<?php echo htmlspecialchars($speaker['colorClass'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($speaker['name'], ENT_QUOTES, 'UTF-8'); ?></span>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>

    <div class="chart-grid">
        <section class="card">
            <h2>Beszédidő beszélőnként</h2>
            <canvas id="speaking-time-chart"></canvas>
        </section>
        <section class="card">
            <h2>Beszédidő megoszlása</h2>
            <canvas id="distribution-chart"></canvas>
        </section>
        <section class="card">
            <h2>Megszólalások száma</h2>
            <canvas id="turns-chart"></canvas>
        </section>
        <section class="card">
            <h2>Gyakori szavak</h2>
            <canvas id="words-chart"></canvas>
        </section>
    </div>

    <section class="card">
        <h2>Beszéd a beszélgetés során</h2>
        <p>Minden oszlop <?php echo (int) $bucketSeconds; ?> másodperc. A csend nem szerepel az oszlopokban.</p>
        <canvas id="activity-chart"></canvas>
    </section>

    <?php
        $speakerNames = [];
        $speakerColors = [];
        $speakingSeconds = [];
        $turnCounts = [];
        $activitySeries = [];

        foreach ($statistics['speakers'] as $speaker) {
            $speakerNames[] = $speaker['name'];
            $speakerColors[] = $speaker['color'];
            $speakingSeconds[] = $speaker['seconds'];
            $turnCounts[] = $speaker['turns'];

            $series = [];
            foreach ($statistics['activity'] as $bucket) {
                $series[] = $bucket[$speaker['id']] ?? 0;
            }
            $activitySeries[] = $series;
        }

        $activityLabels = [];
        $bucketCount = count($statistics['activity']);

        for ($index = 0; $index < $bucketCount; $index++) {
            $fromMinute = intdiv($index * $bucketSeconds, 60);
            $toMinute = intdiv(($index + 1) * $bucketSeconds, 60);
            $activityLabels[] = $fromMinute . '–' . $toMinute . ' perc';
        }

        $chartData = [
            'speakerNames' => $speakerNames,
            'colors' => $speakerColors,
            'speakingSeconds' => $speakingSeconds,
            'turnCounts' => $turnCounts,
            'activityLabels' => $activityLabels,
            'activitySeries' => $activitySeries,
            'wordLabels' => array_keys($statistics['frequentWords']),
            'wordCounts' => array_values($statistics['frequentWords']),
        ];
    ?>
    <script id="chart-data" type="application/json"><?php echo json_encode($chartData, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG); ?></script>
    <script src="js/chart.umd.min.js"></script>
    <script src="js/charts.js"></script>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
