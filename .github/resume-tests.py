#!/usr/bin/env python3
"""Résumé des tests pour la page de l'exécution GitHub Actions.

    python3 .github/resume-tests.py "PHP · SQLite" php/junit.xml
    python3 .github/resume-tests.py "Java · MariaDB" java/target/surefire-reports/TEST-*.xml

Lit les rapports JUnit (PHPUnit --log-junit, Maven Surefire) et ajoute au
résumé de l'exécution ($GITHUB_STEP_SUMMARY) le nombre de tests qui passent,
puis la liste des tests encore rouges. Le même compteur est affiché :
  - en annotation, en haut de la page de l'exécution ;
  - en statut du commit (« Tests · PHP · SQLite — 6 / 12 tests passent »), visible
    en cliquant sur ✓ / ✗ à côté du dernier commit, sur la page d'accueil du dépôt.
    Il faut pour cela GH_TOKEN et SHA dans l'environnement (voir tests.yml).
"""
import glob
import json
import os
import sys
import urllib.request
import xml.etree.ElementTree as ET

titre, motifs = sys.argv[1], sys.argv[2:]
fichiers = sorted({f for m in motifs for f in glob.glob(m)})

total, rouges, ignores = 0, [], 0
for f in fichiers:
    for cas in ET.parse(f).iter('testcase'):
        total += 1
        classe = (cas.get('classname') or cas.get('class') or '').split('.')[-1]   # sans le paquetage
        nom = f"{classe}::{cas.get('name')}".lstrip(':')
        if cas.find('failure') is not None or cas.find('error') is not None:
            rouges.append(nom)
        elif cas.find('skipped') is not None:
            ignores += 1

verts = total - len(rouges) - ignores
if not fichiers or total == 0:
    lignes = [f"### ⚠️ {titre} : aucun résultat de test", "",
              "Les tests n'ont pas pu s'exécuter (erreur de compilation ou d'installation ?) : "
              "ouvrez le journal du job.", ""]
else:
    icone = '✅' if not rouges else '❌'
    lignes = [f"### {icone} {titre} : {verts} / {total} tests passent"
              + (f" ({ignores} ignorés)" if ignores else ''), ""]
    if rouges:
        lignes += ["<details><summary>Tests encore rouges</summary>", ""]
        lignes += [f"- `{n}`" for n in rouges]
        lignes += ["", "</details>", ""]

texte = '\n'.join(lignes) + '\n'
sortie = os.environ.get('GITHUB_STEP_SUMMARY')
if sortie:
    with open(sortie, 'a', encoding='utf-8') as s:
        s.write(texte)
print(texte)

# le compteur, en une ligne
if total == 0:
    court, etat = "aucun résultat : voir le journal du job", 'error'
else:
    court = f"{verts} / {total} tests passent"
    etat = 'success' if not rouges else 'failure'

# annotation en haut de la page de l'exécution
print(f"::{'notice' if etat == 'success' else 'warning'} title={titre}::{court}")

# statut du commit (sans bloquer le job si l'API refuse)
jeton, sha, depot = os.environ.get('GH_TOKEN'), os.environ.get('SHA'), os.environ.get('GITHUB_REPOSITORY')
if jeton and sha and depot:
    serveur = os.environ.get('GITHUB_SERVER_URL', 'https://github.com')
    lien = f"{serveur}/{depot}/actions/runs/{os.environ.get('GITHUB_RUN_ID', '')}"
    requete = urllib.request.Request(
        f"{os.environ.get('GITHUB_API_URL', 'https://api.github.com')}/repos/{depot}/statuses/{sha}",
        data=json.dumps({'state': etat, 'context': f"Tests · {titre}",
                         'description': court, 'target_url': lien}).encode(),
        headers={'Authorization': f'Bearer {jeton}', 'Accept': 'application/vnd.github+json'},
        method='POST')
    try:
        urllib.request.urlopen(requete, timeout=15)
    except Exception as e:      # droits insuffisants, réseau… : le résumé suffit
        print(f"::notice::statut de commit non publié ({e})")
