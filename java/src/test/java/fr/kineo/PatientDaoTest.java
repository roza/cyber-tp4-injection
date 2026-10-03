package fr.kineo;

import static org.junit.jupiter.api.Assertions.assertEquals;
import static org.junit.jupiter.api.Assertions.assertTrue;

import java.sql.Connection;
import java.sql.DriverManager;
import java.util.List;
import org.junit.jupiter.api.AfterEach;
import org.junit.jupiter.api.BeforeEach;
import org.junit.jupiter.api.DisplayName;
import org.junit.jupiter.api.Test;

/**
 * Chaque test repart d'une base neuve : SQLite en mémoire par défaut, ou la
 * base désignée par JDBC_URL (MariaDB / MySQL, voir LISEZMOI.md).
 */
class PatientDaoTest {

    private Connection cnx;
    private PatientDao dao;

    @BeforeEach
    void ouvrir() throws Exception {
        String url = System.getenv().getOrDefault("JDBC_URL", "jdbc:sqlite::memory:");
        cnx = DriverManager.getConnection(url, System.getenv("DB_USER"), System.getenv("DB_PASS"));
        Schema.initialiser(cnx);
        dao = new PatientDao(cnx);
    }

    @AfterEach
    void fermer() throws Exception {
        cnx.close();
    }

    @Test
    @DisplayName("La recherche trouve un nom")
    void rechercheSimple() throws Exception {
        List<Patient> resultat = dao.rechercherParNom("Mart");
        assertEquals(1, resultat.size());
        assertEquals("Claire", resultat.get(0).prenom());
    }

    @Test
    @DisplayName("Étape 6 — la recherche trouve un nom avec apostrophe")
    void rechercheAvecApostrophe() throws Exception {
        List<Patient> resultat = dao.rechercherParNom("N'Diaye");
        assertEquals(1, resultat.size(), "Awa N'Diaye doit pouvoir être retrouvée");
        assertEquals("Awa", resultat.get(0).prenom());
    }

    @Test
    @DisplayName("La liste se trie pour une colonne prévue")
    void triColonnePrevue() throws Exception {
        List<Patient> liste = dao.listerTriePar("dateNaissance", "asc");
        assertEquals(8, liste.size());
        assertEquals("Joao", liste.get(0).prenom(), "Le plus âgé en premier");
    }

    @Test
    @DisplayName("La liste se trie en sens décroissant")
    void triDecroissant() throws Exception {
        assertEquals("Petit", dao.listerTriePar("nom", "desc").get(0).nom());
    }

    @Test
    @DisplayName("Étape 6 — une colonne de tri inconnue donne le tri par nom")
    void colonneInconnue() throws Exception {
        List<Patient> liste = dao.listerTriePar("age", "asc");
        assertEquals(8, liste.size());
        assertEquals("Bernard", liste.get(0).nom());
    }

    @Test
    @DisplayName("Étape 6 — un sens de tri inconnu donne le tri croissant")
    void sensInconnu() throws Exception {
        List<Patient> liste = dao.listerTriePar("nom", "au hasard");
        assertEquals(8, liste.size());
        assertEquals("Bernard", liste.get(0).nom());
    }

    @Test
    @DisplayName("Un patient se retrouve par son identifiant")
    void parIdentifiant() throws Exception {
        assertTrue(dao.trouverParId(4).isPresent());
        assertEquals("N'Diaye", dao.trouverParId(4).get().nom());
        assertTrue(dao.trouverParId(999).isEmpty());
    }
}
