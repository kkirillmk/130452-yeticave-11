<?php
require_once "helpers.php";
require_once "init.php";

if (empty($_SESSION)) {
    http_response_code(403);
    exit();
}

$cats = getCategories($sql_connect);
$cats_ids = array_column($cats, 'id');

$lot = [];
$fields = [];
$errors = [];
$id_author = $_SESSION["user"]["id"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fields = ["lot-name", "category", "message", "lot-img", "lot-rate", "lot-step", "lot-date"];

    $rules = [
        "category" => function ($value) use ($cats_ids) {
            return validateCategory($value, $cats_ids);
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

    $lot = filter_input_array(INPUT_POST, [
            "lot-name" => FILTER_DEFAULT,
            "category" => FILTER_DEFAULT,
            "message" => FILTER_DEFAULT,
            "lot-rate" => FILTER_DEFAULT,
            "lot-step" => FILTER_DEFAULT,
            "lot-date" => FILTER_DEFAULT
            ], true
    );

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

    if (count($errors) !== 0) {
        $main_content = include_template("add.php", ["lot" => $lot,
                                        "errors" => $errors, "cats" => $cats]);
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
    $main_content = include_template("add.php", ["cats" => $cats]);
}

echo include_template("layout.php", [
    "main_content" => $main_content,
    "title" => "Главная",
    "cats" => $cats
]);
