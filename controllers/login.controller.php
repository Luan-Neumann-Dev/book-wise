<?php

$mensagem = $_REQUEST["mensagem"] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_REQUEST["email"];
    $password = $_REQUEST["password"];

    $validation = Validation::validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ], $_POST);

    if ($validation->failed('login')) {
        header("Location: /login");
        exit();
    }

    $user = $database->query(
        query: 'select * from users where email = :email',
        class: User::class,
        params: compact("email")
    )->fetch();

    if ($user) {
        $password = $_POST["password"];
        $bdPassword = $user->password;
        if (!password_verify($password, $bdPassword)) {
            flash()->push('validations_login', ['Usuário ou senha estão incorretos!']);
            header("Location: /login");
            exit();
        }

        $_SESSION['auth'] = $user;
        flash()->push('mensagem', "Seja bem-vindo {$user->name}!");
        header("Location: /");
        exit();
    }  else {
        flash()->push('validations_login', ['Usuário ou senha estão incorretos!']);
        header("Location: /login");
        exit();
    }
}

view('login');