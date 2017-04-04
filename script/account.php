<?php

switch ($Module) {
    case 'editImage':
        editImage($CONNECT, $_POST['image'], $_POST['id']);
        break;
    case 'registration':
        registration($CONNECT, $_POST['login'], $_POST['password']);
        break;
    case 'getData':
        getData($CONNECT, $_POST['id'], $_POST['user_id']);
        break;
    case 'getDataSettings':
        getDataSettings($CONNECT, $_POST['user_id']);
        break;
    case 'edit':
        edit($CONNECT, $_POST['user_id'], $_POST['name'], $_POST['surname'], $_POST['city'], $_POST['country'],
            $_POST['study'], $_POST['work'], $_POST['email'], $_POST['work_email'], $_POST['birthday'],
            $_POST['prev_password'], $_POST['new_password']);
        break;
    case 'short_edit':
        shortEdit($CONNECT, $_POST['user_id'], $_POST['city'], $_POST['country'], $_POST['study'], $_POST['work'],
            $_POST['work_email'], $_POST['birthday']);
        break;
    case 'search':
        search($CONNECT, $_POST['search']);
        break;
    case 'addContact':
        addContact($CONNECT, $_POST['send_id'], $_POST['get_id']);
        break;
    case 'getContacts':
        getContacts($CONNECT, $_POST['id']);
        break;
}

/**
 * Получение списка контактов пользователя
 * @param $connect - соединение
 * @param $post_id - идентификатор пользователя
 */
