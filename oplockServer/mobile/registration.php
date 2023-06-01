<?php
// Страница регистрации нового пользователя

// Соединямся с БД
$link=mysqli_connect("localhost", "root", "", "oplock");

if(isset($_POST['submit']))
{
    $err = [];

	if(!preg_match("/^(8|\+7)[\d]{10}$/",$_POST['phone']))
		{
			echo("0");
			exit();
		}

    // проверям логин
    if(!preg_match("/^[a-zA-Z0-9]+$/",$_POST['username']))
    {
        echo("0");
		exit();
    }

    if(strlen($_POST['username']) < 6)
    {
        echo("0");
		exit();
    }
	// проверяем пароль
	if(!preg_match("/^[a-zA-Z0-9]+$/",$_POST['password']))
    {
        echo("0");
		exit();
    }

    if(strlen($_POST['password']) < 6)
    {
        echo("0");
		exit();
    }
	// проверяем почту
	if(!preg_match("/^([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9_-]+)$/", $_POST['mail']))
    {
        echo("0");
		exit();
    }
	
    // проверяем, не сущестует ли пользователя с таким именем
    $query = mysqli_query($link, "SELECT id FROM users WHERE username='".mysqli_real_escape_string($link, $_POST['username'])."'");
    if(mysqli_num_rows($query) > 0)
    {
		echo("0");
		exit();
    }

    $username = $_POST['username'];

    // Убераем лишние пробелы и делаем двойное хеширование
    $password = md5($_POST['password']);

    mysqli_query($link,"INSERT INTO users SET username='".$username."', password='".$password."', email='".$_POST['mail']."', phone='". $_POST['phone'] ."', registrationDate=SYSDATE()");
    echo("1");
}
?>