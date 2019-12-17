<?php
require_once "helpers.php";
require_once "init.php";
require_once "vendor/autoload.php";

if (!empty($_SESSION)) {
    http_response_code(403);
    exit();
}

$categories = getCategories($sql_connect);

$form = [];
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fields = ["email", "password", "name", "message"];
    $form = filter_input_array(INPUT_POST, [
        "email" => FILTER_VALIDATE_EMAIL,
        "password" => FILTER_DEFAULT,
        "name" => FILTER_DEFAULT,
        "message" => FILTER_DEFAULT
    ], true
    );

    foreach ($fields as $value) {
        if (empty($form[$value])) {
            $errors[$value] = "Поле $value не заполнено";
        }
    }
    if ($form["email"] === false) {
        $errors["email"] = "Введён некорректный email";
    }

    if (empty($errors)) {
        $email = mysqli_real_escape_string($sql_connect, $form["email"]);
        $sql = getUserIDByEmail($email);
        $res = mysqli_query($sql_connect, $sql);

        if (mysqli_num_rows($res) > 0) {
            $errors["email"] = "Пользователь с данным email уже зарегестрирован";
        } else {
            $password = password_hash($form["password"], PASSWORD_DEFAULT);

            $sql = "INSERT INTO `users` (`date_registration`, `email`, `password`, `name`, `contacts`)
            VALUES (NOW(), ?, ?, ?, ?)";
            $res = dbInsertData($sql_connect, $sql, [$form["email"], $password, $form["name"], $form["message"]]);
        }

        if ($res && empty($errors)) {
            header("Location: login.php");
            exit();
        }
    }
}

$main_content = include_template("sign-up.php", [
    "categories" => $categories,
    "errors" => $errors,
    "form" => $form
]);
echo include_template("layout.php", [
    "main_content" => $main_content,
    "title" => "Регистрация",
    "categories" => $categories
]);
