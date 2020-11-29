## P6 OPENCLASSROOMS (Snowtricks)


## Installation

 - **Etape 1 :** Transférer les fichiers dans le dossier racine de votre serveur web.
- **Etape 2:** Dans le fichier .env Modifiez les parametre suivants:
 -DATABASE_URL 
-Mailer_DSN
 - Pour lancer le serveur entrée cette commande: symfony server:start
 
- **Etape 3 :** Pour crée la base de donnée entre la commande suivantes : php bin/console doctrine:database:create
