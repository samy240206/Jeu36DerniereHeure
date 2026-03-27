<?php
$erreur = $_SESSION['erreur'] ?? null;
unset($_SESSION['erreur']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Les 36 Dernières Heures</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
<div class="accueil-wrapper">
    <div class="anime-entree" style="width:100%;max-width:480px;">
        <h1>Les 36<br>Dernières<br>Heures</h1>
        <p class="sous-titre">Une histoire dont vous êtes le héros</p>
        <div class="separator"></div>
        <p class="intro-texte">La radio grésille. La ville est silencieuse. Quelque chose monte dans la cage d'escalier.<br>Vous avez 36 heures pour atteindre la Base Delta.</p>
        <div class="carte anime-entree-retard">
            <?php if ($erreur): ?>
                <div class="alerte-erreur"><?= htmlspecialchars($erreur) ?></div>
            <?php endif; ?>
            <h2>Identifiez-vous</h2>
            <form method="post" action="index.php?action=nouvelle_partie">
                <div class="champ-groupe">
                    <label class="champ-label" for="pseudo">Votre nom</label>
                    <input class="champ-input" type="text" id="pseudo" name="pseudo" placeholder="" maxlength="50" required autocomplete="off">
                </div>
                <button type="submit" class="btn btn-principal">Commencer la mission</button>
            </form>
            <div class="separator" style="margin:1.75rem auto 1.25rem;"></div>
            <p style="font-size:0.85rem;color:var(--muted);font-style:italic;text-align:center;">
                Vos choix auront des conséquences.<br>
                Blessure. Griffure. Preuve. Chaque décision compte.
            </p>
        </div>
    </div>
</div>
</body>
</html>
