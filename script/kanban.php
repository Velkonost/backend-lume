<?php

	if ($Module == 'showBoards') {
		$_POST['id'] = FormChars($_POST['id']);
		
		$response = array();
		$bids = array();

		$contributedBoards = mysqli_query($CONNECT, "SELECT * FROM `in_board` WHERE `uid` = '$_POST[id]'");

		while ($board = mysqli_fetch_assoc($contributedBoards)){
			$bid = $board['bid'];
			$getName = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `name` FROM `boards` WHERE `id` = $bid"));

			array_push($bids, $bid);
			$response[$bid] = $getName['name'];
		}
		$response['bids'] = $bids;

		echo json_encode($response);
	}

	else if ($Module == 'getBoardInfo') {
		$_POST['bid'] = FormChars($_POST['bid']);

		$response = array();

		$uids = array();
		$cids = array();
		$cNames = array();


		$boardInfo = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `name`, `description` FROM `boards` WHERE `id` = '$_POST[bid]'"));
		$boardName = $boardInfo['name'];
		$boardDescription = $boardInfo['description'];

		$columns = mysqli_query($CONNECT, "SELECT * FROM `columns` WHERE `board_id` = '$_POST[bid]'");

		while ($column = mysqli_fetch_assoc($columns)) {
			$columnId = $column['id'];
			$columnName = $column['name'];
			$columnPosition = $column['position'];

			$cids[$columnPosition] = $columnId;
			$cNames[$columnId] = $columnName;
			
		}

		ksort($cids);

		$sortedColumns = array_values(array_unique($cids));
		foreach ($sortedColumns as $key => $value) {
			
			$column = array();
			$column['id'] = $value;
			$column['name'] = $cNames[$value];

			$response[$value] = $column;
		}



		$boardParticipants = mysqli_query($CONNECT, "SELECT `uid` FROM `in_board` WHERE `bid` = '$_POST[bid]'");

		while ($user = mysqli_fetch_assoc($boardParticipants)) {
			$userId = $user['uid'];

			$userInfo = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `login`, `avatar` FROM `users` WHERE `id` = '$userId'"));

			$participant = array();
			$participant['id'] = $userId;
			$participant['login'] = $userInfo['login'];
			$participant['avatar'] = $userInfo['avatar'];

			$userId .= 'user';
			array_push($uids, $userId);

			$response[$userId] = $participant;

		}

		$response['cids'] = array_values(array_unique($sortedColumns));
		$response['uids'] = array_values(array_unique($uids));
		$response['bname'] = $boardName;
		$response['bdescription'] = $boardDescription;

		echo json_encode($response);
	}

	else if ($Module == 'getBoardParticipants') {
		$_POST['bid'] = FormChars($_POST['bid']);

		$response = array();
		$ids = array();

		$boardParticipants = mysqli_query($CONNECT, "SELECT `uid` FROM `in_board` WHERE `bid` = '$_POST[bid]'");

		while ($user = mysqli_fetch_assoc($boardParticipants)) {
			$userId = $user['uid'];

			$userInfo = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `login`, `name`, `surname`, `avatar` FROM `users` WHERE `id` = '$userId'"));

			$participant = array();
			$participant['id'] = $userId;
			$participant['login'] = $userInfo['login'];
			$participant['avatar'] = $userInfo['avatar'];
			$participant['name'] = $userInfo['name'];
			$participant['surname'] = $userInfo['surname'];

			array_push($ids, $userId);

			$response[$userId] = $participant;

		}

		$response['ids'] = array_values(array_unique($ids));
		echo json_encode($response);
	}

	else if ($Module == 'getColumnInfo') {
		$_POST['cid'] = FormChars($_POST['cid']);
		$_POST['id'] = FormChars($_POST['id']);
		
		$response = array();

		$uids = array();
		$cardIds = array();
		$cardNames = array();
		$cardColors = array();


		$cards = mysqli_query($CONNECT, "SELECT `id`, `name`, `description`, `position`, `color` FROM `cards` WHERE `column_id` = '$_POST[cid]'");

		while ($card = mysqli_fetch_assoc($cards)) {
			$cardId = $card['id'];
			$cardName = $card['name'];
			$cardPosition = $card['position'];
			$cardColor = $card['color'];

			$cardIds[$cardPosition] = $cardId;
			$cardNames[$cardId] = $cardName;
			$cardColors[$cardId] = $cardColor;
			
		}

		ksort($cardIds);

		$sortedCards = array_values(array_unique($cardIds));
		foreach ($sortedCards as $key => $value) {
			
			$card = array();
			$card['name'] = $cardNames[$value];
			$card['card_color'] = $cardColors[$value];

			$isBelong = false;

			$cardParticipants = mysqli_query($CONNECT, "SELECT `uid` FROM `in_card` WHERE `cid` = '$value'");

			$participants = array();
			$count = 0;
			while($user = mysqli_fetch_assoc($cardParticipants)) {

				$participant = array();

				$userId = $user['uid'];
				$count ++;

				if ($userId == $_POST['id']) $isBelong = true;

				$userInfo = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `login`, `avatar` FROM `users` WHERE `id` = $userId"));

				$participant['login'] = $userInfo['login'];
				$participant['avatar'] = $userInfo['avatar'];

				$participants[$userId] = $participant;
				array_push($uids, $userId);
			}

			$card['participants'] = $participants;
			$card['amount'] = $count;
			$card['belong'] = $isBelong;

			$response[$value] = $card;
		}

		$response['cids'] = array_values(array_unique($sortedCards));
		$response['uids'] = array_values(array_unique($uids));

		echo json_encode($response);
	}

	else if ($Module == 'getCardInfo') {
		$_POST['card_id'] = FormChars($_POST['card_id']);
		
		$response = array();
		$ids = array();

		$comments = array();
		$texts = array();
		$users = array();
		$times = array();

		$cardInfo = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `description`, `color` FROM `cards` WHERE `id` = '$_POST[card_id]'"));
		$cardParticipants = mysqli_query($CONNECT, "SELECT `uid` FROM `in_card` WHERE `cid` = '$_POST[card_id]'");

		while ($user = mysqli_fetch_assoc($cardParticipants)) {
			$userId = $user['uid'];

			$userInfo = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `login`, `name`, `surname`, `avatar` FROM `users` WHERE `id` = '$userId'"));

			$participant = array();
			$participant['id'] = $userId;
			$participant['login'] = $userInfo['login'];
			$participant['avatar'] = $userInfo['avatar'];
			$participant['name'] = $userInfo['name'];
			$participant['surname'] = $userInfo['surname'];

			array_push($ids, $userId);

			$response[$userId] = $participant;

		}

		$getComments = mysqli_query($CONNECT, "SELECT `send_id`, `text`, `date`, `id` FROM `comments` WHERE `card_id` = '$_POST[card_id]'");

		while($comment = mysqli_fetch_assoc($getComments)){
			$userId = $comment['send_id'];

			$getUserLogin = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `login` FROM `users` WHERE `id` = '$userId'"));

			$userLogin = $getUserLogin['login'];

			$commentId = $comment['id'];
			$commentTime = $comment['date'];
			$commentText = $comment['text'];

			$comments[$commentTime] = $commentId;
			$texts[$commentId] = $commentText;
			$users[$commentId] = $userLogin;
			$times[$commentId] = $commentTime;
		}

		ksort($comments);
		$sortedComments = array_values(array_unique($comments));

		foreach ($sortedComments as $key => $value) {

			$comment = array();
			$comment['id'] = $value;
			$comment['text'] = $texts[$value];
			$comment['user'] = $users[$value];
			$comment['date'] = $times[$value];

			$response[$value . "comment"] = $comment;
		}

		$response['comment_ids'] = array_values(array_unique($sortedComments));

		$response['card_description'] = $cardInfo['description'];
		$response['card_color'] = $cardInfo['color'];

		$response['uids'] = array_values(array_unique($ids));
		echo json_encode($response);
	}

	else if ($Module == 'getCardParticipants') {
		$_POST['card_id'] = FormChars($_POST['card_id']);
		
		$response = array();
		$ids = array();

		$cardParticipants = mysqli_query($CONNECT, "SELECT `uid` FROM `in_card` WHERE `cid` = '$_POST[card_id]'");

		while ($user = mysqli_fetch_assoc($cardParticipants)) {
			$userId = $user['uid'];

			$userInfo = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `login`, `name`, `surname`, `avatar` FROM `users` WHERE `id` = '$userId'"));

			$participant = array();
			$participant['id'] = $userId;
			$participant['login'] = $userInfo['login'];
			$participant['avatar'] = $userInfo['avatar'];
			$participant['name'] = $userInfo['name'];
			$participant['surname'] = $userInfo['surname'];

			array_push($ids, $userId);

			$response[$userId] = $participant;

		}

		$response['ids'] = array_values(array_unique($ids));
		echo json_encode($response);
	}

	else if ($Module == 'cardAddComment') { 
		$_POST['card_id'] = FormChars($_POST['card_id']);
		$_POST['id'] = FormChars($_POST['id']);
		$_POST['text'] = FormChars($_POST['text']);

		mysqli_query($CONNECT, "INSERT INTO `comments` VALUES ('', '$_POST[id]', '$_POST[text]', '$_POST[card_id]', NOW())");
	}

	else if ($Module == 'leaveCard'){
		$_POST['id'] = FormChars($_POST['id']);
		$_POST['card_id'] = FormChars($_POST['card_id']);

		mysqli_query($CONNECT, "DELETE FROM `in_card` WHERE `uid` = '$_POST[id]' AND `cid` = '$_POST[card_id]'");
	}

	else if ($Module == 'leaveBoard'){
		$_POST['id'] = FormChars($_POST['id']);
		$_POST['bid'] = FormChars($_POST['bid']);

		mysqli_query($CONNECT, "DELETE FROM `in_board`  WHERE `uid` = '$_POST[id]' AND `bid` = '$_POST[bid]'");		
	}

	else if ($Module == 'inviteInBoard') {
		$_POST['id'] = FormChars($_POST['id']);
		$_POST['bid'] = FormChars($_POST['bid']);

		$checkUser = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `id` FROM `in_board` WHERE `uid` = '$_POST[id]' AND `bid` = '$_POST[bid]'"));

		if (!$checkUser) mysqli_query($CONNECT, "INSERT INTO `in_board` VALUES ('', '$_POST[id]', '$_POST[bid]')");		
	}

	else if ($Module == 'inviteInCard') {
		$_POST['id'] = FormChars($_POST['id']);
		$_POST['card_id'] = FormChars($_POST['card_id']);

		$checkUser = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `id` FROM `in_card` WHERE `uid` = '$_POST[id]' AND `cid` = '$_POST[card_id]'"));

		if (!$checkUser) mysqli_query($CONNECT, "INSERT INTO `in_card` VALUES ('', '$_POST[id]', '$_POST[card_id]')");		
	}

	else if ($Module == 'changeBoardSettings') {
		$_POST['bid'] = FormChars($_POST['bid']);
		$_POST['bname'] = FormChars($_POST['bname']);
		$_POST['bdescription'] = FormChars($_POST['bdescription']);

		mysqli_query($CONNECT, "UPDATE `boards` SET `name` = '$_POST[bname]', `description` = '$_POST[bdescription]' WHERE `id` = $_POST[bid]");
	}

	else if ($Module == 'changeCardSettings') {
		$_POST['card_id'] = FormChars($_POST['card_id']);
		$_POST['card_name'] = FormChars($_POST['card_name']);
		$_POST['card_description'] = FormChars($_POST['card_description']);

		mysqli_query($CONNECT, "UPDATE `cards` SET `name` = '$_POST[card_name]', `description` = '$_POST[card_description]' WHERE `id` = $_POST[card_id]");
	}

	else if ($Module == 'getInBoardToInvite') {
		$_POST['bid'] = FormChars($_POST['bid']);

		$response = array();
		$ids = array();

		$getContacts = mysqli_query($CONNECT, "SELECT `uid` FROM `in_board` WHERE `bid` = '$_POST[bid]'");

		while ($getContact = mysqli_fetch_assoc($getContacts)) {
			$userId = $getContact['uid'];

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

	else if ($Module == 'getBoardColumns') {
		$_POST['bid'] = FormChars($_POST['bid']);

		$response = array();
		$cids = array();
		$cNames = array();

		$columns = mysqli_query($CONNECT, "SELECT * FROM `columns` WHERE `board_id` = '$_POST[bid]'");

		while ($column = mysqli_fetch_assoc($columns)) {
			$columnId = $column['id'];
			$columnName = $column['name'];
			$columnPosition = $column['position'];

			$cids[$columnPosition] = $columnId;
			$cNames[$columnId] = $columnName;
			
		}

		ksort($cids);

		$sortedColumns = array_values(array_unique($cids));
		foreach ($sortedColumns as $key => $value) {
			
			$column = array();
			$column['id'] = $value;
			$column['name'] = $cNames[$value];

			$response[$value] = $column;
		}

		$response['cids'] = array_values(array_unique($sortedColumns));

		echo json_encode($response);
	}

	else if ($Module == 'moveCard') {
		$_POST['card_id'] = FormChars($_POST['card_id']);
		$_POST['cid'] = FormChars($_POST['cid']);

		$getPosition = mysqli_query($CONNECT, "SELECT `position` FROM `cards` WHERE `column_id` = '$_POST[cid]'");
		$curPosition = 0;
		while ($position = mysqli_fetch_assoc($getPosition)) {
			$prevPosition = $position['position'];
			if ($prevPosition > $curPosition) $curPosition = $prevPosition;
		}
		$curPosition ++;

		$getPosition = mysqli_query($CONNECT, "SELECT `position` FROM `cards` WHERE `column_id` = '$_POST[cid]'");
		$curPosition = 0;
		while ($position = mysqli_fetch_assoc($getPosition)) {
			$prevPosition = $position['position'];
			if ($prevPosition > $curPosition) $curPosition = $prevPosition;
		}
		$curPosition ++;

		$getInfoOfThisCard = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `position`, `column_id` FROM `cards` WHERE `id` = '$_POST[card_id]'"));
		// change position of another cards
		mysqli_query($CONNECT, "UPDATE `cards` SET `position` = `position` - 1 WHERE `position` > '$getInfoOfThisCard[position]' AND `column_id` = '$getInfoOfThisCard[column_id]' ");


		mysqli_query($CONNECT, "UPDATE `cards` SET `column_id` = '$_POST[cid]', `position` = $curPosition WHERE `id` = $_POST[card_id]");
	}

	else if ($Module == 'addColumn'){
		$_POST['name'] = FormChars($_POST['name']);
		$_POST['bid'] = FormChars($_POST['bid']);

		$getPosition = mysqli_query($CONNECT, "SELECT `position` FROM `columns` WHERE `board_id` = '$_POST[bid]'");

		$curPosition = 0;
		while ($position = mysqli_fetch_assoc($getPosition)) {
			$prevPosition = $position['position'];

			if ($prevPosition > $curPosition) $curPosition = $prevPosition;
		}
		$curPosition ++;

		mysqli_query($CONNECT, "INSERT INTO `columns`  VALUES ('', '$_POST[name]', '$_POST[bid]', $curPosition)");
	}

	else if ($Module == 'addCard'){
		$_POST['name'] = FormChars($_POST['name']);
		$_POST['description'] = FormChars($_POST['description']);
		$_POST['cid'] = FormChars($_POST['cid']);

		$getPosition = mysqli_query($CONNECT, "SELECT `position` FROM `cards` WHERE `column_id` = '$_POST[cid]'");

		$curPosition = 0;
		while ($position = mysqli_fetch_assoc($getPosition)) {
			$prevPosition = $position['position'];
			if ($prevPosition > $curPosition) $curPosition = $prevPosition;
		}
		$curPosition ++;

		mysqli_query($CONNECT, "INSERT INTO `cards` VALUES ('', '$_POST[name]', '$_POST[cid]', '$_POST[description]', $curPosition, '000000')");
	}

	else if ($Module == 'addBoard'){
		$_POST['id'] = FormChars($_POST['id']);
		$_POST['name'] = FormChars($_POST['name']);
		$_POST['description'] = FormChars($_POST['description']);

		$time = $_SERVER['REQUEST_TIME'];

		mysqli_query($CONNECT, "INSERT INTO `boards`  VALUES ('', '$_POST[name]', '$_POST[description]', $time)");

		$row = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `id` FROM `boards` WHERE `date` = $time"));
		mysqli_query($CONNECT, "INSERT INTO `in_board`  VALUES ('', '$_POST[id]', '$row[id]')");
	}

	else if ($Module == 'changeColumnSettings') {
		$_POST['bid'] = FormChars($_POST['bid']);
		$_POST['name'] = FormChars($_POST['name']);
		$_POST['previous_name'] = FormChars($_POST['previous_name']);
		$_POST['position'] = FormChars($_POST['position']);

		mysqli_query($CONNECT, "UPDATE `columns` SET `name` = '$_POST[name]' WHERE `board_id` = '$_POST[bid]' AND `position` = '$_POST[position]'");
	}

	else if ($Module == 'changeCardColor') {
		$_POST['card_id'] = FormChars($_POST['card_id']);
		
		mysqli_query($CONNECT, "UPDATE `cards` SET `color` = '$_POST[card_color]' WHERE `id` = '$_POST[card_id]'");
	}









?>