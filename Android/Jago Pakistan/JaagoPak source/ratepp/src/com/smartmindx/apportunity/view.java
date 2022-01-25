package com.smartmindx.apportunity;

import com.smartmindx.apportunity.R;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.telephony.TelephonyManager;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.RadioButton;
import android.widget.Toast;

public class view extends Activity{
	  /** Called when the activity is first created. */
	RadioButton r1,r2,r3,r4,r5;
	Button vote;
	String id;
	String val;
	 ProgressDialog dialog ;
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.view3);
         r1=(RadioButton)findViewById(R.id.r1);
         r2=(RadioButton)findViewById(R.id.r2);
         r3=(RadioButton)findViewById(R.id.r3);
         r4=(RadioButton)findViewById(R.id.r4);
         r5=(RadioButton)findViewById(R.id.r5);
         r5.setChecked(true);
         TelephonyManager telephonyManager = (TelephonyManager)getSystemService(Context.TELEPHONY_SERVICE);
		 id=telephonyManager.getDeviceId();
       
      r1.setOnClickListener(new View.OnClickListener() {
		
		public void onClick(View v) {
			// TODO Auto-generated method stub
			r2.setChecked(false);
			r3.setChecked(false);
			r4.setChecked(false);
			r5.setChecked(false);
			
		}
	});
      r2.setOnClickListener(new View.OnClickListener() {
  		
  		public void onClick(View v) {
  			// TODO Auto-generated method stub
  			r1.setChecked(false);
  			r3.setChecked(false);
  			r4.setChecked(false);
  			r5.setChecked(false);
  			
  		}
  	});
      r3.setOnClickListener(new View.OnClickListener() {
  		
  		public void onClick(View v) {
  			// TODO Auto-generated method stub
  			r1.setChecked(false);
  			r2.setChecked(false);
  			r4.setChecked(false);
  			r5.setChecked(false);
  			
  		}
  	});
      r4.setOnClickListener(new View.OnClickListener() {
  		
  		public void onClick(View v) {
  			// TODO Auto-generated method stub
  			r1.setChecked(false);
  			r2.setChecked(false);
  			r3.setChecked(false);
  			r5.setChecked(false);
  			
  		}
  	});
      r5.setOnClickListener(new View.OnClickListener() {
  		
  		public void onClick(View v) {
  			// TODO Auto-generated method stub
  			r1.setChecked(false);
  			r2.setChecked(false);
  			r3.setChecked(false);
  			r4.setChecked(false);
  			
  		}
  	});
      vote=(Button)findViewById(R.id.vote);
      vote.setOnClickListener(new View.OnClickListener() {
		
		public void onClick(View v) {
			// TODO Auto-generated method stub

			// make the progress bar cancelable
	           // dialog.setCancelable(true);
	 
	            // set a message text
	           //dialog.setMessage("Please wait....");
	 
	            // show it
	           // dialog.show(); //show moving dialog
			String x=checkVal();
			 val="";
			if(x=="r1")
				val="kut";
			else if(x=="r2")
				val="imr";
			if(x=="r3")
				val="naw";
			if(x=="r4")
				val="mus";
			if(x=="r5")
				val="cha";
			
			 //printval(val);
			 Intent myIntent = new Intent(v.getContext(), connectActivity.class);
			 Bundle sent=new Bundle();  //Create a bundle
			 sent.putString("comment", val);  //Put the Strings in the Bundle
			 sent.putString("id", id);
			 sent.putString("status", "vote");
			Log.i("hi", "hello");
		 finish(); //kill previous activity
			 myIntent.putExtras(sent);  //Atatch the intent to Activty
			startActivity(myIntent); //Start activty
				
			
		}

		
	});
      
    }
    String checkVal()

    {
    	String val="";
    	if(r1.isChecked())
    	 val="r1";
    	else if(r2.isChecked())
       	 val="r2";
    	else if(r3.isChecked())
       	 val="r3";
    	else if(r4.isChecked())
       	 val="r4";
    	else if(r5.isChecked())
       	 val="r5";
    	
    	//Toast.makeText(this, val, 2000).show();
    	return val;
    	
    }
    private void printval(String val) {
		// TODO Auto-generated method stub
		Toast.makeText(this, val, 399).show();
	}
}
