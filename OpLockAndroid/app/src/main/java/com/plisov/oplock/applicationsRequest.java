package com.plisov.oplock;

import android.content.Context;

import java.io.ByteArrayOutputStream;
import java.io.InputStream;
import java.io.OutputStream;
import java.net.HttpURLConnection;
import java.net.URL;

public class applicationsRequest extends Thread{
    private String cookie = "";
    private String res;
    private boolean my;
    private Context context;

    public void setContext(Context context) { this.context = context; }

    public void setMy(boolean my) {
        this.my = my;
    }

    public void setCookie(String cookie) {
        this.cookie = cookie;
    }

    public String getRes() {
        return res;
    }

    public void run() {
        String ip = context.getResources().getString(R.string.ip);

        String myURL = "http://" + ip + "/virthosts/oplock/mobile/getApplications.php";
        String parameters = "my=" + (my?"1":"0");
        byte[] data = null;
        InputStream is = null;

        try {
            URL url = new URL(myURL);
            HttpURLConnection conn = (HttpURLConnection) url.openConnection();
            conn.setRequestMethod("POST");
            conn.setDoOutput(true);
            conn.setDoInput(true);

            conn.setRequestProperty("Cookie", cookie);
            conn.setRequestProperty("Content-Length", "" + Integer.toString(parameters.getBytes().length));
            OutputStream os = conn.getOutputStream();
            data = parameters.getBytes("UTF-8");
            os.write(data);
            data = null;

            conn.connect();
            int responseCode = conn.getResponseCode();

            ByteArrayOutputStream baos = new ByteArrayOutputStream();
            is = conn.getInputStream();

            byte[] buffer = new byte[8192]; // Такого вот размера буфер
            // Далее, например, вот так читаем ответ
            int bytesRead;
            while ((bytesRead = is.read(buffer)) != -1) {
                baos.write(buffer, 0, bytesRead);
            }
            //cookie = conn.getHeaderField("Set-Cookie").split(";")[0];
            data = baos.toByteArray();

            // Сохраняем cookie авторизации
            //cookie += conn.getHeaderFields().get("Set-Cookie").get(0).split(";")[0] += "; ";
            //cookie += conn.getHeaderFields().get("Set-Cookie").get(1).split(";")[0];

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
