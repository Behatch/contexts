#language: pt
Funcionalidade: Debug

  @user
  Cenário: Testando um breakpoint
    Quando Eu estou em "index.html"
    E coloco um breakpoint
    Então devo ver "Congratulations, you've correctly set up your apache environment."
    E coloco um breakpoint

  @javascript
  Cenário: Capturando uma screenshot
    Quando Eu estou em "index.html"
    E salvo uma screenshot em "index.png"
