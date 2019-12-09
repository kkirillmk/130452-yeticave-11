<?php
require_once "helpers.php";
require_once "init.php";

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
$form = [];
$rule = [];
$errors = [];
$id_author = $_SESSION["user"]["id"] ?? "";
$price = 0;
if ($_SERVER["REQUEST_METHOD"] === "POST") {
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

    if ($bet_step > $form["cost"]) {
        $errors[] = "Введенная ставка меньше минимальной";
    }
    $errors = array_filter($errors);

    if (!empty($errors)) {
        $main_content = include_template("lot.php", [
            "lots" => $lots,
            "errors" => $errors,
            "cats" => $cats,
            "bet_step" => $bet_step
        ]);
    }
    else {
        $price = $lot["current_price"] ?? $lot["starting_price"];
        $price += $form["cost"];
        mysqli_real_escape_string($sql_connect, $price);
        $sql = "INSERT INTO `bets` (date_placing, id_user, id_lot, bet_sum)
                VALUES (NOW(), '$id_author', '$id_lot', '$price')";
        $res = mysqli_query($sql_connect, $sql);

        $main_content = include_template("lot.php", [
            "cats" => $cats,
            "lots" => $lots,
            "bet_step" => $bet_step
        ]);
        header ("Location: /lot.php?id=$id_lot");
    }
} else {
    $main_content = include_template("lot.php", [
        "cats" => $cats,
        "lots" => $lots,
        "bet_step" => $bet_step
    ]);
}

echo include_template("layout.php", [
    "main_content" => $main_content,
    "title" => "Главная",
    "cats" => $cats
]);

