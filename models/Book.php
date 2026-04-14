<?php

/**
 * Representação de 1 registro da Tabela Books
 */
class Book {
    public $id;
    public $title;
    public $author;
    public $description;
    public $release_year;
    public $image;
    public $user_id;
    public $evaluation_note;
    public $evaluation_count;

    public function query($where, $params)
    {
        $database = new Database(config('database'));

        return $database->query(
            query: "
            select
                b.id, 
                b.title,
                b.author,
                b.description,
                b.release_year,
                b.image,
                round(sum(e.note) / 5.0) as evaluation_note,
                count(e.id) as evaluation_count
            from books b
            left join evaluations e on e.book_id = b.id
            where $where
            group by
                b.id, b.title, b.author, b.description, b.release_year, b.image
        ",
            class: Book::class,
            params: $params
        );
    }

    public static function get($id) {
        return (new self)->query('b.id = :id', ['id' => $id])->fetch();
    }

    public static function all($filter) {
        return (new self)->query('b.title like :title', ['title' => "%$filter%"])->fetchAll();
    }

    public static function mine($user_id) {
        return (new self)->query('b.user_id = :user_id', ['user_id' => $user_id])->fetchAll();
    }
}
