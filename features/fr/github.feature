#language: fr
Fonctionnalité:

  Scénario:
    Étant donnée je suis sur "http://github.com/sanpii/behatch-contexts"
    Alors je devrais voir "Behatch contexts"

  Scénario:
    Étant donné je suis sur "http://github.com/sanpii/behatch-contexts"
    Quand je suis "features"
    Et je suis "github.feature"
    Alors je devrais voir "WE NEED TO GO DEEP !!"
