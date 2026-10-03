<?php
// Crée (ou recrée) la base de l'application : composer init-db
require __DIR__ . '/../vendor/autoload.php';

use Kineo\Db;
use Kineo\Schema;

$pdo = Db::connexion();
Schema::initialiser($pdo);
echo "Base initialisée (" . $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) . ").\n";
