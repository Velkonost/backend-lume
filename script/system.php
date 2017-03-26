<?php
// CARD

//deifh;eorhgeorejfwregoi;ojergrij
// удаление карточки
	if ($Module == 'deleteCard' && $Param['CardId']){
		$Param['CardId'] = FormChars($Param['CardId']);

		$getComments = mysqli_query($CONNECT, "SELECT `id` FROM `comments` WHERE `card_id` = '$Param[CardId]'");			
		while ($comments = mysqli_fetch_assoc($getComments)) {
			$curComment = $comments['id'];

			// delete comment here
			mysqli_query($CONNECT, "DELETE FROM `comments`  WHERE `id` = $curComment");		
		}

		$getInCard = mysqli_query($CONNECT, "SELECT `id` FROM `in_card` WHERE `cid` = '$Param[CardId]'");			
		while ($inCard = mysqli_fetch_assoc($getInCard)) {
			$curInCard = $inCard['id'];

			// delete in_card here
			mysqli_query($CONNECT, "DELETE FROM `in_card`  WHERE `id` = $curInCard");		
		}
		
		$getInfoOfThisCard = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `position`, `column_id` FROM `cards` WHERE `id` = '$Param[CardId]'"));
		// change position of another columns
		mysqli_query($CONNECT, "UPDATE `cards` SET `position` = `position` - 1 WHERE `position` > '$getInfoOfThisCard[position]'");
		//delete card here
		mysqli_query($CONNECT, "DELETE FROM `cards` WHERE `id` = '$Param[CardId]'");

		exit(header('Location: /column/'.$getInfoOfThisCard['column_id']));
	}


// добавление карточки в колонку
	if ($Module == 'addCard' && $Param['ColumnId'] && $_POST['name']){
		$_POST['name'] = FormChars($_POST['name']);
		$_POST['description'] = FormChars($_POST['description']);
		$Param['ColumnId'] = FormChars($Param['ColumnId']);

		$getPosition = mysqli_query($CONNECT, "SELECT `position` FROM `cards` WHERE `column_id` = '$Param[ColumnId]'");
		$curPosition = 0;
		while ($position = mysqli_fetch_assoc($getPosition)) {
			$prevPosition = $position['position'];
			if ($prevPosition > $curPosition) $curPosition = $prevPosition;
		}
		$curPosition ++;

		mysqli_query($CONNECT, "INSERT INTO `cards`  VALUES ('', '$_POST[name]', '$Param[ColumnId]', '$_POST[description]', $curPosition)");
		exit(header('Location: /column/'.$Param['ColumnId']));		
	}
	
// удаление юзера из карточки
	if ($Module == 'deleteUserFromCard' && $Param['CardId'] && $Param['UserId']){
		$Param['UserId'] = FormChars($Param['UserId']);
		$Param['CardId'] = FormChars($Param['CardId']);

		mysqli_query($CONNECT, "DELETE FROM `in_card` WHERE `uid` = '$Param[UserId]' AND `cid` = '$Param[CardId]'");
		exit(header('Location: /card/'.$Param['CardId']));	
	}

// добавление юзера в карточку из тех, кто состоит в доске
	if ($Module == 'addUserInCard' && $Param['CardId'] && $Param['UserId']){
		$Param['UserId'] = FormChars($Param['UserId']);
		$Param['CardId'] = FormChars($Param['CardId']);

		$getInCard = mysqli_query($CONNECT, "SELECT `uid` FROM `in_card` WHERE `cid` = '$Param[CardId]'");
		while ($row = mysqli_fetch_assoc($getInCard)) {
			$user = $row['uid'];
			if ($user == $Param['UserId']) MessageSend(1, 'User already in board!');
		}
		mysqli_query($CONNECT, "INSERT INTO `in_card`  VALUES ('', '$Param[UserId]', '$Param[CardId]')");
		exit(header('Location: /card/'.$Param['CardId']));		
	}
	

// COLUMN

