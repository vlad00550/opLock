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
$query = mysqli_query($link, "SELECT status FROM applications WHERE id = '".intval($_POST['id'])."' LIMIT 1");
$appData = mysqli_fetch_assoc($query);

if(mysqli_query($link, "SELECT id FROM complaints WHERE userFrom = '". $userdata['id'] ."' AND application = '". intval($_POST['id']). "'")->num_rows == 0){
	echo("2");
	exit();
}

if($userdata['status'] == 0 && $appData['status'] == 3){
	if(mysqli_query($link, "INSERT INTO `complaints` VALUES (NULL, '" . 
								intval($userdata['id']) . "', (SELECT taken FROM applications WHERE id = ". 
								intval($_POST['id']) ."), '". intval($_POST['id']) ."', SYSDATE(), '". mysqli_real_escape_string($link, $_POST['text']) ."')")){
									
		mysqli_query($link, "UPDATE users SET complainsFrom = complainsFrom  + 1 WHERE id = ". intval($userdata['id']));
		mysqli_query($link, "UPDATE users SET complainsTo = complainsTo  + 1 WHERE id = (SELECT taken FROM applications WHERE id = ". intval($userdata['id']) .")");
		echo("1");
		exit();
	}
}elseif($userdata['status'] == 1 && $appData['status'] == 3){
	if(mysqli_query($link, "INSERT INTO `complaints` VALUES (NULL, '" . 
								intval($userdata['id']) . "', (SELECT user_id FROM applications WHERE id = ". 
								intval($_POST['id']) ."), '". intval($_POST['id']) ."', SYSDATE(), '". mysqli_real_escape_string($link, $_POST['text']) ."')")){
									
		mysqli_query($link, "UPDATE users SET complainsFrom = complainsFrom  + 1 WHERE id = ". intval($userdata['id']));
		mysqli_query($link, "UPDATE users SET complainsTo = complainsTo  + 1 WHERE id = (SELECT user_id FROM applications WHERE id = ". intval($userdata['id']) .")");
		echo("1");
		exit();
	}
}
echo("0");
?>