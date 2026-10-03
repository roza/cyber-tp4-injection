<?php

namespace Kineo;

use PDO;

/**
 * (Ré)initialise la base à partir des scripts de ../sql : le schéma propre au
 * SGBD (SQLite ou MariaDB/MySQL), puis les données de test communes.
 * Ce fichier n'exécute que des scripts du dépôt : il n'est pas à corriger.
 */
final class Schema
{
    public static function initialiser(PDO $pdo): void
    {
        $sgbd = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite' ? 'sqlite' : 'mysql';
        $dossier = __DIR__ . '/../../sql';
        foreach (["$dossier/schema-$sgbd.sql", "$dossier/donnees.sql"] as $fichier) {
            foreach (preg_split('/;\s*\n/', file_get_contents($fichier)) as $instruction) {
                if (trim(preg_replace('/^\s*--.*$/m', '', $instruction)) !== '') {
                    $pdo->exec($instruction);
                }
            }
        }
    }
}
