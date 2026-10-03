-- Données de test, communes à SQLite et MariaDB / MySQL.
-- Mots de passe (empreintes bcrypt ci-dessous) :
--   a.martin / Genou-42   ·   s.leroy / Cheville-7   ·   m.d'almeida / Epaule-17
INSERT INTO Utilisateur (login, nom, motDePasse) VALUES
  ('a.martin',    'Anne Martin',      '$2y$12$JAhKNbM2OrNfGq3qHpFw7uE0U5Nembo6RU0oI3WDA.2MbBvZ/ZZJ6'),
  ('s.leroy',     'Sophie Leroy',     '$2y$12$d8jBGUh1tnlv2NOyoGF8Z.8.IMkupNcUvP9dgLCTN/qL0a3dLNQ1G'),
  ('m.d''almeida', 'Manuel D''Almeida', '$2y$12$UQ7rq2ApmixtF22SsPji4e4C83aTq0W9/XxYYiJXxEAs4HvznWb9K');

INSERT INTO Patient (nom, prenom, ville, dateNaissance) VALUES
  ('Martin',    'Claire', 'Orleans', '1985-03-12'),
  ('Bernard',   'Louis',  'Tours',   '1972-11-02'),
  ('Dupont',    'Marie',  'Blois',   '1990-06-25'),
  ('N''Diaye',  'Awa',    'Orleans', '1988-01-17'),
  ('D''Almeida','Joao',   'Tours',   '1965-09-30'),
  ('Petit',     'Lucas',  'Blois',   '2001-04-08'),
  ('Garnier',   'Emma',   'Orleans', '1979-12-21'),
  ('Leroy',     'Hugo',   'Tours',   '1995-07-14');
