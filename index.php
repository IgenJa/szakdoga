<?php

require __DIR__ . '/data/mock.php';

$pageTitle = 'Áttekintés';

$conversationCount = count($conversations);
$completedCount = 0;
$totalDuration = 0;

foreach ($conversations as $conversation) {
    $totalDuration += (int) $conversation['duration'];

    if ($conversation['status'] === 'completed') {
        $completedCount++;
    }
}

$recentConversations = $conversations;

usort($recentConversations, function ($first, $second) {
    return strcmp($second['date'], $first['date']);
});

$recentLimit = 5;
$recentConversations = array_slice($recentConversations, 0, $recentLimit);

require __DIR__ . '/includes/header.php';

?>

<p class="demo-note">Demonstrációs adatok. A számok nem valós feldolgozásból származnak.</p>

<section class="summary-grid">
    <article class="card summary-card">
        <p class="summary-label">Beszélgetések</p>
        <p class="summary-value"><?php echo $conversationCount; ?></p>
    </article>
    <article class="card summary-card">
        <p class="summary-label">Befejezett elemzések</p>
        <p class="summary-value"><?php echo $completedCount; ?></p>
    </article>
    <article class="card summary-card">
        <p class="summary-label">Összes hanghossz</p>
        <p class="summary-value"><?php echo htmlspecialchars(formatDuration($totalDuration), ENT_QUOTES, 'UTF-8'); ?></p>
    </article>
</section>

<section class="card">
    <div class="section-heading">
        <h2>Legutóbbi beszélgetések</h2>
        <a class="button" href="upload.php">Új elemzés</a>
    </div>

    <?php if (count($recentConversations) === 0): ?>
        <p>Még nincs beszélgetés.</p>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Cím</th>
                        <th>Dátum</th>
                        <th>Hossz</th>
                        <th>Beszélők</th>
                        <th>Állapot</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentConversations as $conversation): ?>
                        <tr>
                            <td><a href="conversation.php?id=<?php echo (int) $conversation['id']; ?>"><?php echo htmlspecialchars($conversation['title'], ENT_QUOTES, 'UTF-8'); ?></a></td>
                            <td><?php echo htmlspecialchars(formatDate($conversation['date']), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars(formatDuration($conversation['duration']), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo (int) $conversation['speakers']; ?></td>
                            <td><?php echo htmlspecialchars(statusLabel($conversation['status']), ENT_QUOTES, 'UTF-8'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p><a href="conversations.php">Összes beszélgetés</a></p>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
