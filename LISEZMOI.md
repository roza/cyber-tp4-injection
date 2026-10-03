# TP4 — Sécuriser l'accès aux données

**BTS SIO 2ᵉ année · option SLAM · Bloc 3 — cybersécurité**

Une version simplifiée de **KinéGest**, le logiciel du cabinet Kinéo, en deux
variantes : une application web en **PHP / PDO** et un DAO en **Java / JDBC**.
Toutes deux fonctionnent avec **SQLite** (rien à installer) ou **MariaDB /
MySQL**.

Le code fonctionne… presque : certains patients restent introuvables, un
utilisateur ne peut pas se connecter, certains tris plantent. Ces bugs ont tous
la même cause, une **faille de sécurité**. Votre travail : la trouver, la
corriger et le prouver par les tests. L'énoncé complet est dans le PDF du TP.

## 1. Récupérer le projet

1. Sur la page de ce dépôt : **Use this template** › **Create a new repository**.
2. Gardez le nom `cyber-tp4-injection`, cochez **Private**.
3. **Settings** › **Collaborators** : ajoutez `roza`.
4. Clonez **votre** dépôt et ouvrez le **dossier** dans VS Code.

## 2. La partie PHP

Prérequis : PHP 8.2 ou plus, avec `pdo_sqlite` (et `pdo_mysql` pour MariaDB),
et Composer. Vérifiez avec `php -m`.

```sh
cd php
composer install          # installe PHPUnit
composer init-db          # crée la base SQLite var/kinegest.sqlite
php -S localhost:8000 -t public
```

Ouvrez <http://localhost:8000>. Comptes de test :

| Identifiant | Mot de passe |
|---|---|
| `a.martin` | `Genou-42` |
| `s.leroy` | `Cheville-7` |
| `m.d'almeida` | `Epaule-17` |

Les tests :

```sh
vendor/bin/phpunit
```

## 3. La partie Java

Prérequis : JDK 17 ou plus, et Maven (ou VS Code avec l'extension Java).

```sh
cd java
mvn test                                          # les tests
mvn -q compile exec:java -Dexec.args="init"       # crée kinegest.sqlite
mvn -q exec:java -Dexec.args="lister ville desc"
mvn -q exec:java -Dexec.args="rechercher Mart"
```

## 4. Sans rien installer : GitHub Codespaces

Depuis la page de **votre** dépôt : **Code** › **Codespaces** › **Create
codespace on main**. PHP, Composer, Java et Maven y sont installés, et la base
SQLite est créée au premier lancement. Pour l'application web :
`cd php && php -S 0.0.0.0:8000 -t public`, puis ouvrez le port 8000 proposé.

*Pensez à arrêter le Codespace en fin de séance : le quota gratuit est mensuel.*

## 5. Avec MariaDB ou MySQL (facultatif)

Créez la base et un compte dédié à l'application (jamais `root`) :

```sql
CREATE DATABASE kinegest CHARACTER SET utf8mb4;
CREATE USER 'kinegest_app'@'localhost' IDENTIFIED BY 'un-mot-de-passe-solide';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP ON kinegest.* TO 'kinegest_app'@'localhost';
```

*`CREATE` et `DROP` ne servent qu'à (ré)initialiser la base pendant le TP. En
production, l'application n'aurait que `SELECT, INSERT, UPDATE, DELETE` :
c'est le **moindre privilège**, que nous verrons en séance 9.*

Puis indiquez la base par des variables d'environnement, **dans le terminal où
vous lancez les commandes** :

| | Linux, macOS, Git Bash | PowerShell |
|---|---|---|
| PHP | `export DB_DSN="mysql:host=127.0.0.1;dbname=kinegest;charset=utf8mb4"` | `$env:DB_DSN="mysql:host=127.0.0.1;dbname=kinegest;charset=utf8mb4"` |
| Java | `export JDBC_URL="jdbc:mariadb://127.0.0.1:3306/kinegest"` | `$env:JDBC_URL="jdbc:mariadb://127.0.0.1:3306/kinegest"` |
| Les deux | `export DB_USER=kinegest_app DB_PASS=...` | `$env:DB_USER="kinegest_app"; $env:DB_PASS="..."` |

Les mêmes commandes (`composer init-db`, `vendor/bin/phpunit`, `mvn test`)
travaillent alors sur MariaDB.

## 6. L'intégration continue

À chaque `git push`, GitHub lance tous les tests, sur SQLite **et** sur MariaDB
(onglet **Actions**). **Au départ, plusieurs tests sont rouges : c'est normal.**
Le TP est terminé quand tout est vert.

## Organisation

```
sql/        schémas SQLite et MariaDB, données de test communes
php/src/    Db, Schema, UtilisateurRepository, PatientRepository
php/public/ les pages : connexion, liste des patients
php/tests/  tests PHPUnit
java/       DAO JDBC, client en ligne de commande, tests JUnit
```

`Schema` (PHP et Java) n'exécute que les scripts du dossier `sql/` : il n'est
pas à corriger.
