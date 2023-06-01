<?php
// Скрипт проверки

// Соединямся с БД
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>applications</title>
	<link rel="stylesheet" href="css/main.css">
	<link rel="stylesheet" href="css/complaints.css">
	<script src="javaScript/tableSelect.js"></script>
</head>
<body>
	<header>
		<nav>
			<a class="a_menu" href="index.php">Заявки</a>
			<a class="a_menu" href="accounts.php">Управление учетными записями</a>
			<a class="a_menu now" href="complaints.php">Жалобы</a>
			<div class="exit">
				<a class="exit_href" href="logout.php"><img class="exit_button" height="30ph" width="30px" src="img/exit.png"></a>
			</div>
		</nav>
	</header>
	<div class="underTable">
	<div class="scrollt">
		<table>
		<tr>
			<th class="th1">id</th>
			<th class="th2">Пользователь</th>
			<th class="th3">Жалоба на</th>
			<th class="th4">Заявка</th>
			<th class="th5">Дата</th>
		</tr>
		
			<?php
				$query = mysqli_query($link, "	SELECT complaints.id AS id1, f.username as username1, t.username as username2, applications.id AS id2, complaints.date 
													FROM complaints 
														JOIN users t ON complaints.userTo = t.id
														JOIN users f ON complaints.userFrom = f.id
														JOIN applications ON complaints.application = applications.id
													ORDER BY date DESC");
				$compDate = "";
				$appDate = "";
				$appId = "";
				$userFrom = "";
				$userTo = "";
				$adress = "";
				$text = "";
				$time = "";
													
				for ($i = 0; $i < $query->num_rows; $i++) {
					$query->data_seek($i);
					$row = $query->fetch_assoc();
					
					$selected = "";
					if(isset($_GET['selectedId']) && is_numeric($_GET['selectedId']) && $_GET['selectedId'] == $row['id1']){
						$selected = " selected";
						
						$complaintQuery = mysqli_query($link, "	SELECT complaints.date AS date1, applications.date AS date2, applications.id, f.username AS f, adress, time, t.username AS t, text
																	FROM complaints 
																			JOIN users t ON complaints.userTo = t.id
																		JOIN users f ON complaints.userFrom = f.id
																		JOIN applications ON complaints.application = applications.id
																	WHERE complaints.id = " . $row['id1']);
						$complaintData = mysqli_fetch_assoc($complaintQuery);
						$compDate = $complaintData['date1'];
						$appDate = $complaintData['date2'];
						$appId = $complaintData['id'];
						$userFrom = $complaintData['f'];
						$userTo = $complaintData['t'];
						$adress = $complaintData['adress'];
						$text = $complaintData['text'];
						switch ($complaintData['time']) {
							case 1:
								$time = "10:00-14:00";
								break;
							case 2:
								$time = "14:00-18:00";
								break;
						}
						
					}
					
					echo "<tr class=\"selectable". $selected ."\"><td class=\"th1\">" . $row['id1'] . "</td>";
					echo "<td class=\"th2\">" . $row['username1'] . "</td>";
					echo "<td class=\"th3\">" . $row['username2'] . "</td>";
					echo "<td class=\"th4\">" . $row['id2'] . "</td>";
					echo "<td class=\"th5\">" . $row['date'] . "</td>";

					echo("</tr>");
				}
			?>
			
		</table>
	</div>
	</div>
	<div class="textBlock">
		Информация<br>
		Дата жалобы: <?php echo($compDate) ?><br>
		Дата заявки: <?php echo($appDate) ?><br>
		id заявки: <?php echo($appId) ?><br>
		Пользаватель: <?php echo($userFrom) ?><br>
		Адрес: <?php echo($adress) ?><br>
		Время: <?php echo($time) ?><br>
		Жалоба на: <?php echo($userTo) ?><br>
		Жалоба: <?php echo($text) ?><br>
	</div>
	</body>
</html>