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

    Scénario:
        Étant donné je suis sur "/browser/elements.html"
        Alors je devrais voir 2 "div" dans le 1er "body"
        Alors je devrais voir moins de 3 "div" dans le 1er "body"
        Alors je devrais voir plus de 1 "div" dans le 1er "body"

    @javascript
    Scénario:
        Étant donnée je vais sur "/browser/timeout.html"
        Alors I wait 3 seconds until I see "timeout"

    @javascript
    Scénario:
        Étant donnée je suis sur "/browser/index.html"
        Alors l'élément "#visible-element" devrait être visible
        Et l'élément "#hidden-element" ne devrait pas être visible
