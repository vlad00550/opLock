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

if($userdata['status'] != 0){
	echo("0");
	exit();
}

	if(mysqli_query($link, "	INSERT INTO `applications` 
									VALUES (NULL, '". intval($_COOKIE['id']) ."', '". $_POST['adress'] ."'
												, '". $_POST['latitude'] ."', '". $_POST['longitude'] ."'
												, SYSDATE(), '". $_POST['time'] ."', '". $_POST['dopinfo'] ."'
												, '0', NULL);")){
		echo(1);						
	}else{
		echo(0);
	}


?>