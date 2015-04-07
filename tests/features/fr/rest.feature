#language: fr
Fonctionnalité:

    Scénario:
        Quand j'envoie une requête GET sur "rest/index.php"
        Et l'entête "Content-Type" devrait contenir "text"
        Et l'entête "Content-Type" devrait être égal à "text/html; charset=UTF-8"
        Et l'entête "Content-Type" ne devrait pas contenir "text/json"
        Et l'entête "xxx" ne devrait pas exister
        Et la réponse devrait expirer dans le futur
        Et la réponse devrait être encodée en "UTF-8"

    Scénario:
        Étant donné j'envoie une requête GET sur "rest/index.php"
        Alors je devrais voir "You have sent a GET request. "
        Et je devrais voir "No parameter received"

        Quand j'envoie une requête GET sur "/rest/index.php?first=foo&second=bar"
        Alors je devrais voir "You have sent a GET request. "
        Et je devrais voir "2 parameter(s)"
        Et je devrais voir "first : foo"
        Et je devrais voir "second : bar"

        Quand j'envoie une requête POST sur "/rest/index.php" avec les paramètres :
            | key     | value      |
            | foo     | bar        |
            | foofile | @lorem.txt |
        Alors je devrais voir "You have sent a POST request. "
        Et je devrais voir "1 parameter(s)"
        Et je devrais voir "1 file(s)"
        Et je devrais voir "foo : bar"
        Et je devrais voir "foofile - name : lorem.txt"
        Et je devrais voir "foofile - error : 0"
        Et je devrais voir "foofile - size : 39"

        Quand j'envoie une requête PUT sur "rest/index.php"
        Alors je devrais voir "You have sent a PUT request. "

        Quand j'envoie une requête DELETE sur "rest/index.php"
        Alors je devrais voir "You have sent a DELETE request. "

        Quand j'envoie une requête POST sur "/rest/index.php" avec le contenu :
            """
            This is a body.
            """
        Alors je devrais voir "Body : This is a body."

    Scénario:
        Étant donné j'ajoute l'entête "xxx" égale à "yyy"
        Quand j'envoie une requête GET sur "/rest/index.php"
        Alors je devrais voir "HTTP_XXX : yyy"

    Scénario: Nom d'entête insenible à la casse
        Comme décrit dans la rfc2616 §4.2
        https://tools.ietf.org/html/rfc2616#section-4.2

        Quand j'envoie une requête GET sur "/rest/index.php"
        Alors l'entête "Content-Type" devrait contenir "text"

    Scénario: Debug
        Étant donné j'ajoute l'entête "xxx" égale à "yyy"
        Quand j'envoie une requête POST sur "/rest/index.php" avec les paramètres :
          | key | value |
          | foo | bar   |
        Alors imprimer les entêtes de la dernière réponse
        Et imprimer la commande curl correspondante
