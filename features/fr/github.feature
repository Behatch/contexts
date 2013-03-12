#language: fr
Fonctionnalité:

  Scénario:
    Étant donnée je suis sur "http://github.com/sanpii/behatch-skeleton"
    Alors je devrais voir "Behat Custom Helper"

  Scénario:
    Étant donné je suis sur "http://github.com/sanpii/behatch-skeleton"
    Quand je suis "features"
    Et je suis "github.feature"
    Alors je devrais voir "WE NEED TO GO DEEP !!"
