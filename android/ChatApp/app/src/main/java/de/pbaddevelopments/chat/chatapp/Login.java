package de.pbaddevelopments.chat.chatapp;

import android.app.ProgressDialog;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.AsyncTask;
import android.preference.PreferenceManager;
import android.support.v7.app.ActionBarActivity;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

import org.apache.http.NameValuePair;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONException;
import org.json.JSONObject;

import java.util.ArrayList;
import java.util.List;


public class Login extends ActionBarActivity implements View.OnClickListener {
    private EditText etUsername, etPassword;
    private Button btnSubmitLogin;
    private TextView tvRegisterLink;

    // php script location
    // localhost:
    private static final String LOGIN_URL = "http://192.168.178.76:80/chat/webservice/login.php";

    private static final String TAG_SUCCESS = "success";
    private static final String TAG_MESSAGE = "message";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.login);

        // setup input fields
        etUsername = (EditText) findViewById(R.id.login_et_username);
        etPassword = (EditText) findViewById(R.id.login_et_password);

        // setup button
        btnSubmitLogin = (Button) findViewById(R.id.login_btn_submit_login);
        btnSubmitLogin.setOnClickListener(this);

        // setup link
        tvRegisterLink = (TextView) findViewById(R.id.login_tv_link_register);
        tvRegisterLink.setOnClickListener(this);
    }

    @Override
    public void onClick(View v) {
        switch (v.getId()) {
            case R.id.login_btn_submit_login:
                new AttemptLogin().execute();
                break;

            case R.id.login_tv_link_register:
                Intent intent = new Intent(this, Register.class);
                startActivity(intent);
                break;

            default:
                break;
        }
    }

    private class AttemptLogin extends AsyncTask<String, String, String> {
        // Progress Dialog
        private ProgressDialog progressDialog;

        @Override
        protected void onPreExecute() {
            super.onPreExecute();

            progressDialog = new ProgressDialog(Login.this);
            progressDialog.setMessage(getString(R.string.login_progress_dialog));
            progressDialog.setIndeterminate(false);
            progressDialog.setCancelable(false);
            progressDialog.show();
        }

        @Override
        protected String doInBackground(String... params) {
            String username = etUsername.getText().toString();
            String password = etPassword.getText().toString();

            List<NameValuePair> postParams = new ArrayList<NameValuePair>();
            postParams.add(new BasicNameValuePair("username", username));
            postParams.add(new BasicNameValuePair("password", password));

            Log.d("request!", "starting");
            // getting json data by making a http request
            JSONObject jsonObject = JSONParser.makeHttpRequest(LOGIN_URL, "POST", postParams);

            // check your log for json response
            Log.d("Login attempt", jsonObject.toString());

            // json success tag
            int success = 0;
            try {
                success = jsonObject.getInt(TAG_SUCCESS);
                if(success == 1) {
                    Log.d("Login Successful!", jsonObject.toString());

                    // save user data
                    SharedPreferences sp = PreferenceManager.getDefaultSharedPreferences(Login.this);
                    SharedPreferences.Editor edit = sp.edit();
                    edit.putString("active_username", username);
                    edit.putString("password", password);
                    edit.commit();

                    Intent intent = new Intent(Login.this, ListChats.class);
                    finish();
                    startActivity(intent);
                    return jsonObject.getString(TAG_MESSAGE);
                }
                else {
                    Log.d("Login Failure!", jsonObject.getString(TAG_MESSAGE));
                    return jsonObject.getString(TAG_MESSAGE);
                }

            } catch (JSONException e) {
                e.printStackTrace();
            }

            return null;
        }

        @Override
        protected void onPostExecute(String message) {
            super.onPostExecute(message);

            progressDialog.dismiss();
            if(message != null) {
                Toast.makeText(Login.this, message, Toast.LENGTH_LONG).show();
            }
        }
    }
}
