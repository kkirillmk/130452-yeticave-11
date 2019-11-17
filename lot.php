<?php
require_once "helpers.php";

$sql_connect = connectDB("127.0.0.1", "root", "", "yeticave");

$id_lot = filter_input(INPUT_GET, 'id');
if (!$id_lot) {
    echo "Ошибка получения параметра запроса";
    exit;
}

$lots = [];
$cats = [];

$sql = "SELECT lots.`id`, lots.`name`, `starting_price`, `img`, MAX(bets.`bet_sum`) AS `current_price`,
        cats.`name`  AS `category`, `date_end`, `description`, `bet_step` FROM lots
        LEFT JOIN `bets` ON bets.`id_lot` = lots.`id`
        JOIN `categories` cats ON cats.`id` = lots.`id_category`
        WHERE lots.id = $id_lot";
$lots = sqlToArray($sql_connect, $sql);

$sql = "SELECT * FROM `categories`";
$cats = sqlToArray($sql_connect, $sql);

$lots_id_list = [];

foreach ($lots as $lot) {
    $lots_id_list[] = $lot["id"];
}
if (!in_array($id_lot, $lots_id_list)) {
    http_response_code(404);
    exit;
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

