<nav class="nav">
    <ul class="nav__list container">
        <?php foreach ($cats as $value): ?>
            <li class="nav__item">
                <a class="promo__link"
                   href="../all-lots.php?category_name=<?= $value["name"]; ?>"><?= $value["name"]; ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
<div class="container">
    <section class="lots">
        <h2>Все лоты в категории <span>«<?= $_GET["category_name"]; ?>»</span></h2>
        <ul class="lots__list">
            <?php foreach ($lots as $lot): ?>
                <li class="lots__item lot">
                    <div class="lot__image">
                        <img src="<?= $lot["img"]; ?>" width="350" height="260" alt="">
                    </div>
                    <div class="lot__info">
                        <span class="lot__category"><?= $lot["category"]; ?></span>
                        <h3 class="lot__title"><a class="text-link"
                                                  href="../lot.php?id=<?= $lot["id"]; ?>"><?= $lot["name"]; ?></a></h3>
                        <div class="lot__state">
                            <div class="lot__rate">
                                <?php if ($lot["current_price"]): ?>
                                    <span class="lot__amount">Ставок: <?= $lot["bets_count"] ?></span>
                                    <span class="lot__cost"><?= $lot["current_price"] ?><b class="rub">р</b></span>
                                <?php else: ?>
                                    <span class="lot__amount">Стартовая цена</span>
                                    <span class="lot__cost"><?= $lot["starting_price"] ?><b class="rub">р</b></span>
                                <?php endif; ?>
                            </div>
                            <div class="lot__timer timer
                                    <?php if (strpos((dateEndOfLot($lot["date_end"])), "00:") === 0): ?>
                                    timer--finishing
                                    <?php endif; ?>">
                                <?= dateEndOfLot($lot["date_end"]); ?>
                            </div>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
</div>