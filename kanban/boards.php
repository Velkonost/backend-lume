<head><meta charset="utf8"></head>
<body>
<?php
	MessageShow();
?>

<?php
	$Result = mysqli_query($CONNECT, "SELECT * FROM `in_board` WHERE `uid` = $_SESSION[USER_ID]");
?>

	<form method="POST" action="/system/create">
		name: <input type="text" name="name">
		<input type="submit" name="enter" value="create">
	</form>

	<br>
	My boards :
	<?php
		while ($row = mysqli_fetch_assoc($Result)){
			$bid = $row['bid'];
			$getName = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `name` FROM `boards` WHERE `id` = $bid"));
			echo '<a href="board/'.$bid.'"><span>'.$getName['name'].'</span></a>&nbsp;<a href="/system/deleteBoard/BoardId/'.$bid.'">-</a><br>';
		}
			

	?>

</body>