package com.smartmindx.apportunity;

import java.io.BufferedReader;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.util.ArrayList;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.NameValuePair;
import org.apache.http.client.HttpClient;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;




import android.app.Activity;
import android.content.Intent;
import android.database.Cursor;
import android.os.Bundle;
import android.util.Log;
import android.widget.LinearLayout;
import android.widget.TextView;
import android.widget.Toast;

/* The actual Class that Will Actual connect your App with the cloud*/
public class connectActivity extends Activity {
/** Called when the activity is first created. */
   String d=""; //name get from the cloud
   TextView txt; //Text view to show Connecting
   String comment=""; //username
   String id=""; //password
   String KEY_121=""; //Ip to connect
   String status;
@Override
public void onCreate(Bundle savedInstanceState) {
    super.onCreate(savedInstanceState);
    //setContentView(R.layout.main);
    // Create a crude view - this should really be set via the layout resources  
    // but since its an example saves declaring them in the XML.  
    LinearLayout rootLayout = new LinearLayout(getApplicationContext());  
    txt = new TextView(getApplicationContext());  
    rootLayout.addView(txt);  
    setContentView(rootLayout);  

    // Set the text and call the connect function.  
    txt.setText("Connecting..."); 
  //call the method to run the data retreival
    Bundle received=getIntent().getExtras(); 
    comment=received.getString("comment"); //get username
    id=received.getString("id"); //get password
    status=received.getString("status");
    if(status.equals("vote"))
    	KEY_121="http://smartmindx.com/jaagoPakistan/vote.php";
    else if(status.equals("comp"))
    	KEY_121="http://smartmindx.com/jaagoPakistan/comp.php";
    else 
    	KEY_121="http://smartmindx.com/jaagoPakistan/rec.php";
    	
   // KEY_121=received.getString("ip");
    
    
    txt.setText(getServerData(KEY_121));  //call function by passing the server adddress as argument



}

/* 
 * @param the value of the IP of the database
 * 
 * @return A string to show successfull result or failure of authentication
 * */


private String getServerData(String returnString) {
    
	InputStream is = null;
    
	   String result = "";
	    //the year data to send
	    ArrayList<NameValuePair> nameValuePairs = new ArrayList<NameValuePair>();
	    

	    
	    	
	            HttpClient httpclient = new DefaultHttpClient();
	            HttpPost httppost = new HttpPost(returnString);
	            
	
            	nameValuePairs.add(new BasicNameValuePair("id",id));
            	nameValuePairs.add(new BasicNameValuePair("comment",comment));
            	Log.i("variables",id+" "+comment);
     	  
try {
            	httppost.setEntity(new UrlEncodedFormEntity(nameValuePairs));
	            HttpResponse response = httpclient.execute(httppost);
	            HttpEntity entity = response.getEntity();
	            is = entity.getContent();
	            

}
catch (Exception e)
{
	   Log.i("Error in Server Call "+e.toString(),KEY_121);
}

              
          //convert response to string
            try{
            	Log.i("Website to connect ",KEY_121+" "+status);
                    BufferedReader reader = new BufferedReader(new InputStreamReader(is,"iso-8859-1"),8);
                    StringBuilder sb = new StringBuilder();
                    String line = null;
                    while ((line = reader.readLine()) != null) {
                            sb.append(line + "\n");
                    }
                    is.close();
                    result=sb.toString();
                    
                    Log.i("result",result);
            }catch(Exception e){
                    Log.e("log_tag", "Error converting result "+e.getMessage());
            }
            //parse json data
            if(result.contains("tan"))
            {
         	   
         	
         	 String a="df";
         	  finish();
         	  startActivity(new Intent(this,ratepp.class));
         	  
            	Toast.makeText(this, "Data Sent Successfully Visit http://smartmindx.com/jaagoPakistan/", Toast.LENGTH_LONG).show();
            }
            else 
            {
         	   finish();
          	  startActivity(new Intent(this,ratepp.class));
         	   if(status.equals("vote"))
          	  Toast.makeText(this, "You can Vote only once in a Week", Toast.LENGTH_LONG).show();
         	   else
               	  Toast.makeText(this, "Unable to Send Data try Again Later", Toast.LENGTH_LONG).show();

            }
          
            
            return "hi";
        }
        
    
  





@Override
public void onBackPressed() {
	// TODO Auto-generated method stub
	super.onBackPressed();
	startActivity(new Intent(this,actv3.class));
}    

}