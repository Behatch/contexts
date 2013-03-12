#language: fr
Fonctionnalité:

  Scénario:
    Étant donné je suis sur "/table/index.html"
    Alors je devrais voir "You are about to test table."

  Scénario:
    Étant donné je suis sur "/table/index.html"

    Alors je devrais voir 2 colonnes dans le tableau "table"

    Et le schéma des colonnes du tableau "table" devrait correspondre à :
      | columns |
      | Lorem   |
      | Ipsum   |

  Scénario:
    Étant donné je suis sur "/table/index.html"

    Alors je devrais voir 2 lignes dans le tableau "table"
    Et je devrais voir 2 lignes dans le 1ier tableau "table"

    Et les données dans la 1ière ligne du tableau "table" devraient correspondre à :
      | col1   | col2   |
      | Lorem  | Ipsum  |

    Et les données dans la 2ième ligne du tableau "table" devraient correspondre à :
      | col1   | col2   |
      | Dolor  | Sit    |

  Scénario:
    Étant donné je suis sur "/table/index.html"

    Alors la 1ière colonne de la 1ière ligne du tableau "table" devrait contenir "Lorem"
    Et la 2nde colonne de la 1ière ligne du tableau "table" devrait contenir "Ipsum"
