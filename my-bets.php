<?php
require_once "helpers.php";
require_once "init.php";

if (empty($_SESSION)) {
    http_response_code(403);
    exit();
}

$cats = getCategories($sql_connect);

$main_content = include_template("my-bets.php", [
    "cats" => $cats
]);
echo include_template("layout.php", [
    "main_content" => $main_content,
    "title" => "Главная",
    "cats" => $cats
]);
