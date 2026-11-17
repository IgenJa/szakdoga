# Beszélgetéselemző – fejlesztési dokumentáció

Ez a dokumentum a szakdolgozat **webes felületének** (`conversation-analyzer` mappa) jelenlegi állapotát írja le, hogy a további fejlesztés ugyanabból a kiindulópontból indulhasson. A rövid telepítési útmutató továbbra is a [conversation-analyzer/README.md](conversation-analyzer/README.md) fájlban van.

**Utolsó frissítés:** 2026. november 17.

---

## 1. Projekt célja

A rendszer célja, hogy **hangalapú beszélgetéseket** lehessen feltölteni, feldolgozni, majd **időbélyeges leiratként** megjeleníteni és **statisztikailag elemezni**. A jelenlegi verzió a szakdolgozat **demonstrációs webes rétege**: magyar nyelvű felület, angol forráskód-azonosítók, bemutató adatokkal.

A későbbi terv szerint a háttérben **Python + WhisperX** (átírás, időbélyegek, diarizáció) és **FFmpeg** (előfeldolgozás) dolgozná fel a hangot; az eredmény ugyanabba az adatszerkezetbe illeszkedne, mint a mostani `mock.php` elemek.

---

## 2. Jelenlegi állapot – összefoglaló

| Terület | Állapot |
|--------|---------|
| Oldalak, navigáció, közös layout | Kész |
| Bemutató beszélgetések (6 db, vegyes állapottal) | Kész |
| Részletes nézet: adatok, beszélők, leirat, keresés | Kész |
| Hangszinkronizált leirat (ha van helyi `audio` fájl) | Kész |
| Statisztikák: táblázat, idővonal, 5 diagram | Kész |
| Új elemzés: űrlap, fájlvalidáció, drag-and-drop | Kész (nincs mentés) |
| Feldolgozási beállítások: űrlap + szerveroldali validáció | Kész (nincs mentés) |
| Feldolgozás állapota: időzített demo | Kész |
| Adatbázis, fájlmentés, valódi ASR | **Nincs** |
| Python szolgáltatás kapcsolat | **Nincs** |

A felületen szereplő **„demonstráció”** jelzések szándékosak: jelezik, hogy az adatok és a feltöltés nem valós feldolgozásból származnak.

---

## 3. Technológiai környezet

| Réteg | Technológia |
|-------|-------------|
| Szerver | PHP 8+ beépített fejlesztői szerver (`php -S`) |
| Adattárolás | PHP tömbök fájlban (`data/mock.php`) – nincs DB |
| Megjelenítés | HTML, CSS, minimális PHP sablonok |
| Kliensoldali logika | Vanilla JavaScript (nincs bundler, nincs npm) |
| Diagramok | Chart.js (helyi: `js/chart.umd.min.js`) |

**Nem szükséges:** Node.js, Docker, Composer, adatbázis. **Chart.js offline** is működik.

### Indítás

A `conversation-analyzer` mappából:

```bash
php -S localhost:8000
```

XAMPP esetén (ha a PHP nincs a PATH-on):

```bash
C:\xampp\php\php.exe -S localhost:8000
```

Böngésző: `http://localhost:8000`

---

## 4. Architektúra

### 4.1 Oldalmodell

Minden képernyő **önálló PHP fájl**. Közös keret:

1. `includes/header.php` – HTML fej, CSS, sidebar + topbar + `<main>` nyitás  
2. `includes/sidebar.php` – márkanév + főmenü  
3. Oldalspecifikus tartalom  
4. `includes/footer.php` – layout lezárás  

A menüpontok a `sidebar.php` `$navItems` tömbjében:

- `index.php` – Áttekintés  
- `upload.php` – Új elemzés  
- `conversations.php` – Beszélgetések  
- `statistics.php` – Statisztikák  

A **feldolgozási beállítások** (`processing.php`) és az **állapot** (`status.php`) a feltöltési folyamat részei, de **nincsenek a főmenüben** (linkekkel érhetők el).

### 4.2 Adatforrások

| Fájl | Szerep |
|------|--------|
| `data/mock.php` | `$conversations` tömb + `formatDuration`, `formatDate`, `formatTimestamp`, `statusLabel` |
| `data/options.php` | Whisper-szerű `$models`, `$languages`, `$devices` listák |
| `includes/stats.php` | `calculateStatistics()` és segédfüggvények |

### 4.3 Szerver vs. böngésző

