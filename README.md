# CourMaker - notes de développement

## Activation Mail (Mailtrap) pour les tests

1. Variables à définir dans le fichier `.env` (ou `.env.local` non commité) :

    - `MAILER_DSN` : chaîne de connexion SMTP Mailtrap, ex :
      `MAILER_DSN="smtp://USER:PASS@smtp.mailtrap.io:2525?encryption=tls"`
    - `MAIL_TO` : adresse destinataire utilisée par le formulaire de contact, ex :
      `MAIL_TO="b7.lavi@yahoo.com"`

2. Pour tester localement :

    - Démarrer le serveur depuis la racine du projet :

        ```bash
        php -S 127.0.0.1:8000 -t public
        # ou si vous avez Symfony CLI : symfony server:start
        ```

    - Ouvrir : http://127.0.0.1:8000/contact et soumettre le formulaire.
    - Vérifier l'inbox Mailtrap (https://mailtrap.io/) pour voir le message capturé.

3. Si vous préférez ne pas envoyer de mails en dev, mettez :

    ```dotenv
    MAILER_DSN=null://null
    ```

    Le formulaire affichera le message de confirmation mais aucun mail ne sera réellement envoyé.

4. Commit des changements relatifs au mail :

    ```bash
    git add .env src/Controller/ContactController.php README.md
    git commit -m "Activer Mailtrap pour tests mail + documentation"
    ```

Besoin d'assistance pour vérifier la réception sur Mailtrap ou pour automatiser des fixtures ? Dites-moi quelle étape vous voulez que je fasse ensuite.
