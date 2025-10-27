<?php require_once __DIR__ . '/../../config.php'; ?>
<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/app.css">
  <title>About | Glass Market</title>
</head>
<body>
  <?php include __DIR__ . '/../../includes/navbar.php'; ?>

  <main>
    <section class="hero">
      <div class="container">
        <h1 class="hero-title">Over Glass Market</h1>
        <p class="hero-subtitle">De wereldwijde marktplaats voor glas, kristallen en handgemaakt glaswerk. Verbinding tussen kunstenaars en verzamelaars sinds 2020.</p>
      </div>
    </section>

    <section class="section">
      <div class="container grid grid-4">
        <div class="stat">
          <h3 class="num">10K+</h3>
          <p class="label">Actieve verkopers</p>
        </div>
        <div class="stat">
          <h3 class="num">50K+</h3>
          <p class="label">Producten online</p>
        </div>
        <div class="stat">
          <h3 class="num">100+</h3>
          <p class="label">Landen</p>
        </div>
        <div class="stat">
          <h3 class="num">4.8</h3>
          <p class="label">Gemiddelde rating</p>
        </div>
      </div>
    </section>

    <section class="section values">
      <div class="container values-grid">
        <div class="value">
          <div class="value-icon">✦</div>
          <h4>Geselecteerde collectie</h4>
          <p>Elk stuk wordt zorgvuldig gecontroleerd door ons team van glass art experts</p>
        </div>
        <div class="value">
          <div class="value-icon">🛡</div>
          <h4>Kopersbescherming</h4>
          <p>Shop veilig met onze bescherming en echtheidsgarantie</p>
        </div>
        <div class="value">
          <div class="value-icon">🚚</div>
          <h4>Wereldwijde verzending</h4>
          <p>Professioneel verpakt, verzekerd geleverd tot aan je deur</p>
        </div>
      </div>
    </section>

    <section class="cta">
      <div class="container center">
        <h2>Word onderdeel van de community</h2>
        <p class="muted">Verkoop jouw creaties aan liefhebbers wereldwijd of ontdek unieke glass art</p>
        <a class="btn btn-primary" href="#">Aanmelden als verkoper</a>
      </div>
    </section>

    <?php include __DIR__ . '/../../includes/footer.php'; ?>
  </main>

</body>
</html>
