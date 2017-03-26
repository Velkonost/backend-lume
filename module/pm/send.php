<?php
	// ULogin(1);
	if ($_POST['enter'] and $_POST['text'] && $_POST['login']){
		SendMessage($_POST['login'], $_POST['text']);
		MessageSend(3, 'Message sent');
	}
?>
<head><meta charset="utf8"></head>
<body>
<?php
	MessageShow();
?>
		
	<a href="/pm/dialog">My Dialogs</a><br><br>

	<form method="POST" action="/pm/send">
		<input type="text" name="login" required="required"><br>
		<textarea name="text" required="required" style="width: 300px;height: 40px;resize: none; padding: 5px"></textarea>
		<input type="submit" name="enter" value="send">
	</form>

</body>