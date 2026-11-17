# AI-alapú beszélgetéselemző és adatvizualizációs rendszer

Webes alkalmazás hangfelvételek feldolgozására, beszélgetések elemzésére és az eredmények interaktív vizualizációjára.

A projekt szakdolgozati fejlesztés keretében készül. Célja egy egyszerűen használható, lokálisan működő rendszer létrehozása, amely mesterséges intelligencia segítségével képes többszereplős, elsősorban magyar nyelvű beszélgetések feldolgozására.

A rendszer alapját a **WhisperX** beszédfelismerő keretrendszer adja, amely automatikus beszédátírást, időbélyeg-generálást és beszélőszétválasztást tesz lehetővé.

A fejlesztés középpontjában nem egy saját AI-modell tanítása, hanem meglévő technológiák integrálása, az adatok strukturált feldolgozása és felhasználóbarát megjelenítése áll.

## A projekt célja

Az alkalmazás lehetővé teszi hangfelvételek feltöltését, feldolgozását és a beszélgetések különböző statisztikai szempontok szerinti elemzését.

A feldolgozás eredményeként a felhasználó megtekintheti a beszélgetés szöveges átiratát, az egyes beszélők megszólalásait, valamint a kommunikációs aktivitásukat bemutató diagramokat.

A rendszer lokálisan működik, így a hangfelvételek feldolgozásához nincs szükség külső, felhőalapú AI-szolgáltatásra.

## Főbb funkciók

### Hangfelvételek kezelése

- MP3 és WAV hangfájlok feltöltése
- Beszélgetések címének, dátumának és leírásának megadása
- Korábban feltöltött felvételek megtekintése
- Hangfelvételek lejátszása
- Feldolgozási állapotok követése

### AI-alapú beszédfeldolgozás

- Automatikus beszédfelismerés WhisperX segítségével
- Magyar nyelvű beszélgetések támogatása
- Időbélyegzett szöveges átiratok készítése
- Beszélők automatikus elkülönítése
- Különböző beszédfelismerő modellek kiválasztása
- Feldolgozási paraméterek konfigurálása

A beszélőszétválasztás célja annak meghatározása, hogy mikor melyik résztvevő beszél, nem pedig a beszélők valós személyazonosságának felismerése.

### Beszélgetések elemzése

A rendszer a feldolgozott adatok alapján különböző statisztikákat készít:

- Beszédidő beszélőnként
- Beszédidő százalékos megoszlása
- Megszólalások száma
- Átlagos megszólalási idő
- Beszélőváltások száma
- Felismert szavak száma
- Beszédaktivitás időbeli alakulása
- Leggyakrabban előforduló szavak

### Adatvizualizáció

Az elemzési eredmények egy böngészőből elérhető, interaktív felületen jelennek meg.

A tervezett vizualizációk:

- Beszédidő-eloszlási diagramok
- Beszélgetési idővonal
- Beszédaktivitási diagramok
- Beszélőnkénti statisztikák
- Kereshető, időbélyegzett szöveges átirat

A felhasználó az átirat egy adott időbélyegére kattintva a hangfelvétel megfelelő részéhez navigálhat.

## Felhasznált technológiák

A projekt célja, hogy egyszerű, jól dokumentálható és könnyen karbantartható technológiákra épüljön.

| Technológia | Felhasználás |
|---|---|
| PHP | Webalkalmazás és szerveroldali működés |
| HTML5 | Webes felület szerkezete |
| CSS3 | Megjelenés és reszponzív kialakítás |
| JavaScript | Interaktív funkciók |
| Chart.js | Statisztikai diagramok |
| Python | AI-feldolgozási szolgáltatás |
| FastAPI | PHP és Python közötti kommunikáció |
| WhisperX | Beszédfelismerés és időbélyegek |
| pyannote.audio | Beszélőszétválasztás |
| SQLite | Strukturált adattárolás |
| FFmpeg | Hangfájlok előfeldolgozása |

A Python-alapú feldolgozórendszer és az adatbázis integrációja egy későbbi fejlesztési szakaszban valósul meg.

## Tervezett rendszerarchitektúra

A rendszer két fő részből épül fel.

**1. Webalkalmazás**

A PHP, HTML, CSS és JavaScript alapú webes felület felelős a felhasználói interakciókért, a hangfelvételek kezeléséért, a feldolgozási beállítások megadásáért és az elemzési eredmények megjelenítéséért.

