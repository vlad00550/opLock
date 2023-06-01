package com.plisov.oplock;

import androidx.appcompat.app.AppCompatActivity;

import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.view.View;
import android.widget.TextView;
import android.widget.Toast;

import com.yandex.mapkit.Animation;
import com.yandex.mapkit.MapKitFactory;
import com.yandex.mapkit.geometry.Point;
import com.yandex.mapkit.map.CameraPosition;
import com.yandex.mapkit.mapview.MapView;

public class appViewActivity extends AppCompatActivity {
    private application app;
    private TextView textAdress, textTime, textStatus, textPhone, textDop;
    private MapView mapview;
    private SharedPreferences settings;
    private boolean client;

    androidx.appcompat.widget.AppCompatButton doneButton, cancelButton, takeButton, complainButton;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        MapKitFactory.initialize(this);
        settings = getSharedPreferences("cookie", MODE_PRIVATE);
        Bundle arguments = getIntent().getExtras();


        if(arguments!=null){
            app = (application) arguments.getSerializable(application.class.getSimpleName());
        }
        setContentView(R.layout.activity_app_view);

        mapview = findViewById(R.id.mapview);
        mapview.getMap().move(
                new CameraPosition(new Point(app.getLatitude(), app.getLongitude()), 16, 0.0f, 0.0f),
                new Animation(Animation.Type.SMOOTH, 1),
                null);
        mapview.getMap().getMapObjects().addPlacemark(new Point(app.getLatitude(), app.getLongitude()));

        doneButton = findViewById(R.id.doneButton);
        cancelButton = findViewById(R.id.cancelButton);
        takeButton = findViewById(R.id.takeButton);
        complainButton = findViewById(R.id.complainButton);

        client = (boolean) arguments.getSerializable("client");
        if(client){
            doneButton.setVisibility(View.INVISIBLE);
            takeButton.setVisibility(View.INVISIBLE);
        }else{
            cancelButton.setVisibility(View.INVISIBLE);
        }

        if(app.getStatus() != 3) {
            complainButton.setEnabled(false);
            complainButton.setTextColor(getResources().getColor(R.color.dark_grey));
        }
        if(app.getStatus() != 1) {
            takeButton.setEnabled(false);
            takeButton.setTextColor(getResources().getColor(R.color.dark_grey));
        }
        if(app.getStatus() != 2) {
            doneButton.setEnabled(false);
            doneButton.setTextColor(getResources().getColor(R.color.dark_grey));
        }
        if(app.getStatus() != 0 && app.getStatus() != 1) {
            cancelButton.setEnabled(false);
            cancelButton.setTextColor(getResources().getColor(R.color.dark_grey));
        }

        textAdress = findViewById(R.id.textAdress);
        textTime = findViewById(R.id.textTime);
        textStatus = findViewById(R.id.textStatus);
        textPhone = findViewById(R.id.textPhone);
        textDop = findViewById(R.id.textDop);

        textAdress.setText(textAdress.getText() + " " + app.getAdress());
        switch (app.getTime()){
            case 1:  textTime.setText(textTime.getText() + " " + app.getDate() + " 10:00-14:00");
                break;
            case 2:  textTime.setText(textTime.getText() + " " + app.getDate() + " 14:00-18:00");
                break;
        }
        switch (app.getStatus()){
            case 0:  textStatus.setText(textStatus.getText() + " Подано");
                break;
            case 1:  textStatus.setText(textStatus.getText() + " Одобрено");
                break;
            case 2:  textStatus.setText(textStatus.getText() + " Взято");
                break;
            case 3:  textStatus.setText(textStatus.getText() + " Выполнено");
                break;
            case 4:  textStatus.setText(textStatus.getText() + " Отклонено");
                break;
        }
        textPhone.setText(textPhone.getText() + " " + app.getPhone());
        textDop.setText(textDop.getText() + " " + app.getDopinfo());
    }

    @Override
    protected void onStart() {
        super.onStart();
        MapKitFactory.getInstance().onStart();
        mapview.onStart();
    }

    @Override
    protected void onStop() {
        mapview.onStop();
        MapKitFactory.getInstance().onStop();
        super.onStop();
    }

    public void exit(View v){
        Intent intent = new Intent(this, userApplications.class);
        startActivity(intent);
        finish();
    }

    public void cancel(View v){
        cancelRequest req = new cancelRequest();
        req.setContext(this);
        req.setCookie(settings.getString("cookie", ""));
        req.setId(app.getId());
        req.start();
        try {
            req.join();
        } catch (InterruptedException e) {
            throw new RuntimeException(e);
        }
        if(req.getRes().equals("1")){
            Toast.makeText(this, "Заявка отменена", Toast.LENGTH_LONG).show();
            Intent intent = new Intent(this, userApplications.class);
            startActivity(intent);
            finish();
        }else{
            Toast.makeText(this, "Ошибка отмены!", Toast.LENGTH_LONG).show();
        }
    }
    public void done(View v){
        doneRequest req = new doneRequest();
        req.setContext(this);
        req.setCookie(settings.getString("cookie", ""));
        req.setId(app.getId());
        req.start();
        try {
            req.join();
        } catch (InterruptedException e) {
            throw new RuntimeException(e);
        }
        if(req.getRes().equals("1")){
            Toast.makeText(this, "Заявка отмечена как выполненная", Toast.LENGTH_LONG).show();
            Intent intent = new Intent(this, userApplications.class);
            startActivity(intent);
            finish();
        }else{
            Toast.makeText(this, "Ошибка!", Toast.LENGTH_LONG).show();
        }
    }
    public void take(View v){
        takeRequest req = new takeRequest();
        req.setContext(this);
        req.setCookie(settings.getString("cookie", ""));
        req.setId(app.getId());
        req.start();
        try {
            req.join();
        } catch (InterruptedException e) {
            throw new RuntimeException(e);
        }
        if(req.getRes().equals("1")){
            Toast.makeText(this, "Заявка взята", Toast.LENGTH_LONG).show();
            Intent intent = new Intent(this, userApplications.class);
            startActivity(intent);
            finish();
        }else{
            Toast.makeText(this, "Ошибка!", Toast.LENGTH_LONG).show();
        }
    }
    public void complain(View v){
        Intent intent = new Intent(this, complaintsAcivity.class);
        intent.putExtra(application.class.getSimpleName(), app);
        intent.putExtra("client", client);
        startActivity(intent);
        finish();
    }
}