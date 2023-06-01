package com.plisov.oplock;

import java.io.Serializable;

public class application implements Serializable {
    private String adress, date, dopinfo, phone;
    private int id, status, time;
    private double latitude, longitude;

    public application(int id, String adress, String date, int status, int time, double latitude, double longitude, String dopinfo, String phone){
        this.id = id;
        this.adress = adress;
        this.date = date;
        this.status = status;
        this.time = time;
        this.latitude = latitude;
        this.longitude = longitude;
        this.dopinfo = dopinfo;
        this.phone = phone;
    }

    public void setId(int id) { this.id = id; }
    public int getId() { return id; }
    public String getAdress(){ return adress; }
    public String getDate(){
        return date;
    }
    public int getStatus(){
        return status;
    }
    public double getLatitude() { return latitude; }
    public double getLongitude() { return longitude; }
    public int getTime(){
        return time;
    }

    public String getDopinfo() { return dopinfo; }

    public String getPhone() { return phone; }

    public void setAdress(String adress){
        this.adress = adress;
    }
    public void setDate(String date){
        this.date = date;
    }
    public void setStatus(int status){
        this.status = status;
    }
    public void setTime(int time){
        this.time = time;
    }
    public void setPhone(String phone) { this.phone = phone; }
    public void setDopinfo(String dopinfo) { this.dopinfo = dopinfo; }
    public void setLatitude(float latitude) { this.latitude = latitude; }
    public void setLongitude(float longitude) { this.longitude = longitude; }
}
