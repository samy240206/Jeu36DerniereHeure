<?php
require_once __DIR__ . '/../models/Joueur.php';
require_once __DIR__ . '/../models/Page.php';

class JeuController {
    public static function jouer(): void {
        self::verifierSession();
        $joueurId = $_SESSION['joueur_id'];
        $joueur = Joueur::trouver($joueurId);
        $page   = Page::pageActuelle($joueurId);
        if (!$joueur || !$page) { header('Location: index.php?action=accueil'); exit; }
        if ($joueur['points_de_vie'] <= 0 || $joueur['sante_mentale'] <= 0) { header('Location: index.php?action=fin&raison=mort'); exit; }
        if ($page['est_fin']) { header('Location: index.php?action=fin&raison=' . urlencode($page['type_fin']) . '&page_id=' . $page['id']); exit; }

        // Activation des flags selon le contenu de la page
        $contenu = $page['contenu'];
        if (strpos($contenu, 'BLESSURE = OUI') !== false) Joueur::activerFlag($joueurId, 'blessure');
        if (strpos($contenu, 'GRIFFURE = OUI') !== false) Joueur::activerFlag($joueurId, 'griffure');
        if (strpos($contenu, 'PREUVE = OUI')   !== false) Joueur::activerFlag($joueurId, 'preuve');

        // Objets à ramasser selon la page
        $objetsParPage = [
            1  => ['Couteau de cuisine', 'Sac à dos', 'Téléphone'],
            14 => ['Trousse médicale', 'Lampe torche', 'Carte PREUVE DELTA'],
            21 => ['Revolver chargé'],
            22 => ['Procédure Delta'],
            27 => ['Rations militaires', 'Ruban adhésif'],
            43 => ['Gilet pare-balles', 'Rations militaires', 'Procédure Delta'],
            83 => ['Couverture légère'],
        ];
        $objetsTrouves = [];
        if (isset($objetsParPage[$page['id']])) {
            $pdo = getDB();
            foreach ($objetsParPage[$page['id']] as $nomObjet) {
                $stmt = $pdo->prepare('SELECT * FROM objet WHERE nom = ?');
                $stmt->execute([$nomObjet]);
                $obj = $stmt->fetch();
                if ($obj) {
                    Joueur::ajouterObjet($joueurId, $obj['id']);
                    $objetsTrouves[] = $obj;
                }
            }
        }

        // Filtrage des choix selon les flags et l'inventaire
        $tousChoix  = Page::choix($page['id']);
        $inventaire = Joueur::inventaire($joueurId);
        $nomsObjets = array_column($inventaire, 'nom');
        $joueur     = Joueur::trouver($joueurId); // refresh après flags

        $choixDispo = [];
        foreach ($tousChoix as $choix) {
            // Filtre conditionnel sur objet
            if ($choix['condition_objet'] !== null && !in_array($choix['condition_objet'], $nomsObjets, true)) continue;

            // Page 72 : brancher selon griffure
            if ($page['id'] == 72) {
                if ($joueur['griffure'] && $choix['page_destination_id'] == 74) continue;
                if (!$joueur['griffure'] && $choix['page_destination_id'] == 75) continue;
            }
            // Page 74 : montrer preuve seulement si PREUVE=OUI
            if ($page['id'] == 74 && $choix['page_destination_id'] == 78 && !$joueur['preuve']) continue;
            // Page 96 : brancher selon blessure
            if ($page['id'] == 96) {
                if ($joueur['blessure'] && $choix['page_destination_id'] == 100) continue;
                if (!$joueur['blessure'] && $choix['page_destination_id'] == 99)  continue;
            }
            $choixDispo[] = $choix;
        }

        require __DIR__ . '/../views/pages/jeu.php';
    }

    public static function choisir(): void {
        self::verifierSession();
        $joueurId = $_SESSION['joueur_id'];
        $choixId  = (int)($_POST['choix_id'] ?? 0);
        if ($choixId <= 0) { header('Location: index.php?action=jouer'); exit; }
        $pdo = getDB();
        $pageCourante = Page::pageActuelle($joueurId);
        if (!$pageCourante) { header('Location: index.php?action=accueil'); exit; }
        $stmt = $pdo->prepare('SELECT * FROM choix WHERE id = ? AND page_source_id = ?');
        $stmt->execute([$choixId, $pageCourante['id']]);
        $choix = $stmt->fetch();
        if (!$choix) { header('Location: index.php?action=jouer'); exit; }
        Joueur::mettreAJourStats($joueurId, (int)$choix['degat_vie'], (int)$choix['degat_mental'], 10);
        Page::enregistrerProgression($joueurId, (int)$choix['page_destination_id']);
        header('Location: index.php?action=jouer');
        exit;
    }

    public static function fin(): void {
        self::verifierSession();
        $joueurId   = $_SESSION['joueur_id'];
        $joueur     = Joueur::trouver($joueurId);
        $raison     = $_GET['raison'] ?? 'defaite';
        $pageId     = (int)($_GET['page_id'] ?? 0);
        $inventaire = Joueur::inventaire($joueurId);
        require __DIR__ . '/../views/pages/fin.php';
    }

    private static function verifierSession(): void {
        if (empty($_SESSION['joueur_id'])) { header('Location: index.php?action=accueil'); exit; }
    }
}
