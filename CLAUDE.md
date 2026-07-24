# En Toutes Lettres — site associatif (Noisy-le-Sec)

Association : ateliers de français/lecture-écriture, actions culturelles,
« La Cabane » (bar associatif). Remplacement d'un Site Creator Infomaniak par
du PHP sur mesure. Développeur unique : un stagiaire — le site doit rester
maintenable par des bénévoles non techniques après son départ.

## Hébergement — contraintes non négociables

- Mutualisé Infomaniak : Apache + PHP 8.4, MySQL/MariaDB. Pas de root, pas de
  démon, pas de process long, pas de Docker, pas de Node en prod.
- Déploiement : `git push prod main` vers un dépôt nu, hook `post-receive` qui
  fait `git checkout -f` dans la racine web. Tout ce qui est livré doit
  tourner tel quel, sans étape de build serveur.
  - Deux remotes : `origin` (GitHub, sauvegarde) et `prod` (Infomaniak, mise
    en ligne). Workflow noté dans `.aide_push_git` (fichier local, gitignoré).
- Si Composer est utilisé, `vendor/` est versionné ou installé à la main une
  fois — jamais de commande lancée par le déploiement.
- Tâches planifiées = endpoint HTTP protégé par un jeton secret (Infomaniak
  n'appelle qu'une URL).
- `uploads/`, `medias/`, `config.local.php`, `.env` sont gitignorés (écrasés
  par le `checkout -f`, et les secrets ne doivent jamais être versionnés).

## Stack

- PHP 8.4 OO, routeur maison, pas de framework lourd.
- PDO + requêtes préparées uniquement. Migrations SQL numérotées
  (`001_init.sql`, `002_...`), appliquées à la main.
- HTML/CSS/JS écrits à la main, pas de SPA/React. Un préprocesseur éventuel
  doit livrer son résultat compilé committé.
- Aucune dépendance à un service payant.

## Paiements

HelloAsso (gratuit pour assos françaises) via iframe/lien sortant + page de
retour côté site, pour adhésions, dons, billetterie. Pas d'encaissement en
propre développé ici.

## Back-office — critère de réussite principal

Utilisé par des bénévoles sans compétence technique, souvent sur téléphone.
- Connexion email + mot de passe, rôles administrateur/rédacteur.
- CRUD sur actualités, annonces, activités, agenda, partenaires, pages simples.
- Éditeur enrichi minimal (gras, italique, titres, listes, liens) — jamais de
  HTML à écrire.
- Upload d'images : redimensionnement/compression auto + alt obligatoire.
- Statuts brouillon / publié / programmé, avec prévisualisation.
- Export CSV des inscrits newsletter ; consultation des inscriptions activités.
- Interface 100% française sans jargon technique (proscrire "slug", "CRUD",
  "upload", "publier le post" → dire "mettre en ligne", etc.).
- Confirmation nommée explicite avant toute action destructive.
- Messages d'erreur en langage courant, actionnables.
- Utilisable au doigt sur mobile. Jamais de manipulation fichier/FTP/code.

## Sécurité / RGPD

- CSRF sur tous les formulaires, `password_hash()`, sessions sécurisées,
  régénération d'ID de session à la connexion.
- Échappement systématique en sortie, validation MIME réelle des uploads,
  dossier uploads sans exécution PHP.
- En-têtes de sécurité HTTP + `.htaccess` commentés.
- Mentions légales, politique de confidentialité, bandeau cookies (si cookies
  non essentiels), consentement explicite newsletter (double opt-in),
  désinscription en un clic.
- Accessibilité : contrastes conformes, site pensé pour public peu à l'aise
  numériquement et non francophone natif.

## Périmètre public

Accueil · présentation/équipe/valeurs · fiches activités (ateliers, actions
culturelles, La Cabane) · actualités/annonces avec archives · agenda ·
partenaires · réseaux sociaux (discrets) · newsletter (double opt-in) ·
adhésion / inscription activité / don ponctuel (via HelloAsso) · contact/accès.
