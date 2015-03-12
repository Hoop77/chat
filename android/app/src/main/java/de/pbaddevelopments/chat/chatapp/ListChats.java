package de.pbaddevelopments.chat.chatapp;

import android.app.Activity;
import android.app.ListActivity;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.SharedPreferences;
import android.os.AsyncTask;
import android.os.Bundle;
import android.preference.PreferenceManager;
import android.text.SpannableString;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.ImageView;
import android.widget.ListAdapter;
import android.widget.ListView;
import android.widget.SimpleAdapter;
import android.widget.TextView;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

/**
 * Created by Philipp on 05.01.2015.
 */
public class ListChats extends ListActivity {
    private ProgressDialog progressDialog;
    private static final String LOGIN_URL = "http://192.168.178.76:80/chat/webservice/list_chats.php";
    private String activeUsername, password;

    private List<HashMap<String, String>> chats = new ArrayList<>();

    private static final String TAG_SUCCESS = "success";
    private static final String TAG_MESSAGE = "message";
    private static final String TAG_CHATS = "chats";
    private static final String TAG_CHAT_ID = "chat_id";
    private static final String TAG_USERNAMES = "usernames";
    private static final String TAG_LAST_MESSAGE_INFO = "last_message_info";
    private static final String TAG_LAST_MESSAGE_EXISTS = "exists";
    private static final String TAG_LAST_MESSAGE_FROM_USERNAME = "from_username";
    private static final String TAG_LAST_MESSAGE = "message";
    private static final String TAG_LAST_MESSAGE_DATETIME = "datetime";
    private static final String TAG_LAST_MESSAGE_STATE = "state";

