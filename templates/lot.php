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
<section class="lot-item container">
    <h2><?= $lots[0]["name"]; ?></h2>
    <div class="lot-item__content">
        <div class="lot-item__left">
            <div class="lot-item__image">
                <img src="<?= $lots[0]["img"]; ?>" width="730" height="548" alt="">
            </div>
            <p class="lot-item__category">Категория: <span><?= $lots[0]["category"]; ?></span></p>
            <p class="lot-item__description"><?= $lots[0]["description"]; ?></p>
        </div>
        <div class="lot-item__right">
            <div class="lot-item__state">
                <?php if (strtotime($lots[0]["date_end"]) <= time()): ?>
                    <div class="lot__timer timer timer--end">Торги окончены</div><br>
                    <div class="lot-item__cost-state">
                        <?php if ($lots[0]["current_price"]): ?>
                            <span class="lot__amount">Лот продан за:</span>
                            <span class="lot__cost"><?= priceFormat($lots[0]["current_price"]); ?></span>
                        <?php else: ?>
                            <span class="lot__amount">Лот не был продан</span>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="lot__timer timer <?php if (strpos((dateEndOfLot($lots[0]["date_end"])), "00:") === 0): ?>
                                        timer--finishing
                                        <?php endif; ?>">
                        <?= dateEndOfLot($lots[0]["date_end"]); ?>
                    </div>
                    <div class="lot-item__cost-state">
                        <?php if ($lots[0]["current_price"]): ?>
                            <span class="lot__amount">Текущая цена</span>
                            <span class="lot__cost"><?= priceFormat($lots[0]["current_price"]); ?></span>
                        <?php else: ?>
                            <span class="lot__amount">Стартовая цена</span>
                            <span class="lot__cost"><?= priceFormat($lots[0]["starting_price"]); ?></span>
                        <?php endif; ?>
                        <div class="lot-item__min-cost">
                            Мин. ставка <span><?= priceFormat($lots[0]["bet_step"]); ?> р</span>
                        </div>
                    </div>
                    <?php if ($_SESSION && $lots[0]["id_author"] !== $_SESSION["user"]["id"]
                                        && $last_bet[0]["id_user"] !== $_SESSION["user"]["id"]): ?>
                        <?php $classname = empty($errors) ? "" : "form--invalid"; ?>
                        <form class="lot-item__form <?= $classname; ?>" action="../lot.php?id=<?= htmlspecialchars($_GET["id"]); ?>"
                              method="post" autocomplete="off">
                            <?php $classname = !empty($errors) ? "form__item--invalid" : ""; ?>
                            <p class="lot-item__form-item form__item <?= $classname ?>">
                                <label for="cost">Ваша ставка</label>
                                <input id="cost" type="text" name="cost" placeholder="<?= $min_bet; ?>"
                                       value="<?= getPostVal("cost") ?>">
                                <?php if (!empty($errors)): ?>
                                    <span class="form__error">Введите корректную ставку</span>
                                <?php endif; ?>
                            </p>
                            <button type="submit" class="button">Сделать ставку</button>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="history">
                <h3>История ставок:</h3>
                <table class="history__list">
                    <?php foreach ($bets as $bet): ?>
                        <tr class="history__item">
                            <td class="history__name"><?= $bet["name"]; ?></td>
                            <td class="history__price"><?= priceFormat($bet["bet_sum"]); ?></td>
                            <td class="history__time"><?= countingFromTheDateInHours($bet["date_placing"]) . " " .
                                get_noun_plural_form(((int)countingFromTheDateInHours($bet["date_placing"])),
                                    "час", "часа", "часов") ?> назад</td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
</section>
