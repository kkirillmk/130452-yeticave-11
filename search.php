<?php
require_once "helpers.php";
require_once "init.php";

$cats = getCategories($sql_connect);

$lots = [];
$search = $_GET["search"] ?? "";

if ($search) {
    $sql = "SELECT lots.`id`, MATCH(lots.`name`, lots.`description`) AGAINST(?) AS relev, `img`, 
            cats.`name` AS category_name, lots.`name`, `starting_price`, 
            MAX(bets.`bet_sum`) AS current_price, `date_end`,
            COUNT(`id_lot`) AS bets_count FROM `lots`
            LEFT JOIN `bets` ON bets.`id_lot` = lots.`id`
            JOIN `categories` cats ON cats.`id` = lots.`id_category`
            WHERE MATCH(lots.`name`, lots.`description`) AGAINST(?)
            GROUP BY lots.`id` ORDER BY relev DESC";

    $stmt = db_get_prepare_stmt($sql_connect, $sql, [$search, $search]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $lots = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $main_content = include_template("search.php", ["cats" => $cats, "lots" => $lots]);
} else {
    $main_content = include_template("search.php", ["cats" => $cats]);
}

echo include_template("layout.php", [
    "main_content" => $main_content,
    "title" => "Главная",
    "cats" => $cats
]);
