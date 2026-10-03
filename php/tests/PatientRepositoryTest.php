<?php

namespace Kineo\Tests;

use Kineo\PatientRepository;

final class PatientRepositoryTest extends BaseTestCase
{
    private function depot(): PatientRepository
    {
        return new PatientRepository($this->pdo);
    }

    public function testLaRechercheTrouveUnNom(): void
    {
        $resultat = $this->depot()->rechercherParNom('Mart');
        $this->assertCount(1, $resultat);
        $this->assertSame('Claire', $resultat[0]['prenom']);
    }

    /** Étape 3 — Awa N'Diaye doit pouvoir être retrouvée. */
    public function testLaRechercheTrouveUnNomAvecApostrophe(): void
    {
        $resultat = $this->depot()->rechercherParNom("N'Diaye");
        $this->assertCount(1, $resultat);
        $this->assertSame('Awa', $resultat[0]['prenom']);
    }

    public function testLaListeSeTriePourUneColonnePrevue(): void
    {
        $liste = $this->depot()->lister('dateNaissance', 'asc');
        $this->assertCount(8, $liste);
        $this->assertSame('Joao', $liste[0]['prenom'], 'Le plus âgé en premier');
    }

    public function testLaListeSeTrieEnSensDecroissant(): void
    {
        $this->assertSame('Petit', $this->depot()->lister('nom', 'desc')[0]['nom']);
    }

    /** Étape 4 — une colonne inconnue donne le tri par défaut, sans erreur. */
    public function testUneColonneDeTriInconnueDonneLeTriParNom(): void
    {
        $liste = $this->depot()->lister('age', 'asc');
        $this->assertCount(8, $liste);
        $this->assertSame('Bernard', $liste[0]['nom']);
    }

    /** Étape 4 — un sens inconnu donne le tri croissant, sans erreur. */
    public function testUnSensDeTriInconnuDonneLeTriCroissant(): void
    {
        $liste = $this->depot()->lister('nom', 'au hasard');
        $this->assertCount(8, $liste);
        $this->assertSame('Bernard', $liste[0]['nom']);
    }
}
