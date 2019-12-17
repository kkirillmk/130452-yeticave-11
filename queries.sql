INSERT INTO categories (name, character_code)
VALUES ('Доски и лыжи', 'boards'),
       ('Крепления', 'attachment'),
       ('Ботинки', 'boots'),
       ('Одежда', 'clothing'),
       ('Инструменты', 'tools'),
       ('Разное', 'other');
INSERT INTO users (date_registration, email, name, password, contacts)
VALUES ('2019-10-22 12:00:00', 'kirillmk_kmk@mail.ru', 'Kirill', '12321', 'kirillmk_kmk@mail.ru'),
       ('2019-10-22 13:00:00', 'alex@mail.ru', 'Alex', 'qwerty', 'alex@mail.ru'),
       ('2019-10-22 14:00:00', 'artur@mail.ru', 'Artur', 'aezakmi', 'artur@mail.ru');
INSERT INTO lots (id_category, id_author, date_created, name, description, img, starting_price, date_end, bet_step)
VALUES ('1', '1', '2019-10-28 23:00:00', '2014 Rossignol District Snowboard', '123', 'img/lot-1.jpg', '10999',
        '2019-12-30', '500'),
       ('1', '1', '2019-10-28 23:00:00', 'DC Ply Mens 2016/2017 Snowboard', '321', 'img/lot-2.jpg', '159999',
        '2019-12-29', '1000'),
       ('2', '1', '2019-10-28 23:00:00', 'Крепления Union Contact Pro 2015 года размер L/XL', '123', 'img/lot-3.jpg',
        '8000', '2019-12-28', '200'),
       ('3', '3', '2019-10-28 23:00:00', 'Ботинки для сноуборда DC Mutiny Charocal', '321', 'img/lot-4.jpg', '10999',
        '2019-12-27', '500'),
       ('4', '2', '2019-10-28 23:00:00', 'Куртка для сноуборда DC Mutiny Charocal', '123', 'img/lot-5.jpg', '7500',
        '2019-12-26', '100'),
       ('6', '3', '2019-10-28 23:00:00', 'Маска Oakley Canopy', '321', 'img/lot-6.jpg', '5400', '2019-12-25', '100');
INSERT INTO bets (id_user, id_lot, date_placing, bet_sum)
VALUES ('2', '5', '2019-11-10 11:30:00', '5500'),
       ('1', '5', '2019-11-10 12:31:30', '5600'),
       ('3', '1', '2019-11-10 13:02:00', '11499'),
       ('3', '3', '2019-11-10 13:35:21', '8200');

SELECT name
FROM categories;

SELECT l.name, starting_price, img, MAX(b.bet_sum) AS current_price, c.name AS category, date_end
FROM lots l
         LEFT JOIN bets b ON b.id_lot = l.id
         JOIN categories c ON c.id = l.id_category
WHERE l.date_end > NOW()
GROUP BY l.id
ORDER BY l.id;

SELECT l.*, c.name
FROM lots l
         JOIN categories c on c.id = l.id_category
WHERE l.id = 1;

UPDATE lots
SET name = '2014 Rossignol District Snowboard1'
WHERE id = 1;

SELECT *
FROM bets
WHERE id_lot = 5
ORDER BY date_placing;
