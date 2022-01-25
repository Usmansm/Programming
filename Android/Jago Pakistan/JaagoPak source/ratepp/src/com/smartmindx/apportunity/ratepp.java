package com.smartmindx.apportunity;

import com.smartmindx.apportunity.R;
import android.app.Activity;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.Toast;

public class ratepp extends Activity {
    /** Called when the activity is first created. */
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.main);
        
        Button play = (Button) findViewById(R.id.activity1);
		play.setOnClickListener(new View.OnClickListener() {
			public void onClick(View view) {
				try {
					Intent myIntent = new Intent(view.getContext(), view.class);
					startActivityForResult(myIntent,0);
					
				} catch (Exception ok) {
					Log.e("MainSoft", "Error: Starting Notification", ok); 							

					Toast.makeText(
							getApplicationContext(),
							ok.getClass().getName() + "" + ok.getMessage()
									+ "\nNotification", Toast.LENGTH_LONG)
							.show();
				}
			

			}

		});
		
		Button act2 = (Button) findViewById(R.id.activity2);
		act2.setOnClickListener(new View.OnClickListener() {
			public void onClick(View view) {
				Intent myIntent = new Intent(view.getContext(), actv2.class);
				startActivityForResult(myIntent,1);

			}

		});
		
		Button act3 = (Button) findViewById(R.id.activity3);
		act3.setOnClickListener(new View.OnClickListener() {
			public void onClick(View view) {
				Intent myIntent = new Intent(view.getContext(), actv3.class);
				startActivityForResult(myIntent,2);

			}

		});
		
    }
    
    
    
}