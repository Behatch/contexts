#language: ru
Функционал: Отладка

    @user
    Сценарий: Тестирование паузы
        Пусть я на странице "index.html"
        Тогда я ставлю паузу
        Тогда я должен видеть "Congratulations, you've correctly set up your apache environment."
        Тогда я ставлю паузу

    @javascript
    Сценарий: Снятие скриншота
        Пусть я на странице "index.html"
        И я сохраняю скриншот в "index.png"
