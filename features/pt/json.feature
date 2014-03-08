#language: pt
Funcionalidade: Testando o JSONContext

  Cenário: Eu sou um JSON?
    Quando Eu estou em "/json/imajson.json"
    Então a resposta deve estar em JSON
    Quando Eu estou em "/json/emptyarray.json"
    Então a resposta deve estar em JSON
    Quando Eu estou em "/json/emptyobject.json"
    Então a resposta deve estar em JSON
    Quando Eu estou em "/json/imnotajson.json"
    Então a resposta não deve estar em JSON

  Cenário: Contar os elementos JSON
    Quando Eu estou em "/json/imajson.json"
    Então o nó JSON "numbers" deve ter 4 elementos

  Cenário: Verificar a interpretação do JSON
    Quando Eu estou em "/json/imajson.json"

    Então o nó JSON "foo" deve existir
    E o nó JSON "root.foo" deve existir
    E o nó JSON "foo" deve conter "bar"
    E o nó JSON "foo" não deve conter "something else"

    E o nó JSON "numbers[0]" deve conter "one"
    E o nó JSON "numbers[1]" deve conter "two"
    E o nó JSON "numbers[2]" deve conter "three"
    E o nó JSON "numbers[3].complexeshizzle" deve ser igual a "true"
    E o nó JSON "numbers[3].so[0]" deve ser igual a "very"
    E o nó JSON "numbers[3].so[1].complicated" deve ser igual a "indeed"

    E o nó JSON "bar" não deve existir

  Cenário: Validação do JSON com schema
    Quando Eu estou em "/json/imajson.json"
    Então o JSON deve ser válido de acordo com o schema "fixtures/www/json/schema.json"

  Cenário: Validação do JSON
    Quando Eu estou em "/json/imajson.json"
    Então o JSON deve ser válido de acordo com esse schema:
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

  Cenário: Validação do conteúdo do JSON
    Quando Eu estou em "/json/imajson.json"
    Então o JSON deve ser igual a:
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
    E exiba a última resposta JSON

  Cenário: Verificar o nó raiz do JSON
    Quando Eu estou em "/json/rootarray.json"
    Então a resposta deve estar em JSON
    E o nó JSON "root[0].name" deve existir
