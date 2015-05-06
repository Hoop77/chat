package de.pbaddevelopments.chat.chatapp;

import android.app.ProgressDialog;
import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.support.v7.app.ActionBarActivity;
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

/**
 * Created by Philipp on 04.01.2015.
 */
public class Register extends ActionBarActivity implements View.OnClickListener {

    private EditText etUsername, etPassword;
    private Button btnSubmitRegister;
    private TextView tvLoginLink;

    // php script location
    // localhost:
    private static final String LOGIN_URL = "http://192.168.178.76:80/chat/webservice/register.php";

    private static final String TAG_SUCCESS = "success";
    private static final String TAG_MESSAGE = "message";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.register);

        // setup input fields
        etUsername = (EditText) findViewById(R.id.register_et_username);
        etPassword = (EditText) findViewById(R.id.register_et_password);

        // setup button
        btnSubmitRegister = (Button) findViewById(R.id.register_btn_submit_register);
        btnSubmitRegister.setOnClickListener(this);

        // setup link
        tvLoginLink = (TextView) findViewById(R.id.register_tv_link_login);
        tvLoginLink.setOnClickListener(this);
    }

    @Override
    public void onClick(View v) {
        switch (v.getId()) {
            case R.id.register_btn_submit_register:
                new AttemptRegister().execute();
                break;

            case R.id.register_tv_link_login:
                Intent intent = new Intent(this, Login.class);
                startActivity(intent);
                break;

            default:
                break;
        }
    }

    private class AttemptRegister extends AsyncTask<String, String, String> {
        // Progress Dialog
        private ProgressDialog progressDialog;

        @Override
        protected void onPreExecute() {
            super.onPreExecute();

            progressDialog = new ProgressDialog(Register.this);
            progressDialog.setMessage("Attempting register...");
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

            Log.d("request starting!", "starting");
            JSONObject jsonObject = JSONParser.makeHttpRequest(LOGIN_URL, "POST", postParams);

            Log.d("Login attempt", jsonObject.toString());

            int success = 0;
            try {
                success = jsonObject.getInt(TAG_SUCCESS);
                if(success == 1) {
                    Log.d("Register successful!", jsonObject.toString());
                    Intent intent = new Intent(Register.this, Login.class);
                    finish();
                    startActivity(intent);
                    return jsonObject.getString(TAG_MESSAGE);
                }
                else {
                    Log.d("Register failure!", jsonObject.toString());
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
                Toast.makeText(Register.this, message, Toast.LENGTH_LONG).show();
            }
        }
    }
}
