package fr.kineo;

import java.sql.Connection;

/**
 * Petit client en ligne de commande :
 *   mvn -q compile exec:java -Dexec.args="init"
 *   mvn -q compile exec:java -Dexec.args="lister ville desc"
 *   mvn -q compile exec:java -Dexec.args="rechercher Mart"
 */
public class App {

    public static void main(String[] args) throws Exception {
        try (Connection cnx = Connexions.ouvrir()) {
            PatientDao dao = new PatientDao(cnx);
            String commande = args.length > 0 ? args[0] : "lister";
            switch (commande) {
                case "init" -> {
                    Schema.initialiser(cnx);
                    System.out.println("Base initialisée.");
                }
                case "rechercher" -> dao.rechercherParNom(args.length > 1 ? args[1] : "")
                        .forEach(System.out::println);
                default -> dao.listerTriePar(args.length > 1 ? args[1] : "nom", args.length > 2 ? args[2] : "asc")
                        .forEach(System.out::println);
            }
        }
    }
}
