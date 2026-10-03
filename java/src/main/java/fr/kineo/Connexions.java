package fr.kineo;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;

/**
 * Connexion JDBC, configurée par variables d'environnement :
 *   JDBC_URL   par défaut : SQLite, fichier kinegest.sqlite
 *              MariaDB    : jdbc:mariadb://127.0.0.1:3306/kinegest
 *   DB_USER, DB_PASS (inutiles avec SQLite)
 */
public final class Connexions {

    private Connexions() {
    }

    public static Connection ouvrir() throws SQLException {
        String url = System.getenv().getOrDefault("JDBC_URL", "jdbc:sqlite:kinegest.sqlite");
        return DriverManager.getConnection(url, System.getenv("DB_USER"), System.getenv("DB_PASS"));
    }
}
