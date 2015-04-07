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
        Étant donné je suis sur "/browser/elements.html"
        Alors je devrais voir 4 "div" dans le 1er "body"
        Et je devrais voir moins de 6 "div" dans le 1er "body"
        Et je devrais voir plus de 2 "div" dans le 1er "body"
        Et la liste de sélection "months_selector" ne devrait pas contenir "december"
        Et la liste de sélection "months_selector" devrait contenir "january"
        Quand je clique sur le 1er élément "ul li"
        Alors je devrais voir "You clicked First"

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
        Alors j'attends 3 secondes de voir "timeout"
        Et j'attends 1 seconde
        Et j'attends l'élément "#iframe"
        Et j'attends 5 secondes l'élément "#iframe"

    @javascript
    Scénario:
        Étant donnée je suis sur "/browser/index.html"
        Alors l'élément "#visible-element" devrait être visible
        Et l'élément "#hidden-element" ne devrait pas être visible
