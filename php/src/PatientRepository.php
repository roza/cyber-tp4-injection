<?php

namespace Kineo;

use PDO;

final class PatientRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    /** Patients dont le nom contient $texte, triés par nom. */
    public function rechercherParNom(string $texte): array
    {
        $sql = "SELECT id, nom, prenom, ville FROM Patient WHERE nom LIKE '%" . $texte . "%' ORDER BY nom";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tous les patients, triés selon la colonne $tri ('nom', 'prenom', 'ville'
     * ou 'dateNaissance') et le sens $sens ('asc' ou 'desc'). Une colonne ou
     * un sens inconnu donne le tri par défaut : nom, croissant.
     */
    public function lister(string $tri = 'nom', string $sens = 'asc'): array
    {
        $sql = "SELECT id, nom, prenom, ville, dateNaissance FROM Patient ORDER BY " . $tri . " " . $sens;
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
