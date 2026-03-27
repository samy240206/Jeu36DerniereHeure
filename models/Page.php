<?php
// models/Page.php

class Page {

    /** Retourne une page par son ID */
    public static function trouver(int $id): ?array {
        $pdo = getDB();
        $stmt = $pdo->prepare('SELECT * FROM page WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    /** Retourne les choix disponibles depuis une page */
    public static function choix(int $pageId): array {
        $pdo = getDB();
        $stmt = $pdo->prepare(
            'SELECT * FROM choix WHERE page_source_id = ? ORDER BY id ASC'
        );
        $stmt->execute([$pageId]);
        return $stmt->fetchAll();
    }

    /** Retourne la page de départ (id = 1) */
    public static function pageDepart(): ?array {
        return self::trouver(1);
    }

    /** Enregistre ou met à jour la progression du joueur */
    public static function enregistrerProgression(int $joueurId, int $pageId): void {
        $pdo = getDB();
        // Supprime l'ancienne progression et insère la nouvelle
        $del = $pdo->prepare('DELETE FROM progression WHERE joueur_id = ?');
        $del->execute([$joueurId]);

        $ins = $pdo->prepare(
            'INSERT INTO progression (joueur_id, page_actuelle_id) VALUES (?, ?)'
        );
        $ins->execute([$joueurId, $pageId]);
    }

    /** Récupère la page actuelle d'un joueur */
    public static function pageActuelle(int $joueurId): ?array {
        $pdo = getDB();
        $stmt = $pdo->prepare(
            'SELECT p.* FROM page p
             JOIN progression pr ON pr.page_actuelle_id = p.id
             WHERE pr.joueur_id = ?'
        );
        $stmt->execute([$joueurId]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Recherche dans la page les objets à ramasser automatiquement.
     * Convention : un objet est ramassable si son nom apparaît dans le contenu de la page.
     * Retourne le tableau des objets trouvés.
     */
    public static function objetsTrouvesDansPage(int $pageId): array {
        $pdo = getDB();
        $page = self::trouver($pageId);
        if (!$page) return [];

        $objets = $pdo->query('SELECT * FROM objet')->fetchAll();
        $trouves = [];
        foreach ($objets as $objet) {
            if (stripos($page['contenu'], $objet['nom']) !== false) {
                $trouves[] = $objet;
            }
        }
        return $trouves;
    }
}
