<nav class="nav">
    <ul class="nav__list container">
        <?php foreach ($categories as $category): ?>
            <li class="nav__item">
                <a class="promo__link"
                   href="../all-lots.php?category_name=<?= $category["name"]; ?>"><?= $category["name"]; ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
<section class="rates container">
    <h2>Мои ставки</h2>
    <table class="rates__list">
        <?php foreach ($bets as $bet): ?>
            <?php if (in_array($bet["id"], $win_bet_ids)) {
                $classname = "rates__item--win";
            } elseif (strtotime($bet["date_end"]) <= time()) {
                $classname = "rates__item--end";
            } else {
                $classname = "";
            } ?>
            <tr class="rates__item <?= $classname; ?>">
                <td class="rates__info">
                    <div class="rates__img">
                        <img src="../<?= $bet["img"]; ?>" width="54" height="40" alt="Сноуборд">
                    </div>
                    <div>
                        <h3 class="rates__title">
                            <a href="lot.php?id=<?= $bet["id_lot"]; ?>"><?= htmlspecialchars($bet["lot_name"]); ?></a>
                        </h3>
                        <p><?php if (in_array($bet["id"], $win_bet_ids)) {
                                echo $bet["contacts"];
                            } ?></p>
                    </div>
                </td>
                <td class="rates__category">
                    <?= $bet["category"]; ?>
                </td>
                <td class="rates__timer">
                    <?php if (in_array($bet["id"], $win_bet_ids)): ?>
                        <div class="timer timer--win">Ставка выиграла</div>
                    <?php elseif (strtotime($bet["date_end"]) <= time()): ?>
                        <div class="timer timer--end">Торги окончены</div>
                    <?php else: ?>
                        <div class="timer <?php if (strpos((dateEndOfLot($bet["date_end"])), "00:") === 0): ?>
                                        timer--finishing
                                        <?php endif; ?>">
                            <?= dateEndOfLot($bet["date_end"]); ?>
                        </div>
                    <?php endif; ?>
                </td>
                <td class="rates__price">
                    <?= priceFormat($bet["bet_sum"]); ?>
                </td>
                <td class="rates__time">
                    <?= $bet["date_placing"] . "<br>" . countingFromTheDateInHours($bet["date_placing"]) . " " .
                    getNounPluralForm(((int)countingFromTheDateInHours($bet["date_placing"])),
                        "час", "часа", "часов") ?> назад
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</section>
