<?php
	MessageShow();
?>
<head><meta charset="utf8"></head>
<?php
// получение инфы о карточке
	$getCardInfo = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `name`, `description`, `column_id` FROM `cards` WHERE `id` = '$Module'"));
	$BoardId = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `board_id` FROM `columns` WHERE `id` = '$getCardInfo[column_id]'"));

	echo "NAME : $getCardInfo[name]<br>";
	echo "DESCRIPTION : $getCardInfo[description]<br>";




	echo "IN CARD : ";

// вывод тех, кто состоит в карточке
	$getInCard = mysqli_query($CONNECT, "SELECT `uid` FROM `in_card` WHERE `cid` = '$Module'");
	while ($row = mysqli_fetch_assoc($getInCard)) {
		$user = $row['uid'];
		echo '<a href="/system/deleteUserFromCard/CardId/'.$Module.'/UserId/'.$user.'">'.$user.'</a>&nbsp;';
	}




// вывод тех, кто состоит в доске
	echo "<br>ADD USER : ";
	$getInBoard = mysqli_query($CONNECT, "SELECT `uid` FROM `in_board` WHERE `bid` = '$BoardId[board_id]'");
	while ($row = mysqli_fetch_assoc($getInBoard)) {
		$user = $row['uid'];
		echo '<a href="/system/addUserInCard/CardId/'.$Module.'/UserId/'.$user.'">'.$user.'</a><br>';
	}

	// echo "<br>BUTTONS : <br>";
	/* ADD USER, DELETE USER, CHANGE DESCRIPTION, CHANGE NAME, ADD CHECKBOX, CHANGE COLUMN, GET LINK ON THE CARD
	*/

?>

<!-- Comments -->
<body>
<div class="ChatBox" style="overflow: auto; height: 300px; border: 1px solid #dddddd">
	<?php
		$Query = mysqli_query($CONNECT, "SELECT * FROM `comments` WHERE `card_id` = '$Module' ORDER BY `date` DESC LIMIT 500");

		// if ($Query)
		while ($Row = mysqli_fetch_assoc($Query)) echo '<div class="ChatBlock" style="border: 1px solid #dddddd; background: #f2f2f2; padding: 10px; color: #000000; margin: 10px;"><span style="color: #828282; font-size: 16px; display: block">'.$Row['send_id'].' | '.$Row['date'].'</span>'.$Row['text'].'</div>';
	?>

</div>
<?php
	echo '
	<form method="POST" action="/system/addComment/CardId/'.$Module.'/UserId/'.$_SESSION['USER_ID'].'">
		<textarea name="text" required="required" style="width: 300px;height: 40px;resize: none; padding: 5px"></textarea>
		<input type="submit" name="enter" value="send">
	</form>
	';
?>
</body>