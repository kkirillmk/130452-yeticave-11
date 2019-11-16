<nav class="nav">
    <ul class="nav__list container">
        <?php foreach ($cats as $value): ?>
            <li class="nav__item">
                <a href="../pages/all-lots.html"><?= $value["name"]; ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
<section class="lot-item container">
    <?php foreach ($lots as $lot): ?>
        <h2><?= $lot["name"]; ?></h2>
        <div class="lot-item__content">
            <div class="lot-item__left">
                <div class="lot-item__image">
                    <img src="<?= $lot["img"]; ?>" width="730" height="548" alt="Сноуборд">
                </div>
                <p class="lot-item__category">Категория: <span><?= $lot["category"]; ?></span></p>
                <p class="lot-item__description"><?= $lot["description"]; ?></p>
            </div>
            <div class="lot-item__right">
                <div class="lot-item__state">
                    <div class="lot__timer timer
                                    <?php if(strpos((dateEndOfLot($lot["date_end"])),"00:")): ?>
                                    timer--finishing
                                    <?php endif; ?>">
                        <?= dateEndOfLot($lot["date_end"]); ?>
                    </div>
                    <div class="lot-item__cost-state">
                        <?php if ($lot["current_price"]): ?>
                            <span class="lot__amount">Текущая цена</span>
                            <span class="lot__cost"><?= priceFormat($lot["current_price"]); ?></span>
                        <?php else: ?>
                            <span class="lot__amount">Стартовая цена</span>
                            <span class="lot__cost"><?= priceFormat($lot["starting_price"]); ?></span>
                        <?php endif; ?>
                        <div class="lot-item__min-cost">
                            Мин. ставка <span><?= priceFormat($lot["bet_step"]); ?> р</span>
                        </div>
                    </div>
            </div>
        </div>
    <?php endforeach; ?>
</section>