package com.smartmindx.visitingcardsender;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.InputStreamReader;

import org.json.JSONException;
import org.json.JSONObject;

import com.smartmindx.visitingcardsender.R;

import android.app.Activity;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.os.Bundle;
import android.os.Environment;
import android.util.Log;
import android.widget.ImageView;
import android.widget.TextView;

public class ViewOwnCard extends Activity {
	private ImageView myImage;
	private TextView nameTv;
	private TextView emailTv;
	private TextView addressTv;
	private TextView phoneTv;
	private TextView facebookTv;


	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.viewowncard);
		File rootsd = Environment.getExternalStorageDirectory();
		String infoPath = rootsd.getAbsolutePath() + "/DCIM/contact_info.txt";

		FileInputStream fIs;
		try {
			fIs = new FileInputStream(infoPath);

			String str, fileText = "";
			InputStreamReader isr = new InputStreamReader(fIs);
			BufferedReader bufRead = new BufferedReader(isr);
			while ((str = bufRead.readLine()) != null) {
				fileText += str;
				Log.v("ViewFlipper", "json: " + str);
			}

			JSONObject contactJsonObject = new JSONObject(fileText);

			Log.v("ViewFlipper", "json: " + contactJsonObject);

			String imageName = contactJsonObject.getString("name");
			String email = contactJsonObject.getString("email");
			String phone = contactJsonObject.getString("phone");
			String facebook = contactJsonObject.getString("facebookId");
			String address = contactJsonObject.getString("address");
			
			myImage = (ImageView) findViewById(R.id.myImage);
			nameTv = (TextView) findViewById(R.id.contactname) ;
			emailTv = (TextView) findViewById(R.id.contactemail) ;
			addressTv = (TextView) findViewById(R.id.contactaddress) ;
			phoneTv = (TextView) findViewById(R.id.contactphone) ;
			facebookTv = (TextView) findViewById(R.id.contactfacebook) ;
			

//			nameTv.setText("Name: "+imageName);
//			emailTv.setText("Email: "+email);
//			addressTv.setText("Address: "+address);
//			phoneTv.setText("Phone: "+phone);
//			facebookTv.setText("Facebook Id: "+facebook);
			
			Bitmap myBitmap = BitmapFactory.decodeFile(Environment
					.getExternalStorageDirectory().getAbsolutePath()
					+ "/DCIM/"+imageName+".jpg");
			
			
			myImage.setImageBitmap(myBitmap);

		} catch (FileNotFoundException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (JSONException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}


	}
}
