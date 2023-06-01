<?php
// Скрипт проверки

// Вывод подробных ошибок
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

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

$latitude = "";
$longitude = "";
$dopinfo = "";
if(isset($_GET['selectedId']) && is_numeric($_GET['selectedId'])){
	$selAppQ = mysqli_query($link, "SELECT latitude, longitude, dopinfo FROM applications WHERE id = '".intval($_GET['selectedId'])."' LIMIT 1");
	if($selAppQ){
		$selApp = mysqli_fetch_assoc($selAppQ);
		$latitude = $selApp['latitude'];
		$longitude = $selApp['longitude'];
		$dopinfo = $selApp['dopinfo'];
	}
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>applications</title>
	<link rel="stylesheet" href="css/main.css">
	<link rel="stylesheet" href="css/applications.css">
	<script src="javaScript/tableSelect.js"></script>
	<script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&amp;apikey=6d511e99-55e2-4c1d-aad3-54a7b3944284" type="text/javascript"></script>
	<?php 
	if($latitude && $longitude){
		echo("	<script type=\"text/javascript\">
					ymaps.ready(function(){
						// Указывается идентификатор HTML-элемента.
						var moscow_map = new ymaps.Map(\"map\", {
							center: [". $latitude .", ". $longitude ."],
							zoom: 17
						});
						// Создание геообъекта с типом точка (метка).
						var myGeoObject = new ymaps.GeoObject({
							geometry: {
								type: \"Point\", // тип геометрии - точка
								coordinates: [". $latitude .", ". $longitude ."] // координаты точки
							}
						});
						moscow_map.geoObjects.add(myGeoObject); 
					}); 
				</script>");
	}
	?>
</head>
<body>
	<header>
		<nav>
			<a class="a_menu now" href="index.php">Заявки</a>
			<a class="a_menu" href="accounts.php">Управление учетными записями</a>
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
					<th class="th2">Пользаватель</th>
					<th class="th3">Адрес</th>
					<th class="th4">Дата</th>
					<th class="th5">Время</th>
					<th class="th6">Статус</th>
			<?php
				
				$query = mysqli_query($link, "	SELECT applications.id, user.username, adress, DATE(date) as date, time, applications.status, take.username AS taker
													FROM applications 
														JOIN users user ON applications.user_id = user.id
														LEFT JOIN users take ON taken = take.id
													ORDER BY status, date ASC");
				$nonactive = "class=\"nonactive\" disabled";
				$dialog = "";
				
				for ($i = 0; $i < $query->num_rows; $i++) {
					$query->data_seek($i);
					$row = $query->fetch_assoc();
					
					$selected = "";
					
					if(isset($_GET['selectedId']) && is_numeric($_GET['selectedId']) && $_GET['selectedId'] == $row['id']){
						$selected = " selected";
						$value = "value=\"". $row['id'] ."\"";
						
						if(isset($_GET['selectedId']) && $row['status'] == 0){
							$nonactive = "";
							if(isset($_POST['approve'])){
									if(mysqli_query($link, "UPDATE `applications` SET `status` = '1' WHERE `id` = " . $row['id'])){
										$row['status'] = "1";
										$nonactive = "class=\"nonactive\" disabled";
										
										$dialog = "Заявка успешно одобрена";
									}else{
										$dialog = "Ошибка базы данных";
									}
							}
							if(isset($_POST['reject'])){
									if(mysqli_query($link, "UPDATE `applications` SET `status` = '4' WHERE `id` = " . $row['id'])){
										$row['status'] = "4";
										$nonactive = "class=\"nonactive\" disabled";
										
										$dialog = "Заявка успешно отклонена";
									}else{
										$dialog = "Ошибка базы данных";
									}
							}
						}
					}
					
					echo "<tr class=\"selectable". $selected ."\"><td class=\"th1\">" . $row['id'] . "</td>";
					echo "<td class=\"th2\">" . $row['username'] . "</td>";
					echo "<td class=\"th3\">" . $row['adress'] . "</td>";
					echo "<td class=\"th4\">" . $row['date'] . "</td>";
					switch ($row['time']) {
						case 1:
							echo "<td class=\"th5\">10:00-14:00</td>";
							break;
						case 2:
							echo "<td class=\"th5\">14:00-18:00</td>";
							break;
					}
							
					switch ($row['status']) {
						case 0:
							echo "<td class=\"th6\">подано</td>";
							break;
						case 1:
							echo "<td class=\"th6\">одобрено</td>";
							break;
						case 2:
							echo "<td class=\"th6\">Взято " . $row['taker'] . "</td>";
							break;
						case 3:
							echo "<td class=\"th6\">выполнено</td>";
							break;
						case 4:
							echo "<td class=\"th6\">отклонено</td>";
							break;
					}
					echo("</tr>");
				}
			?>
		</table>
	</div>
	</div>
	<div class="map">
		<div id="map" style="height: 100%; width: 100%;"></div>
	</div>
	<div class="textBlock">
		Дополнительная информация: <?php echo($dopinfo) ?>
	</div>
	
	<form method="POST">
	<button <?php echo($nonactive) ?> type="submit" name="approve">Одобрить</button>
	<button <?php echo($nonactive) ?> type="submit" name="reject">Отклонить</button>
	</form>
	
	<dialog <?php if($dialog){ echo("open"); } ?>>
		<?php echo($dialog) ?>
		<br><br>
		<button id="closeDialog">Ок</button>
	</dialog>
	<script>
	var dialog = document.querySelector('dialog');
	document.querySelector('#closeDialog').onclick = function() {
		dialog.close(); // Прячем диалоговое окно
	}
	</script>
	
	</body>

</html>