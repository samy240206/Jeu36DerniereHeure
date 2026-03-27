<?php
// controllers/JoueurController.php

require_once __DIR__ . '/../models/Joueur.php';
require_once __DIR__ . '/../models/Page.php';

class JoueurController {

    /** Affiche la page d'accueil */
    public static function accueil(): void {
        require __DIR__ . '/../views/accueil.php';
    }

    /** Crée une nouvelle partie */
    public static function nouvellePartie(): void {
        $pseudo = trim($_POST['pseudo'] ?? '');

        if (empty($pseudo)) {
            $_SESSION['erreur'] = 'Veuillez saisir un pseudo.';
            header('Location: index.php?action=accueil');
            exit;
        }

        
        $pseudo = htmlspecialchars($pseudo, ENT_QUOTES, 'UTF-8');
        if (mb_strlen($pseudo) > 50) {
            $pseudo = mb_substr($pseudo, 0, 50);
        }

        // Crée le joueur et initialise la session
        $joueurId = Joueur::creer($pseudo);
        $_SESSION['joueur_id'] = $joueurId;
        $_SESSION['pseudo']    = $pseudo;

        // Enregistre la progression sur la page de départ
        Page::enregistrerProgression($joueurId, 1);

        // Redirige vers le jeu
        header('Location: index.php?action=jouer');
        exit;
    }
}
