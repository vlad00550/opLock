<?php
// Скрипт проверки

// Соединямся с БД

function generateCode($length=6) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
    $code = "";
    $clen = strlen($chars) - 1;
    while (strlen($code) < $length) {
            $code .= $chars[mt_rand(0,$clen)];
    }
    return $code;
}

$link=mysqli_connect("localhost", "root", "", "oplock");

if (isset($_COOKIE['id']) and isset($_COOKIE['hash']))
{
    $query = mysqli_query($link, "SELECT *,INET_NTOA(ip) AS ip FROM users WHERE id = '".intval($_COOKIE['id'])."' LIMIT 1");
    $userdata = mysqli_fetch_assoc($query);


    if(($userdata['hash'] !== $_COOKIE['hash']) or ($userdata['id'] !== $_COOKIE['id']) 
		or ($userdata['ip'] !== $_SERVER['REMOTE_ADDR']) or ($userdata['status'] < 2) or $userdata['banned'])
    {
        setcookie("id", "", time() - 3600*24*30*12, "/");
        setcookie("hash", "", time() - 3600*24*30*12, "/", null, null, true); // httponly
        header("Location: login.php"); exit();
    }
}
else
{
    header("Location: login.php"); exit();
}
$dialog = "";
if(isset($_POST['addW']) && !empty($_POST['username'])){
	if(preg_match("/^[a-zA-Z0-9]+$/",$_POST['username']) && strlen($_POST['username']) > 5){
		$pass = generateCode(8);
		if(mysqli_query($link, 	"INSERT INTO `users` VALUES (NULL, '". mysqli_real_escape_string($link, $_POST['username']) .
								"', '". md5($pass) ."', NULL, NULL, SYSDATE(), '0', '0', '1', NULL, NULL, '0')")){
			$dialog = "Добавить мастера<br><br>". "Логин: " . $_POST['username'] . "<br>Пароль: " . $pass;
		}else{
			$dialog = "Ошибка базы данных";
		}
	}else{
		$dialog = "Логин должен состоять из 6 или более символов английского алфавита или цифр";
	}
}
if(isset($_POST['addM']) && !empty($_POST['username']) && $userdata['status'] > 2){
	if(preg_match("/^[a-zA-Z0-9]+$/",$_POST['username']) && strlen($_POST['username']) > 5){
		$pass = generateCode(8);
		if(mysqli_query($link, 	"INSERT INTO `users` VALUES (NULL, '". mysqli_real_escape_string($link, $_POST['username']) .
								"', '". md5($pass) ."', NULL, NULL, SYSDATE() , '0', '0', '2', NULL, NULL, '0');")){
			$dialog = "Добавить менеджера<br><br>". "Логин: " . $_POST['username'] . "<br>Пароль: " . $pass;
		}else{
			$dialog = "Ошибка базы данных";
		}
	}else{
		$dialog = "Логин должен состоять из 6 или более символов английского алфавита или цифр";
	}
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>applications</title>
	<link rel="stylesheet" href="css/main.css">
	<link rel="stylesheet" href="css/accounts.css">
	<script src="javaScript/tableSelect.js"></script>
</head>
<body>
	<header>
		<nav>
			<a class="a_menu" href="index.php">Заявки</a>
			<a class="a_menu now" href="accounts.php">Управление учетными записями</a>
			<a class="a_menu" href="complaints.php">Жалобы</a>
			<div class="exit">
				<a class="exit_href" href="logout.php"><img class="exit_button" height="30ph" width="30px" src="img/exit.png"></a>
			</div>
		</nav>
	</header>
	<div class="underTable">
	<div class="scrollt">
		<table>
					<th class="th1">id</th>
					<th class="th2">Имя пользователя</th>
					<th class="th3">Телефон</th>
					<th class="th4">Почта</th>
					<th class="th5">Поданные жалобы</th>
					<th class="th6">Жалобы на пользователя</th>
					<th class="th7">Дата регистрации</th>
					<th class="th8">Статус</th>
			<?php
				$query = mysqli_query($link, "	SELECT id, username, phone, email, complainsFrom, complainsTo, registrationDate, status, banned 
													FROM users ");
				$nonactiveBlock = "class=\"nonactive\" disabled";
				$nonactiveUnlock = "class=\"nonactive\" disabled";

				if($userdata['status'] == 3){
					$nonactiveAddManager = "";
				}else{
					$nonactiveAddManager = "class=\"nonactive\" disabled";
				}
				
				for ($i = 0; $i < $query->num_rows; $i++) {
					$query->data_seek($i);
					$row = $query->fetch_assoc();
					
					$selected = "";
					
					if(isset($_GET['selectedId']) && is_numeric($_GET['selectedId']) && $_GET['selectedId'] == $row['id']){
						$selected = " selected";
						if($row['status'] < $userdata['status'] && !$row['banned']){
							$nonactiveBlock = "";
						}
						if($row['status'] < $userdata['status'] && $row['banned']){
							$nonactiveUnlock = "";
						}
						
						if(isset($_POST['block']) && $row['status'] < $userdata['status'] && !$row['banned']){
									if(mysqli_query($link, "UPDATE `oplock`.`users` SET `banned` = '1' WHERE `users`.`id` = " . $row['id'])){
										$row['banned'] = "1";
										$nonactiveBlock = "class=\"nonactive\" disabled";
										$nonactiveUnlock = "";
										$dialog = "Пользователь успешно заблокирован";
									}else{
										$dialog = "Ошибка базы данных";
									}
							}
							if(isset($_POST['unBlock']) && $row['status'] < $userdata['status'] && $row['banned']){
									if(mysqli_query($link, "UPDATE `oplock`.`users` SET `banned` = '0' WHERE `users`.`id` = " . $row['id'])){
										$row['banned'] = "0";
										$nonactiveUnlock = "class=\"nonactive\" disabled";
										$nonactiveBlock = "";
										$dialog = "Пользователь успешно разблокирован";
									}else{
										$dialog = "Ошибка базы данных";
									}
							}
						
					}
					if($row['banned']){
						$banned = " banned";
						$banStatus = "(ЗАБЛОКИРОВАН)";
					}else{
						$banned = "";
						$banStatus = "";
					}
					
					echo "<tr class=\"selectable". $selected . $banned . "\"><td class=\"th1\">" . $row['id'] . "</td>";
					echo "<td class=\"th2\">" . $row['username'] . "</td>";
					echo "<td class=\"th3\">" . $row['phone'] . "</td>";
					echo "<td class=\"th4\">" . $row['email'] . "</td>";
					echo "<td class=\"th5\">" . $row['complainsFrom'] . "</td>";
					echo "<td class=\"th6\">" . $row['complainsTo'] . "</td>";
					echo "<td class=\"th7\">" . $row['registrationDate'] . "</td>";
					
					switch ($row['status']) {
						case 0:
							echo "<td class=\"th8\">пользователь". $banStatus ."</td>";
							break;
						case 1:
							echo "<td class=\"th8\">мастер". $banStatus ."</td>";
							break;
						case 2:
							echo "<td class=\"th8\">менеджер". $banStatus ."</td>";
							break;
						case 3:
							echo "<td class=\"th8\">владелец". $banStatus ."</td>";
							break;
					}
					echo("</tr>");
				}
			?>
			
		</table>
	</div>
	</div>
	<div class="buttons">
	<form method="POST">
		<button <?php echo($nonactiveBlock) ?> name="block" type="submit">Заблокировать</button>
		<button <?php echo($nonactiveUnlock) ?> name="unBlock" type="submit">Разблокировать</button>
	</form>
	
	<button id="openW">Добавить мастера</button>
	<button <?php echo($nonactiveAddManager) ?> id="openM">Добавить менеджера</button>
	
	<dialog class="ok" <?php if($dialog){ echo("open"); } ?>>
		<?php echo($dialog) ?>
		<br><br>
		<button id="closeOk">Ок</button>
	</dialog>
	
	<dialog class="addM">
		Добавить менеджера
		<br><br>
		<form method="POST">
			<input name="username" placeholder="Логин" type="text">
			<br><br>
			<button name="addM" id="closeAddM">Добавить</button>
		</form>
	</dialog>
	
	<dialog class="addW">
		Добавить мастера
		<br><br>
		<form method="POST">
			<input name="username" placeholder="Логин" type="text">
			<br><br>
			<button name="addW" id="closeAddW">Добавить</button>
		</form>
	</dialog>
	
	<script>
	var ok = document.querySelector('.ok');
	document.querySelector('#closeOk').onclick = function() {
		ok.close(); // Прячем диалоговое окно
	}
	
	var addM = document.querySelector('.addM');
	document.querySelector('#openM').onclick = function() {
		addM.show(); // Показываем диалоговое окно
	}
	document.querySelector('#closeAddM').onclick = function() {
		addM.close(); // Прячем диалоговое окно
	}
	
	var addW = document.querySelector('.addW');
	document.querySelector('#openW').onclick = function() {
		addW.show(); // Показываем диалоговое окно
	}
	document.querySelector('#closeAddW').onclick = function() {
		addW.close(); // Прячем диалоговое окно
	}

  </script>
	
	
	</div>
	
	
	</body>

</html>