// удаление колонки
	if ($Module == 'deleteColumn' && $Param['ColumnId']){
		$Param['ColumnId'] = FormChars($Param['ColumnId']);

		$getCards = mysqli_query($CONNECT, "SELECT `id` FROM `cards` WHERE `column_id` = '$Param[ColumnId]'");			
		while ($cards = mysqli_fetch_assoc($getCards)) {
			$curCard = $cards['id'];

			$getComments = mysqli_query($CONNECT, "SELECT `id` FROM `comments` WHERE `card_id` = $curCard");			
			while ($comments = mysqli_fetch_assoc($getComments)) {
				$curComment = $comments['id'];

				// delete comment here
				mysqli_query($CONNECT, "DELETE FROM `comments`  WHERE `id` = $curComment");		
			}

			$getInCard = mysqli_query($CONNECT, "SELECT `id` FROM `in_card` WHERE `cid` = $curCard");			
			while ($inCard = mysqli_fetch_assoc($getInCard)) {
				$curInCard = $inCard['id'];

				// delete in_card here
				mysqli_query($CONNECT, "DELETE FROM `in_card`  WHERE `id` = $curInCard");		
			}		
			// delete card here
			mysqli_query($CONNECT, "DELETE FROM `cards` WHERE `id` = $curCard");
		}


		$getInfoOfThisColumn = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `position`, `board_id` FROM `columns` WHERE `id` = '$Param[ColumnId]'"));
		// change position of another columns
		mysqli_query($CONNECT, "UPDATE `columns` SET `position` = `position` - 1 WHERE `position` > '$getInfoOfThisColumn[position]'");
		//delete column here
		mysqli_query($CONNECT, "DELETE FROM `columns`  WHERE `id` = '$Param[ColumnId]'");

		exit(header('Location: /board/'.$getInfoOfThisColumn['board_id']));
	}


// BOARD


	if ($Module == 'deleteBoard' && $Param['BoardId']){
		$Param['BoardId'] = FormChars($Param['BoardId']);

		$getColumns = mysqli_query($CONNECT, "SELECT `id` FROM `columns` WHERE `board_id` = '$Param[BoardId]'");
		while ($columns = mysqli_fetch_assoc($getColumns)) {
			$curColumn = $columns['id'];

			$getCards = mysqli_query($CONNECT, "SELECT `id` FROM `cards` WHERE `column_id` = $curColumn");			
			while ($cards = mysqli_fetch_assoc($getCards)) {
				$curCard = $cards['id'];

				$getComments = mysqli_query($CONNECT, "SELECT `id` FROM `comments` WHERE `card_id` = $curCard");			
				while ($comments = mysqli_fetch_assoc($getComments)) {
					$curComment = $comments['id'];

					// delete comment here
					mysqli_query($CONNECT, "DELETE FROM `comments`  WHERE `id` = $curComment");		
				}

				$getInCard = mysqli_query($CONNECT, "SELECT `id` FROM `in_card` WHERE `cid` = $curCard");			
				while ($inCard = mysqli_fetch_assoc($getInCard)) {
					$curInCard = $inCard['id'];

					// delete in_card here
					mysqli_query($CONNECT, "DELETE FROM `in_card`  WHERE `id` = $curInCard");		
				}		
				// delete card here
				mysqli_query($CONNECT, "DELETE FROM `cards` WHERE `id` = $curCard");
			}
			//delete column here
			mysqli_query($CONNECT, "DELETE FROM `columns`  WHERE `id` = $curColumn");
		}
		$getInBoard = mysqli_query($CONNECT, "SELECT `id` FROM `in_board` WHERE `bid` = '$Param[BoardId]'");			
		while ($inBoard = mysqli_fetch_assoc($getInBoard)) {
			$curInBoard = $inBoard['id'];

			// delete in_board here
			mysqli_query($CONNECT, "DELETE FROM `in_board`  WHERE `id` = $curInBoard");
		}
		// delete board here
		mysqli_query($CONNECT, "DELETE FROM `boards`  WHERE `id` = '$Param[BoardId]'");
		exit(header('Location: /boards'));		
	}


// добавление колонок в доску
	if ($Module == 'addColumn' && $Param['BoardId']){
		$_POST['name'] = FormChars($_POST['name']);
		$Param['BoardId'] = FormChars($Param['BoardId']);

		$getPosition = mysqli_query($CONNECT, "SELECT `position` FROM `columns` WHERE `board_id` = '$Param[BoardId]'");
		$curPosition = 0;
		while ($position = mysqli_fetch_assoc($getPosition)) {
			$prevPosition = $position['position'];
			if ($prevPosition > $curPosition) $curPosition = $prevPosition;
		}
		$curPosition ++;
		mysqli_query($CONNECT, "INSERT INTO `columns`  VALUES ('', '$_POST[name]', '$Param[BoardId]', $curPosition)");
		exit(header('Location: /board/'.$Param['BoardId']));			
	}

