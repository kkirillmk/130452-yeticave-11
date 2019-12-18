<?php
require_once "vendor/autoload.php";
/**
 * Проверяет переданную дату на соответствие формату 'ГГГГ-ММ-ДД'
 *
 * Примеры использования:
 * is_date_valid('2019-01-01'); // true
 * is_date_valid('2016-02-29'); // true
 * is_date_valid('2019-04-31'); // false
 * is_date_valid('10.10.2010'); // false
 * is_date_valid('10/10/2010'); // false
 *
 * @param string $date Дата в виде строки
 *
 * @return bool true при совпадении с форматом 'ГГГГ-ММ-ДД', иначе false
 */
function is_date_valid(string $date) : bool {
    $format_to_check = 'Y-m-d';
    $dateTimeObj = date_create_from_format($format_to_check, $date);

    return $dateTimeObj !== false && array_sum(date_get_last_errors()) === 0;
}

function dateEndOfLot($end_date) {
    date_default_timezone_set("Asia/Novosibirsk");

    if (is_date_valid($end_date)) {
        $cur_ts_time = time();
        $end_date = strtotime($end_date);
        $ts_remain = $end_date - $cur_ts_time;
        $hours = floor($ts_remain / 3600);
        $minutes = floor(($ts_remain % 3600) / 60);
        $hours = str_pad($hours, 2, "0", STR_PAD_LEFT);
        $minutes = str_pad($minutes, 2, "0", STR_PAD_LEFT);
        return $hours . ":" . $minutes;
    }
    else {
        return false;
    }
};

/**
 * Создает подготовленное выражение на основе готового SQL запроса и переданных данных
 *
 * @param $link mysqli Ресурс соединения
 * @param $sql string SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return mysqli_stmt Подготовленное выражение
 */
function db_get_prepare_stmt($link, $sql, $data = []) {
    $stmt = mysqli_prepare($link, $sql);

    if ($stmt === false) {
        $errorMsg = 'Не удалось инициализировать подготовленное выражение: ' . mysqli_error($link);
        die($errorMsg);
    }

    if ($data) {
        $types = '';
        $stmt_data = [];

        foreach ($data as $value) {
            $type = 's';

            if (is_int($value)) {
                $type = 'i';
            }
            else if (is_string($value)) {
                $type = 's';
            }
            else if (is_double($value)) {
                $type = 'd';
            }

            if ($type) {
                $types .= $type;
                $stmt_data[] = $value;
            }
        }

        $values = array_merge([$stmt, $types], $stmt_data);

        $func = 'mysqli_stmt_bind_param';
        $func(...$values);

        if (mysqli_errno($link) > 0) {
            $errorMsg = 'Не удалось связать подготовленное выражение с параметрами: ' . mysqli_error($link);
            die($errorMsg);
        }
    }

    return $stmt;
}

/**
 * Возвращает корректную форму множественного числа
 * Ограничения: только для целых чисел
 *
 * Пример использования:
 * $remaining_minutes = 5;
 * echo "Я поставил таймер на {$remaining_minutes} " .
 *     get_noun_plural_form(
 *         $remaining_minutes,
 *         'минута',
 *         'минуты',
 *         'минут'
 *     );
 * Результат: "Я поставил таймер на 5 минут"
 *
 * @param int $number Число, по которому вычисляем форму множественного числа
 * @param string $one Форма единственного числа: яблоко, час, минута
 * @param string $two Форма множественного числа для 2, 3, 4: яблока, часа, минуты
 * @param string $many Форма множественного числа для остальных чисел
 *
 * @return string Рассчитанная форма множественнго числа
 */
function get_noun_plural_form (int $number, string $one, string $two, string $many): string
{
    $number = (int) $number;
    $mod10 = $number % 10;
    $mod100 = $number % 100;

    switch (true) {
        case ($mod100 >= 11 && $mod100 <= 20):
            return $many;

        case ($mod10 > 5):
            return $many;

        case ($mod10 === 1):
            return $one;

        case ($mod10 >= 2 && $mod10 <= 4):
            return $two;

        default:
            return $many;
    }
}

/**
 * Подключает шаблон, передает туда данные и возвращает итоговый HTML контент
 * @param string $name Путь к файлу шаблона относительно папки templates
 * @param array $data Ассоциативный массив с данными для шаблона
 * @return string Итоговый HTML
 */
function include_template($name, array $data = []) {
    $name = 'templates/' . $name;
    $result = '';

    if (!is_readable($name)) {
        return $result;
    }

    ob_start();
    extract($data);
    require $name;

    $result = ob_get_clean();

    return $result;
}

function priceFormat($price) {
    $price = ceil($price);

    if ($price < 1000) {
        return $price;
    }

    $result = number_format($price, 0, '.',' ');
    return $result . " ₽";
}

function connectDB($host, $user, $password, $database) {
    $sql_connect = mysqli_connect($host, $user, $password, $database);
    mysqli_set_charset($sql_connect, "utf-8");

    if (!$sql_connect) {
        echo ("Ошибка подключения: " . mysqli_connect_error());
        exit;
    }

    return $sql_connect;
}

