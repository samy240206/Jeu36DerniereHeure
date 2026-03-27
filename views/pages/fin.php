<?php
$estVictoire = ($raison === 'victoire');
$classFin = match($raison) { 'victoire' => 'fin-victoire', 'defaite' => 'fin-defaite', default => 'fin-neutre' };

$titresFin = [
    76 => ['🟢', 'Survivant Intègre', 'Vous avez traversé l\'enfer avec votre humanité intacte. Les portes de Delta s\'ouvrent.'],
    77 => ['🟡', 'Citoyen Surveillé', 'Vivant, mais sous surveillance. Delta vous a accepté… avec méfiance.'],
    78 => ['⭐', 'Vétéran Reconnu', 'Votre preuve a parlé pour vous. Vous entrez comme quelqu\'un qui a vu des choses.'],
    79 => ['🔵', 'Le Suspect', 'Quarantaine. Une chaise blanche. Une vitre. Vous attendez de savoir qui vous serez demain.'],
    73 => ['💀', 'Abattu au portail', 'Votre refus a tout décidé. Le monde ne vous a pas laissé de deuxième chance.'],
    80 => ['💀', 'Panique fatale', 'Un geste de trop. Un tir. Le monde s\'est éteint en une fraction de seconde.'],
    95 => ['🚪', 'L\'Exilé', 'Delta vous a rejeté. Vous êtes vivant, mais seul face au monde.'],
    99 => ['🕊️', 'Le Sacrifié', 'Vous avez tenu la porte. La base est sauvée. Vous, non.'],
    101 => ['👤', 'L\'Anonyme', 'Vous survivez. Sans titre, sans rôle. Un visage parmi d\'autres.'],
    102 => ['❤️', 'L\'Humanité', 'Vous avez ouvert. Le chaos, puis la vie. Delta survit grâce à vous.'],
    103 => ['🏰', 'La Forteresse', 'Delta devient une forteresse froide et efficace. Vous en avez fermé les portes.'],
    104 => ['🔨', 'Reconstruction', 'Pas un héros. Un point fixe dans un monde brisé. La reconstruction commence.'],
];

$pageId = (int)($_GET['page_id'] ?? 0);
$info   = $titresFin[$pageId] ?? ($raison === 'defaite' ? ['💀', 'Fin', 'Votre histoire s\'achève ici.'] : ['✅', 'Fin', 'Vous avez survécu.']);
if ($raison === 'mort') $info = ['💀', 'Corps et âme brisés', 'Vos forces vous ont abandonné. Le monde vous a réclamé.'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($info[1]) ?> — Les 36 Dernières Heures</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body class="<?= $classFin ?>">
<div class="conteneur" style="max-width:580px;padding-top:3rem;">
    <div class="carte anime-entree" style="text-align:center;">
        <p style="font-size:2.5rem;margin-bottom:0.75rem;"><?= $info[0] ?></p>
        <h1><?= htmlspecialchars($info[1]) ?></h1>
        <div class="separator" style="margin:1.5rem auto;"></div>
        <p style="font-style:italic;margin-bottom:2rem;"><?= htmlspecialchars($info[2]) ?></p>

        <div class="stats-finales">
            <div class="stat-finale-bloc">
                <span class="stat-finale-chiffre"><?= (int)$joueur['score'] ?></span>
                <span class="stat-finale-label">Score</span>
            </div>
            <div class="stat-finale-bloc">
                <span class="stat-finale-chiffre"><?= max(0,(int)$joueur['points_de_vie']) ?></span>
                <span class="stat-finale-label">Vie restante</span>
            </div>
            <div class="stat-finale-bloc">
                <span class="stat-finale-chiffre"><?= max(0,(int)$joueur['sante_mentale']) ?></span>
                <span class="stat-finale-label">Mental</span>
            </div>
            <div class="stat-finale-bloc">
                <span class="stat-finale-chiffre"><?= count($inventaire) ?></span>
                <span class="stat-finale-label">Objets</span>
            </div>
        </div>

        <!-- Flags atteints -->
        <div style="display:flex;gap:0.5rem;justify-content:center;flex-wrap:wrap;margin-bottom:1.5rem;">
            <span class="flag <?= $joueur['blessure'] ? 'flag-on' : 'flag-off' ?>">Blessure : <?= $joueur['blessure'] ? 'OUI' : 'NON' ?></span>
            <span class="flag <?= $joueur['griffure'] ? 'flag-on' : 'flag-off' ?>">Griffure : <?= $joueur['griffure'] ? 'OUI' : 'NON' ?></span>
            <span class="flag <?= $joueur['preuve'] ? 'flag-on vert' : 'flag-off' ?>">Preuve : <?= $joueur['preuve'] ? 'OUI' : 'NON' ?></span>
        </div>

        <?php if (!empty($inventaire)): ?>
        <div class="inventaire-section" style="text-align:left;">
            <div class="inventaire-titre">Équipement collecté</div>
            <ul class="inventaire-liste">
                <?php foreach ($inventaire as $obj): ?>
                <li class="objet-badge"><?= htmlspecialchars($obj['nom']) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <div class="separator" style="margin:2rem auto;"></div>
        <a href="index.php?action=accueil" class="btn btn-principal" style="display:inline-block;width:auto;text-decoration:none;">
            Nouvelle partie
        </a>
    </div>
</div>
<script>history.replaceState(null,'','index.php?action=fin');</script>
</body>
</html>
