#language: fr
Fonctionnalité:

    Scénario:
        Étant donné je suis sur "/index.html"
        Alors je devrais voir "Congratulations, you've correctly set up your apache environment."

    Scénario:
        Étant donnée je suis sur "/browser/auth.php"
        Alors le code de status de la réponse devrait être 401
        Et je devrais voir "NONE SHALL PASS"

        Quand je remplis l'authentification basique avec "something" et "wrong"
        Et je vais sur "/browser/auth.php"
        Alors le code de status de la réponse devrait être 401
        Et je devrais voir "NONE SHALL PASS"

        Quand je remplis l'authentification basique avec "gabriel" et "30091984"
        Et je vais sur "/browser/auth.php"
        Alors le code de status de la réponse devrait être 200
        Et je devrais voir "Successfuly logged in"

        Quand je vais sur "/browser/auth.php?logout"
        Et je devrais voir "Logged out"

        Quand je vais sur "/browser/auth.php"
        Alors le code de status de la réponse devrait être 401
        Et je devrais voir "NONE SHALL PASS"

    @javascript
    Scénario:
        Étant donné je suis sur une url composée par:
            | parameters     |
            | /browser       |
            | /elements.html |
        Alors je devrais voir 4 "div" dans le 1er "body"
        Et je devrais voir moins de 6 "div" dans le 1er "body"
        Et je devrais voir plus de 2 "div" dans le 1er "body"
        Et la liste de sélection "months_selector" ne devrait pas contenir "december"
        Et la liste de sélection "months_selector" devrait contenir "january"
        Quand je clique sur le 1er élément "ul li"
        Alors je devrais voir "You clicked First"
        Quand je presse le 2nd "Submit" bouton
        Alors je devrais voir "You clicked Second BUTTON"
        Quand je suis le 1ier "Second" lien
        Alors je devrais voir "You clicked Second A"

    @javascript
    Scénario:
        Étant donné je suis sur "/browser/frames.html"
        Quand je bascule vers l'iframe "index"
        Alors je devrais voir "Visible"

        Quand je bascule vers le cadre principal

        Quand je bascule vers le cadre "elements"
        Alors la liste de sélection "months_selector" devrait contenir "january"

    @javascript
    Scénario:
        Étant donnée je vais sur "/browser/timeout.html"
        Quand j'attends 3 secondes de voir "timeout"
        Et j'attends 1 seconde
        Et j'attends l'élément "#iframe"
        Et j'attends 5 secondes l'élément "#iframe"
        Alors le temps total écoulé devrait être "more" que 3 secondes

    @javascript
    Scénario:
        Étant donné je suis sur "/browser/timeout.html"
        Alors je ne devrais pas voir "timeout"
        Quand j'attends 3 secondes de voir "timeout"
        Alors je devrais voir "timeout"

    Scénario:
        Étant donné je suis sur "/browser/index.html"
        Alors je ne devrais pas voir "foobar" durant 1 seconde

    @javascript
    Scénario:
        Étant donné je suis sur "/browser/index.html"
        Alors l'élément "#visible-element" devrait être visible
        Et l'élément "#hidden-element" ne devrait pas être visible

    @javascript
    Scénario:
        Étant donné je suis sur "/browser/elements.html"
        Alors je remplis "today" avec la date actuelle
        Et je remplis "today" avec la date actuelle et modificateur "-1 day"

    Scénario:
        Étant donné je suis sur "/browser/elements.html"
        Alors je sauvegarde la valeur de "today" dans le paramètre "today"

    Scénario:
        Étant donnée je suis sur "/browser/index.html"
        Et j'attends 1.9 seconde
        Et j'attends 1.9 seconde
        Et j'attends 1.9 seconde
        Alors le temps total écoulé devrait être "more" que 4 secondes
