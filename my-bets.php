<?php
require_once "helpers.php";
require_once "init.php";
require_once "vendor/autoload.php";

if (empty($_SESSION)) {
    http_response_code(403);
    exit();
}

$id_user = $_SESSION["user"]["id"];

$cats = getCategories($sql_connect);
$sql = "SELECT `date_placing`, `bet_sum`, lots.name AS lot_name,
                lots.img, lots.date_end, cats.name AS category,
                bets.id_lot, users.contacts, `bets`.`id`
        FROM `bets`
            JOIN `lots` 
                ON `lots`.`id` = `bets`.`id_lot`
            JOIN `categories` cats 
                ON cats.`id` = `lots`.`id_category`
            JOIN `users` 
                ON `users`.`id` = `lots`.`id_author`
        WHERE `id_user` = '$id_user' ORDER BY `bets`.`id` DESC";
$bets = sqlToArray($sql_connect, $sql);

$win_bet_ids = [];
$all_winners = [];
foreach ($bets as $bet){
    if (strtotime($bet["date_end"]) <= time()) {
        $sql = "SELECT `id`, `id_user` FROM `bets`
                WHERE `id_lot` = {$bet["id_lot"]} ORDER BY `id` DESC LIMIT 1";
        $all_winners = sqlToArrayAssoc($sql_connect, $sql);

        if ($all_winners["id_user"] === $id_user) {
            $win_bet_ids[] = $all_winners["id"];
        }
    }
}
$win_bet_ids = array_unique($win_bet_ids);

$main_content = include_template("my-bets.php", [
    "cats" => $cats,
    "bets" => $bets,
    "win_bet_ids" => $win_bet_ids
]);
echo include_template("layout.php", [
    "main_content" => $main_content,
    "title" => "Главная",
    "cats" => $cats,
    "win_bet_ids" => $win_bet_ids
]);
