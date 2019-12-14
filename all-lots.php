<?php
require_once "helpers.php";
require_once "init.php";
require_once "vendor/autoload.php";

$cats = getCategories($sql_connect);

$category_name = $_GET["category_name"] ?? "";

$sql = "SELECT lots.`id`, lots.`name`, `starting_price`, `img`,
               MAX(bets.`bet_sum`) AS `current_price`,
               cats.`name`  AS `category`, `date_end`,
               COUNT(`id_lot`) AS bets_count
        FROM lots
            LEFT JOIN `bets` 
                ON bets.`id_lot` = lots.`id`
            JOIN `categories` cats 
                ON cats.`id` = lots.`id_category`

            WHERE lots.`date_end` > NOW() AND cats.`name` = '$category_name'
            GROUP BY lots.`id` ORDER BY lots.`id` DESC";
$lots = sqlToArray($sql_connect, $sql);

$main_content = include_template("all-lots.php", [
    "cats" => $cats,
    "lots" => $lots
]);
echo include_template("layout.php", [
    "main_content" => $main_content,
    "title" => "Главная",
    "cats" => $cats
]);