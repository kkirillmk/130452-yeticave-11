<?php
require_once "helpers.php";
require_once "init.php";
require_once "vendor/autoload.php";

if (empty($_SESSION)) {
    http_response_code(403);
    exit();
}

$categories = getCategories($sql_connect);
$categories_ids = array_column($categories, 'id');

$id_author = $_SESSION["user"]["id"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $lot = filter_input_array(INPUT_POST, [
        "lot-name" => FILTER_DEFAULT,
        "category" => FILTER_DEFAULT,
        "message" => FILTER_DEFAULT,
        "lot-rate" => FILTER_DEFAULT,
        "lot-step" => FILTER_DEFAULT,
        "lot-date" => FILTER_DEFAULT
    ], true);

    $rules = [
        "category" => function ($value) use ($categories_ids) {
            return validateCategory($value, $categories_ids);
        },
        "lot-rate" => function ($value) {
            return validateGreaterThanZero($value);
        },
        "lot-date" => function ($date) {
            return validateDateEndOfLot($date);
        },
        "lot-step" => function ($value) {
            return validateIntGreaterThanZero($value);
        }
    ];
    foreach ($lot as $key => $value) {
        if (isset($rules[$key])) {
            $rule = $rules[$key];
            $errors[$key] = $rule($value);
        }

        if (empty($value)) {
            $errors[$key] = "Поле $key надо заполнить";
        }
    }

    $errors = array_filter($errors);

    $value_of_save = saveImage($lot, "lot-img", $errors);

    if (strpos($value_of_save, "uploads") === 0) {
        $lot["path"] = $value_of_save;
    } else {
        $errors["lot-img"] = $value_of_save;
    }

    if (!empty($errors)) {
        $main_content = include_template("add.php", [
                                        "lot" => $lot,
                                        "errors" => $errors,
                                        "categories" => $categories
        ]);
    } else {
        $sql = "INSERT INTO `lots` (`date_created`, `id_author`, `name`, `id_category`, `description`, `starting_price`,
                                    `bet_step`,  `date_end`, `img`)
                VALUES (NOW(), '$id_author', ?, ?, ?, ?, ?, ?, ?)";
        $stmt = db_get_prepare_stmt($sql_connect, $sql, $lot);
        $res = mysqli_stmt_execute($stmt);

        if ($res) {
            $lot_id = mysqli_insert_id($sql_connect);

            header("Location: lot.php?id=" . $lot_id);
        }
    }
} else {
    $main_content = include_template("add.php", ["categories" => $categories]);
}

echo include_template("layout.php", [
    "main_content" => $main_content,
    "title" => "Добавление лота",
    "categories" => $categories
]);
