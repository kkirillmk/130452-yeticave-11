<?php
require_once "helpers.php";
require_once "init.php";
require_once "vendor/autoload.php";

$categories = getCategories($sql_connect);

$category_name = shieldedDataEntry($sql_connect, $_GET["category_name"]);
$cur_page = $_GET["page"] ?? 1;
$cur_page = mysqli_real_escape_string($sql_connect, $cur_page);
$page_items = 9;

$sql = "SELECT COUNT(lots.id) as cnt 
        FROM lots
            JOIN categories
                ON categories.id = lots.id_category
            WHERE lots.`date_end` > NOW() AND categories.`name` = '$category_name'";
$result = mysqli_query($sql_connect, $sql);
$items_count = mysqli_fetch_assoc($result)['cnt'];

$pages_count = ceil($items_count / $page_items);
$offset = ($cur_page - 1) * $page_items;
$pages = range(1, $pages_count);

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
        GROUP BY lots.`id` ORDER BY lots.`id` DESC LIMIT {$page_items} OFFSET {$offset}";
$lots = sqlToArray($sql_connect, $sql);

$main_content = include_template("all-lots.php", [
    "categories" => $categories,
    "lots" => $lots,
    "pages_count" => $pages_count,
    "pages" => $pages,
    "cur_page" => $cur_page
]);
echo include_template("layout.php", [
    "main_content" => $main_content,
    "title" => $category_name,
    "categories" => $categories
]);