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
        Alors la sortie devrait contenir "Hello world"
        Et la sortie devrait contenir "Hel.*ld"
        Et la sortie ne devrait pas contenir "Hello John"
        Et la sortie ne devrait pas contenir "Hel.*hn"

    Scénario:
        Étant donné j'exécute "echo 'Hello world'"
        Alors la sortie devrait être égale à :
        """
        Hello world
        How are you?
        """
        Et la sortie ne devrait pas être égale à :
        """
        Hello John
        How are you?
        """

    Scénario:
        Étant donné j'exécute "bin/behat --help"

    Scénario: création de fichier
        Quand je crée le fichier "tests/fixtures/test" contenant :
        """
        A new file
        """
        Alors imprimer le contenu du fichier "tests/fixtures/test"
