package fr.kineo;

import static org.junit.jupiter.api.Assertions.assertFalse;

import java.nio.file.Files;
import java.nio.file.Path;
import java.util.regex.Pattern;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;

/**
 * Revue de code automatique : le DAO n'exécute que des requêtes préparées,
 * et ne concatène jamais de valeur dans prepareStatement(...).
 */
class RevueCodeTest {

    private static final Path DAO = Path.of("src/main/java/fr/kineo/PatientDao.java");

    @Test
    @DisplayName("Étape 6 — le DAO n'utilise pas de Statement")
    void pasDeStatement() throws Exception {
        assertFalse(Files.readString(DAO).contains("createStatement("),
                "PatientDao : utiliser prepareStatement(...) avec des ?, pas createStatement()");
    }

    @Test
    @DisplayName("Étape 6 — aucune concaténation dans prepareStatement(...)")
    void pasDeConcatenationDansPrepare() throws Exception {
        assertFalse(Pattern.compile("prepareStatement\\([^\\n]*\"\\s*\\+").matcher(Files.readString(DAO)).find(),
                "PatientDao : ce qui protège, c'est le ?, pas le nom de la méthode");
    }
}
