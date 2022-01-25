package com.smartmindx.apportunity;





import com.smartmindx.apportunity.R;

import android.R.string;
import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.telephony.TelephonyManager;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;

public class actv2 extends Activity{
	  /** Called when the activity is first created. */
	String comment;
	String post;
	Button button_post;
	String id;
	 ProgressDialog dialog ;
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity2);
        post=((Button)findViewById(R.id.p1)).toString();
        button_post=((Button)findViewById(R.id.p1));
        comment=((EditText)findViewById(R.id.text1)).getText().toString();
        
		 
		 button_post.setOnClickListener(new View.OnClickListener() {
	        	
				
				public void onClick(View view) {
					// TODO Auto-generated method stub
					
					
					
					
					comment=((EditText)findViewById(R.id.text1)).getText().toString();
					
					 Intent myIntent = new Intent(view.getContext(), connectActivity.class);
					 Bundle sent=new Bundle();  //Create a bundle
					 sent.putString("comment", comment);  //Put the Strings in the Bundle
					 sent.putString("id", "abc");
					 sent.putString("status", "comp");
					 
					Log.i("hi", "hello");
					// finish(); //kill previous activity
					 myIntent.putExtras(sent);  //Atatch the intent to Activty
					 startActivity(myIntent); //Start activty
				}
			});
    }
}