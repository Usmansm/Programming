package com.smartmindx.visitingcardsender;

import java.io.BufferedInputStream;
import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.OutputStream;
import java.net.URISyntaxException;
import java.util.zip.ZipEntry;

import org.json.JSONObject;

import android.app.Activity;
import android.content.ContentValues;
import android.content.Context;
import android.content.Intent;
import android.database.Cursor;
import android.database.sqlite.SQLiteDatabase;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.net.Uri;
import android.os.Bundle;
import android.os.Environment;
import android.provider.MediaStore;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;

import com.google.gson.Gson;
import com.smartmindx.visitingcardsender.R;

public class infoAddActivity extends Activity implements View.OnClickListener {
	/** Called when the activity is first created. */

	private SQLiteDatabase db;
	private DatabaseHandler dbhelper;

	private Button buttonSaveUpadte, buttonSelImg;
	private EditText name;
	private EditText email;
	private EditText phone;
	private EditText facebook;
	private EditText address;

	private static final int FILE_SELECT_CODE = 0;
	public static String TAG = "Info >>>>>>>>>";
	public static String selectedImagePath = null;

	public static String VIEWMODE = "VIEW";

	private String uname = "", uemail = "", uphone = "", ufacebook = "",
			uaddress = "";
	private byte[] myCard;

	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);

		if ((!this.getInfo()) || (VIEWMODE == "EDIT")) {
			loadLayout();
			if (VIEWMODE == "EDIT") {
				name.setText(uname.toString());
				email.setText(uemail.toString());
				phone.setText(uphone.toString());
				facebook.setText(ufacebook.toString());
				address.setText(uaddress.toString());
				buttonSelImg.setText("Change image");
				buttonSaveUpadte.setText("Update");
			}
		} else {
			Intent intent = new Intent(this, ViewAndSendActivity.class);
			startActivity(intent);
		}
	}

	public boolean getInfo() {

		dbhelper = new DatabaseHandler(this);
		db = dbhelper.getReadableDatabase();
		Cursor cursor = db.query("info", null, null, null, null, null, null);
		startManagingCursor(cursor);
		while (cursor.moveToNext()) {
			uname = cursor.getString(cursor.getColumnIndex("name"));
			uemail = cursor.getString(cursor.getColumnIndex("email"));
			uphone = cursor.getString(cursor.getColumnIndex("phone"));
			ufacebook = cursor.getString(cursor.getColumnIndex("facebook"));
			uaddress = cursor.getString(cursor.getColumnIndex("address"));
			myCard = cursor.getBlob(cursor.getColumnIndex("cardimage"));
		}
		if (uname.equals("") || uemail.equals("") || uphone.equals("")
				|| ufacebook.equals("") || uaddress.equals("")
				|| (myCard == null)) {
			return false;
		} else {
			return true;
		}

	}

	private void loadLayout() {
		setContentView(R.layout.main);
		buttonSaveUpadte = (Button) findViewById(R.id.button1);
		name = (EditText) findViewById(R.id.editText1);
		email = (EditText) findViewById(R.id.editText2);
		phone = (EditText) findViewById(R.id.editText3);
		facebook = (EditText) findViewById(R.id.editText4);
		address = (EditText) findViewById(R.id.editText5);
		buttonSelImg = (Button) findViewById(R.id.buttonSelImg);
		buttonSaveUpadte.setOnClickListener(this);
		buttonSelImg.setOnClickListener(this);

	}

	public void onClick(View v) {
		if (v == buttonSaveUpadte) {
			addContact();
		} else if (v == buttonSelImg) {
			showFileChooser();
		}
	}

	private void showFileChooser() {
		Intent intent = new Intent(Intent.ACTION_GET_CONTENT);
		intent.setType("image/*");
		intent.addCategory(Intent.CATEGORY_OPENABLE);

		try {
			startActivityForResult(
					Intent.createChooser(intent, "Select a File to Upload"),
					FILE_SELECT_CODE);
		} catch (android.content.ActivityNotFoundException ex) {
			// Potentially direct the user to the Market with a Dialog
			Toast.makeText(this, "Please install a File Manager.",
					Toast.LENGTH_SHORT).show();
		}
	}

	@Override
	protected void onActivityResult(int requestCode, int resultCode, Intent data) {
		switch (requestCode) {
		case FILE_SELECT_CODE:
			if (resultCode == RESULT_OK) {
				Uri uri = data.getData();
				Log.d(TAG, "File Uri: " + uri.toString());
				// String path;
				try {
					selectedImagePath = FileUtils.getPath(this, uri);
					Log.d(TAG, "File Path: " + selectedImagePath);
				} catch (URISyntaxException e) {
					// TODO Auto-generated catch block
					e.printStackTrace();
				}

			}
			break;
		}
		super.onActivityResult(requestCode, resultCode, data);
	}

	public void addContact() {
		if (name.getText().toString().trim().equals("")
				|| email.getText().toString().trim().equals("")
				|| phone.getText().toString().equals("")
				|| facebook.getText().toString().equals("")
				|| address.getText().toString().equals("")
				|| (myCard == null && selectedImagePath == null)) {
			Toast.makeText(this, "Please fill all fields", Toast.LENGTH_LONG)
					.show();
		} else {
			byte[] byteImage = null;
			if (selectedImagePath != null) {
				FileInputStream instream;
				try {

					instream = new FileInputStream(selectedImagePath);
					BufferedInputStream bif = new BufferedInputStream(instream);
					try {
						byteImage = new byte[bif.available()];
						bif.read(byteImage);
					} catch (IOException e) {
						Log.e("Img Info", "IO EX");
						// TODO Auto-generated catch block
						e.printStackTrace();
					}

				} catch (FileNotFoundException e1) {
					// TODO Auto-generated catch block
					Log.e("Img Info", "File not found");
				}
			}
			dbhelper = new DatabaseHandler(this);
			db = dbhelper.getWritableDatabase();
			ContentValues values = new ContentValues();
			values.put("name", name.getText().toString());
			values.put("email", email.getText().toString());
			values.put("phone", phone.getText().toString());
			values.put("facebook", facebook.getText().toString());
			values.put("address", address.getText().toString());
			if (byteImage != null) {
				values.put("cardimage", byteImage);
			}

			// values.put("image", null);
			// Log.i(">>>>>>>>", ">>>>>>>" + byteImage.length);
			db.insert("info", null, values);
			db.close(); // Closing database connection
			dbhelper.close();
			try {
				saveImageToExternalStorage(CreateImage(name.getText()
						.toString(), email.getText().toString(), phone
						.getText().toString(), facebook.getText().toString(),
						address.getText().toString()));

				Toast.makeText(getApplicationContext(),
						"Image Save Successfull.", Toast.LENGTH_LONG).show();
			} catch (Exception e) {
				Toast.makeText(getApplicationContext(), e.getMessage(),
						Toast.LENGTH_LONG).show();
			}
			VIEWMODE = "VIEW";
			// this.finish();

			Intent intent = new Intent(this, ViewAndSendActivity.class);
			startActivity(intent);
			finish();

		}
	}

	public Bitmap CreateImage(String name, String email, String phone,
			String facebook, String address) {
		ImageCreator _obj = new ImageCreator(name, email, phone, facebook,
				address);
		Bitmap bitmap = _obj.CreateBitmap();
		return bitmap;
	}

	public static boolean createDirIfNotExists(String path) {
		boolean ret = true;

		File file = new File(path);
		if (!file.exists()) {
			if (!file.mkdirs()) {
				Log.e("TravellerLog :: ", "Problem creating Image folder");
				ret = false;
			}
		} else {
			file.delete();
			file.mkdirs();
		}
		return ret;
	}

	public boolean saveImageToExternalStorage(Bitmap image) {
		try {

			if (null != name && null != name.getText()) {
				File rootsd = Environment.getExternalStorageDirectory();
				createDirIfNotExists(rootsd.getAbsolutePath() + "/DCIM/");
				File imagePath = new File(rootsd.getAbsolutePath() + "/DCIM/");
				File infoPath = new File(rootsd.getAbsolutePath() + "/DCIM/");
				Gson gson = new Gson();

				ContactObject cobject = new ContactObject();

				cobject.setName(name.getText().toString());
				cobject.setEmail(email.getText().toString());
				cobject.setPhone(phone.getText().toString());
				cobject.setFacebookId(facebook.getText().toString());
				cobject.setAddress(address.getText().toString());

				// cobject.setIcon(convertBitmapToString(image));

				String json = gson.toJson(cobject);

				OutputStream fOut = null;
				File file = new File(imagePath, name.getText().toString()
						+ ".jpg");
				file.createNewFile();
				fOut = new FileOutputStream(file);
				image.compress(Bitmap.CompressFormat.PNG, 70, fOut);

				fOut.flush();
				fOut.close();

				OutputStream fOut1 = null;
				File file1 = new File(infoPath, "contact_info" + ".txt");
				file1.createNewFile();
				fOut1 = new FileOutputStream(file1);
				fOut1.write(json.getBytes());

				fOut1.flush();
				fOut1.close();

				// image.compress(Bitmap.CompressFormat.PNG, 10, fOut);

				// String filename = "icon.png";
				// byte[] bytes = stream.toByteArray();
				// ZipEntry entry = new ZipEntry(filename);
				// zos.putNextEntry(entry);
				// zos.write(bytes);
				// zos.closeEntry();

				MediaStore.Images.Media.insertImage(this.getContentResolver(),
						file.getAbsolutePath(), file.getName(), file.getName());

				return true;
			}
			return false;

		} catch (Exception e) {
			Log.e("saveToExternalStorage()", e.getMessage());
			return false;
		}
	}

	public static String convertBitmapToString(Bitmap src) {
		ByteArrayOutputStream os = new ByteArrayOutputStream();
		src.compress(android.graphics.Bitmap.CompressFormat.PNG, 100,
				(OutputStream) os);
		return os.toString();
	}

	public static Bitmap getBitMapFromString(String src) {
		Log.i("b=", "" + src.getBytes().length);// returns 12111 as a length.
		return BitmapFactory.decodeByteArray(src.getBytes(), 0,
				src.getBytes().length);
	}

}

class FileUtils {
	public static String getPath(Context context, Uri uri)
			throws URISyntaxException {
		if ("content".equalsIgnoreCase(uri.getScheme())) {
			String[] projection = { "_data" };
			Cursor cursor = null;

			try {
				cursor = context.getContentResolver().query(uri, projection,
						null, null, null);
				int column_index = cursor.getColumnIndexOrThrow("_data");
				if (cursor.moveToFirst()) {
					return cursor.getString(column_index);
				}
			} catch (Exception e) {
				// Eat it
			}
		} else if ("file".equalsIgnoreCase(uri.getScheme())) {
			return uri.getPath();
		}

		return null;
	}

}