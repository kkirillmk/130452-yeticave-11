<?php
require_once "helpers.php";
require_once "init.php";
require_once "vendor/autoload.php";

$sql = "SELECT lots.id AS id_lot, 
               lots.name AS title, 
               bets.id AS id_bet,
    	       users.email, users.name, 
               bets.id_user
	      FROM lots
             JOIN (SELECT id_lot, MAX(bet_sum) AS max_bet_sum FROM bets GROUP BY id_lot) AS max_bets
                 ON lots.`id` = max_bets.`id_lot`
             JOIN bets
                 ON lots.`id` = bets.`id_lot` AND bets.bet_sum = max_bets.max_bet_sum
             JOIN users 
                 ON users.`id` = bets.`id_user`
          
             WHERE lots.`date_end` <= NOW() AND lots.id_winner IS NULL";
$win_bets = sqlToArray($sql_connect, $sql);


$id_user = $_SESSION["user"]["id"] ?? "";

foreach ($win_bets as $win_bet) {
    if ($win_bet["id_user"] === $id_user) {
        $sql = "UPDATE lots SET id_winner = {$win_bet["id_user"]} WHERE id = {$win_bet["id_lot"]}";
        $result = mysqli_query($sql_connect, $sql);

        if ($result === true) {
            $transport = new Swift_SmtpTransport("phpdemo.ru", 25);
            $transport->setUsername("keks@phpdemo.ru");
            $transport->setPassword("htmlacademy");

            $mailer = new Swift_Mailer($transport);

            $message = new Swift_Message();
            $message->setSubject("Ваша ставка победила");
            $message->setFrom(['keks@phpdemo.ru' => 'keks@phpdemo.ru']);
            $message->setBcc($win_bet["email"]);

            $message_content = include_template('email.php', ['win_bet' => $win_bet]);
            $message->setBody($message_content, 'text/html');

            $result = $mailer->send($message);
        }
    }
}