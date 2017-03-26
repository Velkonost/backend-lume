<?php
	// ULogin(1);
	$Param['id'] += 0;

	$Info = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `receive`, `send` FROM `dialog` WHERE `id` = $Param[id]"));

	if (!in_array($_SESSION['USER_ID'], $Info)) MessageSend(1, 'Dialog doesn\'t exist!', '/');

	if ($Info['receive'] == $_SESSION['USER_ID']) mysqli_query($CONNECT, "UPDATE `dialog` SET `status` = 1 WHERE `id` = $Param[id]");

	if ($Info['send'] == $_SESSION['USER_ID']) $Info['send'] = $Info['receive'];

	$User = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `login` FROM `users` WHERE `id` = $Info[send]"));


?>
<head><meta charset="utf8"></head>
<body>
<?php
	MessageShow();
?>
		
	<a href="/pm/dialog">My Dialogs</a><br><br>

	<?php
		$Query = mysqli_query($CONNECT, "SELECT * FROM `message` WHERE `did` = $Param[id] ORDER BY `id` DESC"); 

		while ($Row = mysqli_fetch_assoc($Query)) {
			if ($Row['user'] == $_SESSION['USER_ID']) $delete = '| <a href="/pm/control/delete/message/id/'.$Row['id'].'">Delete</a>';
			else $delete = '';

			if ($Info['send'] == $Row['user']) $Row['user'] = $User['login'];
			else $Row['user'] = $_SESSION['USER_LOGIN'];

			echo '<div class="ChatBlock" style="border: 1px solid #dddddd; background: #f2f2f2; padding: 10px; color: #000000; margin: 10px;"><span style="color: #828282; font-size: 16px; display: block">'.$Row['user'].' | '.$Row['date'].$delete.'</span>'.$Row['text'].'</div>';
		}
	?>

	<form method="POST" action="/pm/send">
		<input type="hidden" name="login" value="<?php echo $User['login'];?>"><br>
		<textarea name="text" required="required" style="width: 300px;height: 40px;resize: none; padding: 5px"></textarea>
		<input type="submit" name="enter" value="send">
	</form>

</body>