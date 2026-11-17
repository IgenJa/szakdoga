<?php

// A színek egyezzenek a css/style.css --speaker-1, --speaker-2 és --speaker-3 értékeivel.

function speakerPalette()
{
    return ['#c4a882', '#8eae96', '#d08a6a'];
}

function splitWords($text)
{
    $text = mb_strtolower($text, 'UTF-8');
    $text = preg_replace('/[^\p{L}\p{N}]+/u', ' ', $text);
    $parts = preg_split('/\s+/u', trim((string) $text));
    $words = [];

    foreach ($parts as $part) {
        if ($part !== '') {
            $words[] = $part;
        }
    }

    return $words;
}

function frequentWords($wordCounts, $limit)
{
    $ignored = [
        'a', 'az', 'és', 'hogy', 'is', 'nem', 'egy', 'meg', 'van', 'de',
        'ha', 'ezt', 'azt', 'vagy', 'csak', 'még', 'már', 'ki', 'mit',
    ];

    foreach ($ignored as $word) {
        unset($wordCounts[$word]);
    }

    arsort($wordCounts);

    return array_slice($wordCounts, 0, $limit, true);
}

function speechActivity($blocks, $duration, $bucketSeconds)
{
    $bucketCount = (int) ceil($duration / $bucketSeconds);

    if ($bucketCount < 1) {
        $bucketCount = 1;
    }

    $buckets = array_fill(0, $bucketCount, []);

    foreach ($blocks as $block) {
        if ($block['type'] !== 'speech') {
            continue;
        }

        $cursor = $block['start'];
        $end = $block['end'];

        while ($cursor < $end) {
            $index = intdiv($cursor, $bucketSeconds);

            if ($index >= $bucketCount) {
                break;
            }

            $chunkEnd = min($end, ($index + 1) * $bucketSeconds);
            $speakerId = $block['speakerId'];

            if (!isset($buckets[$index][$speakerId])) {
                $buckets[$index][$speakerId] = 0;
            }

            $buckets[$index][$speakerId] += $chunkEnd - $cursor;
            $cursor = $chunkEnd;
        }
    }

    return $buckets;
}

// Csend: az első megszólalás előtti idő senkihez sem tartozik.
// Két sor között nincs külön csend, az idő az előző beszélőé.
// Az utolsó sortól a felvétel végéig tartó idő az utolsó beszélőé.
// Átfedés: a bemutató sorok nem fedik egymást, egyszerre egy beszélő beszél.
// Ha egy sor kezdete mégis korábbi lenne a következőnél, annak hossza 0.

function calculateStatistics($conversation, $bucketSeconds)
{
    $duration = max(0, (int) $conversation['duration']);
    $palette = speakerPalette();
    $speakers = [];
    $colorIndex = 0;

    foreach ($conversation['speaker_list'] ?? [] as $speaker) {
        $speakers[$speaker['id']] = [
            'id' => $speaker['id'],
            'name' => $speaker['name'],
            'color' => $palette[$colorIndex % count($palette)],
            'colorClass' => 'speaker-color-' . (($colorIndex % 3) + 1),
            'seconds' => 0,
            'turns' => 0,
            'words' => 0,
        ];
        $colorIndex++;
    }

    $segments = $conversation['segments'] ?? [];

    usort($segments, function ($first, $second) {
        return $first['start'] <=> $second['start'];
    });

    $blocks = [];
    $transitions = 0;
    $previousSpeaker = null;
    $wordCounts = [];
    $segmentCount = count($segments);

    if ($segmentCount > 0 && (int) $segments[0]['start'] > 0) {
        $blocks[] = [
            'type' => 'silence',
            'seconds' => (int) $segments[0]['start'],
            'start' => 0,
            'end' => (int) $segments[0]['start'],
        ];
    }

    for ($index = 0; $index < $segmentCount; $index++) {
        $segment = $segments[$index];
        $start = (int) $segment['start'];

        if ($index + 1 < $segmentCount) {
            $end = (int) $segments[$index + 1]['start'];
        } else {
            $end = $duration;
        }

        if ($end < $start) {
            $end = $start;
        }

        $speakerId = $segment['speaker'];

        if (!isset($speakers[$speakerId])) {
            $speakers[$speakerId] = [
                'id' => $speakerId,
                'name' => $speakerId,
                'color' => $palette[$colorIndex % count($palette)],
                'colorClass' => 'speaker-color-' . (($colorIndex % 3) + 1),
                'seconds' => 0,
                'turns' => 0,
                'words' => 0,
            ];
            $colorIndex++;
        }

        $seconds = $end - $start;
        $speakers[$speakerId]['seconds'] += $seconds;
        $speakers[$speakerId]['turns']++;

        foreach (splitWords($segment['text']) as $word) {
            $speakers[$speakerId]['words']++;

            if (!isset($wordCounts[$word])) {
                $wordCounts[$word] = 0;
            }

            $wordCounts[$word]++;
        }

        if ($previousSpeaker !== null && $previousSpeaker !== $speakerId) {
            $transitions++;
        }

        $previousSpeaker = $speakerId;

        $blocks[] = [
            'type' => 'speech',
            'speakerId' => $speakerId,
            'name' => $speakers[$speakerId]['name'],
            'colorClass' => $speakers[$speakerId]['colorClass'],
            'start' => $start,
            'end' => $end,
            'seconds' => $seconds,
        ];
    }

    $speakingSeconds = 0;
    $wordCount = 0;

    foreach ($speakers as $speaker) {
        $speakingSeconds += $speaker['seconds'];
        $wordCount += $speaker['words'];
    }

    $leadingSilence = 0;

    if ($segmentCount > 0) {
        $leadingSilence = max(0, (int) $segments[0]['start']);
    }

    return [
        'duration' => $duration,
        'speakingSeconds' => $speakingSeconds,
        'leadingSilence' => $leadingSilence,
        'transitions' => $transitions,
        'turnCount' => $segmentCount,
        'wordCount' => $wordCount,
        'speakers' => array_values($speakers),
        'frequentWords' => frequentWords($wordCounts, 8),
        'blocks' => $blocks,
        'activity' => speechActivity($blocks, $duration, $bucketSeconds),
        'bucketSeconds' => $bucketSeconds,
    ];
}
