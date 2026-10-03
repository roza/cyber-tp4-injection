package fr.kineo;

import java.sql.Connection;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.time.LocalDate;
import java.util.ArrayList;
import java.util.List;
import java.util.Optional;

public class PatientDao {

    private final Connection cnx;

    public PatientDao(Connection cnx) {
        this.cnx = cnx;
    }

    /** Patients dont le nom contient {@code texte}, triés par nom. */
    public List<Patient> rechercherParNom(String texte) throws SQLException {
        String sql = "SELECT id, nom, prenom, ville, dateNaissance FROM Patient "
                   + "WHERE nom LIKE '%" + texte + "%' ORDER BY nom";
        Statement st = cnx.createStatement();
        return lire(st.executeQuery(sql));
    }

    /**
     * Tous les patients, triés selon {@code colonne} ("nom", "prenom", "ville"
     * ou "dateNaissance") et {@code sens} ("asc" ou "desc"). Une colonne ou un
     * sens inconnu donne le tri par défaut : nom, croissant.
     */
    public List<Patient> listerTriePar(String colonne, String sens) throws SQLException {
        String sql = "SELECT id, nom, prenom, ville, dateNaissance FROM Patient ORDER BY "
                   + colonne + " " + sens;
        Statement st = cnx.createStatement();
        return lire(st.executeQuery(sql));
    }

    public Optional<Patient> trouverParId(int id) throws SQLException {
        String sql = "SELECT id, nom, prenom, ville, dateNaissance FROM Patient WHERE id = " + id;
        Statement st = cnx.createStatement();
        return lire(st.executeQuery(sql)).stream().findFirst();
    }

    private static List<Patient> lire(ResultSet rs) throws SQLException {
        List<Patient> patients = new ArrayList<>();
        while (rs.next()) {
            patients.add(new Patient(rs.getInt("id"), rs.getString("nom"), rs.getString("prenom"),
                    rs.getString("ville"), LocalDate.parse(rs.getString("dateNaissance"))));
        }
        return patients;
    }
}
