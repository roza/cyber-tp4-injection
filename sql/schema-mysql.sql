-- Base KinéGest (version simplifiée) — MariaDB / MySQL
DROP TABLE IF EXISTS Patient;
DROP TABLE IF EXISTS Utilisateur;

CREATE TABLE Utilisateur (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    login       VARCHAR(50)  NOT NULL UNIQUE,
    nom         VARCHAR(80)  NOT NULL,
    motDePasse  VARCHAR(255) NOT NULL  -- empreinte bcrypt
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE Patient (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    nom            VARCHAR(80) NOT NULL,
    prenom         VARCHAR(80) NOT NULL,
    ville          VARCHAR(80) NOT NULL,
    dateNaissance  DATE        NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
