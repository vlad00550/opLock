package com.plisov.oplock;

import androidx.appcompat.app.AppCompatActivity;

import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.view.View;
import android.widget.EditText;
import android.widget.Toast;

public class complaintsAcivity extends AppCompatActivity {

    private application app;
    private SharedPreferences settings;
    private boolean client;

    private EditText complaintText;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        settings = getSharedPreferences("cookie", MODE_PRIVATE);
        Bundle arguments = getIntent().getExtras();
        if(arguments!=null){
            app = (application) arguments.getSerializable(application.class.getSimpleName());
            client = (boolean) arguments.getSerializable("client");
        }

        setContentView(R.layout.activity_complaints_acivity);
        complaintText = findViewById(R.id.complaint);
    }

    public void exit(View v){
        Intent intent = new Intent(this, appViewActivity.class);
        intent.putExtra(application.class.getSimpleName(), app);
        intent.putExtra("client", client);
        startActivity(intent);
        finish();
    }

    public void makeComplaint(View v){
        complaintRequest req = new complaintRequest();
        req.setContext(this);
        req.setCookie(settings.getString("cookie", ""));
        req.setId(app.getId());
        req.setText(complaintText.getText().toString());
        req.start();
        try {
            req.join();
        } catch (InterruptedException e) {
            throw new RuntimeException(e);
        }
        if(req.getRes().equals("1")){
            Toast.makeText(this, "Жалоба подана", Toast.LENGTH_LONG).show();
            Intent intent = new Intent(this, userApplications.class);
            startActivity(intent);
            finish();
        }else if(req.getRes().equals("2")){
            Toast.makeText(this, "Вы уже подали жалобу", Toast.LENGTH_LONG).show();
        }else{
            //Toast.makeText(this, "Ошибка подачи жалобы!", Toast.LENGTH_LONG).show();
            Toast.makeText(this, req.getRes(), Toast.LENGTH_LONG).show();
        }
    }
}