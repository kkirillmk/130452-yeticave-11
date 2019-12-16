<?php
require_once "helpers.php";
require_once "init.php";
require_once "vendor/autoload.php";

$categories = getCategories($sql_connect);

$lots = [];

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $search = trim($search = $_GET["search"]);
    $cur_page = $_GET['page'] ?? 1;
    $page_items = 9;

    $sql = "SELECT COUNT(lots.id) as cnt 
        FROM lots
            JOIN categories
                ON categories.id = lots.id_category
            WHERE MATCH(lots.`name`, lots.`description`) AGAINST(?)
                AND lots.`date_end` > NOW()";
    $stmt = db_get_prepare_stmt($sql_connect, $sql, [$search]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $items_count = mysqli_fetch_assoc($result)['cnt'];

    $pages_count = ceil($items_count / $page_items);
    $offset = ($cur_page - 1) * $page_items;
    $pages = range(1, $pages_count);


    $sql = "SELECT lots.`id`, `img`, cats.`name` AS category_name,
                    lots.`name`, `starting_price`, 
                    MAX(bets.`bet_sum`) AS current_price,
                    `date_end`, COUNT(`id_lot`) AS bets_count 
                FROM `lots`
                    LEFT JOIN `bets` 
                        ON bets.`id_lot` = lots.`id`
                    JOIN `categories` cats 
                        ON cats.`id` = lots.`id_category`
            WHERE MATCH(lots.`name`, lots.`description`) AGAINST(?)
                AND `lots`.`date_end` > NOW()
            GROUP BY `lots`.`id` 
            ORDER BY lots.id DESC LIMIT ? OFFSET ?";

    $stmt = db_get_prepare_stmt($sql_connect, $sql, [$search, $page_items, $offset]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $lots = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $main_content = include_template("search.php", [
        "categories" => $categories,
        "lots" => $lots,
        "pages_count" => $pages_count,
        "pages" => $pages,
        "cur_page" => $cur_page
    ]);
} else {
    $main_content = include_template("search.php", ["categories" => $categories]);
}

echo include_template("layout.php", [
    "main_content" => $main_content,
    "title" => "Поиск",
    "categories" => $categories
]);
