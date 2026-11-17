<?php

require __DIR__ . '/data/mock.php';

$pageTitle = 'Beszélgetések';

$conversationList = $conversations;

usort($conversationList, function ($first, $second) {
    return strcmp($second['date'], $first['date']);
});

require __DIR__ . '/includes/header.php';

?>

<p class="demo-note">Demonstrációs lista. A beszélgetések nincsenek adatbázisban, törlés nincs.</p>

<section class="card">
    <?php if (count($conversationList) === 0): ?>
        <p>Még nincs beszélgetés.</p>
    <?php else: ?>
        <div class="list-filters">
            <div class="form-field">
                <label for="conversation-search">Keresés a címben</label>
                <input type="text" id="conversation-search" placeholder="Például: interjú">
            </div>
            <div class="form-field">
                <label for="status-filter">Állapot</label>
                <select id="status-filter">
                    <option value="">Mindegyik</option>
                    <option value="waiting">Várakozás</option>
                    <option value="processing">Feldolgozás</option>
                    <option value="completed">Befejezve</option>
                    <option value="failed">Sikertelen</option>
                </select>
            </div>
        </div>
        <p id="filter-summary"><?php echo count($conversationList); ?> beszélgetés látható</p>
        <p id="filter-empty" class="note" hidden>Nincs a keresésnek megfelelő beszélgetés.</p>
        <div class="table-wrap">
            <table id="conversation-table">
                <thead>
                    <tr>
                        <th>Cím</th>
                        <th>Dátum</th>
                        <th>Hossz</th>
                        <th>Beszélők</th>
                        <th>Állapot</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($conversationList as $conversation): ?>
                        <tr
                            data-title="<?php echo htmlspecialchars($conversation['title'], ENT_QUOTES, 'UTF-8'); ?>"
                            data-status="<?php echo htmlspecialchars($conversation['status'], ENT_QUOTES, 'UTF-8'); ?>"
                        >
                            <td><?php echo htmlspecialchars($conversation['title'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars(formatDate($conversation['date']), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars(formatDuration($conversation['duration']), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo (int) $conversation['speakers']; ?></td>
                            <td><?php echo htmlspecialchars(statusLabel($conversation['status']), ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <a class="button button-secondary" href="conversation.php?id=<?php echo (int) $conversation['id']; ?>">Részletek</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <script src="js/conversations.js"></script>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
