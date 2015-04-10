#language: fr
Fonctionnalité:

    Scénario:
        Étant donné j'exécute "ls"

    Scénario:
        Étant donné j'exécute "bin/behat --help"

    Scénario: création de fichier
        Quand je crée le fichier "tests/fixtures/test" contenant :
        """
        A new file
        """
        Alors imprimer le contenu du fichier "tests/fixtures/test"
