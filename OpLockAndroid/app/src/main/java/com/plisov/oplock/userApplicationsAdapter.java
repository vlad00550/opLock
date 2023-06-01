package com.plisov.oplock;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.ImageView;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import java.util.List;


public class userApplicationsAdapter extends RecyclerView.Adapter<userApplicationsAdapter.ViewHolder> {
    private LayoutInflater inflater;
    private List<application> apps;
    private Context context;

    private OnItemClickListener mListener;

    userApplicationsAdapter(Context context, List<application> apps, OnItemClickListener listener){
        this.apps = apps;
        this.inflater = LayoutInflater.from(context);
        this.context = context;

        mListener = listener;
    }

    @NonNull
    @Override
    public ViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = inflater.inflate(R.layout.list_application, parent, false);
        return new ViewHolder(view, mListener);
    }

    @Override
    public void onBindViewHolder(@NonNull ViewHolder holder, int position) {
        application app = apps.get(position);
        holder.adress.setText(app.getAdress());
        switch (app.getTime()){
            case 1:  holder.date.setText(app.getDate() + " 10:00-14:00");
                break;
            case 2:  holder.date.setText(app.getDate() + " 14:00-18:00");
                break;
        }
        switch (app.getStatus()){
            case 0:  holder.imageView.setImageResource(R.drawable.status0);
                break;
            case 1:  holder.imageView.setImageResource(R.drawable.status1);
                break;
            case 2:  holder.imageView.setImageResource(R.drawable.status2);
                break;
            case 3:  holder.imageView.setImageResource(R.drawable.status3);
                break;
            case 4:  holder.imageView.setImageResource(R.drawable.status4);
                break;
        }

    }
    @Override
    public int getItemCount() {
        return apps.size();
    }

    public static class ViewHolder extends RecyclerView.ViewHolder{
        TextView adress, date;
        ImageView imageView;
        public ViewHolder(@NonNull View view, final OnItemClickListener listener) {
            super(view);

            adress = view.findViewById(R.id.adress);
            date = view.findViewById(R.id.date);
            imageView = view.findViewById(R.id.imageView);

            view.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    int position = getAdapterPosition();
                    if (position != RecyclerView.NO_POSITION) {
                        listener.onItemClick(position, v.getContext());
                    }
                }
            });
        }
    }
}