| Funkció | Hol fut |
|---------|---------|
| Oldal renderelés, statisztika számítás | PHP |
| Feltöltés ellenőrzés, DnD | `upload.js` (fájl nem megy a szerverre) |
| Lista szűrés/keresés | `conversations.js` |
| Leirat keresés, időbélyeg → hang | `transcript.js` |
| Diagramok | `charts.js` + PHP-ből JSON |
| Feldolgozási progress demo | `status.js` |
| Beállítások űrlap validáció | PHP POST (`processing.php`) |

---

## 5. Elkészült funkciók (részletesen)

### 5.1 Áttekintés (`index.php`)

- Összesítő kártyák: beszélgetésszám, befejezett elemzések, összes hanghossz  
- Legutóbbi **5** beszélgetés táblázatban (dátum szerint csökkenő)  
- Link az új elemzéshez és a teljes listához  

### 5.2 Beszélgetések (`conversations.php`)

- Teljes lista, dátum szerint rendezve  
- **Kliensoldali** címkeresés és állapotszűrés (`waiting`, `processing`, `completed`, `failed`)  
- Részletek gomb: `conversation.php?id=…`  

### 5.3 Beszélgetés részletei (`conversation.php`)

- Metaadatok, beszélőlista szerepkörrel és színnel (max. 3 paletta)  
- **Hang:** HTML5 `<audio>`, ha az `audio` mező létező, biztonságos útvonal  
- **Leirat:** időbélyeg, beszélő, szöveg; keresés név/szó alapján  
- Ha van hang: időbélyeg **gomb**, kattintásra tekerés (`transcript.js`)  
- Linkek: vissza a listához, statisztika ugyanerre az `id`-ra  

**Hangbiztonság:** az `audio` útvonal nem tartalmazhat `..` részt; a fájl létezését `is_file()` ellenőrzi.

### 5.4 Statisztikák (`statistics.php`)

- Legördülő: melyik beszélgetés (GET `id`)  
- `$bucketSeconds = 60` – aktivitásdiagram időablak  
- Összesítő kártyák: teljes hossz, beszédidő, eleji csend, megszólalások, váltások, szavak  
- Beszélőnkénti táblázat: idő, fordulók, átlagos hossz, szavak  
- **Vizuális idővonal** (beszéd vs. eleji csend)  
- **Diagramok:** beszédidő, megoszlás (pie), megszólalások, gyakori szavak, időbeli aktivitás  

A diagram adatokat a PHP JSON-ként adja át (`#chart-data`), a `charts.js` rajzol.

### 5.5 Új elemzés (`upload.php` + `upload.js`)

- Mezők: cím (kötelező), dátum, leírás, hang (MP3/WAV)  
- Drag-and-drop zóna  
- Validáció: üres cím, hiányzó fájl, nem engedélyezett kiterjesztés  
- Sikeres „ellenőrzés” után összegző kártya – **a fájl nem kerül mentésre**  
- Link a feldolgozási beállításokhoz  

### 5.6 Feldolgozási beállítások (`processing.php`)

- Modellméret, nyelv, diarizáció kapcsoló, min/max beszélő, CPU/GPU  
- POST után szerveroldali validáció; hiba esetén üzenetlista  
- Sikeres bemutató után összegzés + link `status.php`-re query paraméterekkel  

### 5.7 Feldolgozás állapota (`status.php` + `status.js`)

- Query-ből jönnek a beállítások (sanitizálva az `options.php` listák szerint)  
- **Időzített demo:** várakozás → feldolgozás → kész (vagy szimulált hiba)  
- Progress bar, ARIA attribútumok  
- Valódi hangfelismerés **nem fut**  

---

## 6. Adatmodell (beszélgetés rekord)

Egy elem a `$conversations` tömbben:

```php
[
    'id' => int,
    'title' => string,
    'description' => string,          // opcionális
    'date' => 'YYYY-MM-DD',
    'duration' => int,                // másodperc (felvétel teljes hossza)
    'status' => 'waiting'|'processing'|'completed'|'failed',
    'speakers' => int,
    'speaker_list' => [               // opcionális, leirathoz
        ['id' => 'A', 'name' => '...', 'role' => '...'],
    ],
    'segments' => [                     // opcionális, leirat
        ['start' => int, 'speaker' => 'A', 'text' => '...'],
    ],
    'audio' => string,                  // opcionális, pl. 'assets/interju.wav'
]
```

### Bemutató adatok állapota (2026. november)

