package com.plisov.oplock;

import androidx.appcompat.app.AppCompatActivity;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.os.Handler;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ImageView;
import android.widget.Toast;

import com.yandex.mapkit.MapKitFactory;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.Timer;
import java.util.TimerTask;

public class userApplications extends AppCompatActivity {
    private RecyclerView recView;
    private ArrayList<application> apps = new ArrayList<>();
    private SharedPreferences settings;

    private ImageView approvedApps, myApps;
    private boolean my = true;
    private boolean client;

    private androidx.appcompat.widget.AppCompatButton approvedButton, myButton, applyButton;

    private Handler mHandler = new Handler();
    private Runnable mRunnable;
    static boolean mapset = false;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        if(!mapset){
            MapKitFactory.setApiKey("b87b49f8-4ea0-40d3-87a8-fbbcda96ed68");
            mapset = true;
        }
        setContentView(R.layout.activity_user_applications);

        approvedApps = findViewById(R.id.approved);
        myApps = findViewById(R.id.my);
        approvedButton = findViewById(R.id.approvedButton);
        myButton = findViewById(R.id.myButton);
        applyButton = findViewById(R.id.applyButton);


        settings = getSharedPreferences("cookie", MODE_PRIVATE);
        setData(my);
        userApplicationsAdapter adapter = new userApplicationsAdapter(this, apps, new OnItemClickListener() {
            @Override
            public void onItemClick(int position, Context context) {
                Intent intent = new Intent(context, appViewActivity.class);
                intent.putExtra(application.class.getSimpleName(), apps.get(position));
                intent.putExtra("client", client);
                startActivity(intent);
                finish();
                //Toast.makeText(context, "norm", Toast.LENGTH_LONG).show();
            }

        });
        recView = findViewById(R.id.recView);
        recView.setLayoutManager(new LinearLayoutManager(this));
        recView.setAdapter(adapter);

        Context context = this;
        mRunnable = new Runnable() {
            @Override
            public void run() {
                setData(my);
                recView.getAdapter().notifyDataSetChanged();
                mHandler.postDelayed(mRunnable, 5000); // Повторное выполнение мRunnable через 5 секунд
            }
        };
        mHandler.postDelayed(mRunnable, 0);
    }
    // Запуск mRunnable при возобновлении работы Activity
    @Override
    protected void onResume() {
        super.onResume();
        mHandler.postDelayed(mRunnable, 0);
    }

    // Остановка mRunnable при приостановке работы Activity
    @Override
    protected void onPause() {
        super.onPause();
        mHandler.removeCallbacks(mRunnable);
    }

    private void setData(boolean my){

        applicationsRequest request = new applicationsRequest();
        request.setCookie(settings.getString("cookie", ""));
        request.setMy(my);
        request.setContext(this);
        request.start();
        try {
            request.join();
        } catch (InterruptedException e) {
            throw new RuntimeException(e);
        }
        if(request.getRes().equals("0")){
            //Toast.makeText(this, "Ошибка входа!", Toast.LENGTH_LONG).show();
            settings.edit().remove("cookie").apply();
            Intent intent = new Intent(this, MainActivity.class);
            startActivity(intent);
            finish();
        }else{
            //Toast.makeText(this, request.getRes(), Toast.LENGTH_LONG).show();
            try {
                JSONArray jsonarray = new JSONArray(request.getRes());
                JSONObject json = new JSONObject();

                if(jsonarray.getString(0).equals("0")){
                    client = true;
                    setClient();
                }else{
                    client = false;
                    setMaster(my);
                }
                apps.clear();
                for(int i = 1;i < jsonarray.length();i++){
                    json = new JSONObject(jsonarray.getString(i));
                    apps.add(new application(json.getInt("id")
                            , json.getString("adress")
                            , json.getString("date")
                            , json.getInt("status")
                            , json.getInt("time")
                            , json.getDouble("latitude")
                            , json.getDouble("longitude")
                            , json.getString("dopinfo")
                            , json.getString("phone")));
                }
                //Toast.makeText(this, jsonarray.getString(3), Toast.LENGTH_LONG).show();
            } catch (JSONException e) {
                throw new RuntimeException(e);
            }
        }
    }

    public void setMaster(boolean my){
        if(my){
            approvedApps.setVisibility(View.INVISIBLE);
            myButton.setVisibility(View.INVISIBLE);
            applyButton.setVisibility(View.INVISIBLE);

            myApps.setVisibility(View.VISIBLE);
            approvedButton.setVisibility(View.VISIBLE);
        }else{
            approvedApps.setVisibility(View.VISIBLE);
            applyButton.setVisibility(View.INVISIBLE);
            approvedButton.setVisibility(View.INVISIBLE);

            myApps.setVisibility(View.INVISIBLE);
            myButton.setVisibility(View.VISIBLE);
        }
    }

    public void setClient(){
        approvedApps.setVisibility(View.INVISIBLE);
        myApps.setVisibility(View.INVISIBLE);
        approvedButton.setVisibility(View.INVISIBLE);
        myButton.setVisibility(View.INVISIBLE);

        applyButton.setVisibility(View.VISIBLE);
    }

    public void Logout(View v){
        settings.edit().remove("cookie").apply();
        Intent intent = new Intent(this, MainActivity.class);
        startActivity(intent);
        finish();
    }
    public void approvedApplications(View v){
        my = false;
        setMaster(my);
        setData(my);
        recView.getAdapter().notifyDataSetChanged();
    }
    public void myApplications(View v){
        my = true;
        setMaster(my);
        setData(my);
        recView.getAdapter().notifyDataSetChanged();
    }
    public void makeApplication(View v){
        Intent intent = new Intent(this, applyActivity.class);
        startActivity(intent);
        finish();
    }
}