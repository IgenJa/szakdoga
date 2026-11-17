<?php

// Demonstrációs adatok. Kézzel szerkeszthetők.
// A duration és a segment start mező másodpercben van.
// A status lehetséges értékei: waiting, processing, completed, failed.
// Az audio mező helyi fájlútvonal az alkalmazás mappájához képest.
// Ha hiányzik vagy a fájl nem létezik, nincs lejátszás.

$conversations = [
    [
        'id' => 1,
        'title' => 'Szakmai interjú',
        'description' => 'Szoftverfejlesztői állásinterjú.',
        'date' => '2026-10-15',
        'duration' => 720,
        'status' => 'completed',
        'speakers' => 2,
        'speaker_list' => [
            ['id' => 'A', 'name' => 'Kiss Anna', 'role' => 'Interjúztató'],
            ['id' => 'B', 'name' => 'Nagy Bence', 'role' => 'Jelentkező'],
        ],
        'segments' => [
            ['start' => 8, 'speaker' => 'A', 'text' => 'Jó napot, köszönöm, hogy eljött. Kezdjük a tapasztalatával.'],
            ['start' => 18, 'speaker' => 'B', 'text' => 'Jó napot. Három éve fejlesztek webes rendszereket, főleg PHP-ban.'],
            ['start' => 41, 'speaker' => 'A', 'text' => 'Milyen feladatot vitt végig egyedül a legutóbbi munkahelyén?'],
            ['start' => 52, 'speaker' => 'B', 'text' => 'Egy belső ügyfélnyilvántartást készítettem, a kereséstől a jogosultságokig.'],
            ['start' => 78, 'speaker' => 'A', 'text' => 'Hogyan egyeztetett a felhasználókkal, ha változtak az igények?'],
            ['start' => 91, 'speaker' => 'B', 'text' => 'Hetente ültünk össze, és a nyitott kérdéseket írásban is rögzítettük.'],
            ['start' => 130, 'speaker' => 'A', 'text' => 'Rendben. Beszéljünk arról, hogyan derít fel egy hibát éles rendszerben.'],
            ['start' => 146, 'speaker' => 'B', 'text' => 'Először a naplót nézem meg, utána egy kisebb adathalmazon próbálom újra előidézni.'],
            ['start' => 400, 'speaker' => 'A', 'text' => 'Van kérdése a csapat mindennapi munkájáról?'],
            ['start' => 412, 'speaker' => 'B', 'text' => 'Igen. Hogyan osztják el a feladatokat, és ki nézi át a kódot?'],
            ['start' => 640, 'speaker' => 'A', 'text' => 'Köszönöm a beszélgetést. A héten még jelentkezünk a következő lépésről.'],
            ['start' => 655, 'speaker' => 'B', 'text' => 'Én köszönöm a lehetőséget. Várom a hírt.'],
        ],
    ],
    [
        'id' => 2,
        'title' => 'Projektmegbeszélés',
        'description' => 'Háromfős egyeztetés a következő sprint feladatairól.',
        'date' => '2026-10-22',
        'duration' => 1845,
        'status' => 'completed',
        'speakers' => 3,
        'speaker_list' => [
            ['id' => 'A', 'name' => 'Tóth Csilla', 'role' => 'Projektvezető'],
            ['id' => 'B', 'name' => 'Szabó Dávid', 'role' => 'Fejlesztő'],
            ['id' => 'C', 'name' => 'Varga Eszter', 'role' => 'Tervező'],
        ],
        'segments' => [
            ['start' => 15, 'speaker' => 'A', 'text' => 'Kezdjük a bemutató oldal nyitott pontjaival.'],
            ['start' => 28, 'speaker' => 'B', 'text' => 'A feltöltés ellenőrzése kész, a mentés még nincs bekötve.'],
            ['start' => 47, 'speaker' => 'C', 'text' => 'A táblázat mobilon túl széles, ezt a tervben jeleztem.'],
            ['start' => 70, 'speaker' => 'A', 'text' => 'Dávid, a mentést a következő körbe tegyük. Eszter, a táblázat legyen görgethető.'],
            ['start' => 96, 'speaker' => 'B', 'text' => 'Rendben. A hangfájlt addig nem tároljuk, csak a bemutató fut.'],
            ['start' => 118, 'speaker' => 'C', 'text' => 'A beszélők színe maradjon visszafogott, három szín elég.'],
            ['start' => 300, 'speaker' => 'A', 'text' => 'Hol tart a leirat keresése?'],
            ['start' => 314, 'speaker' => 'B', 'text' => 'A kereső a látható sorokat szűri, adatbázis nincs mögötte.'],
            ['start' => 900, 'speaker' => 'C', 'text' => 'A diagramokat a következő egyeztetésre hagyjuk.'],
            ['start' => 918, 'speaker' => 'A', 'text' => 'Egyetértek. Jövő héten megnézzük a statisztikát is.'],
            ['start' => 1700, 'speaker' => 'B', 'text' => 'Addig frissítem a bemutató adatokat, ha új példa kell.'],
            ['start' => 1722, 'speaker' => 'A', 'text' => 'Köszönöm. A mai egyeztetést lezárom.'],
        ],
    ],
    [
        'id' => 3,
        'title' => 'Ügyfélbemutató',
        'description' => 'Rövid bemutató arról, hogyan nézhető vissza egy rögzített beszélgetés.',
        'date' => '2026-11-03',
        'duration' => 960,
        'status' => 'completed',
        'speakers' => 2,
        'speaker_list' => [
            ['id' => 'A', 'name' => 'Fekete Gábor', 'role' => 'Előadó'],
            ['id' => 'B', 'name' => 'Horváth Júlia', 'role' => 'Ügyfél'],
        ],
        'segments' => [
            ['start' => 20, 'speaker' => 'A', 'text' => 'Röviden megmutatom, hogyan jelenik meg egy rögzített beszélgetés.'],
            ['start' => 36, 'speaker' => 'B', 'text' => 'A lényeg, hogy a kollégák utólag is vissza tudják keresni, ki mit mondott.'],
            ['start' => 55, 'speaker' => 'A', 'text' => 'A leirat időbélyeggel készül, a beszélők külön színnel látszanak.'],
            ['start' => 74, 'speaker' => 'B', 'text' => 'És ha nincs meg a hangfájl, a lejátszó akkor is jelzi, hogy nem elérhető?'],
            ['start' => 88, 'speaker' => 'A', 'text' => 'Igen. Ilyenkor nem indítunk hamis lejátszást.'],
            ['start' => 240, 'speaker' => 'B', 'text' => 'A kereső a teljes szövegben néz, vagy csak a címekben?'],
            ['start' => 255, 'speaker' => 'A', 'text' => 'A leirat soraiban. Ha nincs találat, azt kiírjuk.'],
            ['start' => 800, 'speaker' => 'B', 'text' => 'Ez így áttekinthető. A részletes számokat majd a statisztikánál nézzük meg.'],
            ['start' => 820, 'speaker' => 'A', 'text' => 'Köszönöm, a következő verzióban ott folytatjuk.'],
        ],
    ],
    [
        'id' => 4,
        'title' => 'Csoportos egyeztetés',
        'date' => '2026-11-08',
        'duration' => 2410,
        'status' => 'processing',
        'speakers' => 3,
    ],
    [
        'id' => 5,
        'title' => 'Felvételi beszélgetés',
        'date' => '2026-11-12',
        'duration' => 540,
        'status' => 'failed',
        'speakers' => 2,
    ],
    [
        'id' => 6,
        'title' => 'Heti státusz',
        'date' => '2026-11-14',
        'duration' => 1260,
        'status' => 'waiting',
        'speakers' => 3,
    ],
];

