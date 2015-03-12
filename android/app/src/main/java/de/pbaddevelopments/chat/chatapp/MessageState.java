package de.pbaddevelopments.chat.chatapp;

import android.graphics.Color;
import android.text.SpannableString;
import android.text.style.ForegroundColorSpan;

/**
 * Created by Philipp on 11.01.2015.
 */
public class MessageState {
    public static final int STATE_SENDING = 0;
    public static final int STATE_SERVER_RECEIVED = 1;
    public static final int STATE_CLIENT_RECEIVED = 2;
    public static final int STATE_CLIENT_WATCHED = 3;

    public static SpannableString getMessageStateSymbol(int state, String string) {
        SpannableString result;
        String symbol;

        switch (state) {
            case STATE_SENDING:
                symbol = "⏰" + string;
                result = new SpannableString(symbol);
                break;

            case STATE_SERVER_RECEIVED:
                symbol = "✓" + string;
                result = new SpannableString(symbol);
                break;

            case STATE_CLIENT_RECEIVED:
                symbol = "✓✓" + string;
                result = new SpannableString(symbol);
                break;

            case STATE_CLIENT_WATCHED:
                symbol = "✓✓" + string;
                result = new SpannableString(symbol);
                result.setSpan(new ForegroundColorSpan(Color.BLUE), 0, 1, 0);
                break;

            default:
                result = new SpannableString(string);
                break;

        }

        return result;
    }

}
