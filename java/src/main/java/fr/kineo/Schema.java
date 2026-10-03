package fr.kineo;

import java.io.IOException;
import java.nio.file.Files;
import java.nio.file.Path;
import java.sql.Connection;
import java.sql.SQLException;
import java.sql.Statement;

/**
 * (Ré)initialise la base à partir des scripts du dossier sql/ du dépôt.
 * N'exécute que des scripts du dépôt : ce fichier n'est pas à corriger.
 */
public final class Schema {

    private Schema() {
    }

    public static void initialiser(Connection cnx) throws SQLException, IOException {
        String produit = cnx.getMetaData().getDatabaseProductName().toLowerCase();
        String sgbd = produit.contains("sqlite") ? "sqlite" : "mysql";
        Path dossier = dossierSql();
        for (Path fichier : new Path[] {dossier.resolve("schema-" + sgbd + ".sql"), dossier.resolve("donnees.sql")}) {
            try (Statement st = cnx.createStatement()) {
                for (String instruction : Files.readString(fichier).split(";\\s*\\n")) {
                    if (!instruction.replaceAll("(?m)^\\s*--.*$", "").isBlank()) {
                        st.execute(instruction);
                    }
                }
            }
        }
    }

    /** Le dossier sql/ du dépôt, cherché depuis le répertoire courant. */
    private static Path dossierSql() {
        for (Path p = Path.of("").toAbsolutePath(); p != null; p = p.getParent()) {
            if (Files.exists(p.resolve("sql/donnees.sql"))) {
                return p.resolve("sql");
            }
        }
        throw new IllegalStateException("Dossier sql/ du dépôt introuvable");
    }
}
