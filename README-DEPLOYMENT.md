# Glass Market - Deployment Guide

## 🚀 Dynamische URL Configuratie

Dit project gebruikt een dynamische URL configuratie die automatisch werkt, ongeacht de naam van je projectmap.

### Hoe het werkt

Het bestand `config.php` detecteert automatisch de basis URL van je project:

- Als je project in `htdocs/glass-market/` staat → BASE_URL = `/glass-market`
- Als je project in `htdocs/mijn-project/` staat → BASE_URL = `/mijn-project`
- Als je project in `htdocs/` staat → BASE_URL = ``

### Voor teamleden

**Geen configuratie nodig!** Je kunt het project gewoon clonen/downloaden en het werkt meteen:

1. Clone/download het project naar je XAMPP htdocs map
2. Benoem de map zoals je wilt (bijv. `glass-market`, `project`, `test`, etc.)
3. Open in browser: `http://localhost/jouw-map-naam/public/index.php`

Alle links en assets worden automatisch aangepast!

### Beschikbare constanten

In je PHP bestanden kun je deze constanten gebruiken:

```php
BASE_URL      // Basis pad (bijv. /glass-market)
PUBLIC_URL    // /glass-market/public
VIEWS_URL     // /glass-market/resources/views
CSS_URL       // /glass-market/resources/css
JS_URL        // /glass-market/resources/js
```

### Voorbeeld gebruik

```php
<!-- CSS laden -->
<link rel="stylesheet" href="<?php echo CSS_URL; ?>/app.css">

<!-- Link naar home -->
<a href="<?php echo PUBLIC_URL; ?>/index.php">Home</a>

<!-- Link naar browse pagina -->
<a href="<?php echo VIEWS_URL; ?>/browse.php">Browse</a>
```

### Belangrijk

- Alle PHP pagina's laden automatisch `config.php`
- De navbar en footer laden het automatisch als het nog niet geladen is
- Gebruik NOOIT hardcoded paden zoals `/glass-market/...`
- Gebruik altijd de PHP constanten

## 🔧 Voor ontwikkelaars

Als je nieuwe pagina's toevoegt:

```php
<?php require_once __DIR__ . '/../../config.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="<?php echo CSS_URL; ?>/app.css">
```

Pas het pad van `require_once` aan afhankelijk van waar je bestand staat!
