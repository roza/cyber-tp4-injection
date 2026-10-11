# TP4 — Sécuriser l'accès aux données

**BTS SIO 2ᵉ année · option SLAM · Bloc 3 — cybersécurité**

Une version simplifiée de **KinéGest**, le logiciel du cabinet Kinéo, en deux
variantes : une application web en **PHP / PDO** et un DAO en **Java / JDBC**.

Le code fonctionne… presque : certains patients restent introuvables, un
utilisateur ne peut pas se connecter, certains tris plantent. Ces bugs ont tous
la même cause, une **faille de sécurité**. Votre travail : la trouver, la
corriger et le prouver par les tests.

Ce fichier explique **comment faire tourner le projet**. Les questions du TP
sont dans l'énoncé, sur le site du cours.

| Partie | Contenu |
|---|---|
| **A — À faire** | La mise en route, pas à pas : c'est l'étape 0 du TP |
| **B — À savoir** | Comptes de test, commandes, tests automatiques, organisation du code |
| **C — Facultatif** | Utiliser MariaDB au lieu de SQLite (bonus de l'étape 7) |

---

## A — À faire : la mise en route

### 1. Créer votre dépôt

1. Sur la page du dépôt modèle : **Use this template** › **Create a new
   repository** — *pas « Open in a codespace » : vous n'auriez pas de dépôt à
   vous.*
2. Gardez le nom `cyber-tp4-injection`, cochez **Private**.
3. Dans **votre** dépôt : **Settings** › **Collaborators** › **Add people** :
   ajoutez `roza`.

> GitHub vous écrit aussitôt « All jobs have failed » : c'est normal, les tests
> de sécurité sont rouges au départ (voir B3).

### 2. Ouvrir le projet

**Dans GitHub Codespaces** — un navigateur suffit, rien à installer :

1. Sur la page de **votre** dépôt : **Code** › **Codespaces** › **Create
   codespace on main**.
2. Attendez la fin de l'installation : 3 à 4 minutes la première fois.
3. Fermez l'onglet « Get Started with Java Development » s'il s'affiche :
   n'installez **pas** de JDK, Java est déjà là.

**Ou sur votre poste**, si PHP 8.2 ou plus (avec `pdo_sqlite`), Composer, un
JDK 17 ou plus et Maven y sont installés :

1. Clonez **votre** dépôt et ouvrez le **dossier** dans VS Code.
2. Installez les dépendances et créez la base :

   ```sh
   cd php
   composer install
   composer init-db
   ```

### 3. Lancer l'application

Dans un terminal :

| Dans Codespaces | Sur votre poste |
|---|---|
| `cd php && php -S 0.0.0.0:8000 -t public` | `cd php && php -S localhost:8000 -t public` |
| Ouvrez le port 8000 proposé, ou l'onglet **Ports** | Ouvrez <http://localhost:8000> |

Connectez-vous avec `a.martin` / `Genou-42`.

### 4. Lancer les tests

Dans un **second** terminal, les mêmes commandes partout :

```sh
cd php && vendor/bin/phpunit
```

```sh
cd java && mvn test
```

**Plusieurs tests sont rouges : c'est le point de départ du TP.** Au départ,
PHPUnit annonce 12 tests, dont 4 erreurs et 2 échecs.

> Dans Codespaces, la ligne « Xdebug: [Step Debug] Could not connect to
> debugging client » s'affiche avant les résultats : ignorez-la, ce n'est pas
> une erreur du TP.

### 5. En fin de séance

1. Enregistrez votre travail : `git add`, `git commit`, `git push`. Rien n'est
   conservé sur GitHub sans `git push`.
2. Dans Codespaces, arrêtez le Codespace : **Code** › **Codespaces** › **⋯** ›
   **Stop codespace**. Le quota gratuit est mensuel.

---

## B — À savoir

### B1. Les comptes de test

| Identifiant | Mot de passe |
|---|---|
| `a.martin` | `Genou-42` |
| `s.leroy` | `Cheville-7` |
| `m.d'almeida` | `Epaule-17` |

### B2. La base de données

Par défaut, tout fonctionne avec **SQLite** : la base est un simple fichier,
sans serveur à lancer. Il n'y a donc **rien à démarrer côté SQL**, ni dans
Codespaces ni sur votre poste.

| Commande | Effet |
|---|---|
| `cd php && composer init-db` | (re)crée la base PHP `php/var/kinegest.sqlite` — déjà fait à l'ouverture d'un Codespace |
| `cd java && mvn -q compile exec:java -Dexec.args="init"` | crée la base Java `java/kinegest.sqlite` |
| `mvn -q exec:java -Dexec.args="lister ville desc"` | client Java : liste des patients, triée |
| `mvn -q exec:java -Dexec.args="rechercher Mart"` | client Java : recherche par nom |

### B3. Les tests automatiques sur GitHub

À chaque `git push`, GitHub relance tous les tests, sur SQLite **et** sur
MariaDB (onglet **Actions** de votre dépôt). Vous n'avez rien à configurer.
**Au départ, plusieurs tests sont rouges : c'est normal.** Le TP est terminé
quand tout est vert.

### B4. L'organisation du code

```
sql/        schémas SQLite et MariaDB, données de test communes
php/src/    Db, Schema, UtilisateurRepository, PatientRepository
php/public/ les pages : connexion, liste des patients
php/tests/  tests PHPUnit
java/       DAO JDBC, client en ligne de commande, tests JUnit
```

`Schema` (PHP et Java) n'exécute que les scripts du dossier `sql/` : il n'est
pas à corriger.

---

## C — Facultatif : avec MariaDB ou MySQL

*Bonus de l'étape 7, **sur votre poste uniquement** : il faut un serveur
MariaDB ou MySQL (celui de WAMP, par exemple), et il n'y en a pas dans le
Codespace. Sans cette partie, MariaDB est tout de même testé à chaque `git
push` (voir B3).*

1. Créez la base et un compte dédié à l'application (jamais `root`) :

   ```sql
   CREATE DATABASE kinegest CHARACTER SET utf8mb4;
   CREATE USER 'kinegest_app'@'localhost' IDENTIFIED BY 'un-mot-de-passe-solide';
   GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP ON kinegest.* TO 'kinegest_app'@'localhost';
   ```

2. Indiquez la base par des variables d'environnement, **dans le terminal où
   vous lancez les commandes** :

   | | Linux, macOS, Git Bash | PowerShell |
   |---|---|---|
   | PHP | `export DB_DSN="mysql:host=127.0.0.1;dbname=kinegest;charset=utf8mb4"` | `$env:DB_DSN="mysql:host=127.0.0.1;dbname=kinegest;charset=utf8mb4"` |
   | Java | `export JDBC_URL="jdbc:mariadb://127.0.0.1:3306/kinegest"` | `$env:JDBC_URL="jdbc:mariadb://127.0.0.1:3306/kinegest"` |
   | Les deux | `export DB_USER=kinegest_app DB_PASS=...` | `$env:DB_USER="kinegest_app"; $env:DB_PASS="..."` |

3. Relancez les mêmes commandes (`composer init-db`, `vendor/bin/phpunit`,
   `mvn test`) : elles travaillent alors sur MariaDB. PHP a besoin de
   l'extension `pdo_mysql`.

> `CREATE` et `DROP` ne servent qu'à (ré)initialiser la base pendant le TP. En
> production, l'application n'aurait que `SELECT, INSERT, UPDATE, DELETE` :
> c'est le **moindre privilège**, que nous verrons en séance 9.