| id | Cím | Állapot | Leirat |
|----|-----|---------|--------|
| 1 | Szakmai interjú | completed | Igen (2 beszélő) |
| 2 | Projektmegbeszélés | completed | Igen (3 beszélő) |
| 3 | Ügyfélbemutató | completed | Igen (2 beszélő) |
| 4 | Csoportos egyeztetés | processing | Nem |
| 5 | Felvételi beszélgetés | failed | Nem |
| 6 | Heti státusz | waiting | Nem |

Az első három rekord tartalmaz metaadat-leírást és hosszú leiratot; a többi **állapot-bemutató** célú, leirat nélkül.

---

## 7. Statisztikai szabályok (`includes/stats.php`)

A számítás a **leiratsorok** és a `duration` mező alapján történik:

1. **Eleji csend:** az első `segment.start` időtartama külön „silence” blokk; senkihez nem tartozik.  
2. **Két sor között:** nincs külön csend – az idő az **előző beszélő** beszédidejébe számít (a következő sor `start` határol).  
3. **Utolsó sor:** a `duration`-ig tartó rész az **utolsó beszélő** ideje.  
4. **Átfedés:** a bemutató adatokban nincs; egyszerre egy beszélő.  
5. **Gyakori szavak:** UTF-8 kisbetűsítés, írásjelek levágása, magyar **stop-szavak** kiszűrése (`frequentWords()`).  
6. **Aktivitás diagram:** másodpercenkénti bucketökben beszélőnként összesített beszéd másodperc.  

A diagram színek a CSS változókkal egyeznek: `speakerPalette()` ↔ `--speaker-1` … `--speaker-3` a `style.css`-ben.

---

## 8. Felület és design

### 8.1 Kinézet

- **Sötét téma** (meleg semleges tónusok), visszafogott arany hangsúly (`--accent`)  
- Layout: **oldalsáv + főtartalom** (grid)  
- Komponensek: `.card`, `.button`, `.button-secondary`, `.summary-grid`, táblázatok görgethető `.table-wrap`-ben  

### 8.2 Tipográfia (frissítés: 2026-11-17)

Korábban a címek és a „Beszélgetéselemző” márkanév **Georgia serif** betűt használtak. Jelenleg:

- Egységes **sans-serif** stack: `"Segoe UI Variable", "Segoe UI", system-ui, -apple-system, sans-serif`  
- Címek és `.brand`: `font-weight: 600`, enyhe negatív `letter-spacing`  

Módosítás helye: `conversation-analyzer/css/style.css` (`:root` `--font`, valamint `h1, h2, .brand` szabály).

### 8.3 Akadálymentesség

- Aktív menüpont: `aria-current="page"`  
- Progress bar: `role="progressbar"` és `aria-valuenow`  
- Színpontok dekoratív elemeknél: `aria-hidden="true"` ahol indokolt  

### 8.4 Kimenet biztonság

Felhasználói szöveg megjelenítésekor következetesen `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`. JSON a diagramokhoz: `JSON_HEX_TAG` a script injection ellen.

---

## 9. JavaScript modulok

| Fájl | Felelősség |
|------|------------|
| `upload.js` | Űrlap, DnD, fájltípus és méret kijelzés, hibák, eredmény panel |
| `conversations.js` | Táblasorok szűrése `data-title` / `data-status` alapján |
| `transcript.js` | Leiratsorok szűrése; audio seek időbélyegre |
| `charts.js` | Chart.js inicializálás a beágyazott JSON-ból |
| `status.js` | Demo állapotgép, progress, opcionális hiba szimuláció |

---

## 10. Fájlstruktúra

```text
szakdoga/
├── DOKUMENTACIO.md                 ← ez a fájl
└── conversation-analyzer/
    ├── index.php
    ├── conversations.php
    ├── conversation.php
    ├── statistics.php
    ├── upload.php
    ├── processing.php
    ├── status.php
    ├── includes/
    │   ├── header.php
    │   ├── sidebar.php
    │   ├── footer.php
    │   └── stats.php
    ├── data/
    │   ├── mock.php
    │   └── options.php
    ├── css/
    │   └── style.css
    ├── js/
    │   ├── upload.js
    │   ├── conversations.js
    │   ├── transcript.js
    │   ├── charts.js
    │   ├── status.js
    │   └── chart.umd.min.js
    ├── assets/                       (üres / későbbi hangfájlok)
    └── README.md
```

---