**2. AI-feldolgozó szolgáltatás**

A Python-alapú szolgáltatás a WhisperX segítségével feldolgozza a hangfelvételeket, elkészíti az időbélyegzett átiratokat, és megkísérli elkülöníteni az egyes beszélőket.

A webalkalmazás és az AI-feldolgozó szolgáltatás helyi API-n keresztül kommunikál majd egymással.

A feldolgozási eredmények SQLite-adatbázisban kerülnek tárolásra.

## Fejlesztési ütemterv

A projekt fejlesztése több egymásra épülő szakaszban történik.

### 1. Webes felület

- [ ] Projektstruktúra kialakítása
- [ ] Alapvető megjelenés és navigáció
- [ ] Kezdőoldal és dashboard
- [ ] Hangfelvétel-feltöltési felület
- [ ] Feldolgozási paraméterek beállítása
- [ ] Feldolgozási állapot megjelenítése
- [ ] Beszélgetések részletes nézete
- [ ] Időbélyegzett átiratok megjelenítése
- [ ] Statisztikai diagramok
- [ ] Beszélgetések keresése és kezelése
- [ ] Webes felület tesztelése

Ebben a szakaszban az alkalmazás demonstrációs adatokkal működik.

### 2. Backend és adatbázis

- [ ] SQLite-adatbázis kialakítása
- [ ] Beszélgetések adatainak tárolása
- [ ] Hangfájlok helyi kezelése
- [ ] Python-feldolgozószolgáltatás kialakítása
- [ ] PHP–Python kommunikáció megvalósítása

### 3. AI-integráció

- [ ] WhisperX telepítése és konfigurálása
- [ ] Automatikus beszédfelismerés
- [ ] Időbélyeges átiratok készítése
- [ ] Beszélőszétválasztás integrálása
- [ ] Feldolgozási paraméterek kezelése
- [ ] Eredmények adatbázisba mentése

### 4. Statisztikai elemzés és tesztelés

- [ ] Valós feldolgozási eredmények vizualizációja
- [ ] Beszélgetési statisztikák kiszámítása
- [ ] Különböző modellek összehasonlítása
- [ ] Feldolgozási sebesség mérése
- [ ] CPU- és memóriahasználat vizsgálata
- [ ] Beszédfelismerési pontosság értékelése
- [ ] Rendszertesztelés és dokumentáció

## Telepítés és futtatás

A webes prototípus fejlesztéséhez PHP 8 vagy újabb verzió szükséges.

A projekt klónozása:

```bash
git clone <repository-url>
```

Belépés a projekt könyvtárába:

```bash
cd conversation-analyzer
```

A helyi fejlesztői szerver indítása:

```bash
php -S localhost:8000
```

A webalkalmazás ezután böngészőből érhető el:

`http://localhost:8000`

A fenti lépések a tervezett egyszerű PHP-webalkalmazás futtatására vonatkoznak. A teljes AI-feldolgozó rendszer telepítési útmutatója később készül el.

## Fejlesztési alapelvek

A projekt fejlesztése során kiemelt szempont:

- Egyszerű, áttekinthető architektúra
- Könnyen olvasható és módosítható forráskód
- Minimális külső függőség
- Moduláris, de nem túlbonyolított felépítés
- Reszponzív és felhasználóbarát webes felület
- Lokális adatfeldolgozás
- Könnyű telepíthetőség és karbantarthatóság

A cél nem egy összetett vállalati rendszer, hanem egy jól működő, dokumentálható és továbbfejleszthető szakdolgozati prototípus létrehozása.

## Projekt állapota

**Fejlesztés alatt**

A projekt jelenlegi fejlesztési célja a PHP-alapú webes felület megvalósítása.

A valós WhisperX-integráció, az adatbázis-kezelés és az automatikus beszédfeldolgozás későbbi fejlesztési szakaszok feladata.

## Szakdolgozat

A projekt egy egyetemi szakdolgozat gyakorlati megvalósításaként készül.

**Szakdolgozat címe:** Mesterséges intelligencián alapuló beszélgetéselemző és adatvizualizációs rendszer fejlesztése

**Angol cím:** Development of an AI-Based Conversation Analysis and Data Visualization System

A projekt célja a meglévő mesterségesintelligencia-technológiák gyakorlati integrációjának, konfigurálhatóságának és alkalmazhatóságának vizsgálata egy lokálisan működő informatikai rendszerben.
