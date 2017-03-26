<?php

	if ($Module == 'showDialogs') {
		$_POST['user_id'] = FormChars($_POST['user_id']);

		$response = array();
		$ids = array();
		$idDialogs = array();
		$dialogStatus = array();

		$idsEmpty = array();
		$idDialogsEmpty = array();

		$getReceiveDialogs = mysqli_query($CONNECT, "SELECT `status`, `receive`, `date`, `id` FROM `dialog` WHERE `send` = '$_POST[user_id]'");
		$getSendDialogs = mysqli_query($CONNECT, "SELECT `status`, `send`, `date`, `id` FROM `dialog` WHERE `receive` = '$_POST[user_id]'");

		while($receiveDialogs = mysqli_fetch_assoc($getReceiveDialogs)){
			$userId = $receiveDialogs['receive'];
			$lastMessage = $receiveDialogs['date'];
			$dialogId = $receiveDialogs['id'];
			$status = $receiveDialogs['status'];

			if ($status == 1) {
				$ids[$lastMessage] = $userId;
				$idDialogs[$userId] = $dialogId;
			} else {
				$idsEmpty[$lastMessage] = $userId;
				$idDialogsEmpty[$userId] = $dialogId;
			}
			$dialogStatus[$userId] = $status;
		}


		while($sendDialogs = mysqli_fetch_assoc($getSendDialogs)){
			$userId = $sendDialogs['send'];
			$lastMessage = $sendDialogs['date'];
			$dialogId = $sendDialogs['id'];
			$status = $sendDialogs['status'];

			if ($status == 1) {
				$ids[$lastMessage] = $userId;
				$idDialogs[$userId] = $dialogId;
			} else {
				$idsEmpty[$lastMessage] = $userId;
				$idDialogsEmpty[$userId] = $dialogId;
			}
			$dialogStatus[$userId] = $status;
		}

		ksort($ids);
		
		$sortedIds = array_reverse(array_values(array_unique($ids)));

		foreach ($sortedIds as $key => $value) {
    		$userId = $value;

    		$getUserInfo = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `id`, `login`, `name`, `surname`, `avatar` 
				FROM `users` WHERE `id` = '$userId'"));

			$userInfo = array();

			foreach($getUserInfo as $key => $value) {
				$userInfo[$key] = $value;
			}
			$userInfo['did'] = $idDialogs[$userInfo['id']];

			$countUnread = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT COUNT(`status`) FROM `message` WHERE `did` = $userInfo[did] AND `user` != $_POST[user_id] AND `status` = 1"));

			$userInfo['unread'] = $countUnread[0];

			$userInfo['status'] = $dialogStatus[$userInfo['id']];

			$response[$userInfo['id']] = $userInfo;
		}

		

		$response['ids'] = array_values(array_unique($sortedIds));

		echo json_encode($response);

	}

	else if ($Module == 'showMessages') {
		$_POST['did'] = FormChars($_POST['did']);
		$_POST['addresseeId'] = FormChars($_POST['addresseeId']);

		$response = array();
		$messagesIds = array();

		$messages = array();
		$texts = array();
		$users = array();
		$statuses = array();
		$times = array();

		$getMessages = mysqli_query($CONNECT, "SELECT `user`, `text`, `status`, `date`, `id` FROM `message` WHERE `did` = '$_POST[did]'");

		while($message = mysqli_fetch_assoc($getMessages)){
			$userId = $message['user'];
			$messageId = $message['id'];
			$messageTime = $message['date'];
			$messageText = $message['text'];
			$messageStatus = $message['status'];

			$messages[$messageTime] = $messageId;
			$texts[$messageId] = $messageText;
			$users[$messageId] = $userId;
			$statuses[$messageId] = $messageStatus;
			$times[$messageId] = $messageTime;
		}

		ksort($messages);
		$sortedMessages = array_values(array_unique($messages));
		foreach ($sortedMessages as $key => $value) {
			
			$message = array();
			$message['id'] = $value;
			$message['text'] = $texts[$value];
			$message['user'] = $users[$value];
			$message['status'] = $statuses[$value];
			$message['date'] = $times[$value];

			$response[$value] = $message;
		}

		$response['mids'] = array_values(array_unique($sortedMessages));

		echo json_encode($response);

		mysqli_query($CONNECT, "UPDATE `message` SET `status` = '0' WHERE `did` = $_POST[did] AND `user` = $_POST[addresseeId]");
	}

	else if ($Module == 'sendMessage') {
		$_POST['did'] = FormChars($_POST['did']);
		$_POST['id'] = FormChars($_POST['id']);
		$_POST['text'] = FormChars($_POST['text']);

		mysqli_query($CONNECT, "INSERT INTO `message` VALUES ('', '$_POST[did]', '$_POST[id]', 
				'$_POST[text]', NOW(), '1')");

		$checkDialog = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `send`, `receive` FROM `dialog` WHERE `id` = '$_POST[did]'"));

		$receive = $checkDialog['send'] == $_POST['id'] ? $checkDialog['receive'] : $checkDialog['send'];

		mysqli_query($CONNECT, "UPDATE `dialog` SET `status` = 1, `send` = $_POST[id], `receive` = $receive, `date` = NOW() WHERE `id` = $_POST[did]");
	}

	else if ($Module == 'createDialog') {
		$_POST['senderId'] = FormChars($_POST['senderId']);
		$_POST['addresseeId'] = FormChars($_POST['addresseeId']);

		$response = array();

		mysqli_query($CONNECT, "INSERT INTO `dialog` VALUES ('', '0', '$_POST[senderId]', '$_POST[addresseeId]', NOW())");
		$getDialogId = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `id` FROM `dialog` WHERE `send` = '$_POST[senderId]' AND `receive` = '$_POST[addresseeId]'"));		

		$response['did'] = $getDialogId['id'];

		echo json_encode($response);
	}