## 11. Eddigi fejlesztési mérföldkövek

Az alábbi lista a **webes prototípus** megvalósított lépéseit foglalja össze (nem git history, hanem funkcionális bontás).

1. **Alap layout** – sidebar, topbar, sötét téma, navigáció négy fő menüponttal.  
2. **Mock adatok** – hat beszélgetés, három teljes leirattal, különböző feldolgozási állapotokkal.  
3. **Lista és áttekintés** – összesítők, rendezés, linkek a részletekhez.  
4. **Részletek oldal** – beszélők, leirat, opcionális audio + időbélyeg szinkron.  
5. **Statisztika motor** – PHP számítások, idővonal, Chart.js diagramok.  
6. **Feltöltési folyamat UI** – validáció, DnD, feldolgozási űrlap, állapot demo.  
7. **Kliensoldali szűrések** – beszélgetéslista és leirat keresés.  
8. **Tipográfia finomítás** – serif → modern sans-serif a letisztultabb megjelenésért.

---

## 12. Ami szándékosan hiányzik

- Hangfájl és metaadat **perzisztens mentése** (fájlrendszer vagy DB)  
- Feltöltött fájl és beállítások **összekapcsolása** egy beszélgetés rekorddal  
- **SQLite** (vagy más) adatbázis és CRUD  
- **Valódi** WhisperX / FFmpeg feldolgozás és státusz lekérdezés  
- Háttérfolyamat, sor, újrapróbálás, hiba naplózás  
- Felhasználókezelés, jogosultságok  
- Törlés/szerkesztés a listából  

Ezek a hiányosságok **nem hibák**: a jelenlegi fázis a szakdolgozat UI/UX és adatmodell igazolására fókuszál.

---

## 13. Tervezett következő lépések (javasolt sorrend)

### 13.1 Adatréteg

1. SQLite séma: `conversations`, `speakers`, `segments`, `jobs` (feldolgozási feladat).  
2. `mock.php` tartalmának migrálása seed adatként, vagy betöltő réteg, ami DB-ből olvas.  
3. Közös PHP modul: `findConversation($id)`, `listConversations()`, formázó függvények áthelyezése (pl. `includes/helpers.php`).

### 13.2 Feltöltés és feldolgozás

1. `upload.php`: multipart POST, fájl mentése `assets/uploads/` alá (egyedi név, méretlimit).  
2. `processing.php`: beállítások mentése session-be vagy DB `jobs` táblába.  
3. Feldolgozás indítása (később): PHP → CLI Python hívás, vagy REST/WebSocket a Python szolgáltatáshoz.  

### 13.3 Python / WhisperX integráció (vázlat)

- Bemenet: hang útvonal + `{ model, language, diarization, min_speakers, max_speakers, device }`.  
- Kimenet: JSON ugyanazzal a struktúrával, mint egy `mock.php` elem (`speaker_list`, `segments`).  
- FFmpeg: mono/stereo normalizálás, szükség szerint átmintavételezés.  

### 13.4 Állapot oldal élesítése

- `status.php` polling vagy SSE: valódi `processing` / `completed` / `failed`.  
- Befejezéskor átirányítás `conversation.php?id=…`-re.  

### 13.5 Felület

- `processing.php` és `status.php` opcionális megjelenítése menüben vagy varázsló lépésként.  
- Mobil nézet finomítása (táblázatok már görgethetők).  

---

## 14. Gyakorlati tippek a folytatáshoz

- **Új oldal:** állítsd be `$pageTitle`-t, `require header/footer`, szükség esetén `$navItems` bővítés.  
- **Új beszélgetés demóhoz:** szerkeszd `data/mock.php`-t; a `start` értékek legyenek monoton növekvők és `< duration`.  
- **Színek váltása:** `css/style.css` `:root` + `stats.php` `speakerPalette()`.  
- **Stop-szavak:** `frequentWords()` `$ignored` tömbje.  
- **Aktivitás felbontás:** `statistics.php` elején `$bucketSeconds`.  

---

## 15. Kapcsolódó dokumentum

| Dokumentum | Tartalom |
|------------|----------|
| [conversation-analyzer/README.md](conversation-analyzer/README.md) | Rövid útmutató, fájllista, mock mezők, WhisperX jövőkép |

---

*Ha a dokumentációt frissíted egy nagyobb mérföldkő után, érdemes a 2. és 11. fejezetet, valamint az „Utolsó frissítés” dátumot módosítani.*