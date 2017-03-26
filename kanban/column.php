<?php
	MessageShow();
?>
<head><meta charset="utf8"></head>
<?php
	echo 'ADD<br>
		<form method="POST" action="/system/addCard/ColumnId/'.$Module.'">
		name: <input type="text" name="name">
		description: <input type="text" name="description">
		<input type="submit" name="enter" value="add">
		</form>
	';
	$getCards = mysqli_query($CONNECT, "SELECT `name`, `id` FROM `cards` WHERE `column_id` = '$Module'");

	while ($row2 = mysqli_fetch_assoc($getCards)) {
		$cardName = $row2['name'];
		echo '<a href="/card/'.$row2['id'].'">'.$cardName.'</a>&nbsp;<a href="/system/deleteCard/CardId/'.$row2['id'].'">-</a><br>';
	}

?>