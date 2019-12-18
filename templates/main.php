<section class="promo">
    <h2 class="promo__title">Нужен стафф для катки?</h2>
    <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное
        снаряжение.</p>
    <ul class="promo__list">
        <?php foreach ($categories as $category): ?>
            <li class="promo__item promo__item--<?= $category["character_code"] ?>">
                <a class="promo__link"
                   href="../all-lots.php?category_name=<?= $category["name"]; ?>"><?= $category["name"]; ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</section>
<section class="lots">
    <div class="lots__header">
        <h2>Открытые лоты</h2>
    </div>
    <ul class="lots__list">
        <?php foreach ($lots as $lot): ?>
            <li class="lots__item lot">
                <div class="lot__image">
                    <img src="<?= $lot["img"]; ?>" width="350" height="260" alt="">
                </div>
                <div class="lot__info">
                    <span class="lot__category"><?= $lot["category"]; ?></span>
                    <h3 class="lot__title">
                        <a class="text-link"
                           href="../lot.php?id=<?= $lot["id"]; ?>"><?= htmlspecialchars($lot["name"]); ?></a>
                    </h3>
                    <div class="lot__state">
                        <div class="lot__rate">
                            <?php if ($lot["current_price"]): ?>
                                <span class="lot__amount">Текущая цена</span>
                                <span class="lot__cost"><?= priceFormat($lot["current_price"]); ?></span>
                            <?php else: ?>
                                <span class="lot__amount">Стартовая цена</span>
                                <span class="lot__cost"><?= priceFormat($lot["starting_price"]); ?></span>
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