function sqlToArray($sql_connect, $sql) {
    $result = mysqli_query($sql_connect, $sql);

    if (!$result) {
        echo ("Ошибка запроса: " . mysqli_error($sql_connect));
        exit;
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function sqlToArrayAssoc($sql_connect, $sql) {
    $result = mysqli_query($sql_connect, $sql);

    if (!$result) {
        echo ("Ошибка запроса: " . mysqli_error($sql_connect));
        exit;
    }

    return mysqli_fetch_assoc($result);
}

function validateGreaterThanZero($value) {

    if (!is_numeric($value)){
        return "Введенное значение не является числом или равно нулю";
    } elseif ($value <= 0) {
        return "Введенное значение меньше нуля";
    }

    return null;
}

function validateDateEndOfLot($selected_date) {
    date_default_timezone_set("Asia/Novosibirsk");
    $ts_selected_date = strtotime($selected_date);
    $tomorrow = new DateTime('tomorrow');
    $tomorrow = $tomorrow->format("U");

    if (!is_date_valid($selected_date)) {
        return "Дата введена в неправильном формате (ГГГГ-ММ-ДД)";
    } elseif (!($ts_selected_date >= $tomorrow)) {
        return "Указанная дата должна быть больше текущей даты, хотя бы на один день";
    }

    return null;
}

function validateIntGreaterThanZero($value) {

    if (!filter_var($value, FILTER_VALIDATE_INT)){
        return "Введенное значение не является целым числом или равно нулю";
    } elseif ($value < 0) {
        return "Введенное значение меньше нуля";
    }

    return null;
}

function validateCategory($id, $allowed_list) {

    if (!in_array($id, $allowed_list)) {
        return "Указана несуществующая категория";
    }

    return null;
}

function maxLength255($value) {
    $length = mb_strlen($value, "utf-8");

    if ($length >= 256){
        return "Введенное значение является слишком большим (Максимальное количество символов - 255)";
    }

    return null;
}

function maxLength127($value) {
    $length = mb_strlen($value, "utf-8");

    if ($length >= 128){
        return "Введенное значение является слишком большим (Максимальное количество символов - 127)";
    }

    return null;
}

function maxLength9($value) {
    $length = strlen($value);

    if ($length >= 10){
        return "Введенное значение является слишком большим (Максимальное количество символов - 10)";
    }

    return null;
}

function getPostVal($name) {
    $name = filter_input(INPUT_POST, $name);
    return htmlspecialchars($name);
}

function getCategories($sql_connect) {
    $sql = "SELECT * FROM `categories`";
    return sqlToArray($sql_connect, $sql);
}

function saveFormat($tmp_name, string $format) {
    $file_name = uniqid() . $format;
    move_uploaded_file($tmp_name, 'uploads/' . $file_name);
    return $file_name;
}

function saveImage($post, string $name_image, $errors, $path = "path") {

    if (!empty($_FILES[$name_image]["name"])) {
        $tmp_name = $_FILES[$name_image]["tmp_name"];
        $file_type = mime_content_type($tmp_name);

        switch ($file_type) {
            case "image/jpeg":
                return $post[$path] = "uploads/" . saveFormat($tmp_name, ".jpeg");
            case "image/png":
                return $post[$path] = "uploads/" . saveFormat($tmp_name, ".png");
            default:
                return $errors[$name_image] = "Загрузите картинку в формате .jpeg, .jpg или .png";
        }
    }
    else {
        return $errors[$name_image] = 'Вы не загрузили файл';
    }
}

function getUserIDByEmail($email) {
    return "SELECT `id` FROM `users` WHERE `email` = '$email'";
}

function getUserByEmail($email) {
    return "SELECT * FROM `users` WHERE `email` = '$email'";
}

function countingFromTheDateInHours($date_of_reference) {
    date_default_timezone_set("Asia/Novosibirsk");
    $date_of_reference = strtotime($date_of_reference);
    $current_time = time();
    return floor(($current_time - $date_of_reference)/(60*60));
}

function dbInsertData($sql_connect, $sql, $data = []) {
    $stmt = db_get_prepare_stmt($sql_connect, $sql, $data);
    $result = mysqli_stmt_execute($stmt);

    return $result;
}

function dbFetchData($sql_connect, $sql, $data = []) {
    $result = [];
    $stmt = db_get_prepare_stmt($sql_connect, $sql, $data);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if ($res) {
        $result = mysqli_fetch_all($res, MYSQLI_ASSOC);
    }

    return $result;
}

function numberOfPreviousPage($cur_page) {
    $check_on_page = $cur_page - 1;

    if ($check_on_page != 0) {
        return $cur_page - 1;
    }

    return $cur_page;
}

function numberOfNextPage($cur_page, $pages_count) {

    if ($cur_page == $pages_count) {
        return $cur_page;
    }

    return $cur_page + 1;
}

function shieldedDataEntry($sql_connect, $input_data) {
    $result = $input_data ?? "";
    $result = mysqli_real_escape_string($sql_connect, $result);

    return $result;
}