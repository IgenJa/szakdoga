# Beszélgetéselemző

A részletes fejlesztési dokumentáció: [../DOKUMENTACIO.md](../DOKUMENTACIO.md).

Egyszerű PHP webes felület beszélgetések megjelenítésére és elemzésére.
Ez a szakdolgozat webes része. A felület magyar nyelvű, a forráskód azonosítói angolok.

A program bemutató adatokkal dolgozik. Nincs adatbázis, feltöltött fájl nem kerül mentésre, és beszédfelismerés sem fut.

## Hogyan működik

Minden oldal önálló PHP-fájl. A közös keretet az `includes/header.php`, az `includes/sidebar.php` és az `includes/footer.php` adja. Az oldal saját tartalma a fejléc és a lábléc között van.

A böngésző közönséges linkekkel lép oldalról oldalra. JavaScript csak ott fut, ahol a szerver önmagában nem elég:

- a feltöltés ellenőrzése és a fájl húzása
- a feldolgozás állapotának időzített bemutatója
- a keresés a leiratban
- a diagramok
- a lista keresése és szűrése

A fő útvonal:

1. **Áttekintés** – összesítő számok és az öt legutóbbi beszélgetés.
2. **Beszélgetések** – a teljes lista, keresés és állapot szerinti szűrés.
3. **Részletek** – adatok, beszélők és időbélyeges leirat.
4. **Statisztikák** – a leiratból számolt mutatók és diagramok.
5. **Új elemzés** – cím, dátum, leírás és hangfájl ellenőrzése, mentés nélkül.
6. **Feldolgozási beállítások** – modell, nyelv, diarizáció, beszélőszám, CPU vagy GPU.
7. **Feldolgozás állapota** – időzített bemutató: várakozás, feldolgozás, befejezve vagy sikertelen.

## Indítás

A `conversation-analyzer` mappából:

```bash
php -S localhost:8000
```

Ezen a gépen a PHP az XAMPP része, és nincs a PATH-on:

```bash
C:\xampp\php\php.exe -S localhost:8000
```

Ezután a böngészőben: [http://localhost:8000](http://localhost:8000)

Szükséges: PHP 8 vagy újabb. Nincs szükség adatbázisra, Node.js-re, Dockerre vagy egyéb csomagra. A Chart.js a `js/chart.umd.min.js` fájlban van, ezért a diagramok internet nélkül is megjelennek.

## Fájlok

```text
conversation-analyzer/
├── index.php              Áttekintés
├── conversations.php      Teljes lista, keresés és szűrés
├── conversation.php       Egy beszélgetés adatai és leirata
├── statistics.php         Statisztikák és diagramok
├── upload.php             Új elemzés, fájlválasztás ellenőrzéssel
├── processing.php         Feldolgozási beállítások
├── status.php             Feldolgozási állapot, időzített bemutató
├── includes/
│   ├── header.php         HTML-fej, menü és a tartalom kezdete
│   ├── sidebar.php        Oldalsó menü
│   ├── footer.php         A tartalom és a HTML lezárása
│   └── stats.php          Beszédidő, szavak és idővonal számítása
├── css/
│   └── style.css          Az egész felület stílusa
├── js/
│   ├── upload.js          Fájlválasztás, húzás és ellenőrzés
│   ├── status.js          Az állapotbemutató időzítése
│   ├── transcript.js      Keresés a leiratban, tekerés ha van hang
│   ├── charts.js          A diagramok felrajzolása
│   ├── conversations.js   A lista keresése és szűrése
│   └── chart.umd.min.js   Chart.js
├── data/
│   ├── mock.php           Bemutató beszélgetések
│   └── options.php        Modell, nyelv és eszköz listája
└── assets/                Ide tehető később egy helyi hangfájl
```

## Felület módosítása

- Szövegek: az adott oldal PHP-fájlja. A menüpontok az `includes/sidebar.php` `$navItems` tömbjében vannak.
- Színek, térköz, betű: a `css/style.css` fájl elején lévő változók. A beszélőszínek a `--speaker-1`, `--speaker-2` és `--speaker-3`. Ugyanezek a színek szerepelnek az `includes/stats.php` `speakerPalette()` függvényében is, mert a diagramok onnan kapják a színt.
- Új oldal: egy PHP-fájl, amely beállítja a `$pageTitle` változót, majd beilleszti a fejlécet és a láblécet. Ha menüpont is kell, egy sor a `$navItems` tömbbe.
- Gomb: `button` osztály, halványabb változat: `button button-secondary`. Kártya: `card`.

## Bemutató adatok

A beszélgetések a `data/mock.php` `$conversations` tömbjében vannak. Egy elem így épül fel:

- `id` – szám, a részletes oldal ezt kéri
- `title`, `description`, `date` – a dátum `ÉÉÉÉ-HH-NN` alakú
- `duration` – a felvétel hossza másodpercben
- `status` – `waiting`, `processing`, `completed` vagy `failed`
- `speakers` – a beszélők száma
- `speaker_list` – név és szerep, ha már van leirat
- `segments` – a leirat sorai: `start` másodperc, `speaker` az azonosító, `text` a mondat
- `audio` – nem kötelező helyi útvonal az alkalmazás mappájához képest, például `assets/interju.wav`

A leirat sorainak `start` értéke legyen a `duration` értéken belül, és soronként növekedjen. Az áttekintés a legfrissebb öt sort mutatja. A lista az összeset, dátum szerint csökkenve.

Hang csak akkor szólal meg, ha az `audio` fájl létezik, és az útvonal nem tartalmaz `..` részt. Hang nélkül az időbélyeg nem teker, és a lejátszó nem indul el.

A feldolgozási űrlap listái a `data/options.php` fájlban vannak. Ezek az értékek nincsenek elmentve.

## Statisztika

A számítás az `includes/stats.php` fájlban van.

- Két leiratsor közötti idő az előző beszélőé.
- Az első sor előtti idő csend, senkihez sem tartozik.
- Az utolsó sortól a felvétel végéig tartó idő az utolsó beszélőé.
- Átfedő beszéd nincs a bemutató adatokban.
- A gyakori szavak közül kimarad egy rövid lista, a `frequentWords()` függvényben.
- A „beszéd a beszélgetés során” diagram 60 másodperces oszlopokat használ. Ezt a `statistics.php` elején a `$bucketSeconds` állítja.

## Ami még nincs kész

- A hangfájl tartós mentése
- A beállítások és az eredmény mentése
- Adatbázis
- Valódi beszédfelismerés, időbélyeg-igazítás és beszélőszétválasztás
- Python-szolgáltatás és a vele való kapcsolat

## Későbbi Python- és WhisperX-kapcsolat

A PHP felület maradhat a mostani oldalakból. A felismerést egy helyi Python-program végezné: WhisperX a leirathoz és az időbélyegekhez, a beszélők szétválasztásához a WhisperX diarizációja. A hangot FFmpeg készítené elő.

A PHP később átadná a Python-programnak a kiválasztott hangfájlt és a feldolgozási űrlap értékeit. A válasz ugyanolyan szerkezetű lehet, mint egy elem a `data/mock.php` tömbben: beszélők és időbélyeges sorok. Ezeket egy későbbi SQLite-adatbázis tárolná, a felület pedig ugyanúgy jelenítené meg őket, mint most a bemutató adatot.

Ezt a kapcsolatot most nem kell beépíteni. Elég, ha a bemutató tömb mezői megmaradnak, mert a későbbi eredmény ugyanebbe a formába illeszkedik.
