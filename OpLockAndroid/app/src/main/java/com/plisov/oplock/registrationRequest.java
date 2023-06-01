package com.plisov.oplock;

import android.content.Context;

import java.io.ByteArrayOutputStream;
import java.io.InputStream;
import java.io.OutputStream;
import java.net.HttpURLConnection;
import java.net.URL;

public class registrationRequest extends Thread {
    private String res;
    private String mail;
    private String username;
    private String password;
    private String phone;
    private Context context;

    public void setContext(Context context) { this.context = context; }

    public String getRes() {
        return res;
    }

    public void setPhone(String phone) {
        this.phone = phone;
    }

    public void setMail(String mail) {
        this.mail = mail;
    }

    public void setUsername(String username) {
        this.username = username;
    }

    public void setPassword(String password) {
        this.password = password;
    }

    public void run() {
        String ip = context.getResources().getString(R.string.ip);

        String myURL = "http://" + ip + "/virthosts/oplock/mobile/registration.php";
        String parameters = "username=" + username + "&password=" + password + "&mail=" + mail + "&phone=" + phone + "&submit=1";
        byte[] data = null;
        InputStream is = null;

        try {
            URL url = new URL(myURL);
            HttpURLConnection conn = (HttpURLConnection) url.openConnection();
            conn.setRequestMethod("POST");
            conn.setDoOutput(true);
            conn.setDoInput(true);

            conn.setRequestProperty("Content-Length", "" + Integer.toString(parameters.getBytes().length));
            OutputStream os = conn.getOutputStream();
            data = parameters.getBytes("UTF-8");
            os.write(data);
            data = null;

            conn.connect();
            int responseCode = conn.getResponseCode();

            if (responseCode != 200) {
                res = "Не удалось соединиться с сервером!";
                return;
            }

            ByteArrayOutputStream baos = new ByteArrayOutputStream();
            is = conn.getInputStream();

            byte[] buffer = new byte[8192]; // Такого вот размера буфер
            // Далее, читаем ответ
            int bytesRead;
            while ((bytesRead = is.read(buffer)) != -1) {
                baos.write(buffer, 0, bytesRead);
            }

            data = baos.toByteArray();

            res = new String(data, "UTF-8");
        } catch (Exception e) {
            res = e.toString();
        } finally {
            try {
                if (is != null)
                    is.close();
            } catch (Exception ex) {}
        }
    }
}
