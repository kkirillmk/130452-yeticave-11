<?php
require_once "helpers.php";
require_once "init.php";
require_once "vendor/autoload.php";

$id_lot = filter_input(INPUT_GET, 'id');
if (!$id_lot) {
    echo "Ошибка получения параметра запроса";
    exit;
}

$lots = [];
$cats = [];
$sql = "SELECT  lots.`id`, lots.`name`, 
                `starting_price`, `img`, 
                MAX(bets.`bet_sum`) AS `current_price`,
                cats.`name`  AS `category`, 
                `date_end`, `description`, `bet_step`,
                id_author
        FROM lots
        LEFT JOIN `bets` ON bets.`id_lot` = lots.`id`
        JOIN `categories` cats ON cats.`id` = lots.`id_category`
        WHERE lots.id = $id_lot";
$lots = sqlToArray($sql_connect, $sql);
$cats = getCategories($sql_connect);

$lots_id_list = [];
foreach ($lots as $lot) {
    $lots_id_list[] = $lot["id"];
}
if (!in_array($id_lot, $lots_id_list)) {
    http_response_code(404);
    exit;
}

$sql = "SELECT `bet_step` FROM `lots`
        WHERE `id` = '$id_lot'";
$bet_step = sqlToArrayAssoc($sql_connect, $sql);
$bet_step = $bet_step["bet_step"];

$lot_price = "";
if ($lots[0]["current_price"]) {
    $lot_price = $lots[0]["current_price"];
} else {
    $lot_price = $lots[0]["starting_price"];
}

$last_bet = [];
$sql = "SELECT users.name, bet_sum,
               date_placing, id_user
            FROM bets
                JOIN users
                    ON users.id = bets.id_user
            WHERE id_lot = '$id_lot'
            ORDER BY bets.id DESC";
$bets = sqlToArray($sql_connect, $sql);
$last_bet = $bets ? array_slice($bets, 0, 1) : [0];

$min_bet = $bet_step + $lot_price;
$form = [];
$rule = [];
$errors = [];
$id_user = $_SESSION["user"]["id"] ?? "";
$price = 0;
if ($_SERVER["REQUEST_METHOD"] === "POST"
    && $last_bet[0]["id_user"] !== $id_user
    && $lots[0]["id_author"] !== $_SESSION["user"]["id"]
    && strtotime($lots[0]["date_end"]) > time()) {

    $form = filter_input_array(INPUT_POST, [
        "cost" => FILTER_DEFAULT
    ], true);

    $rule = [
        "cost" => function ($value) {
            return validateIntGreaterThanZero($value);
        }
    ];

    if (isset($rule["cost"])) {
        $rule = $rule["cost"];
        $errors["cost"] = $rule($form["cost"]);
    }

    if ($form["cost"] < $min_bet) {
        $errors[] = "Введенная ставка меньше минимальной";
    }
    $errors = array_filter($errors);

    if (!empty($errors)) {
        $main_content = include_template("lot.php", [
            "lots" => $lots,
            "errors" => $errors,
            "cats" => $cats,
            "min_bet" => $min_bet,
            "bets" => $bets,
            "last_bet" => $last_bet
        ]);
    } else {

        $sql = "INSERT INTO `bets` (date_placing, id_user, id_lot, bet_sum)
                VALUES (NOW(), '$id_user', '$id_lot', ?)";
        if (!databaseInsertData($sql_connect, $sql, $form)) {
            echo "Данные не добавлены";
            exit();
        }

        $main_content = include_template("lot.php", [
            "cats" => $cats,
            "lots" => $lots,
            "min_bet" => $min_bet,
            "bets" => $bets,
            "last_bet" => $last_bet
        ]);
        header("Location: /lot.php?id=$id_lot");
    }
} else {
    $main_content = include_template("lot.php", [
        "cats" => $cats,
        "lots" => $lots,
        "min_bet" => $min_bet,
        "bets" => $bets,
        "last_bet" => $last_bet
    ]);
}

echo include_template("layout.php", [
    "main_content" => $main_content,
    "title" => "Главная",
    "cats" => $cats
]);

