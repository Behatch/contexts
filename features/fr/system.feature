#language: fr
Fonctionnalité:

    Scénario:
        Étant donné j'exécute "ls"

    Scénario:
        Étant donné j'exécute "bin/behat --help"

    Scénario: création de fichier
        Quand je crée le fichier "fixtures/test" contenant :
        """
        A new file
        """