    private static final String TAG_CHAT_NAME = "chat_name";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.list_chats);

        SharedPreferences sp = PreferenceManager.getDefaultSharedPreferences(this);
        activeUsername = sp.getString("active_username", null);
        password = sp.getString("password", null);
    }

    @Override
    public void onResume() {
        super.onResume();
        new LoadChats().execute();
    }

    private void addChat() {

    }

    private void removeChat() {

    }

    private void updateJsonData() {
        List<NameValuePair> postParams = new ArrayList<>();
        postParams.add(new BasicNameValuePair("active_username", activeUsername));
        postParams.add(new BasicNameValuePair("password", password));

        JSONObject json = JSONParser.makeHttpRequest(LOGIN_URL, "POST", postParams);

        try {
            int success = json.getInt(TAG_SUCCESS);
            // success loading json
            if(success == 1) {
                Log.d("Json data successfully loaded", json.getString(TAG_MESSAGE));

                JSONArray jsonChats = json.getJSONArray(TAG_CHATS);

                for (int i = 0; i < jsonChats.length(); i++) {
                    JSONObject chat = jsonChats.getJSONObject(i);

                    String chatId = chat.getString(TAG_CHAT_ID);

                    JSONArray usernames = chat.getJSONArray(TAG_USERNAMES);
                    String sUsernames = "";
                    for (int j = 0; j < usernames.length(); j++) {
                        String username = usernames.getString(j);
                        if(!username.equals(activeUsername)) {
                            if(!sUsernames.equals(""))
                                sUsernames += ", ";
                            sUsernames += username;
                        }
                    }

                    String lastMessageFromUsername = "";
                    String lastMessage = "";
                    String lastMessageDatetime = "";
                    String lastMessageState = "";

                    JSONObject lastMessageInfo = chat.getJSONObject(TAG_LAST_MESSAGE_INFO);
                    int lastMessageExists = lastMessageInfo.getInt(TAG_LAST_MESSAGE_EXISTS);
                    if (lastMessageExists == 1) {
                        lastMessageFromUsername = lastMessageInfo.getString(TAG_LAST_MESSAGE_FROM_USERNAME);
                        lastMessage = lastMessageInfo.getString(TAG_LAST_MESSAGE);
                        lastMessageDatetime = DateTime.getFormatDateTime(
                                lastMessageInfo.getString(TAG_LAST_MESSAGE_DATETIME),
                                DateTime.HOURS_MINUTES);
                        lastMessageState = lastMessageInfo.getString(TAG_LAST_MESSAGE_STATE);
                    }

                    HashMap<String, String> map = new HashMap<>();

                    // TODO set the chatName-value according to some contact data from a local database
                    String chatName = sUsernames;

                    map.put(TAG_CHAT_ID, chatId);
                    map.put(TAG_CHAT_NAME, chatName);
                    map.put(TAG_LAST_MESSAGE_FROM_USERNAME, lastMessageFromUsername);
                    map.put(TAG_LAST_MESSAGE, lastMessage);
                    map.put(TAG_LAST_MESSAGE_DATETIME, lastMessageDatetime);
                    map.put(TAG_LAST_MESSAGE_STATE, lastMessageState);

                    chats.add(map);

                }
            }
            // failed loading json
            else {
                Log.d("Failed loading json data", json.getString(TAG_MESSAGE));
            }

        } catch (JSONException e) {
            e.printStackTrace();
        }
    }

    private void updateList() {
        ListAdapter adapter = new ChatListAdapter(this, R.layout.chat_item, chats);
        setListAdapter(adapter);

        ListView lv = getListView();
        lv.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> parent, View view, int position, long id) {

            }
        });
    }

    private class LoadChats extends AsyncTask<Void, Void, Boolean> {
        private ProgressDialog progressDialog;

        @Override
        protected void onPreExecute() {
            super.onPreExecute();

            progressDialog = new ProgressDialog(ListChats.this);
            progressDialog.setMessage("Loading chats...");
            progressDialog.setIndeterminate(false);
            progressDialog.setCancelable(false);
            progressDialog.show();
        }

        @Override
        protected Boolean doInBackground(Void... params) {
            updateJsonData();
            return null;
        }

        @Override
        protected void onPostExecute(Boolean result) {
            super.onPostExecute(result);
            progressDialog.dismiss();
            updateList();
        }
    }

    private class ChatListAdapter extends ArrayAdapter {
        private final Context context;

        private ChatListAdapter(Context context, int resource, List<HashMap<String, String>> values) {
            super(context, resource, values);

            this.context = context;
        }

        @Override
        public View getView(int position, View convertView, ViewGroup parent) {
            LayoutInflater inflater = (LayoutInflater) context
                                .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
            View chatItemView = inflater.inflate(R.layout.chat_item, parent, false);

            // handling icon TODO
            ImageView ivIcon = (ImageView) chatItemView.findViewById(R.id.chat_item_iv_icon);

            // handling chat name
            TextView tvChatName = (TextView) chatItemView.findViewById(R.id.chat_item_tv_chat_name);
            String sChatName = chats.get(position).get(TAG_CHAT_NAME);
            tvChatName.setText(sChatName);

            // handling datetime
            TextView tvDateTime = (TextView) chatItemView.findViewById(R.id.chat_item_tv_datetime);
            String sDateTime = chats.get(position).get(TAG_LAST_MESSAGE_DATETIME);
            tvDateTime.setText(sDateTime);

            // handling last message
            TextView tvLastMessage = (TextView) chatItemView.findViewById(R.id.chat_item_tv_last_message);

            String sLastMessage = chats.get(position).get(TAG_LAST_MESSAGE);
            if(!sLastMessage.equals("")) {

                SpannableString spanLastMessage;

                // if message from active user
                String sLastMessageFromUsername = chats.get(position).get(TAG_LAST_MESSAGE_FROM_USERNAME);
                if(sLastMessageFromUsername.equals(activeUsername)) {
                    sLastMessage = "▶ " + sLastMessage;
                    spanLastMessage = new SpannableString(sLastMessage);
                }
                // message not from active user
                else {
                    String sLastMessageState = chats.get(position).get(TAG_LAST_MESSAGE_STATE);
                    if(!sLastMessageState.equals("")) {
                        int lastMessageState = Integer.parseInt(sLastMessageState);
                        spanLastMessage = MessageState.getMessageStateSymbol(
                                lastMessageState,
                                sLastMessage + " ");
                    }
                    else {
                        spanLastMessage = new SpannableString(sLastMessage);
                    }
                }

                tvLastMessage.setText(spanLastMessage);
            }

            return chatItemView;
        }
    }
}
