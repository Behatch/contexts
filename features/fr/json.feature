#language: fr
Fonctionnalité:

  Scénario:
    Étant donné je suis sur "/json/imajson.json"
    Alors la réponse devrait être du JSON
    Quand je suis sur "/json/imnotajson.json"
    Alors la réponse ne devrait pas être du JSON

  Scénario:
    Étant donné je suis sur "/json/imajson.json"
    Alors le nœud JSON "numbers" devrait avoir 4 éléments

  Scénario:
    Étant donné je suis sur "/json/imajson.json"

    Alors le nœud JSON "foo" devrait exister
    Et le nœud JSON "foo" devrait contenir "bar"
    Et le nœud JSON "foo" ne devrait pas contenir "something else"

    Et le nœud JSON "numbers[0]" devrait contenir "one"
    Et le nœud JSON "numbers[1]" devrait contenir "two"
    Et le nœud JSON "numbers[2]" devrait contenir "three"
    Et le nœud JSON "numbers[3].complexeshizzle" devrait être égal à "true"
    Et le nœud JSON "numbers[3].so[0]" devrait être égal à "very"
    Et le nœud JSON "numbers[3].so[1].complicated" devrait être égal à "indeed"

    Et le nœud JSON "bar" ne devrait pas exister

  Scénario:
    Étant donné je suis sur "/json/imajson.json"
    Alors le JSON devrait être valide avec le schéma "fixtures/www/json/schema.json"

  Scénario: Json validation
    Étant donné je suis sur "/json/imajson.json"
    Alors le JSON devrait être valide avec ce schéma:
    """
    {
      "type": "object",
      "$schema": "http://json-schema.org/draft-03/schema",
      "required":true,
      "properties": {
        "foo": {
          "type": "string",
          "required":true
        },
        "numbers": {
          "type": "array",
          "required":true,
          "one": {
            "type": "string",
            "required":true
          },
          "two": {
            "type": "string",
            "required":true
          },
          "three": {
            "type": "string",
            "required":true
          }
        }
      }
    }
    """

    Scénario: Json contents validation
        Étant donné je suis sur "/json/imajson.json"
        Alors le JSON devrait être égal à :
        """
        {
            "foo": "bar",
            "numbers": [
                "one",
                "two",
                "three",
                {
                    "complexeshizzle": true,
                    "so": [
                        "very",
                        {
                            "complicated": "indeed"
                        }
                    ]
                }
            ]
        }
        """
