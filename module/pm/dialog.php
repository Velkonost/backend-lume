<head><meta charset="utf8"></head>
<?php 
	$Count = mysqli_fetch_row(mysqli_query($CONNECT, "SELECT COUNT(`id`) FROM `dialog` WHERE `send` = $_SESSION[USER_ID] OR `receive` = $_SESSION[USER_ID]"));
	if (!$Count[0]) MessageSend(2, 'У вас нет диалогов');
?>
<body>
<a href="/pm/send">My Dialogs</a><br><br>
<?php
	MessageShow() 
?>
<div class="Page">

<?php 

	if (!$Param['page']) {
		$Param['page'] = 1;
		$Result = mysqli_query($CONNECT, "SELECT * FROM `dialog` WHERE `send` = $_SESSION[USER_ID] OR `receive` = $_SESSION[USER_ID] ORDER BY `id` DESC LIMIT 0, 5");
	} else {
		$Start = ($Param['page'] - 1) * 5;
		$Result = mysqli_query($CONNECT, str_replace('START', $Start, "SELECT * FROM `dialog` WHERE `send` = $_SESSION[USER_ID] OR `receive` = $_SESSION[USER_ID] ORDER BY `id` DESC LIMIT START, 5"));
	}

	PageSelector('/pm/dialog/page/', $Param['page'], $Count);

	while ($Row = mysqli_fetch_assoc($Result)) {
		if ($Row['status']) $Status = 'Прочитано ';
		else $Status = 'Не прочитано ';

		if ($Row['send'] == $_SESSION['USER_ID']) $delete = '| <a href="/pm/control/delete/dialog/id/'.$Row['id'].'">Delete</a>';
		else $delete = '';

		if ($Row['receive'] == $_SESSION['USER_ID']) $Row['receive'] = $Row['send'];
		$User = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `login` FROM `users` WHERE `id` = $Row[receive]"));
		echo '<div class="ChatBlock"><span>'.$Status.$delete.'</span><a href="/pm/message/id/'.$Row['id'].'">Диалог с '.$User['login'].'</a></div>';
	}
?>

</div>

</body>