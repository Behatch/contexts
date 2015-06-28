#language: fr
Fonctionnalité:

    Scénario:
        Étant donné j'exécute "ls"

    Scénario:
        Étant donné j'exécute "sleep 1"
        Alors la commande devrait durer moins de 2 secondes

        Étant donné j'exécute "sleep 2"
        Alors la commande devrait durer plus de 1 secondes

    Scénario:
        Étant donné j'exécute "bin/behat --help"

    Scénario: création de fichier
        Quand je crée le fichier "tests/fixtures/test" contenant :
        """
        A new file
        """
        Alors imprimer le contenu du fichier "tests/fixtures/test"
