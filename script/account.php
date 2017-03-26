<?php 

	if ($Module == 'editImage') {

		//Проверка на изображение 
		if(isset($_POST['image'])) { 

			$response = array();

			$_POST['id'] = FormChars($_POST['id']);
			$checkAvatar = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `avatar` FROM `users` WHERE `id` = '$_POST[id]'"));


			if ($checkAvatar['avatar'] == 0){
				$Files = glob('resource/avatar/*', GLOB_ONLYDIR);
				foreach ($Files as $Num => $Dir) {
					$Num ++;
					$Count = sizeof(glob($Dir.'/*.*'));

					if ($Count < 250){
						$Download = $Dir.'/'.$_POST['id'];
						$userAvatar = $Num;
						mysqli_query($CONNECT, "UPDATE `users` SET `avatar` = '$Num' WHERE `id` = $_POST[id]");

						break;
					}
				}
			} else $Download = 'resource/avatar/'.$checkAvatar['avatar'].'/'.$_POST['id'];
			$Download = $Download .'.jpg';

			//Загрузка 
			// $upload_folder = "upload"; 
			// $path = "$upload_folder/$id.jpeg"; 
			$image = $_POST['image']; 

			if(file_put_contents($Download, base64_decode($image)) != false) $response['image'] = 500;
			else $response['image'] = 501; 
		}

		if ($response['image'] == "") $response['image'] = 502;
		echo json_encode($response);	
	}



	// ULogin(0);
//НАЧАЛО МОДУЛЯ ВОССТАНОВЛЕНИЯ ПАРОЛЯ // НЕ РАБОТАЕТ!!!



	// if ($Module == 'restore' and !$Param['code'] and substr($_SESSION['RESTORE'], 0, 4) == 'wait') MessageSend(2, 'Вы уже отправили заявку на восстановление пароля. Проверьте ваш E-mail адрес');
	// if ($Module == 'restore' and $_SESSION['RESTORE'] and substr($_SESSION['RESTORE'], 0, 4) != 'wait') MessageSend(2, 'Ваш пароль ранее уже был изменен. Для входа используйте нвоый пароль <b>'.$_SESSION['RESTORE'].'</b>', '/login');


	// if ($Module == 'restore' and $Param['code']) {
	// 	$Row = mysqli_fetch_assoc(mysqli_query($CONNECT, 'SELECT `login` FROM `users` WHERE `id` = '.str_replace(md5('YouTube'), '', $Param['code'])));
	// 	if (!$Row['login']) MessageSend(1, 'Невозможно восстановить пароль.', '/login');
	// 	$Random = RandomString(15);
	// 	$_SESSION['RESTORE'] = $Random;
	// 	mysqli_query($CONNECT, "UPDATE `users` SET `password` = '".GenPass($Random, $Row['login'])."' WHERE `login` = '$Row[login]'");
	// 	MessageSend(2, 'Пароль успешно изменен, для входа используйте новый пароль <b>'.$Random.'</b>', '/login');
	// }

	// if ($Module == 'restore' and $_POST['enter']) {
	// 	$_POST['login'] = FormChars($_POST['login']);
	// 	if (!$_POST['login']) MessageSend(1, 'Невозможно обработать форму.');
	// 	$Row = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `id`, `email` FROM `users` WHERE `login` = '$_POST[login]'"));
	// 	if (!$Row['email']) MessageSend(1, 'Пользователь не найден.');
	// 	mail($Row['email'], 'Mr.Shift', 'Ссылка для восстановления: http://vh156342.eurodir.ru/account/restore/code/'.md5('YouTube').$Row['id'], 'From: Lume');
	// 	$_SESSION['RESTORE'] = 'wait_'.$Row['email'];
	// 	MessageSend(2, 'На ваш E-mail адрес отправлено подтерждение смены пароля');
	// }



