<?php
function iconeObjet(string $type): string {
    return match($type) { 'arme' => '🔫', 'cle' => '🗝️', 'document' => '📋', default => '🎒' };
}
$inventaire = Joueur::inventaire($joueur['id']);
$viePercent = max(0, min(100, $joueur['points_de_vie']));
$mentalPct  = max(0, min(100, $joueur['sante_mentale']));
$acteNoms   = [1 => 'Acte 1 — L\'immeuble', 2 => 'Acte 2 — La ville qui s\'éteint', 3 => 'Acte 3 — La traversée', 4 => 'Acte 4 — La base'];
$acteNom    = $acteNoms[$page['acte']] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>§<?= $page['id'] ?> — <?= htmlspecialchars($page['titre']) ?></title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
<div class="conteneur">
    <!-- Stats -->
    <div class="stats-barre anime-entree">
        <div class="stat-item">
            <div class="stat-nom">Vie</div>
            <div class="stat-barre-fond"><div class="stat-barre-rempli barre-vie" style="width:<?= $viePercent ?>%"></div></div>
            <div class="stat-valeur"><?= $viePercent ?>/100</div>
        </div>
        <div class="stat-item">
            <div class="stat-nom">Mental</div>
            <div class="stat-barre-fond"><div class="stat-barre-rempli barre-mental" style="width:<?= $mentalPct ?>%"></div></div>
            <div class="stat-valeur"><?= $mentalPct ?>/100</div>
        </div>
        <div class="score-valeur">⬡ <?= $joueur['score'] ?></div>
    </div>

    <!-- Flags -->
    <div class="flags-barre anime-entree">
        <span class="flag <?= $joueur['blessure'] ? 'flag-on' : 'flag-off' ?>">⚠ Blessure : <?= $joueur['blessure'] ? 'OUI' : 'NON' ?></span>
        <span class="flag <?= $joueur['griffure'] ? 'flag-on' : 'flag-off' ?>">⚠ Griffure : <?= $joueur['griffure'] ? 'OUI' : 'NON' ?></span>
        <span class="flag <?= $joueur['preuve'] ? 'flag-on vert' : 'flag-off' ?>">✓ Preuve : <?= $joueur['preuve'] ? 'OUI' : 'NON' ?></span>
    </div>

    <!-- Carte principale -->
    <div class="carte anime-entree">
        <div class="badge-acte"><?= htmlspecialchars($acteNom) ?> — §<?= $page['id'] ?></div>
        <h2><?= htmlspecialchars($page['titre']) ?></h2>

        <?php
        $paragraphes = array_filter(explode("\n", $page['contenu']));
        foreach ($paragraphes as $para):
            $para = trim($para);
            if ($para === '') continue;
            // Masquer les lignes techniques de flags
            if (strpos($para, '= OUI') !== false || strpos($para, '= NON') !== false) continue;
        ?>
        <p><?= nl2br(htmlspecialchars($para)) ?></p>
        <?php endforeach; ?>

        <?php if (!empty($objetsTrouves)): ?>
        <div style="margin:1.25rem 0;padding:0.75rem 1rem;background:rgba(184,150,12,0.07);border:1px solid rgba(184,150,12,0.2);">
            <?php foreach ($objetsTrouves as $obj): ?>
            <p style="color:var(--or);font-size:0.9rem;margin:0.25rem 0;">
                <?= iconeObjet($obj['type']) ?> <strong><?= htmlspecialchars($obj['nom']) ?></strong> ajouté à votre équipement.
            </p>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="separator" style="margin:1.5rem 0;"></div>

        <?php if (empty($choixDispo)): ?>
        <p style="font-style:italic;color:var(--muted);">Il n'y a plus rien à faire ici…</p>
        <?php else: ?>
        <form method="post" action="index.php?action=choisir" id="formChoix">
            <input type="hidden" name="choix_id" id="choix_id" value="">
            <?php foreach ($choixDispo as $choix): ?>
            <button type="button" class="btn-choix" onclick="choisir(<?= (int)$choix['id'] ?>)">
                <?= htmlspecialchars($choix['texte_choix']) ?>
            </button>
            <?php endforeach; ?>
        </form>
        <?php endif; ?>

        <?php if (!empty($inventaire)): ?>
        <div class="inventaire-section">
            <div class="inventaire-titre">Équipement</div>
            <ul class="inventaire-liste">
                <?php foreach ($inventaire as $obj): ?>
                <li class="objet-badge" title="<?= htmlspecialchars($obj['description']) ?>">
                    <span><?= iconeObjet($obj['type']) ?></span> <?= htmlspecialchars($obj['nom']) ?>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>

    <p style="text-align:center;margin-top:1.25rem;font-size:0.75rem;color:var(--muted);font-family:'Barlow Condensed',sans-serif;letter-spacing:0.1em;">
        <?= htmlspecialchars($_SESSION['pseudo']) ?> — LES 36 DERNIÈRES HEURES
    </p>
</div>

<script>
history.pushState(null, '', location.href);
window.addEventListener('popstate', () => { history.pushState(null, '', location.href); });
function choisir(id) {
    document.getElementById('choix_id').value = id;
    document.getElementById('formChoix').submit();
}
</script>
</body>
</html>
