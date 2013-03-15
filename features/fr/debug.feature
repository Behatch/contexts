#language: fr
Fonctionnalité:

    @user
    Scénario:
        Étant donné je suis sur "index.html"
        Alors je pose un point d'arrêt
        Et je devrais voir "Congratulations, you've correctly set up your apache environment."
        Alors je pose un point d'arrêt

    @javascript
    Scénario:
        Étant donné je suis sur "index.html"
        Et sauvegarde une capture d'écran dans "index.png"