// КОНЕЦ МОДУЛЯ ВОССТАНОВЛЕНИЯ ПАРОЛЯ // НЕ РАБОТАЕТ!!!


	if ($Module == 'registration') {
		$_POST['login'] = FormChars($_POST['login']);
		$_POST['login'] = strtolower($_POST['login']);
		$_POST['password'] = GenPass(FormChars($_POST['password']), $_POST['login']);

		$response = array();

		$checkLogin = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `login` FROM `users` WHERE `login` = '$_POST[login]'"));

		if ($checkLogin['login']) {

			$checkPassword = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `password`, `active` FROM `users` WHERE `login` = '$_POST[login]'"));
			if ($checkPassword['password'] != $_POST['password']) $response['login'] = 201; //Password doesn't correct
			else $response['login'] = 200;
			// if ($Row['active'] == 0) MessageSend(1, 'Аккаунт пользователя <b>'.$_POST['login'].'</b> не подтвержден.');

			if ($response['login'] == 200){
				$userData = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT * FROM `users` WHERE `login` = '$_POST[login]'"));

				$response['id'] = $userData['id'];
			}

		}
		else $response['login'] = 100;

		if ($response['login'] == 100){
			mysqli_query($CONNECT, "INSERT INTO `users`  VALUES ('', '$_POST[login]', '$_POST[password]', 
				NOW(), '$_POST[email]', '', 0, 1, '', '', 0, '', '', '', '')");

			$userData = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `id` FROM `users` WHERE `login` = '$_POST[login]'"));

			$response['id'] = $userData['id'];
		}

		// $Code = str_replace('=', '', base64_encode($_POST['email']));

		// mail($_POST['email'], 'Регистрация на блоге Mr.Shift', 'Ссылка для активации: http://vh156342.eurodir.ru/account/activate/code/'.substr($Code, -5).substr($Code, 0, -5), 'From: Lume');
		// MessageSend(3, 'Регистрация акаунта успешно завершена. На указанный E-mail адрес <b>'.$_POST['email'].'</b> отправленно письмо о подтверждении регистрации.');

		echo json_encode($response);	
	}

	else if($Module == 'getData') {
		$_POST['id'] = FormChars($_POST['id']);
		$_POST['user_id'] = FormChars($_POST['user_id']);

		$response = array();

		$data = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `login`, `work_email`, `name`, `surname`, `country`,
		 `avatar`, `birthday`, `city`, `study`, `work` FROM `users` WHERE `id` = '$_POST[user_id]'"));

		$checkContactBtwn = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `id` FROM `contacts` WHERE `sid` = '$_POST[id]' AND `gid` = '$_POST[user_id]'"));

		if ($data) $response['getdata'] = 300;
		else $response['getdata'] = 301;
		
		foreach ($data as $key => $value) $response[$key] = $value;

		if ($checkContactBtwn) $response['contact'] = true;
		else $response['contact'] = false;

		$getDialogId = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `id` FROM `dialog` WHERE `send` = '$_POST[id]' AND `receive` = '$_POST[user_id]' OR `send` = '$_POST[user_id]' AND `receive` = '$_POST[id]' "));

		if ($getDialogId) $response['did'] = $getDialogId['id'];
		else $response['did'] = -1;

		echo json_encode($response);	
	}

	if($Module == 'getDataSettings') {
		$_POST['user_id'] = FormChars($_POST['user_id']);

		$response = array();

		$data = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `login`, `avatar`, `email`, `work_email`, `name`, `surname`,
		 `country`, `birthday`, `city`, `study`, `work` FROM `users` WHERE `id` = '$_POST[user_id]'"));

		if ($data) $response['getdatasettings'] = 600;
		else $response['getdatasettings'] = 601;
		
		foreach ($data as $key => $value) $response[$key] = $value;


		echo json_encode($response);	
	}

	else if ($Module == 'edit') {
		$_POST['user_id'] = FormChars($_POST['user_id']);
		$_POST['name'] = FormChars($_POST['name']);
		$_POST['surname'] = FormChars($_POST['surname']);
		$_POST['city'] = FormChars($_POST['city']);
		$_POST['country'] = FormChars($_POST['country']);
		$_POST['study'] = FormChars($_POST['study']);
		$_POST['work'] = FormChars($_POST['work']);
		$_POST['email'] = FormChars($_POST['email']);
		$_POST['work_email'] = FormChars($_POST['work_email']);
		$_POST['birthday'] = $_POST['birthday'];

		$response = array();

		$getData = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `login`, `password` FROM `users` WHERE `id` = '$_POST[user_id]'"));

		if (empty($_POST['prev_password'])){
			
			mysqli_query($CONNECT, "UPDATE `users` SET 
				`name` = '$_POST[name]',
				`surname` = '$_POST[surname]', 
				`city` = '$_POST[city]', 
				`country` = '$_POST[country]',
				`study` = '$_POST[study]',
				`work` = '$_POST[work]',
				`email` = '$_POST[email]',
				`work_email` = '$_POST[work_email]',
				`birthday` = '$_POST[birthday]'
			WHERE `id` = '$_POST[user_id]'");

			$response['edit'] = 700;
		}	
		else {
			$_POST['prev_password'] = GenPass(FormChars($_POST['prev_password']), $getData['login']);

			if ($_POST['prev_password'] != $getData['password']) $response['edit'] = 701;
			else{

				$_POST['new_password'] = GenPass(FormChars($_POST['new_password']), $getData['login']);

				mysqli_query($CONNECT, "UPDATE `users` SET 
					`name` = '$_POST[name]',
					`surname` = '$_POST[surname]', 
					`city` = '$_POST[city]', 
					`country` = '$_POST[country]',
					`study` = '$_POST[study]',
					`work` = '$_POST[work]',
					`email` = '$_POST[email]',
					`work_email` = '$_POST[work_email]',
					`birthday` = '$_POST[birthday]',
					`password` = '$_POST[new_password]'
				WHERE `id` = '$_POST[user_id]'");

				$response['edit'] = 700;
			}
		}
		echo json_encode($response);
	}


	else if ($Module == 'short_edit') {
		$_POST['user_id'] = FormChars($_POST['user_id']);
		$_POST['city'] = FormChars($_POST['city']);
		$_POST['country'] = FormChars($_POST['country']);
		$_POST['study'] = FormChars($_POST['study']);
		$_POST['work'] = FormChars($_POST['work']);
		$_POST['work_email'] = FormChars($_POST['work_email']);
		$_POST['birthday'] = $_POST['birthday'];

		$response = array();

		$getData = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `login`, `password` FROM `users` WHERE `id` = '$_POST[user_id]'"));

			mysqli_query($CONNECT, "UPDATE `users` SET 
				`city` = '$_POST[city]', 
				`country` = '$_POST[country]',
				`study` = '$_POST[study]',
				`work` = '$_POST[work]',
				`work_email` = '$_POST[work_email]',
				`birthday` = '$_POST[birthday]'
			WHERE `id` = '$_POST[user_id]'");
			

		$response['edit'] = 700;
		echo json_encode($response);
	}


	else if ($Module == 'activate' and $Param['code']) {
		if (!$_SESSION['USER_ACTIVE_EMAIL']) {
			$Email = base64_decode(substr($Param['code'], 5).substr($Param['code'], 0, 5));
			if (strpos($Email, '@') !== false) {
				mysqli_query($CONNECT, "UPDATE `users`  SET `active` = 1 WHERE `email` = '$Email'");
				$_SESSION['USER_ACTIVE_EMAIL'] = $Email;
				MessageSend(3, 'E-mail <b>'.$Email.'</b> подтвержден.', '/login');
			}
			else MessageSend(1, 'E-mail адрес не подтвержден.', '/login');
		}
		else MessageSend(1, 'E-mail адрес <b>'.$_SESSION['USER_ACTIVE_EMAIL'].'</b> уже подтвержден.', '/login');
	}

	// else if ($Module == 'login' and $_POST['enter']) {
	// 	$_POST['login'] = FormChars($_POST['login']);
	// 	$_POST['password'] = GenPass(FormChars($_POST['password']), $_POST['login']);
	// 	if (!$_POST['login'] or !$_POST['password']) MessageSend(1, 'Невозможно обработать форму.');

		// $Row = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `password`, `active` FROM `users` WHERE `login` = '$_POST[login]'"));
		// if ($Row['password'] != $_POST['password']) MessageSend(1, 'Не верный логин или пароль.');
		// if ($Row['active'] == 0) MessageSend(1, 'Аккаунт пользователя <b>'.$_POST['login'].'</b> не подтвержден.');

		// $Row = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `id`, `date_reg`, `email`, `password`, `login`, `avatar` FROM `users` WHERE `login` = '$_POST[login]'"));		


	// 	$_SESSION['USER_LOGIN_IN'] = 1;
	// 	$_SESSION['USER_REGDATE'] = $Row['date_reg'];
	// 	foreach ($Row as $Key => $Value) $_SESSION['USER_'.strtoupper($Key)] = $Value;
		
 
	// 	if ($_REQUEST['remember']) setcookie('user', $_POST['password'], strtotime('+30 days'), '/');

	// 	exit(header('Location: /profile'));
	// }

	else if ($Module == 'search') {
		$_POST['search'] = FormChars($_POST['search']);
		$_POST['search'] = strtolower($_POST['search']);

		$response = array();
		$ids = array();

		$wordsToSearch = explode(" ", $_POST['search']);

		foreach ($wordsToSearch as $value) {

			$getUsersDataByLogin = mysqli_query($CONNECT, "SELECT `id`, `login`, `avatar`, `country`, `city`,
			 `name`, `surname`, `study`, `work` FROM `users` WHERE `login` LIKE '%$value%'");
			$getUsersDataByName = mysqli_query($CONNECT, "SELECT `id`, `login`, `avatar`, `country`, `city`,
			 `name`, `surname`, `study`, `work` FROM `users` WHERE `name` LIKE '%$value%'");
			$getUsersDataBySurname = mysqli_query($CONNECT, "SELECT `id`, `login`, `avatar`, `country`, `city`,
			 `name`, `surname`, `study`, `work` FROM `users` WHERE `surname` LIKE '%$value%'");


			while ($getUserDataByLogin = mysqli_fetch_assoc($getUsersDataByLogin)) {

				$userSearchedInfo = array();
				foreach($getUserDataByLogin as $key => $value) {
					$userSearchedInfo[$key] = $value;
				}
				$response[$userSearchedInfo['id']] = $userSearchedInfo;
				array_push($ids, $userSearchedInfo['id']);
			}

			while ($getUserDataByName = mysqli_fetch_assoc($getUsersDataByName)) {

				$userSearchedInfo = array();
				foreach($getUserDataByName as $key => $value) {
					$userSearchedInfo[$key] = $value;
				}
				$response[$userSearchedInfo['id']] = $userSearchedInfo;
				array_push($ids, $userSearchedInfo['id']);
			}

			while ($getUserDataBySurname = mysqli_fetch_assoc($getUsersDataBySurname)) {

				$userSearchedInfo = array();
				foreach($getUserDataBySurname as $key => $value) {
					$userSearchedInfo[$key] = $value;
				}
				$response[$userSearchedInfo['id']] = $userSearchedInfo;
				array_push($ids, $userSearchedInfo['id']);
			}
		}
		$response['ids'] = array_values(array_unique($ids));;
		echo json_encode($response);
	}


	else if ($Module == 'addContact') {
		$_POST['send_id'] = FormChars($_POST['send_id']);
		$_POST['get_id'] = FormChars($_POST['get_id']);

		$response = array();

		$checkExist = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `id` FROM `contacts` WHERE `sid` = '$_POST[send_id]' AND `gid` = '$_POST[get_id]'"));

		if ($checkExist) {
			$response['addContact'] = 401; // contact already exist
			mysqli_query($CONNECT, "DELETE FROM `contacts` WHERE `sid` = '$_POST[send_id]' AND `gid` = '$_POST[get_id]'");		
		}
		else {
			$response['addContact'] = 400;

			mysqli_query($CONNECT, "INSERT INTO `contacts` VALUES ('', '$_POST[send_id]', '$_POST[get_id]')");
		}
		 echo json_encode($response);
	}


	else if ($Module == 'getContacts') {
		$_POST['id'] = FormChars($_POST['id']);

		$response = array();
		$ids = array();

		$getContacts = mysqli_query($CONNECT, "SELECT `gid` FROM `contacts` WHERE `sid` = '$_POST[id]'");

		while ($getContact = mysqli_fetch_assoc($getContacts)) {
			$userId = $getContact['gid'];

			$getUserInfo = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `id`, `login`, `name`, `surname`, `avatar` 
				FROM `users` WHERE `id` = '$userId'"));

			$userInfo = array();

			foreach($getUserInfo as $key => $value) {
				$userInfo[$key] = $value;
			}
			$response[$userInfo['id']] = $userInfo;
			array_push($ids, $userInfo['id']);
		}
		$response['ids'] = array_values(array_unique($ids));;

		echo json_encode($response);
	}
?>








