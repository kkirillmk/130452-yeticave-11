<?php
require_once "helpers.php";
require_once "init.php";

$cats = getCategories($sql_connect);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $form = $_POST;
    $fields = ["email", "password"];
    $errors = [];

    foreach ($fields as $value) {
        if (empty($form[$value])) {
            $errors[$value] = "Поле $value надо заполнить";
        }
    }

    $email = mysqli_real_escape_string($sql_connect, $form["email"]);
    $sql = "SELECT * FROM `users` WHERE `email` = '$email'";
    $res = mysqli_query($sql_connect, $sql);

    $user = $res ? mysqli_fetch_array($res, MYSQLI_ASSOC) : null;

    if ($user) {
        if (password_verify($form["password"], $user["password"])) {
            $_SESSION["user"] = $user;
        } else {
            $errors["password"] = "Неверный пароль";
        }
    } else {
        $errors["email"] = "Такой пользователь не найден";
        $errors["password"] = "Неверный пароль";
    }

    if (count($errors) !== 0) {
        $main_content = include_template("login.php", ["cats" => $cats, "form" => $form, "errors" => $errors]);
    } else {
        header("Location: /");
        exit();
    }
} else {
    $main_content = include_template("login.php", ["cats" => $cats]);

    if (isset($_SESSION["user"])) {
        header("Location: /");
        exit();
    }
}

echo include_template("layout.php", [
    "main_content" => $main_content,
    "title" => "Главная",
    "cats" => $cats
]);
