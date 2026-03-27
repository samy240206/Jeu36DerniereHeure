<?php
class Joueur {
    public static function creer(string $pseudo): int {
        $pdo = getDB();
        $stmt = $pdo->prepare('INSERT INTO joueur (pseudo, points_de_vie, sante_mentale, score, blessure, griffure, preuve) VALUES (?, 100, 100, 0, 0, 0, 0)');
        $stmt->execute([$pseudo]);
        return (int) $pdo->lastInsertId();
    }
    public static function trouver(int $id): ?array {
        $pdo = getDB();
        $stmt = $pdo->prepare('SELECT * FROM joueur WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
    public static function mettreAJourStats(int $id, int $degatVie, int $degatMental, int $bonusScore = 10): void {
        $pdo = getDB();
        $stmt = $pdo->prepare('UPDATE joueur SET points_de_vie = GREATEST(0, points_de_vie + ?), sante_mentale = GREATEST(0, sante_mentale + ?), score = score + ? WHERE id = ?');
        $stmt->execute([$degatVie, $degatMental, $bonusScore, $id]);
    }
    public static function activerFlag(int $id, string $flag): void {
        $flags = ['blessure', 'griffure', 'preuve'];
        if (!in_array($flag, $flags, true)) return;
        $pdo = getDB();
        $stmt = $pdo->prepare("UPDATE joueur SET $flag = 1 WHERE id = ?");
        $stmt->execute([$id]);
    }
    public static function ajouterObjet(int $joueurId, int $objetId): void {
        $pdo = getDB();
        $check = $pdo->prepare('SELECT id FROM inventaire WHERE joueur_id = ? AND objet_id = ?');
        $check->execute([$joueurId, $objetId]);
        if (!$check->fetch()) {
            $stmt = $pdo->prepare('INSERT INTO inventaire (joueur_id, objet_id) VALUES (?, ?)');
            $stmt->execute([$joueurId, $objetId]);
        }
    }
    public static function inventaire(int $joueurId): array {
        $pdo = getDB();
        $stmt = $pdo->prepare('SELECT o.* FROM objet o JOIN inventaire i ON i.objet_id = o.id WHERE i.joueur_id = ? ORDER BY i.date_collecte ASC');
        $stmt->execute([$joueurId]);
        return $stmt->fetchAll();
    }
    public static function possedeObjet(int $joueurId, string $nomObjet): bool {
        $pdo = getDB();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM inventaire i JOIN objet o ON o.id = i.objet_id WHERE i.joueur_id = ? AND o.nom = ?');
        $stmt->execute([$joueurId, $nomObjet]);
        return (bool) $stmt->fetchColumn();
    }
}
