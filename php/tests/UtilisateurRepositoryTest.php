<?php

namespace Kineo\Tests;

use Kineo\UtilisateurRepository;

final class UtilisateurRepositoryTest extends BaseTestCase
{
    private function depot(): UtilisateurRepository
    {
        return new UtilisateurRepository($this->pdo);
    }

    public function testUnUtilisateurSeConnecteAvecSonMotDePasse(): void
    {
        $u = $this->depot()->authentifier('a.martin', 'Genou-42');
        $this->assertSame('Anne Martin', $u['nom']);
        $this->assertArrayNotHasKey('motDePasse', $u, "L'empreinte ne doit pas sortir du dépôt");
    }

    public function testUnMauvaisMotDePasseEstRefuse(): void
    {
        $this->assertNull($this->depot()->authentifier('a.martin', 'Cheville-7'));
    }

    public function testUnLoginInconnuEstRefuse(): void
    {
        $this->assertNull($this->depot()->authentifier('inconnu', 'Genou-42'));
    }

    /** Étape 2 — un login avec une apostrophe est un login comme un autre. */
    public function testUnLoginAvecApostropheSeConnecte(): void
    {
        $u = $this->depot()->authentifier("m.d'almeida", 'Epaule-17');
        $this->assertNotNull($u, "Manuel D'Almeida doit pouvoir se connecter");
        $this->assertSame("Manuel D'Almeida", $u['nom']);
    }
}
