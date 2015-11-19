#language: fr
Fonctionnalité:

    Scénario:
        Étant donné j'exécute "true"
        Alors la commande devrait réussir
        Étant donné j'exécute "false"
        Alors la commande planter

    Scénario:
        Étant donné j'exécute "sleep 1"
        Alors la commande devrait durer moins de 2 secondes

        Étant donné j'exécute "sleep 2"
        Alors la commande devrait durer plus de 1 secondes

    Scénario:
        Étant donné j'exécute "echo 'Hello world'"
        Alors je devrais voir sur la sortie "Hello world"
        Et je ne devrais pas voir sur la sortie "Hello John"

    Scénario:
        Étant donné j'exécute "bin/behat --help"

    Scénario: création de fichier
        Quand je crée le fichier "tests/fixtures/test" contenant :
        """
        A new file
        """
        Alors imprimer le contenu du fichier "tests/fixtures/test"
