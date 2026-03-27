<?php

session_start();

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/JoueurController.php';
require_once __DIR__ . '/controllers/JeuController.php';

$action = $_GET['action'] ?? 'accueil';

switch ($action) {
    case 'accueil':
        JoueurController::accueil();
        break;
    case 'nouvelle_partie':
        JoueurController::nouvellePartie();
        break;
    case 'jouer':
        JeuController::jouer();
        break;
    case 'choisir':
        JeuController::choisir();
        break;
    case 'fin':
        JeuController::fin();
        break;
    default:
        JoueurController::accueil();
}
