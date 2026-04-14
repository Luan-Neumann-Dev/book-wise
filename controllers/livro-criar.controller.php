<?php

if($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: /meus-livros");
    exit();
}

if(!auth()) {
    abort(403);
}

$user_id = auth()->id;
$title = $_POST["title"];
$author = $_POST["author"];
$description = $_POST["description"];
$release_year = $_POST["release_year"];

$validation = Validation::validate([
    'title' => ['required', 'min:3'],
    'author' => ['required'],
    'description' => ['required'],
    'release_year' => ['required'],
], $_POST);

if ($validation->failed()) {
    header("Location: /meus-livros");
    exit();
}

$image = null;

if (!empty($_FILES['image']['name'])) {
    $newName = md5(rand());
    $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $image = "public/images/$newName.$extension";

    move_uploaded_file($_FILES['image']['tmp_name'], $image);
}

$database->query(
    query: "insert into books (title, author, description, release_year, user_id, image)
values (:title, :author, :description, :release_year, :user_id, :image);",
    params: compact('title', 'author', 'description', 'release_year', 'user_id', 'image')
);

flash()->push('mensagem', 'Livro cadastrado com sucesso!');
header("Location: /meus-livros");
exit();