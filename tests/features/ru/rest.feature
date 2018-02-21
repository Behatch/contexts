#language: ru
Функционал: Тестирование RESTContext

    Сценарий: Тестирование заголовков
        Когда я отправляю GET запрос на "rest/index.php"
        И заголовок "Content-Type" должен содержать "text"
        И заголовок "Content-Type" должен быть равен "text/html; charset=UTF-8"
        И заголовок "Content-Type" не должен быть равен "x-test/no-such-type"
        И заголовок "Content-Type" не должен содержать "text/json"
        И заголовок "xxx" не должен существовать
        И ответ должен истекать в будущем
        И ответ должен быть закодирован "UTF-8"

    Сценарий: Тестирование методов запросов.
        Пусть я отправляю GET запрос на "/rest/index.php"
        Тогда я должен видеть "You have sent a GET request. "
        И я должен видеть "No parameter received"

        Когда я отправляю GET запрос на "/rest/index.php?first=foo&second=bar"
        Тогда я должен видеть "You have sent a GET request. "
        И я должен видеть "2 parameter(s)"
        И я должен видеть "first : foo"
        И я должен видеть "second : bar"

        Когда я отправляю POST запрос на "/rest/index.php" с параметрами:
            | key     | value      |
            | foo     | bar        |
            | foofile | @lorem.txt |
        Тогда я должен видеть "You have sent a POST request. "
        И я должен видеть "1 parameter(s)"
        И я должен видеть "1 file(s)"
        И я должен видеть "foo : bar"
        И я должен видеть "foofile - name : lorem.txt"
        И я должен видеть "foofile - error : 0"
        И я должен видеть "foofile - size : 39"

        Когда я отправляю PUT запрос на "/rest/index.php"
        Тогда я должен видеть "You have sent a PUT request. "

        Когда я отправляю DELETE запрос на "/rest/index.php"
        Тогда я должен видеть "You have sent a DELETE request. "

        Когда я отправляю POST запрос на "/rest/index.php" с телом:
            """
            This is a body.
            """
        Тогда я должен видеть "Body : This is a body."

        Когда я отправляю PUT запрос на "/rest/index.php" с телом:
            """
            {"this is":"some json"}
            """
        Тогда ответ должен быть пустым

    Сценарий: Добавление заголовка
        Пусть я добавляю заголовок "xxx" со значением "yyy"
        Когда я отправляю GET запрос на "/rest/index.php"
        Тогда я должен видеть "HTTP_XXX : yyy"

    Сценарий: Добавление заголовка с огромным числовым значением
        Пусть я добавляю заголовок "xxx-large-numeric" со значением "92233720368547758070"
        Когда я отправляю GET запрос на "/rest/index.php"
        Тогда я должен видеть "HTTP_XXX_LARGE_NUMERIC : 92233720368547758070"

    Сценарий: Заголовок не должен сохраняться между сценариями
        Когда я отправляю GET запрос на "/rest/index.php"
        Тогда я не должен видеть "HTTP_XXX : yyy"
        Тогда я не должен видеть "HTTP_XXX_LARGE_NUMERIC"

    Сценарий: Регистронезависимость имён заголовков
        Как описано в rfc2614 §4.2
        https://tools.ietf.org/html/rfc2616#section-4.2

        Когда я отправляю GET запрос на "rest/index.php"
        Тогда заголовок "content-type" должен содержать "text"

    Сценарий: Отладка
        Пусть я добавляю заголовок "xxx" со значением "yyy"
        Когда я отправляю POST запрос на "/rest/index.php" с параметрами:
            | key | value |
            | foo | bar   |
        Тогда выведи заголовки последнего ответа
        И выведи соответствующую команду curl

    Сценарий: Тело ответа
        Пусть я отправляю GET запрос на "/"
        Тогда ответ должен быть
        """
        Congratulations, you've correctly set up your apache environment.
        """

    Сценарий: Установка content-заголовка в POST запросе
        Когда я добавляю заголовок "Content-Type" со значением "xxx"
        Когда я отправляю "POST" запрос на "rest/index.php" с телом:
        """
        {"name": "test"}
        """
        Тогда тело ответа должно содержать ">CONTENT_TYPE : xxx"
        Тогда тело ответа должно содержать ">HTTP_CONTENT_TYPE : xxx"

    Сценарий: Content-заголовок очищается между сценариями
        Когда я отправляю "POST" запрос на "rest/index.php" с телом:
        """
        {"name": "test"}
        """
        Тогда тело ответа не должно содержать ">CONTENT_TYPE : xxx"
        Тогда тело ответа не должно содержать ">HTTP_CONTENT_TYPE : xxx"
