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

if($userdata['status'] != 0 && !isset($_POST['submit'])){
	echo("0");
	exit();
}else{
	$query = mysqli_query($link, "SELECT status FROM applications WHERE id = '".intval($_POST['id'])."' LIMIT 1");
    $appData = mysqli_fetch_assoc($query);
	if($appData['status'] == 0 || $appData['status'] == 1)
		if(mysqli_query($link, "UPDATE `applications` SET `status` = '4' WHERE `applications`.`id` = " . intval($_POST['id']))){
			echo("1");
			exit();
		}
}
echo("0");
?>