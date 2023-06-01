<?php
// Страница авторизации

// Функция для генерации случайной строки
function generateCode($length=6) {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
    $code = "";
    $clen = strlen($chars) - 1;
    while (strlen($code) < $length) {
            $code .= $chars[mt_rand(0,$clen)];
    }
    return $code;
}
//подробный вывод ошибки
//mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Соединямся с БД

$link=mysqli_connect("localhost", "root", "", "oplock");
$warn = "";
if(isset($_POST['submit']))
{
    // Вытаскиваем из БД запись, у которой логин равняеться введенному
    $query = mysqli_query($link,"SELECT id, password, status, banned FROM users WHERE username='".mysqli_real_escape_string($link,$_POST['username'])."' LIMIT 1");
    $data = mysqli_fetch_assoc($query);

    // Сравниваем пароли
    if(!empty($data) && $data['password'] === md5($_POST['password']) && $data['status'] < 2 && !$data['banned'])
    {
        // Генерируем случайное число и шифруем его
        $hash = md5(generateCode(10));

        $insip = ", ip=INET_ATON('".$_SERVER['REMOTE_ADDR']."')";

        // Записываем в БД новый хеш авторизации и IP
        mysqli_query($link, "UPDATE users SET hash='".$hash."' ".$insip." WHERE id='".$data['id']."'");

        // Ставим куки
        setcookie("id", $data['id'], time()+60*60*24*30, "/", "", "", true);
        setcookie("hash", $hash, time()+60*60*24*30, "/", "", "", true); // httponly !!!

		echo("1");
        // Переадресовываем браузер на страницу проверки нашего скрипта
        //header("Location: index.php"); exit();
    }
    else
    {
        echo("0");
    }
}
?>