<?php
require_once "helpers.php";

$sql_connect = mysqli_connect("127.0.0.1", "root", "", "yeticave");
mysqli_set_charset($sql_connect, "utf-8");

$lots = [];
$cats = [];

if (!$sql_connect) {
    echo ("Ошибка подключения: " . mysqli_connect_error());
} else {
    $sql = "SELECT l.`name`, `starting_price`, `img`, MAX(b.`bet_sum`) AS `current_price`,
            c.`name`  AS `category`, `date_end` FROM lots l
            LEFT JOIN `bets` b ON b.`id_lot` = l.`id`
            JOIN `categories` c ON c.`id` = l.`id_category`
            WHERE l.`date_end` > NOW()
            GROUP BY l.`id` ORDER BY l.`id` DESC";
    $result = mysqli_query($sql_connect, $sql);
    if (!$result) {
        echo ("Ошибка запроса: " . mysqli_error($sql_connect));
    } else {
        $lots = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}
$sql = "SELECT * FROM `categories`";
$result = mysqli_query($sql_connect, $sql);
if (!$result) {
    echo ("Ошибка запроса: " . mysqli_error($sql_connect));
} else {
    $cats = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

$main_content = include_template("main.php", [
    "cats" => $cats,
    "lots" => $lots
]);
echo $layout_content = include_template("layout.php", [
    "main_content" => $main_content,
    "title" => "Главная",
    "is_auth" => rand(0, 1),
    "user_name" => "Kirill",
    "cats" => $cats
]);
