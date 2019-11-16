<?php
require_once "helpers.php";

$sql_connect = mysqli_connect("127.0.0.1", "root", "", "yeticave");
mysqli_set_charset($sql_connect, "utf-8");

$lots = [];
$cats = [];
$id_lot = filter_input(INPUT_GET, 'id');;

if (!$id_lot) {
    echo "Ошибка получения параметра запроса";
    exit;
}

if (!$sql_connect) {
    echo ("Ошибка подключения: " . mysqli_connect_error());
    exit;
}
$sql = "SELECT l.`id`, l.`name`, `starting_price`, `img`, MAX(b.`bet_sum`) AS `current_price`,
        c.`name`  AS `category`, `date_end`, `description`, `bet_step` FROM lots l
        LEFT JOIN `bets` b ON b.`id_lot` = l.`id`
        JOIN `categories` c ON c.`id` = l.`id_category`
        WHERE l.id = $id_lot";
$result = mysqli_query($sql_connect, $sql);
if (!$result) {
    echo ("Ошибка запроса: " . mysqli_error($sql_connect));
    exit;
}
$lots = mysqli_fetch_all($result, MYSQLI_ASSOC);
$sql = "SELECT * FROM `categories`";
$result = mysqli_query($sql_connect, $sql);
if (!$result) {
    echo ("Ошибка запроса: " . mysqli_error($sql_connect));
    exit;
}
$cats = mysqli_fetch_all($result, MYSQLI_ASSOC);

$lots_id_list = "";

foreach ($lots as $lot) {
    $lots_id_list .= " {$lot["id"]}";
}
if (!strpos("$lots_id_list", "$id_lot")) {
    http_response_code(404);
}

$main_content = include_template("lot.php", [
    "cats" => $cats,
    "lots" => $lots
]);
echo include_template("layout.php", [
    "main_content" => $main_content,
    "title" => "Главная",
    "is_auth" => rand(0, 1),
    "user_name" => "Kirill",
    "cats" => $cats
]);

