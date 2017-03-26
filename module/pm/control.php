<?php

	$Param['id'] += 0;

	if ($Param['delete'] == 'dialog') {
	
		if (!mysqli_num_rows(mysqli_query($CONNECT, "SELECT `id` FROM `dialog` WHERE `id` = $Param[id] AND `send` = $_SESSION[USER_ID]"))) MessageSend(2, 'Вы не можете удлаить этот диалог', '/pm/send');

		mysqli_query($CONNECT, "DELETE FROM `dialog` WHERE `id` = $Param[id]");
		mysqli_query($CONNECT, "DELETE FROM `message` WHERE `did` = $Param[id]");
		MessageSend(2, 'Диалог удален');	
	} else if ($Param['delete'] == 'message') {
	
		if (!mysqli_num_rows(mysqli_query($CONNECT, "SELECT `id` FROM `message` WHERE `id` = $Param[id] AND `user` = $_SESSION[USER_ID]"))) MessageSend(2, 'Вы не можете удлаить это сообщение', '/pm/send');	
		mysqli_query($CONNECT, "DELETE FROM `message` WHERE `id` = $Param[id]");
		MessageSend(2, 'Сообщение удалено');
	}
?>