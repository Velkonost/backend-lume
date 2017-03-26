<?php 
	if ($Module == 'category' and $Param['id'] != 1 and $Param['id'] != 2 and $Param['id'] != 3) MessageSend(1, 'Такой категории не существует.', '/loads');
	$Param['page'] += 0;
?>
<body>
<?php Menu();
	MessageShow() 
?>
<div class="CatHead">
	<?php if ($_SESSION['USER_LOGIN_IN']) echo '<a href="/loads/add"><div class="Cat">Добавить файл</div></a>' ?>
	<a href="/loads"><div class="Cat">Все категории</div></a>
	<a href="/loads/category/id/1"><div class="Cat">Категория 1</div></a>
	<a href="/loads/category/id/2"><div class="Cat">Категория 2</div></a>
	<a href="/loads/category/id/3"><div class="Cat">Категория 3</div></a>
</div>

<div class="Page">
<?php 
	if (!$Module or $Module == 'main') {
		if ($_SESSION['USER_GROUP'] != 2) $Active = 'WHERE `active` = 1';
		$Param1 = 'SELECT `id`, `name`, `added`, `date`, `active` FROM `load_files` '.$Active.' ORDER BY `id` DESC LIMIT 0, 5';
		$Param2 = 'SELECT `id`, `name`, `added`, `date`, `active` FROM `load_files` '.$Active.' ORDER BY `id` DESC LIMIT START, 5';
		$Param3 = 'SELECT COUNT(`id`) FROM `load_files`';
		$Param4 = '/loads/main/page/';
	} else if ($Module == 'category') {
		if ($_SESSION['USER_GROUP'] != 2) $Active = 'AND `active` = 1';
		$Param1 = 'SELECT `id`, `name`, `added`, `date`, `active` FROM `load_files` WHERE `cat` = '.$Param['id'].' '.$Active.' ORDER BY `id` DESC LIMIT 0, 5';
		$Param2 = 'SELECT `id`, `name`, `added`, `date`, `active` FROM `load_files` WHERE `cat` = '.$Param['id'].' '.$Active.' ORDER BY `id` DESC LIMIT START, 5';
		$Param3 = 'SELECT COUNT(`id`) FROM `load_files` WHERE `cat` = '.$Param['id'];
		$Param4 = '/loads/category/id/'.$Param['id'].'/page/';
	}

	$Count = mysqli_fetch_row(mysqli_query($CONNECT, $Param3));

	if (!$Param['page']) {
		$Param['page'] = 1;
		$Result = mysqli_query($CONNECT, $Param1);
	} else {
		$Start = ($Param['page'] - 1) * 5;
		$Result = mysqli_query($CONNECT, str_replace('START', $Start, $Param2));
	}	

	PageSelector($Param4, $Param['page'], $Count);

	while ($Row = mysqli_fetch_assoc($Result)) {
		if (!$Row['active']) $Row['name'] .= ' (Ожидает модерации)';
		echo '<a href="/loads/material/id/'.$Row['id'].'"><div class="ChatBlock"><span>Добавил: '.$Row['added'].' | '.$Row['date'].'</span>'.$Row['name'].'</div></a>';
	}
?>
</div>
</div>

</div>
</body>
</html>