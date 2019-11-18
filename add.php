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
            "lot-img" => FILTER_DEFAULT,
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



    if (!empty($_FILES["lot-img"]["name"])) {
        $tmp_name = $_FILES["lot-img"]["tmp_name"];
        $path = $_FILES["lot-img"]["name"];
        $filename = uniqid() . ".***";
    }
}

$main_content = include_template("add.php", [
    "cats" => $cats,
]);
echo include_template("layout.php", [
    "main_content" => $main_content,
    "title" => "Главная",
    "is_auth" => rand(0, 1),
    "user_name" => "Kirill",
    "cats" => $cats
]);
