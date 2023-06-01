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
		or ($userdata['ip'] !== $_SERVER['REMOTE_ADDR']) or ($userdata['status'] >= 2) or $userdata['banned'])
    {
        setcookie("id", "", time() - 3600*24*30*12, "/");
        setcookie("hash", "", time() - 3600*24*30*12, "/", "", "", true); // httponly
        echo("0");
		exit();
    }
}
else
{
    echo("0");
	exit();
}

if($userdata['status'] > 1){
	echo("0");
	exit();
}elseif($userdata['status'] == 1){//для мастера
	if($_POST['my'] == "1"){//личные заявки мастера
		$query = mysqli_query($link, "	SELECT applications.id, user_id, adress, DATE(date) as date, time, applications.status, latitude, longitude, dopinfo, phone, taken
											FROM applications 
												JOIN users ON users.id = user_id
											WHERE taken = " . intval($_COOKIE['id']));
		$rows = array();
		$rows[] = "1";

		if ($query->num_rows > 0) {
			// Пройти по каждой строке в результате запроса
			while($row = $query->fetch_assoc()) {
				// Добавить строку в массив
				$rows[] = $row;
			}
		}

		$json = json_encode($rows, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
		echo $json;
	}else{// одобреные заявки
		$query = mysqli_query($link, "	SELECT applications.id, user_id, adress, DATE(date) as date, time, applications.status, latitude, longitude, dopinfo, phone, taken
											FROM applications 
												JOIN users ON users.id = user_id
											WHERE applications.status = 1");
		$rows = array();
		$rows[] = "1";

		if ($query->num_rows > 0) {
			// Пройти по каждой строке в результате запроса
			while($row = $query->fetch_assoc()) {
				// Добавить строку в массив
				$rows[] = $row;
			}
		}

		$json = json_encode($rows, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
		echo $json;
	}
}elseif($userdata['status'] == 0){//для клиента
	$query = mysqli_query($link, "	SELECT applications.id, user_id, adress, DATE(date) as date, time, applications.status, latitude, longitude, dopinfo, phone
										FROM applications 
											JOIN users ON users.id = user_id
										WHERE user_id = " . intval($_COOKIE['id']));

	$rows = array();
	$rows[] = "0";

	if ($query->num_rows > 0) {
		// Пройти по каждой строке в результате запроса
		while($row = $query->fetch_assoc()) {
			// Добавить строку в массив
			$rows[] = $row;
		}
	}

	$json = json_encode($rows, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
	echo $json;
}

?>