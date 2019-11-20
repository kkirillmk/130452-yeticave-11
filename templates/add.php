<nav class="nav">
  <ul class="nav__list container">
      <?php foreach ($cats as $value): ?>
          <li class="nav__item">
              <a href="../pages/all-lots.html"><?= $value["name"]; ?></a>
          </li>
      <?php endforeach; ?>
  </ul>
</nav>
<?php $classname = empty($errors) ? "" : "form--invalid"; ?>
<form class="form form--add-lot container <?= $classname; ?>" action="../add.php" method="post"
      enctype="multipart/form-data">
  <h2>Добавление лота</h2>
  <div class="form__container-two">
    <?php $classname = isset($errors["lot-name"]) ? "form__item--invalid" : ""; ?>
    <div class="form__item <?= $classname; ?>">
      <label for="lot-name">Наименование <sup>*</sup></label>
      <input id="lot-name" type="text" name="lot-name" placeholder="Введите наименование лота"
             value="<?= getPostVal("lot-name") ?>">
      <span class="form__error">Введите наименование лота</span>
    </div>
      <?php $classname = isset($errors["category"]) ? "form__item--invalid" : ""; ?>
    <div class="form__item <?= $classname; ?>">
      <label for="category">Категория <sup>*</sup></label>
      <select id="category" name="category">
        <option>Выберите категорию</option>
          <?php foreach ($cats as $value): ?>
              <option value="<?= $value["id"] ?>" <?php if ($value["id"] === getPostVal("category")): ?> selected <?php endif; ?>>
              <?= $value["name"]; ?></option>
          <?php endforeach; ?>
      </select>
      <span class="form__error">Выберите категорию</span>
    </div>
  </div>
  <?php $classname = isset($errors["message"]) ? "form__item--invalid" : ""; ?>
  <div class="form__item form__item--wide <?= $classname; ?>">
    <label for="message">Описание <sup>*</sup></label>
    <textarea id="message" name="message"
              placeholder="Напишите описание лота"><?= getPostVal("message") ?></textarea>
    <span class="form__error">Напишите описание лота</span>
  </div>
  <?php $classname = isset($errors["lot-img"]) ? "form__item--invalid" : ""; ?>
  <div class="form__item form__item--file <?= $classname; ?>">
    <label>Изображение <sup>*</sup></label>
    <div class="form__input-file <?= $classname; ?>">
      <input class="visually-hidden" type="file" id="lot-img" name="lot-img" value="">
      <label for="lot-img">
Добавить
      </label>
    </div>
  </div>
  <div class="form__container-three">
      <?php $classname = isset($errors["lot-rate"]) ? "form__item--invalid" : ""; ?>
      <div class="form__item form__item--small <?= $classname; ?>">
          <label for="lot-rate">Начальная цена <sup>*</sup></label>
          <input id="lot-rate" type="text" name="lot-rate" placeholder="0" value="<?= getPostVal("lot-rate") ?>">
          <span class="form__error">Введите начальную цену</span>
      </div>
      <?php $classname = isset($errors["lot-step"]) ? "form__item--invalid" : ""; ?>
    <div class="form__item form__item--small <?= $classname; ?>">
      <label for="lot-step">Шаг ставки <sup>*</sup></label>
      <input id="lot-step" type="text" name="lot-step" placeholder="0" value="<?= getPostVal("lot-step") ?>">
      <span class="form__error">Введите шаг ставки</span>
    </div>
      <?php $classname = isset($errors["lot-date"]) ? "form__item--invalid" : ""; ?>
    <div class="form__item <?= $classname; ?>">
      <label for="lot-date">Дата окончания торгов <sup>*</sup></label>
      <input class="form__input-date" id="lot-date" type="text" name="lot-date"
             placeholder="Введите дату в формате ГГГГ-ММ-ДД" value="<?= getPostVal("lot-date") ?>">
      <span class="form__error">Введите дату завершения торгов</span>
    </div>
  </div>
  <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме:</span>
    <ul>
        <?php if (!empty($errors)):
        foreach ($errors as $val): ?>
            <li><strong><?= $val; ?></strong></li>
        <?php endforeach; endif;?>
    </ul>
  <button type="submit" class="button">Добавить лот</button>
</form>
