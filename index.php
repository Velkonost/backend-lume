<?php
include_once 'setting.php';

session_start();
$CONNECT = mysqli_connect(HOST, USER, PASSWORD, DB);
mysqli_query($CONNECT, "set names utf8");


// Разбиение адреса на элементы
if ($_SERVER['REQUEST_URI'] == '/') {
    $Page = 'index';
    $Module = 'index';
} else {
    $URL_Path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $URL_Parts = explode('/', trim($URL_Path, ' /'));
    $Page = array_shift($URL_Parts);
    $Module = array_shift($URL_Parts);

    if (!empty($Module)) {
        $Param = array();
        for ($i = 0; $i < count($URL_Parts); $i++) {
            $Param[$URL_Parts[$i]] = $URL_Parts[++$i];
        }
    }
}

if ($Page == 'account') include('script/account.php');
else if ($Page == 'dialogs') include('script/dialogs.php');
else if ($Page == 'kanban') include('script/kanban.php');


/**
 * Выдает случайную строку
 * @param $length - длина строки
 * @return string - получившаяся строка
 */
function RandomString($length)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

/**
 * Проверка строки на специальные символы
 * @param $p1 - изначальная строка
 * @return string - обработанная строка
 */
function FormChars($p1)
{
    return nl2br(htmlspecialchars(trim($p1), ENT_QUOTES), false);
}

/**
 * Генерация пароля пользователя
 *
 * @param $p1 - первая часть
 * @param $p2 - вторая часть
 * @return string - получившийся пароль
 */
function GenPass($p1, $p2)
{
    return md5(md5('IGWOUVYCBIWDOGUBPVRBI')
        . md5('wpire' . $p1 . '324732859')
        . md5('43870fbv' . $p2 . md5('43807gyb'))
        . md5($p1 . $p2 . md5($p1 . $p2)));
}

?>