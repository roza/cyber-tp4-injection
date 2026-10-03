<?php

namespace Kineo\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Revue de code automatique : les dépôts n'exécutent que des requêtes
 * préparées. Faire passer les autres tests en « échappant » la saisie ne
 * suffit pas : échapper n'est pas paramétrer.
 */
final class RevueCodeTest extends TestCase
{
    public static function depots(): array
    {
        return [
            'UtilisateurRepository' => ['UtilisateurRepository.php'],
            'PatientRepository' => ['PatientRepository.php'],
        ];
    }

    #[DataProvider('depots')]
    public function testLeDepotNExecuteQueDesRequetesPreparees(string $fichier): void
    {
        $code = file_get_contents(__DIR__ . '/../src/' . $fichier);
        $this->assertDoesNotMatchRegularExpression(
            '/->(query|exec)\s*\(/',
            $code,
            "$fichier : utiliser prepare() puis execute(), pas query() ni exec()"
        );
        $this->assertDoesNotMatchRegularExpression(
            '/(addslashes|real_escape_string|->quote)\s*\(/',
            $code,
            "$fichier : échapper n'est pas paramétrer"
        );
    }
}
