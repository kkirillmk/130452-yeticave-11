<?php
require_once "helpers.php";

$sql_connect = connectDB("127.0.0.1", "root", "", "yeticave");

$sql = "SELECT * FROM `categories`";
$cats = sqlToArray($sql_connect, $sql);
$cats_ids = array_column($cats, 'id');

$lot = [];
$fields = [];
$errors = [];

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

    if (!empty($_FILES["lot-img"]["name"])) {
        $tmp_name = $_FILES["lot-img"]["tmp_name"];
        $file_type = mime_content_type($tmp_name);
        $file_name = "";

        switch ($file_type) {
            case "image/jpeg":
                $lot["path"] = "uploads/" . saveFormat($tmp_name, ".jpeg");
                break;
            case "image/png":
                $lot["path"] = "uploads/" . saveFormat($tmp_name, ".png");
                break;
            default:
                $errors["lot-img"] = "Загрузите картинку в формате .jpeg, .jpg или .png";
        }
    }
    else {
        $errors["lot-img"] = 'Вы не загрузили файл';
    }

    if (count($errors)) {
        $main_content = include_template("add.php", ["lot" => $lot,
                                        "errors" => $errors, "cats" => $cats]);
    } else {
        $sql = "INSERT INTO `lots` (`date_created`, `id_author`, `name`, `id_category`, `description`, `starting_price`,
                                    `bet_step`,  `date_end`, `img`)
                VALUES (NOW(), 1, ?, ?, ?, ?, ?, ?, ?)";
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
    "is_auth" => rand(0, 1),
    "user_name" => "Kirill",
    "cats" => $cats
]);
