-- Base KinéGest (version simplifiée) — SQLite
DROP TABLE IF EXISTS Patient;
DROP TABLE IF EXISTS Utilisateur;

CREATE TABLE Utilisateur (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    login       VARCHAR(50)  NOT NULL UNIQUE,
    nom         VARCHAR(80)  NOT NULL,
    motDePasse  VARCHAR(255) NOT NULL  -- empreinte bcrypt
);

CREATE TABLE Patient (
    id             INTEGER PRIMARY KEY AUTOINCREMENT,
    nom            VARCHAR(80) NOT NULL,
    prenom         VARCHAR(80) NOT NULL,
    ville          VARCHAR(80) NOT NULL,
    dateNaissance  DATE        NOT NULL
);
