package de.pbaddevelopments.chat.chatapp;

import java.util.Calendar;

/**
 * Created by Philipp on 11.01.2015.
 */
public class DateTime {
    public static final int HOURS_MINUTES = 0;

    public static String getCurrentLocalDateTime(int format) {
        String result;
        Calendar calendar = Calendar.getInstance();

        String year = String.valueOf(calendar.get(Calendar.YEAR));
        String month = String.valueOf(calendar.get(Calendar.MONTH));
        String day = String.valueOf(calendar.get(Calendar.DAY_OF_MONTH));

        String hours = String.valueOf(calendar.get(Calendar.HOUR_OF_DAY));
        String minutes = String.valueOf(calendar.get(Calendar.MINUTE));
        String seconds = String.valueOf(calendar.get(Calendar.SECOND));

        switch (format) {
            case HOURS_MINUTES:
                result = hours + ":" + minutes;
                break;

            default:
                result = year + "-" + month + "-" + day + " " + hours + ":" + minutes + ":" + seconds;
                break;
        }

        return result;
    }

    public static String getFormatDateTime(String dateTime, int format) {
        String result;

        String[] split = dateTime.split(" ");
        String[] date = split[0].split("-");
        String[] time = split[1].split(":");

        String year = date[0];
        String month = date[1];
        String day = date[2];

        String hours = time[0];
        String minutes = time[1];
        String seconds = time[2];

        switch (format) {
            case HOURS_MINUTES:
                result = hours + ":" + minutes;
                break;

            default:
                result = dateTime;
                break;
        }

        return result;
    }
}
