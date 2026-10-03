<?php
require __DIR__ . '/../vendor/autoload.php';

use Kineo\Db;
use Kineo\PatientRepository;

session_start();
if (!isset($_SESSION['utilisateur'])) {
    header('Location: index.php');
    exit;
}

$recherche = $_GET['q'] ?? '';
$message = '';
$patients = [];
try {
    $depot = new PatientRepository(Db::connexion());
    $patients = $recherche !== ''
        ? $depot->rechercherParNom($recherche)
        : $depot->lister($_GET['tri'] ?? 'nom', $_GET['sens'] ?? 'asc');
} catch (PDOException $e) {
    $message = 'Erreur : ' . $e->getMessage();
}

function lienTri(string $colonne, string $libelle): string
{
    return '<a href="?tri=' . $colonne . '&amp;sens=asc">' . $libelle . ' ▲</a> '
         . '<a href="?tri=' . $colonne . '&amp;sens=desc">▼</a>';
}
?>
<!doctype html>
<html lang="fr">
<head><meta charset="utf-8"><title>KinéGest — patients</title></head>
<body>
  <p>Connecté : <?= htmlspecialchars($_SESSION['utilisateur']['nom']) ?></p>
  <form>
    <label>Rechercher un nom <input name="q" value="<?= htmlspecialchars($recherche) ?>"></label>
    <button>Chercher</button> <a href="patients.php">Tout afficher</a>
  </form>
  <p><?= htmlspecialchars($message) ?></p>
  <table border="1" cellpadding="4">
    <tr>
      <th><?= lienTri('nom', 'Nom') ?></th><th><?= lienTri('prenom', 'Prénom') ?></th>
      <th><?= lienTri('ville', 'Ville') ?></th><th><?= lienTri('dateNaissance', 'Naissance') ?></th>
    </tr>
    <?php foreach ($patients as $p): ?>
    <tr>
      <td><?= htmlspecialchars($p['nom']) ?></td><td><?= htmlspecialchars($p['prenom']) ?></td>
      <td><?= htmlspecialchars($p['ville']) ?></td><td><?= htmlspecialchars($p['dateNaissance'] ?? '') ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
</body>
</html>
