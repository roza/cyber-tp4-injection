<?php
// Page de connexion — lancer : php -S localhost:8000 -t public
require __DIR__ . '/../vendor/autoload.php';

use Kineo\Db;
use Kineo\UtilisateurRepository;

session_start();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $depot = new UtilisateurRepository(Db::connexion());
        $utilisateur = $depot->authentifier($_POST['login'] ?? '', $_POST['mdp'] ?? '');
        if ($utilisateur !== null) {
            $_SESSION['utilisateur'] = $utilisateur;
            header('Location: patients.php');
            exit;
        }
        $message = 'Identifiant ou mot de passe incorrect.';
    } catch (PDOException $e) {
        $message = 'Erreur : ' . $e->getMessage();
    }
}
?>
<!doctype html>
<html lang="fr">
<head><meta charset="utf-8"><title>KinéGest — connexion</title></head>
<body>
  <h1>KinéGest</h1>
  <form method="post">
    <p><label>Identifiant <input name="login" required></label></p>
    <p><label>Mot de passe <input name="mdp" type="password" required></label></p>
    <p><button>Se connecter</button></p>
  </form>
  <p><?= htmlspecialchars($message) ?></p>
</body>
</html>
