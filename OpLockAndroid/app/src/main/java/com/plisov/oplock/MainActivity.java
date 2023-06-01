package com.plisov.oplock;

import androidx.appcompat.app.AppCompatActivity;

import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.view.View;
import android.widget.EditText;
import android.widget.Toast;


public class MainActivity extends AppCompatActivity {

    private EditText username, password;
    private String cookie;
    private SharedPreferences settings;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        super.overridePendingTransition(0, 0);
        settings = getSharedPreferences("cookie", MODE_PRIVATE);

        username = findViewById(R.id.username);
        password = findViewById(R.id.Password);

        if(settings.contains("cookie")){
            Intent intent = new Intent(this, userApplications.class);
            startActivity(intent);
            finish();
        }
    }

    public void RegisterActivity(View v){
        Intent intent = new Intent(this, Registration.class);
        startActivity(intent);
        finish();
    }

    public void Entry(View v){
        loginRequest log = new loginRequest();
        log.setUsername(username.getText().toString());
        log.setPassword(password.getText().toString());
        log.setContext(this);
        log.start();
        try {
            log.join();
        } catch (InterruptedException e) {
            throw new RuntimeException(e);
        }
        if(log.getRes().equals("1")){
            //Toast.makeText(this, "гуд!", Toast.LENGTH_LONG).show();
            cookie = log.getCookie();
            settings.edit().putString("cookie", cookie).apply();
            //Toast.makeText(this, cookie, Toast.LENGTH_LONG).show();
            Intent intent = new Intent(this, userApplications.class);
            startActivity(intent);
            finish();
        }else{
            Toast.makeText(this, "Неверный логин или пароль!", Toast.LENGTH_LONG).show();
        }
    }
}