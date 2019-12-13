<nav class="nav">
    <ul class="nav__list container">
        <?php foreach ($cats as $value): ?>
            <li class="nav__item">
                <a href="../pages/all-lots.html"><?= $value["name"]; ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
<div class="container">
    <?php if ($_GET["search"]): ?>
        <section class="lots">
            <h2>Результаты поиска по запросу «<span><?= $_GET["search"] ?></span>»</h2>
            <ul class="lots__list">
                <?php foreach ($lots as $lot): ?>
                    <?php if ($lot["current_price"]): ?>
                        <li class="lots__item lot">
                            <div class="lot__image">
                                <img src="../<?= $lot["img"] ?>" width="350" height="260"
                                     alt="<?= $lot["category_name"] ?>">
                            </div>
                            <div class="lot__info">
                                <span class="lot__category"><?= $lot["category_name"] ?></span>
                                <h3 class="lot__title">
                                    <a class="text-link"
                                       href="../lot.php?id=<?= $lot["id"]; ?>"><?= $lot["name"] ?></a>
                                </h3>
                                <div class="lot__state">
                                    <div class="lot__rate">
                                        <span class="lot__amount"><?= $lot["bets_count"] ?> ставок</span>
                                        <span class="lot__cost"><?= $lot["current_price"] ?><b class="rub">р</b></span>
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
                    <?php else: ?>
                        <li class="lots__item lot">
                            <div class="lot__image">
                                <img src="../<?= $lot["img"] ?>" width="350" height="260"
                                     alt="<?= $lot["category_name"] ?>">
                            </div>
                            <div class="lot__info">
                                <span class="lot__category"><?= $lot["category_name"] ?></span>
                                <h3 class="lot__title">
                                    <a class="text-link"
                                       href="../lot.php?id=<?= $lot["id"]; ?>"><?= $lot["name"] ?></a>
                                </h3>
                                <div class="lot__state">
                                    <div class="lot__rate">
                                        <span class="lot__amount">Стартовая цена</span>
                                        <span class="lot__cost"><?= $lot["starting_price"] ?><b class="rub">р</b></span>
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
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </section>
    <?php else: ?>
        <h2>Ничего не найдено по вашему запросу</h2>
    <?php endif; ?>
</div>
