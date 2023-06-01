package com.plisov.oplock;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;

import android.content.Intent;
import android.content.SharedPreferences;
import android.location.Address;
import android.location.Geocoder;
import android.os.Bundle;
import android.view.View;
import android.widget.EditText;
import android.widget.RadioButton;
import android.widget.Toast;

import com.yandex.mapkit.Animation;
import com.yandex.mapkit.GeoObject;
import com.yandex.mapkit.MapKitFactory;
import com.yandex.mapkit.geometry.Point;
import com.yandex.mapkit.layers.GeoObjectTapEvent;
import com.yandex.mapkit.layers.GeoObjectTapListener;
import com.yandex.mapkit.map.CameraPosition;
import com.yandex.mapkit.map.GeoObjectSelectionMetadata;
import com.yandex.mapkit.map.InputListener;
import com.yandex.mapkit.map.Map;
import com.yandex.mapkit.mapview.MapView;

import java.io.IOException;
import java.util.List;
import java.util.Locale;

public class applyActivity extends AppCompatActivity implements GeoObjectTapListener, InputListener {

    private RadioButton radioButton1, radioButton2;
    private EditText editText;
    private SharedPreferences settings;
    private MapView mapview;
    private String adress = "";
    private double latitude;
    private double longitude;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        MapKitFactory.initialize(this);
        setContentView(R.layout.activity_apply);

        settings = getSharedPreferences("cookie", MODE_PRIVATE);

        radioButton1 = findViewById(R.id.radioButton1);
        radioButton2 = findViewById(R.id.radioButton2);

        editText = findViewById(R.id.dopinfoLine);

        mapview = findViewById(R.id.mapview);
        mapview.getMap().move(
                new CameraPosition(new Point(56.010569, 92.852572), 10, 0.0f, 0.0f),
                new Animation(Animation.Type.SMOOTH, 1),
                null);
        mapview.getMap().addTapListener(this);
        mapview.getMap().addInputListener(this);
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

    public void apply(View v){
        if(adress.isEmpty()) {
            Toast.makeText(this, "Выберите место на карте!", Toast.LENGTH_LONG).show();
            return;
        }
        applyRequest request = new applyRequest();
        request.setCookie(settings.getString("cookie", ""));
        request.setContext(this);
        request.setAdress(adress);
        request.setLatitude(latitude);
        request.setLongitude(longitude);
        request.setTime(radioButton1.isChecked()?1:2);
        request.setDopinfo(editText.getText().toString());

        request.start();
        try {
            request.join();
        } catch (InterruptedException e) {
            throw new RuntimeException(e);
        }
        if(request.getRes().equals("0")){
            Toast.makeText(this, "Ошибка подачи заявки", Toast.LENGTH_LONG).show();
        }else{
            Toast.makeText(this, "Заявка подана", Toast.LENGTH_LONG).show();
        }

        Intent intent = new Intent(this, userApplications.class);
        startActivity(intent);
        finish();
    }

    public void back(View v){
        Intent intent = new Intent(this, userApplications.class);
        startActivity(intent);
        finish();
    }

    @Override
    public boolean onObjectTap(@NonNull GeoObjectTapEvent geoObjectTapEvent) {
        final GeoObjectSelectionMetadata selectionMetadata = geoObjectTapEvent
                .getGeoObject()
                .getMetadataContainer()
                .getItem(GeoObjectSelectionMetadata.class);

        final GeoObject geoObject = geoObjectTapEvent.getGeoObject();
        Point point = new Point(geoObject.getGeometry().get(0).getPoint().getLatitude(),
                geoObject.getGeometry().get(0).getPoint().getLongitude());

        Geocoder geocoder = new Geocoder(this, Locale.getDefault());
        try {
            List<Address> addresses = geocoder.getFromLocation(point.getLatitude(), point.getLongitude(), 1);
            if (addresses != null) {
                Address returnedAddress = addresses.get(0);
                adress = returnedAddress.getAddressLine(0);

                latitude = point.getLatitude();
                longitude = point.getLongitude();
            }
        } catch (IOException e) {
            e.printStackTrace();
        }

        if (selectionMetadata != null) {
            mapview.getMap().selectGeoObject(selectionMetadata.getId(), selectionMetadata.getLayerId());
        }

        return selectionMetadata != null;
    }

    @Override
    public void onMapTap(@NonNull Map map, @NonNull Point point) {
        mapview.getMap().deselectGeoObject();
    }

    @Override
    public void onMapLongTap(@NonNull Map map, @NonNull Point point) {

    }
}