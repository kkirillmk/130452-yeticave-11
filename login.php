<?php
require_once "helpers.php";
require_once "init.php";
require_once "vendor/autoload.php";

$categories = getCategories($sql_connect);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $form = filter_input_array(INPUT_POST, [
        "email" => FILTER_VALIDATE_EMAIL,
        "password" => FILTER_DEFAULT
    ], true
    );
    $fields = ["email", "password"];
    $errors = [];

    foreach ($fields as $value) {
        if (empty($form[$value])) {
            $errors[$value] = "Поле $value надо заполнить";
        }
    }
    if ($form["email"] === false) {
        $errors[] = "Введён некорректный email";
    }

    $email = mysqli_real_escape_string($sql_connect, $form["email"]);
    $sql = getUserByEmail($email);
    $res = mysqli_query($sql_connect, $sql);

    $user = $res ? mysqli_fetch_array($res, MYSQLI_ASSOC) : null;

    if (isset($user)) {
        if (password_verify($form["password"], $user["password"])) {
            $_SESSION["user"] = $user;
        } else {
            $errors["password"] = "Неверный пароль";
        }
    } else {
        $errors["email"] = "Такой пользователь не найден";
    }

    if (empty($errors)) {
        header("Location: /");
        exit();
    }

    $main_content = include_template("login.php", [
        "categories" => $categories,
        "form" => $form,
        "errors" => $errors
    ]);
} else {
    $main_content = include_template("login.php", ["categories" => $categories]);

    if (isset($_SESSION["user"])) {
        header("Location: /");
        exit();
    }
}

echo include_template("layout.php", [
    "main_content" => $main_content,
    "title" => "Вход",
    "categories" => $categories
]);
