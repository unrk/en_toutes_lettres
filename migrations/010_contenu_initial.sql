-- Reprise des contenus du site actuel (Site Creator Infomaniak), relevés en
-- juillet 2026. L'association pourra tout modifier depuis le back-office : ce
-- n'est qu'un point de départ pour que le nouveau site ne soit pas vide.
--
-- Les adresses web reprennent EXACTEMENT celles du site actuel
-- (« la-cabane », « les-ateliers-sociaux-linguistiques »…). Les liens déjà
-- partagés par l'association et les résultats de recherche continueront donc
-- de fonctionner une fois le nouveau site en ligne.
--
-- Note : le texte utilise l'apostrophe typographique (’) et non l'apostrophe
-- droite ('). C'est la forme correcte en français, et cela évite au passage
-- toute question d'échappement dans ce fichier.

INSERT INTO activites
    (titre, adresse, resume, description, creneaux, lieu, public_vise, tarif, inscriptions, ordre, statut)
VALUES
(
    'La médiation de l’écrit',
    'la-mediation',
    'Un accompagnement gratuit et confidentiel pour vos courriers, vos documents administratifs et vos démarches en ligne.',
    '<p>Vous avez reçu un courrier que vous ne comprenez pas ? Vous devez faire une démarche en ligne et vous ne savez pas par où commencer ? Nous vous aidons.</p><p>Deux personnes vous accueillent pour :</p><ul><li>rédiger ou comprendre un courrier ;</li><li>déchiffrer un document administratif ;</li><li>effectuer une démarche en ligne (CAF, sécurité sociale, et autres).</li></ul><p>Des ordinateurs, un scanner et une imprimante sont à votre disposition. Vous êtes reçu en toute confidentialité.</p>',
    'Tous les lundis de 14 h à 17 h.',
    '44 rue Saint-Denis, 93130 Noisy-le-Sec — à l’arrière du bâtiment où se trouve le magasin Picard.',
    'Toute personne ayant besoin d’aide pour une démarche administrative.',
    'Gratuit',
    'Aucune inscription, aucun rendez-vous : venez directement sur place. Renseignements au 06 75 14 83 18.',
    1,
    'publie'
),
(
    'Les ateliers sociaux linguistiques',
    'les-ateliers-sociaux-linguistiques',
    'Apprendre à parler, lire et écrire le français, du tout début jusqu’à la préparation de l’examen B1.',
    '<p>Les cours s’adressent à toutes celles et tous ceux qui souhaitent apprendre à parler, écrire, lire ou améliorer leur français.</p><p>Six niveaux sont proposés, de l’initiation à la préparation de l’examen B1, auxquels s’ajoute un cours de conversation. Les groupes comptent une dizaine de participants, pour que chacun puisse prendre la parole.</p>',
    'Le matin, deux heures deux fois par semaine. Un cours en soirée est également proposé.',
    'Trois lieux différents dans la ville. Siège de l’association : 1 place du Maréchal Foch, 93130 Noisy-le-Sec.',
    'Toute personne souhaitant apprendre ou améliorer son français.',
    '21 € par an',
    'Les pré-inscriptions commencent en juin, les inscriptions ont lieu en septembre.',
    2,
    'publie'
),
(
    'Le livre en ville',
    'le-livre-en-ville',
    'Des livres collectés, triés et remis en circulation dans les boîtes à livres de la ville.',
    '<p>Les livres sont collectés, récupérés, puis triés et prêts à une deuxième vie.</p><p>Nous alimentons les boîtes à livres réparties dans la ville, tenons des stands ponctuels et gérons une librairie d’occasion à tout petit prix.</p>',
    NULL,
    'Librairie d’occasion : 54 rue Jean Jaurès. Siège de l’association : 1 place du Maréchal Foch, 93130 Noisy-le-Sec.',
    'Tous les habitants de Noisy-le-Sec.',
    NULL,
    NULL,
    3,
    'publie'
),
(
    'Le chemin du livre',
    'le-chemin-du-livre',
    'Un parcours dans la ville, à la rencontre de femmes remarquables, le long de boîtes à livres décorées par des artistes.',
    '<p>Des femmes remarquables jalonnent ce chemin, par le biais d’une décoration de chaque boîte à livres de la ville.</p><p>Des artistes locaux les ont ornées, et elles sont régulièrement regarnies de livres.</p>',
    NULL,
    'Dans toute la ville de Noisy-le-Sec.',
    'Tous les habitants, petits et grands.',
    'Gratuit',
    NULL,
    4,
    'publie'
),
(
    'La Cabane',
    'la-cabane',
    'Un café associatif et une librairie d’occasion à tout petit prix, tenus par des bénévoles amoureux des livres.',
    '<p>Occupation éphémère d’un lieu convivial, La Cabane est une librairie d’occasion à tout petit prix, au sein d’un café associatif tenu par des bénévoles amoureux des livres.</p><p>On y trouve aussi des animations pour les enfants, des soirées poésie et des soirées à thèmes.</p><p>Petites gourmandises, bière le week-end, bonne humeur et bienvenue à chacun.</p>',
    'Animations pour les enfants les mercredis ou samedis. La programmation est annoncée sur nos réseaux sociaux.',
    '1 place du Maréchal Foch, 93130 Noisy-le-Sec',
    'Ouvert à tous.',
    NULL,
    NULL,
    5,
    'publie'
);

INSERT INTO pages (titre, adresse, contenu, statut, verrouillee) VALUES
(
    'À propos de l’association',
    'a-propos-de',
    '<p>L’association En Toutes Lettres a été fondée en 2001. Elle œuvre à l’accompagnement, à l’autonomisation et à l’insertion sociale des publics fragilisés.</p><p>Son action se déploie sur trois terrains : l’apprentissage de la langue, l’accès à la culture et la sensibilisation à l’écologie, sur le territoire de Noisy-le-Sec, en Seine-Saint-Denis.</p><h2>Nos bénévoles</h2><p>Une soixantaine de bénévoles engagés font vivre l’association, chacun participant aux activités selon ses envies et ses disponibilités.</p><h2>Nos valeurs</h2><p>L’inclusion, l’émancipation par la lecture et l’écriture, et l’accueil inconditionnel de chacun.</p>',
    'publie',
    0
),
(
    'Contact et accès',
    'contact',
    '<p>L’association En Toutes Lettres vous accueille au <strong>1 place du Maréchal Foch, 93130 Noisy-le-Sec</strong>.</p><p>Pour la médiation de l’écrit, rendez-vous au 44 rue Saint-Denis, à l’arrière du bâtiment où se trouve le magasin Picard, tous les lundis de 14 h à 17 h.</p><h2>Nous joindre</h2><p>Par téléphone : 06 75 14 83 18.</p>',
    'publie',
    0
),
(
    'Mentions légales',
    'mentions-legales',
    '<p><strong>Cette page est à compléter avant la mise en ligne du site.</strong> Les informations ci-dessous sont obligatoires : tant qu’elles ne sont pas renseignées, laissez cette page en brouillon.</p><h2>Éditeur du site</h2><p>Nom de l’association, forme juridique (association loi 1901), adresse du siège social, numéro de téléphone, adresse e-mail, numéro RNA ou SIRET.</p><h2>Directeur de la publication</h2><p>Nom et prénom de la personne responsable (généralement le président ou la présidente de l’association).</p><h2>Hébergeur du site</h2><p>Infomaniak Network SA, Rue Eugène-Marziano 25, 1227 Les Acacias, Genève, Suisse. Téléphone : +41 22 820 35 44.</p>',
    'brouillon',
    1
),
(
    'Politique de confidentialité',
    'politique-de-confidentialite',
    '<p><strong>Cette page est à compléter avant la mise en ligne du site.</strong> Elle doit décrire, en langage simple, ce que le site fait des informations personnelles qu’il collecte.</p><h2>Ce que nous collectons</h2><p>À compléter : adresses e-mail des personnes inscrites à la lettre d’information, informations transmises lors d’une inscription à une activité.</p><h2>À quoi cela sert</h2><p>À compléter : envoyer la lettre d’information, gérer les inscriptions aux activités.</p><h2>Combien de temps nous les gardons</h2><p>À compléter.</p><h2>Vos droits</h2><p>Vous pouvez à tout moment demander à consulter, corriger ou supprimer les informations vous concernant, en nous écrivant à l’adresse de l’association. La désinscription de la lettre d’information se fait en un clic, depuis le lien présent en bas de chaque envoi.</p>',
    'brouillon',
    1
);
