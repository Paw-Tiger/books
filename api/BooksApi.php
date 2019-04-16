<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Api.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Db.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Books.php';

class BooksApi extends Api
{
    public $apiName = 'books';

    /**
     * Метод GET
     * Вывод списка всех записей
     * http://ДОМЕН/books
     * @return string
     */
    public function indexAction()
    {
        $db = (new Db())->getConnection();
        $books = Books::find($db);
        if ($books) {
            return $this->response($books, 200);
        }
        return $this->response('Data not found', 404);
    }

    /**
     * Метод GET
     * Просмотр отдельной записи (по id)
     * http://ДОМЕН/books/1
     * @return string
     */
    public function viewAction()
    {
        //id должен быть первым параметром после /books/x
        $id = array_shift($this->requestUri);

        if ($id !== NULL) {
            $db = (new Db())->getConnection();
            $book = Books::find($db, $id);
            if ($book) {
                return $this->response($book, 200);
            }
        }
        return $this->response('Data not found', 404);
    }

    /**
     * Метод POST
     * Создание новой записи
     * http://ДОМЕН/books + параметры запроса name, email
     * @return string
     */
    public function createAction()
    {
        $name = $this->requestParams['name'] ?? '';
        $author = $this->requestParams['author'] ?? '';
        $shortDescription = $this->requestParams['shortDescription'] ?? '';
        if ($name && $author && $shortDescription) {
            $db = (new Db())->getConnection();
            $book = new Books($db, [
                'name' => $name,
                'author' => $author,
                'shortDescription' => $shortDescription
            ]);
            if ($book) {
                return $this->response('Data saved.', 200);
            }
        }
        return $this->response("Saving error", 500);
    }

    /**
     * Метод PUT
     * Обновление отдельной записи (по ее id)
     * http://ДОМЕН/books/1 + параметры запроса name, email
     * @return string
     */
    public function updateAction()
    {
        $parse_url = parse_url($this->requestUri[0]);
        $id = $parse_url['path'] ?? null;

        $db = (new Db())->getConnection();

        if ($id === NULL || !Books::find($db, $id)) {
            return $this->response("Book with id=$id not found", 404);
        }

        $name = $this->requestParams['name'] ?? '';
        $author = $this->requestParams['author'] ?? '';
        $shortDescription = $this->requestParams['shortDescription'] ?? '';
        if ($name && $author && $shortDescription) {
            if ($book = Books::update(
                $db,
                [
                    'name' => $name,
                    'author' => $author,
                    'shortDescription' => $shortDescription
                ],
                [
                    'id' => $id
                ]
            )) {
                return $this->response('Data updated.', 200);
            }
        }
        return $this->response("Update error", 400);
    }

    /**
     * Метод DELETE
     * Удаление отдельной записи (по ее id)
     * http://ДОМЕН/books/1
     * @return string
     */
    public function deleteAction()
    {
        $parse_url = parse_url($this->requestUri[0]);
        $bookId = $parse_url['path'] ?? null;

        $db = (new Db())->getConnection();

        if ($bookId === NULL || !Books::find($db, $bookId)) {
            return $this->response("Book with id=$bookId not found", 404);
        }
        if (Books::delete($db, $bookId)) {
            return $this->response('Data deleted.', 200);
        }
        return $this->response("Delete error", 500);
    }

}