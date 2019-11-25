<?php
require_once "helpers.php";
require_once  "init.php";

if (($_SESSION)) {
    http_response_code(403);
    exit();
}

$cats = getCategories($sql_connect);

$form = [];
$fields = [];
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fields = ["email", "password", "name", "message"];
    $form = filter_input_array(INPUT_POST, [
        "email" => FILTER_VALIDATE_EMAIL,
        "password" => FILTER_DEFAULT,
        "name" => FILTER_DEFAULT,
        "message" => FILTER_DEFAULT
    ], true
    );

    foreach ($fields as $value){
        if (empty($form[$value])) {
            $errors[$value] = "Поле $value не заполнено";
        }
    }
    if ($form["email"] === false) {
        $errors["email"] = "Введён некорректный email";
    }

    if (empty($errors)) {
        $email = mysqli_real_escape_string($sql_connect, $form["email"]);
        $sql = "SELECT `id` FROM `users` WHERE email = '$email'";
        $res = mysqli_query($sql_connect, $sql);

        if (mysqli_num_rows($res) > 0) {
            $errors["email"] = "Пользователь с данным email уже зарегестрирован";
        } else {
            $password = password_hash($form["password"], PASSWORD_DEFAULT);

            $sql = "INSERT INTO `users` (`date_registration`, `email`, `password`, `name`, `contacts`)
            VALUES (NOW(), ?, ?, ?, ?)";
            $stmt = db_get_prepare_stmt($sql_connect, $sql, [$form["email"], $password,
                                                            $form["name"], $form["message"]]);
            $res = mysqli_stmt_execute($stmt);
        }

        if ($res && empty($errors)) {
            header("Location: login.php");
            exit();
        }
    }
}

$main_content = include_template("sign-up.php", ["cats" => $cats, "errors" => $errors, "form" => $form]);
echo include_template("layout.php", [
    "main_content" => $main_content,
    "title" => "Главная",
    "cats" => $cats
]);