function getContacts($connect, $post_id)
{
    $post_id = FormChars($post_id);

    $response = array();
    $ids = array();

    $getContacts = mysqli_query($connect, "SELECT `gid` FROM `contacts` WHERE `sid` = '$post_id'");

    while ($getContact = mysqli_fetch_assoc($getContacts)) {
        $userId = $getContact['gid'];

        $getUserInfo = mysqli_fetch_assoc(mysqli_query($connect, "SELECT `id`, `login`, `name`, `surname`, `avatar` 
				FROM `users` WHERE `id` = '$userId'"));

        $userInfo = array();

        foreach ($getUserInfo as $key => $value) {
            $userInfo[$key] = $value;
        }
        $response[$userInfo['id']] = $userInfo;
        array_push($ids, $userInfo['id']);
    }
    $response['ids'] = array_values(array_unique($ids));;

    echo json_encode($response);
}


/**
 * Добавление контакта
 * @param $connect - соединение
 * @param $post_send_id - идентификатор пользователя, отправившего запрос
 * @param $post_get_id - идентификатор добавляемого контакта
 */
function addContact($connect, $post_send_id, $post_get_id)
{
    $post_send_id = FormChars($post_send_id);
    $post_get_id = FormChars($post_get_id);

    $response = array();

    $checkExist = mysqli_fetch_assoc(mysqli_query($connect,
        "SELECT `id` FROM `contacts` WHERE `sid` = '$post_send_id' AND `gid` = '$post_get_id'"));

    if ($checkExist) {
        $response['addContact'] = 401; // contact already exist
        mysqli_query($connect, "DELETE FROM `contacts` WHERE `sid` = '$post_send_id' AND `gid` = '$post_get_id'");
    } else {
        $response['addContact'] = 400;

        mysqli_query($connect, "INSERT INTO `contacts` VALUES ('', '$post_send_id', '$post_get_id')");
    }
    echo json_encode($response);
}

/**
 * Поиск
 * @param $connect - соединение
 * @param $post_search - строка для поиска
 */
function search($connect, $post_search)
{
    $post_search = strtolower(FormChars($post_search));

    $response = array();
    $ids = array();

    $wordsToSearch = explode(" ", $post_search);

    foreach ($wordsToSearch as $value) {

        $getUsersDataByLogin = mysqli_query($connect, "SELECT `id`, `login`, `avatar`, `country`, `city`,
			 `name`, `surname`, `study`, `work` FROM `users` WHERE `login` LIKE '%$value%'");
        $getUsersDataByName = mysqli_query($connect, "SELECT `id`, `login`, `avatar`, `country`, `city`,
			 `name`, `surname`, `study`, `work` FROM `users` WHERE `name` LIKE '%$value%'");
        $getUsersDataBySurname = mysqli_query($connect, "SELECT `id`, `login`, `avatar`, `country`, `city`,
			 `name`, `surname`, `study`, `work` FROM `users` WHERE `surname` LIKE '%$value%'");


        while ($getUserDataByLogin = mysqli_fetch_assoc($getUsersDataByLogin)) {

            $userSearchedInfo = array();
            foreach ($getUserDataByLogin as $key => $value) {
                $userSearchedInfo[$key] = $value;
            }
            $response[$userSearchedInfo['id']] = $userSearchedInfo;
            array_push($ids, $userSearchedInfo['id']);
        }

        while ($getUserDataByName = mysqli_fetch_assoc($getUsersDataByName)) {

            $userSearchedInfo = array();
            foreach ($getUserDataByName as $key => $value) {
                $userSearchedInfo[$key] = $value;
            }
            $response[$userSearchedInfo['id']] = $userSearchedInfo;
            array_push($ids, $userSearchedInfo['id']);
        }

        while ($getUserDataBySurname = mysqli_fetch_assoc($getUsersDataBySurname)) {

            $userSearchedInfo = array();
            foreach ($getUserDataBySurname as $key => $value) {
                $userSearchedInfo[$key] = $value;
            }
            $response[$userSearchedInfo['id']] = $userSearchedInfo;
            array_push($ids, $userSearchedInfo['id']);
        }
    }
    $response['ids'] = array_values(array_unique($ids));;
    echo json_encode($response);
}

/**
 * Неполное изменение настроек
 * @param $connect - соединение
 * @param $post_user_id - идентификатор пользователя
 * @param $post_city - город проживания
 * @param $post_country - страна проживания
 * @param $post_study - место учебы
 * @param $post_work - место работы
 * @param $post_work_email - email для связи
 * @param $post_birthday - день рождения
 */
function shortEdit($connect, $post_user_id, $post_city, $post_country, $post_study, $post_work,
                   $post_work_email, $post_birthday)
{
    $post_user_id = FormChars($post_user_id);
    $post_city = FormChars($post_city);
    $post_country = FormChars($post_country);
    $post_study = FormChars($post_study);
    $post_work = FormChars($post_work);
    $post_work_email = FormChars($post_work_email);
    $post_birthday = $post_birthday;

    $response = array();

    $getData = mysqli_fetch_assoc(mysqli_query($connect, "SELECT `login`, `password` FROM `users` WHERE `id` = '$post_user_id'"));

    mysqli_query($connect, "UPDATE `users` SET 
				`city` = '$post_city', 
				`country` = '$post_country',
				`study` = '$post_study',
				`work` = '$post_work',
				`work_email` = '$post_work_email',
				`birthday` = '$post_birthday'
			WHERE `id` = '$post_user_id'");

    $response['edit'] = 700;
    echo json_encode($response);
}

/**
 * Полное изменение настроек
 * @param $connect - соединение
 * @param $post_user_id - идентификатор пользователя
 * @param $post_name - имя
 * @param $post_surname - фамилия
 * @param $post_city - город проживания
 * @param $post_country - страна проживания
 * @param $post_study - место учебы
 * @param $post_work - место работы
 * @param $post_email - основной email
 * @param $post_work_email - email для связи
 * @param $post_birthday - день рождения
 * @param $post_prev_password - старый пароль
 * @param $post_new_password - новый пароль
 */
function edit($connect, $post_user_id, $post_name, $post_surname, $post_city, $post_country, $post_study, $post_work,
              $post_email, $post_work_email, $post_birthday, $post_prev_password, $post_new_password)
{
    $post_user_id = FormChars($post_user_id);
    $post_name = FormChars($post_name);
    $post_surname = FormChars($post_surname);
    $post_city = FormChars($post_city);
    $post_country = FormChars($post_country);
    $post_study = FormChars($post_study);
    $post_work = FormChars($post_work);
    $post_email = FormChars($post_email);
    $post_work_email = FormChars($post_work_email);
    $post_birthday = $post_birthday;

    $response = array();

    $getData = mysqli_fetch_assoc(mysqli_query($connect, "SELECT `login`, `password` FROM `users` WHERE `id` = '$post_user_id'"));

    if (empty($post_prev_password)) {

        mysqli_query($connect, "UPDATE `users` SET 
				`name` = '$post_name',
				`surname` = '$post_surname', 
				`city` = '$post_city', 
				`country` = '$post_country',
				`study` = '$post_study',
				`work` = '$post_work',
				`email` = '$post_email',
				`work_email` = '$post_work_email',
				`birthday` = '$post_birthday'
			WHERE `id` = '$post_user_id'");

        $response['edit'] = 700;
    } else {
        $post_prev_password = GenPass(FormChars($post_prev_password), $getData['login']);

        if ($post_prev_password != $getData['password']) $response['edit'] = 701;
        else {

            $post_new_password = GenPass(FormChars($post_new_password), $getData['login']);

            mysqli_query($connect, "UPDATE `users` SET 
					`name` = '$post_name',
					`surname` = '$post_surname', 
					`city` = '$post_city', 
					`country` = '$post_country',
					`study` = '$post_study',
					`work` = '$post_work',
					`email` = '$post_email',
					`work_email` = '$post_work_email',
					`birthday` = '$post_birthday',
					`password` = '$post_new_password'
				WHERE `id` = '$post_user_id'");

            $response['edit'] = 700;
        }
    }
    echo json_encode($response);
}

/**
 * Получение данных о пользователе и его аккаунте
 * @param $connect - соединение
 * @param $post_user_id - идентификатор пользователя
 */
function getDataSettings($connect, $post_user_id)
{
    $post_user_id = FormChars($post_user_id);

    $response = array();

    $data = mysqli_fetch_assoc(mysqli_query($connect, "SELECT `login`, `avatar`, `email`, `work_email`, `name`, `surname`,
		 `country`, `birthday`, `city`, `study`, `work` FROM `users` WHERE `id` = '$post_user_id'"));

    if ($data) $response['getdatasettings'] = 600;
    else $response['getdatasettings'] = 601;

    foreach ($data as $key => $value) $response[$key] = $value;

    echo json_encode($response);
}

/**
 * Получение данных о пользователе
 * @param $connect - соединение
 * @param $post_id - идентификатор пользователя
 * @param $post_user_id - идентификатор профиля
 */
function getData($connect, $post_id, $post_user_id)
{
    $post_id = FormChars($post_id);
    $post_user_id = FormChars($post_user_id);

    $response = array();

    $data = mysqli_fetch_assoc(mysqli_query($connect, "SELECT `login`, `work_email`, `name`, `surname`, `country`,
		 `avatar`, `birthday`, `city`, `study`, `work` FROM `users` WHERE `id` = '$post_user_id'"));

    $checkContactBtwn = mysqli_fetch_assoc(mysqli_query($connect,
        "SELECT `id` FROM `contacts` WHERE `sid` = '$post_id' AND `gid` = '$post_user_id'"));

    if ($data) $response['getdata'] = 300;
    else $response['getdata'] = 301;

    foreach ($data as $key => $value) $response[$key] = $value;

    if ($checkContactBtwn) $response['contact'] = true;
    else $response['contact'] = false;

    $getDialogId = mysqli_fetch_assoc(mysqli_query($connect,
        "SELECT `id` FROM `dialog` WHERE `send` = '$post_id' AND `receive` = '$post_user_id' OR `send` = '$post_user_id' AND `receive` = '$post_id' "));

    if ($getDialogId) $response['did'] = $getDialogId['id'];
    else $response['did'] = -1;

    echo json_encode($response);
}

/**
 * Регистрация/авторизация
 * @param $connect - соединение
 * @param $post_login - логин
 * @param $post_password - пароль
 */
function registration($connect, $post_login, $post_password)
{

    $post_login = strtolower(FormChars($post_login));
    $post_password = GenPass(FormChars($post_password), $post_login);

    $response = array();

    $checkLogin = mysqli_fetch_assoc(mysqli_query($connect, "SELECT `login` FROM `users` WHERE `login` = '$post_login'"));

    if ($checkLogin['login']) {

        $checkPassword = mysqli_fetch_assoc(mysqli_query($connect,
            "SELECT `password`, `active` FROM `users` WHERE `login` = '$post_login'"));
        if ($checkPassword['password'] != $post_password) $response['login'] = 201; //Password doesn't correct
        else $response['login'] = 200;

        if ($response['login'] == 200) {
            $userData = mysqli_fetch_assoc(mysqli_query($connect, "SELECT * FROM `users` WHERE `login` = '$post_login'"));

            $response['id'] = $userData['id'];
        }

    } else $response['login'] = 100;

    if ($response['login'] == 100) {
        mysqli_query($connect, "INSERT INTO `users`  VALUES ('', '$post_login', '$post_password', 
				NOW(), '', '', 0, 1, '', '', 0, '', '', '', '')");

        $userData = mysqli_fetch_assoc(mysqli_query($connect, "SELECT `id` FROM `users` WHERE `login` = '$post_login'"));

        $response['id'] = $userData['id'];
    }

    echo json_encode($response);
}

/**
 * Изменение аватара
 * @param $connect - соединение
 * @param $post_image - картинка
 * @param $post_id - идентификатор пользователя
 */
function editImage($connect, $post_image, $post_id)
{
    //Проверка на изображение
    if (isset($post_image)) {

        $response = array();

        $post_id = FormChars($post_id);
        $checkAvatar = mysqli_fetch_assoc(mysqli_query($connect, "SELECT `avatar` FROM `users` WHERE `id` = '$post_id'"));

        if ($checkAvatar['avatar'] == 0) {
            $Files = glob('resource/avatar/*', GLOB_ONLYDIR);
            foreach ($Files as $Num => $Dir) {
                $Num++;
                $Count = sizeof(glob($Dir . '/*.*'));

                if ($Count < 250) {
                    $Download = $Dir . '/' . $post_id;
                    $userAvatar = $Num;
                    mysqli_query($connect, "UPDATE `users` SET `avatar` = '$Num' WHERE `id` = $post_id");

                    break;
                }
            }
        } else $Download = 'resource/avatar/' . $checkAvatar['avatar'] . '/' . $post_id;
        $Download = $Download . '.jpg';

        $image = $post_image;

        if (file_put_contents($Download, base64_decode($image)) != false) $response['image'] = 500;
        else $response['image'] = 501;
    }

    if ($response['image'] == "") $response['image'] = 502;
    echo json_encode($response);
}

?>








