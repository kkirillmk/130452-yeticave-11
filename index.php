<?php
require_once "helpers.php";
require_once "init.php";

$lots = [];
$cats = [];

$sql = "SELECT lots.`id`, lots.`name`, `starting_price`, `img`,
               MAX(bets.`bet_sum`) AS `current_price`,
               cats.`name`  AS `category`, `date_end` 
        FROM lots
            LEFT JOIN `bets` 
                ON bets.`id_lot` = lots.`id`
            JOIN `categories` cats 
                ON cats.`id` = lots.`id_category`

            WHERE lots.`date_end` > NOW()
            GROUP BY lots.`id` ORDER BY lots.`id` DESC";
$lots = sqlToArray($sql_connect, $sql);

$sql = "SELECT * FROM `categories`";
$cats = sqlToArray($sql_connect, $sql);

$main_content = include_template("main.php", [
    "cats" => $cats,
    "lots" => $lots
]);
echo include_template("layout.php", [
    "main_content" => $main_content,
    "title" => "Главная",
    "cats" => $cats
]);
