<h1>Поздравляем с победой</h1>
<p>Здравствуйте, <?= htmlspecialchars($win_bet["name"]); ?></p>
<p>Ваша ставка для лота
    <a href="http://yeticave/lot.php?id=<?= $win_bet["id_lot"]; ?>"><?= htmlspecialchars($win_bet["title"]); ?></a>
    победила.</p>
<p>Перейдите по ссылке <a href="http://yeticave/my-bets.php">мои ставки</a>,
    чтобы связаться с автором объявления</p>
<small>Интернет Аукцион "YetiCave"</small>