function formatDuration($seconds)
{
    $seconds = (int) $seconds;
    $hours = intdiv($seconds, 3600);
    $minutes = intdiv($seconds % 3600, 60);
    $remainingSeconds = $seconds % 60;

    $parts = [];

    if ($hours > 0) {
        $parts[] = $hours . ' óra';
    }

    if ($minutes > 0) {
        $parts[] = $minutes . ' perc';
    }

    if ($remainingSeconds > 0 || count($parts) === 0) {
        $parts[] = $remainingSeconds . ' mp';
    }

    return implode(' ', $parts);
}

function formatDate($date)
{
    $time = strtotime($date);

    if ($time === false) {
        return $date;
    }

    return date('Y. m. d.', $time);
}

function formatTimestamp($seconds)
{
    $seconds = max(0, (int) $seconds);
    $hours = intdiv($seconds, 3600);
    $minutes = intdiv($seconds % 3600, 60);
    $remainingSeconds = $seconds % 60;

    if ($hours > 0) {
        return sprintf('%d:%02d:%02d', $hours, $minutes, $remainingSeconds);
    }

    return sprintf('%d:%02d', $minutes, $remainingSeconds);
}

function statusLabel($status)
{
    $labels = [
        'waiting' => 'Várakozás',
        'processing' => 'Feldolgozás',
        'completed' => 'Befejezve',
        'failed' => 'Sikertelen',
    ];

    if (isset($labels[$status])) {
        return $labels[$status];
    }

    return $status;
}
