package fr.kineo;

import java.time.LocalDate;

public record Patient(int id, String nom, String prenom, String ville, LocalDate dateNaissance) {
}
