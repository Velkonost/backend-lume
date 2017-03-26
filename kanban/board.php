<?php
	MessageShow();
?>
<head><meta charset="utf8"></head>
<?php
// получаем имя доски
	$getBoardName = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `name` FROM `boards` WHERE `id` = '$Module'"));
	echo "<strong>$getBoardName[name]</strong><br><br><br>";
	echo "[";
// узнаем кто состоит в доске
	$getInBoard = mysqli_query($CONNECT, "SELECT `uid` FROM `in_board` WHERE `bid` = '$Module'");
	while ($row = mysqli_fetch_assoc($getInBoard)) {
		$user = $row['uid'];
		echo "$user ";
	}
	echo "]";

	echo 'ADD<br>
		<form method="POST" action="/system/addUserInBoard/BoardId/'.$Module.'">
		id: <input type="text" name="id">
		<input type="submit" name="enter" value="add">
		</form>
	';
	echo 'DELET<br>
		<form method="POST" action="/system/deleteUserFromBoard/BoardId/'.$Module.'">
		id: <input type="text" name="id">
		<input type="submit" name="enter" value="delete">
		</form>
	';
// получаем столбцы
	$getColumns	= mysqli_query($CONNECT, "SELECT `name` FROM `columns` WHERE `board_id` = '$Module'");

	$columnArr = [];
	$i = 0;
	while ($row = mysqli_fetch_assoc($getColumns)){
		$nameColumn = $row['name'];
		$columnArr[$i] = $nameColumn;
		$i++;
	}
	// выводим столбцы с карточками в них
	foreach (array_reverse($columnArr) as &$value) {
		$columnId = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `id` FROM `columns` WHERE `name` = '$value' AND `board_id` = '$Module'"));
		$idColumn = $columnId['id'];
		echo '<hr><b><a href="/column/'.$idColumn.'">'.$value.'</a></b>&nbsp;<a href="/system/deleteColumn/ColumnId/'.$idColumn.'">-</a><br><br>';
	}

	echo 'ADD COLUMN
		<form method="POST" action="/system/addColumn/BoardId/'.$Module.'">
		name: <input type="text" name="name">
		<input type="submit" name="enter" value="add">
		</form>';
?>