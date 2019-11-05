<?php
require_once "helpers.php";

$cats = ["Доски и лыжи", "Крепления", "Ботинки", "Одежда", "Инструменты", "Разное"];
$lots = [["name" => "2014 Rossignol District Snowboard", "category" => "Доски и лыжи",
    "price" => 10999, "img" => "img/lot-1.jpg"],
    ["name" => "DC Ply Mens 2016/2017 Snowboard", "category" => "Доски и лыжи",
        "price" => 159999, "img" => "img/lot-2.jpg"],
    ["name" => "Крепления Union Contact Pro 2015 года размер L/XL", "category" => "Крепления",
        "price" => 8000, "img" => "img/lot-3.jpg"],
    ["name" => "Ботинки для сноуборда DC Mutiny Charocal", "category" => "Ботинки",
        "price" => 10999, "img" => "img/lot-4.jpg"],
    ["name" => "Куртка для сноуборда DC Mutiny Charocal", "category" => "Одежда",
        "price" => 7500, "img" => "img/lot-5.jpg"],
    ["name" => "Маска Oakley Canopy", "category" => "Разное",
        "price" => 5400, "img" => "img/lot-6.jpg"]];

$main_content = include_template("main.php", [
    "cats" => $cats,
    "lots" => $lots
]);
print $layout_content = include_template("layout.php", [
    "main_content" => $main_content,
    "title" => "Главная",
    "is_auth" => rand(0, 1),
    "user_name" => "Kirill"
]);
