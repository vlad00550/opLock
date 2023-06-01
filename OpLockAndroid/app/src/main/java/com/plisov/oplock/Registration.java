package com.plisov.oplock;

import androidx.appcompat.app.AppCompatActivity;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

import java.util.regex.Matcher;
import java.util.regex.Pattern;

public class Registration extends AppCompatActivity {

    TextView entry;
    private EditText mail, username,  password, password2, phone;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_register);
        super.overridePendingTransition(0, 0);

        mail = findViewById(R.id.mail);
        username = findViewById(R.id.username);
        password = findViewById(R.id.password);
        password2 = findViewById(R.id.password2);
        phone = findViewById(R.id.phone);
    }
    public void EntryActivity(View v){
        Intent intent = new Intent(this, MainActivity.class);
        startActivity(intent);
        finish();
    }

    private boolean check(){
        Pattern pattern = Pattern.compile("^[a-zA-Z0-9]+$");
        Pattern mailPattern = Pattern.compile("^([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\\.[a-zA-Z0-9_-]+)$");
        Pattern phonePattern = Pattern.compile("^(8|\\+7)\\d{10}$");
        Matcher matcher;

        matcher = phonePattern.matcher(phone.getText().toString());
        if(!matcher.find()){
            Toast.makeText(this, "Неверный формат номера телефона!", Toast.LENGTH_LONG).show();
            return false;
        }

        matcher = mailPattern.matcher(mail.getText().toString());
        if(!matcher.find()){
            Toast.makeText(this, "Неверный формат почты!", Toast.LENGTH_LONG).show();
            return false;
        }
        matcher = pattern.matcher(username.getText().toString());
        if(!matcher.find() | username.length() < 6){
            Toast.makeText(this, "Логин должен состоять из 6 или более символов английского алфавита или цифр!", Toast.LENGTH_LONG).show();
            return false;
        }
        matcher = pattern.matcher(password.getText().toString());
        if(!matcher.find() | password.length() < 6){
            Toast.makeText(this, "Пароль должен состоять из 6 или более символов английского алфавита или цифр!", Toast.LENGTH_LONG).show();
            return false;
        }
        if(!password.getText().toString().equals(password2.getText().toString())){
            Toast.makeText(this, "Пароли не совпадают!", Toast.LENGTH_LONG).show();
            return false;
        }
        return true;
    }

    public void Registration(View v){
        if(!check()) return;

        registrationRequest reg = new registrationRequest();
        reg.setUsername(username.getText().toString());
        reg.setPassword(password.getText().toString());
        reg.setMail(mail.getText().toString());
        reg.setPhone(phone.getText().toString());
        reg.setContext(this);
        reg.start();
        try {
            reg.join();
        } catch (InterruptedException e) {
            throw new RuntimeException(e);
        }
        if(reg.getRes().equals("1")){
            Toast.makeText(this, "Регистрация прошла успешно!", Toast.LENGTH_LONG).show();
            Intent intent = new Intent(this, MainActivity.class);
            startActivity(intent);
            finish();
        }else{
            Toast.makeText(this, "Ошибка регистрации!", Toast.LENGTH_LONG).show();
        }
    }

}