// удаление пользователя из доски
	if ($Module == 'deleteUserFromBoard' && $Param['BoardId']){
		$_POST['id'] = FormChars($_POST['id']);
		$Param['BoardId'] = FormChars($Param['BoardId']);

		$getInBoard = mysqli_query($CONNECT, "SELECT `uid` FROM `in_board` WHERE `bid` = '$Param[BoardId]'");
		$checkUser = false;
		while ($row = mysqli_fetch_assoc($getInBoard)) {
			$user = $row['uid'];
			if ($user == $_POST['id']) $checkUser = true;
		}
		if (!$checkUser) MessageSend(1, 'Unknown user!');
		else if ($_POST['id'] == $_SESSION['USER_ID']) MessageSend(1, 'You can not delete yourself!');

		mysqli_query($CONNECT, "DELETE FROM `in_board`  WHERE `uid` = '$_POST[id]' AND `bid` = '$Param[BoardId]'");		
		exit(header('Location: /board/'.$Param['BoardId']));
	}

// добавление юзера в доску
	if ($Module == 'addUserInBoard' && $Param['BoardId']){
		$_POST['id'] = FormChars($_POST['id']);
		$Param['BoardId'] = FormChars($Param['BoardId']);

		$getInBoard = mysqli_query($CONNECT, "SELECT `uid` FROM `in_board` WHERE `bid` = '$Param[BoardId]'");
		while ($row = mysqli_fetch_assoc($getInBoard)) {
			$user = $row['uid'];
			if ($user == $_POST['id']) MessageSend(1, 'User already in board!');
		}

		$UserCheck = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `login` FROM `users` WHERE `id` = '$_POST[id]'"));
		if (!$UserCheck['login']) MessageSend(1, 'User does not exist!');
		mysqli_query($CONNECT, "INSERT INTO `in_board`  VALUES ('', '$_POST[id]', '$Param[BoardId]')");		
		exit(header('Location: /board/'.$Param['BoardId']));
	}

// создание доски
	if ($Module == 'create' && $_POST['enter']){
		$_POST['name'] = FormChars($_POST['name']);
		$creator = $_SESSION['USER_ID'];
		if (!$_POST['name']) MessageSend(1, 'Enter board name!');

		mysqli_query($CONNECT, "INSERT INTO `boards`  VALUES ('', '$_POST[name]')");
		$Row = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `id` FROM `boards` WHERE `name` = '$_POST[name]'"));
// добавление создателя в карточку
		mysqli_query($CONNECT, "INSERT INTO `in_board`  VALUES ('', '$creator', '$Row[id]')");


// создание трех стартовых колонок
		mysqli_query($CONNECT, "INSERT INTO `columns`  VALUES ('', 'To Do', '$Row[id]', 1)");
		mysqli_query($CONNECT, "INSERT INTO `columns`  VALUES ('', 'In Progress', '$Row[id]', 2)");
		mysqli_query($CONNECT, "INSERT INTO `columns`  VALUES ('', 'Done', '$Row[id]', 3)");

// создание по одной стартовой карточке в каждой колонке
		$column = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `id` FROM `columns` WHERE `name` = 'To Do' AND `board_id` = '$Row[id]'"));	
		mysqli_query($CONNECT, "INSERT INTO `cards`  VALUES ('', 'Add own card', '$column[id]', 'Here will be instruction how to add new card', 1)");		

		$column = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `id` FROM `columns` WHERE `name` = 'In progress' AND `board_id` = '$Row[id]'"));	
		mysqli_query($CONNECT, "INSERT INTO `cards`  VALUES ('', 'Read the name of this card', '$column[id]', 'This card now doing', 1)");	

		$column = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `id` FROM `columns` WHERE `name` = 'Done' AND `board_id` = '$Row[id]'"));	
		mysqli_query($CONNECT, "INSERT INTO `cards`  VALUES ('', 'Create board', '$column[id]', 'You have done it!', 1)");			
		
		exit(header('Location: /boards'));
	}


?>