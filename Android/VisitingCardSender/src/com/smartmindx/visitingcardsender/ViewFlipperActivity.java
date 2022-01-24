package com.smartmindx.visitingcardsender;

import java.io.BufferedReader;
import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.InputStreamReader;
import java.util.ArrayList;

import org.json.JSONException;
import org.json.JSONObject;

import com.smartmindx.visitingcardsender.R;

import android.app.Activity;
import android.app.AlertDialog;
import android.content.ContentProviderOperation;
import android.content.ContentProviderResult;
import android.content.Context;
import android.content.DialogInterface;
import android.content.OperationApplicationException;
import android.database.Cursor;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.net.Uri;
import android.os.Bundle;
import android.os.Environment;
import android.os.RemoteException;
import android.provider.ContactsContract;
import android.provider.MediaStore;
import android.util.Log;
import android.view.GestureDetector;
import android.view.Gravity;
import android.view.MotionEvent;
import android.view.ViewGroup.LayoutParams;
import android.view.Window;
import android.view.WindowManager;
import android.view.animation.AnimationUtils;
import android.widget.ImageView;
import android.widget.Toast;
import android.widget.ViewFlipper;

public class ViewFlipperActivity extends Activity implements
		android.view.GestureDetector.OnGestureListener {
	/** Called when the activity is first created. */

	private static String folderName = "bluetooth";
	private ViewFlipper viewFlipper = null;
	private GestureDetector gestureDetector = null;
	ArrayList<String> ImageList = new ArrayList<String>();
	ArrayList<String> blueImageList = new ArrayList<String>();
	int index = 0;

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		// Remove title bar
		this.requestWindowFeature(Window.FEATURE_NO_TITLE);
		// Remove notification bar
		// this.getWindow().setFlags(WindowManager.LayoutParams.FLAG_FULLSCREEN,
		// WindowManager.LayoutParams.FLAG_FULLSCREEN);
		setContentView(R.layout.viewfliper);
		viewFlipper = (ViewFlipper) findViewById(R.id.flipper);
		// gestureDetector Object is used to detect gesture events
		gestureDetector = new GestureDetector(this);

		ImageList = getImageListFromGalary();
		if (ImageList.size() == 0) {
			Toast.makeText(getApplicationContext(), "No Image Available",
					Toast.LENGTH_LONG).show();
			this.finish();
		}
		File rootsd = Environment.getExternalStorageDirectory();
		for (int i = 0; i < ImageList.size(); i++) {
			File file = null;
			if (ImageList.get(i).contains(".jpg")) {
				String fileName = ImageList.get(i).replace(".jpg", ".txt");
				file = new File(Environment.getExternalStorageDirectory()
						+ "/downloads/bluetooth/", fileName);
			} else
				file = new File("no_path");

			if (file.exists()) {

				Bitmap dest = BitmapFactory.decodeFile(rootsd.getAbsolutePath()
						+ "/downloads/bluetooth/" + ImageList.get(i));

				if (null != dest) {

					Log.v("ViewFlipper", "Exists..");
					blueImageList.add(ImageList.get(i));
					// bluetooth
					ImageView image = new ImageView(this);
					// image.setImageResource(imageID[i]);
					image.setImageBitmap(dest);
					image.setScaleType(ImageView.ScaleType.CENTER_CROP);
					image.setAdjustViewBounds(true);
					LayoutParams params = new LayoutParams(
							LayoutParams.FILL_PARENT, LayoutParams.FILL_PARENT);
					viewFlipper.addView(image, params);
				}

			}
		}
	}

	
	public boolean onDown(MotionEvent arg0) {
		// TODO Auto-generated method stub
		return false;
	}

	
	public boolean onFling(MotionEvent arg0, MotionEvent arg1, float arg2,
			float arg3) {
		// TODO Auto-generated method stub
		if (arg0.getX() - arg1.getX() > 120) {

			this.viewFlipper.setInAnimation(AnimationUtils.loadAnimation(this,
					R.anim.push_left_in));
			this.viewFlipper.setOutAnimation(AnimationUtils.loadAnimation(this,
					R.anim.push_left_out));
			this.viewFlipper.showNext();

			if (blueImageList.size() > 0)
				index = (index + 1) % blueImageList.size();

			return true;
		} else if (arg0.getX() - arg1.getX() < -120) {
			this.viewFlipper.setInAnimation(AnimationUtils.loadAnimation(this,
					R.anim.push_right_in));
			this.viewFlipper.setOutAnimation(AnimationUtils.loadAnimation(this,
					R.anim.push_right_out));
			this.viewFlipper.showPrevious();

			if (blueImageList.size() > 0)
				index = (index - 1) % blueImageList.size();
			return true;
		}
		return true;
	}

	
	public void onLongPress(MotionEvent arg0) {
		// TODO Auto-generated method stub

	}


	public boolean onScroll(MotionEvent arg0, MotionEvent arg1, float arg2,
			float arg3) {
		// TODO Auto-generated method stub
		return false;
	}

	
	public void onShowPress(MotionEvent arg0) {
		// TODO Auto-generated method stub

	}


	public boolean onSingleTapUp(MotionEvent arg0) {

		showDialog();

		return false;
	}

	@Override
	public boolean onTouchEvent(MotionEvent event) {

		return this.gestureDetector.onTouchEvent(event);
	}

	DialogInterface.OnClickListener dialogClickListener = new DialogInterface.OnClickListener() {
		
		public void onClick(DialogInterface dialog, int which) {
			switch (which) {
			case DialogInterface.BUTTON_POSITIVE:
				// Yes button clicked

				if (blueImageList.size() > 0) {
					String name = blueImageList.get(index).substring(0,
							blueImageList.get(index).indexOf("."));

					Log.v("ViewFlipperActivirt", "onSingleTapUp");

					Log.v("ViewFlipperActivirt", "onTouchEvent");
					File rootsd = Environment.getExternalStorageDirectory();
					File imagePath = new File(rootsd.getAbsolutePath()
							+ "/DCIM/");
					File infoPath = new File(rootsd.getAbsolutePath()
							+ "/downloads/bluetooth/" + name + ".txt");

					try {

						FileInputStream fIs = new FileInputStream(infoPath);
						String str, fileText = "";
						InputStreamReader isr = new InputStreamReader(fIs);
						BufferedReader bufRead = new BufferedReader(isr);
						while ((str = bufRead.readLine()) != null) {
							fileText += str;
							Log.v("ViewFlipper", "json: " + str);
						}

						Log.v("ViewFlipper", "json: " + fileText);

						JSONObject contactJsonObject = new JSONObject(fileText);

						Log.v("ViewFlipper", "json: " + contactJsonObject);

						ContactObject cobject = new ContactObject();

						cobject.setName(contactJsonObject.getString("name"));
						cobject.setEmail(contactJsonObject.getString("email"));
						cobject.setPhone(contactJsonObject.getString("phone"));
						cobject.setFacebookId(contactJsonObject
								.getString("facebookId"));
						cobject.setAddress(contactJsonObject
								.getString("address"));

						Bitmap dest = BitmapFactory.decodeFile(rootsd
								.getAbsolutePath()
								+ "/downloads/bluetooth/"
								+ contactJsonObject.getString("name") + ".jpg");

						addContact(cobject, dest, getApplicationContext());

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

				break;

			case DialogInterface.BUTTON_NEGATIVE:
				// No button clicked

				break;
			}
		}
	};

	private void showDialog() {
		AlertDialog.Builder builder = new AlertDialog.Builder(this);
		builder.setMessage("Want to add this contact ?")
				.setTitle("Add Contact!")
				.setPositiveButton("Yes", dialogClickListener)
				.setNegativeButton("No", dialogClickListener).show();
	}

	// /
	//
	public ArrayList<String> getImageListFromGalary() {
		ArrayList<String> imageList = new ArrayList<String>();
		// which image properties are we querying
		String[] projection = new String[] { MediaStore.Images.Media._ID,
				MediaStore.Images.Media.BUCKET_DISPLAY_NAME,
				MediaStore.Images.Media.DATE_TAKEN,
				MediaStore.Images.Media.DISPLAY_NAME };

		// Get the base URI for the People table in the Contacts content
		// provider.
		Uri images = MediaStore.Images.Media.EXTERNAL_CONTENT_URI;

		// Make the query.
		Cursor cur = managedQuery(images, projection, // Which columns to return
				MediaStore.Images.Media.BUCKET_DISPLAY_NAME + "=" + "?", // Which
																			// rows
																			// to
																			// return
																			// (all
																			// rows)
				new String[] { folderName }, // Selection arguments (none)
				"" // Ordering
		);

		// Log.i("ListingImages", " query count=" + cur.getCount());
		if (cur.moveToFirst()) {
			String bucket;
			String date;
			int bucketColumn = cur
					.getColumnIndex(MediaStore.Images.Media.BUCKET_DISPLAY_NAME);

			int dateColumn = cur
					.getColumnIndex(MediaStore.Images.Media.DISPLAY_NAME);

			do {
				// Get the field values
				bucket = cur.getString(bucketColumn);
				date = cur.getString(dateColumn);

				// Do something with the values.
				Log.i("ListingImages", " bucket=" + bucket + "  Display Name="
						+ date);
				imageList.add(date);
			} while (cur.moveToNext());

		}
		return imageList;

	}

	private static void addPhoto(Context context, Bitmap bm, int id) {
		ByteArrayOutputStream stream = new ByteArrayOutputStream();
		bm.compress(Bitmap.CompressFormat.PNG, 75, stream);
		ArrayList<ContentProviderOperation> operations = new ArrayList<ContentProviderOperation>();
		operations
				.add(ContentProviderOperation
						.newInsert(ContactsContract.Data.CONTENT_URI)
						.withValue(ContactsContract.Data.RAW_CONTACT_ID, id)
						// here 9 is _ID where I'm inserting image
						.withValue(ContactsContract.Data.IS_SUPER_PRIMARY, 1)
						.withValue(
								ContactsContract.Data.MIMETYPE,
								ContactsContract.CommonDataKinds.Photo.CONTENT_ITEM_TYPE)
						.withValue(
								ContactsContract.CommonDataKinds.Photo.PHOTO,
								stream.toByteArray()).build());

		try {
			context.getContentResolver().applyBatch(ContactsContract.AUTHORITY,
					operations);
			stream.flush();
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (RemoteException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (OperationApplicationException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
	}

	private static void addContact(ContactObject contact, Bitmap bm,
			Context context) {

		String DisplayName = contact.getName();
		String MobileNumber = contact.getPhone();
		String HomeNumber = null;
		String WorkNumber = null;
		String emailID = contact.getEmail();
		String company = null;
		String jobTitle = null;

		ArrayList<ContentProviderOperation> ops = new ArrayList<ContentProviderOperation>();

		ops.add(ContentProviderOperation
				.newInsert(ContactsContract.RawContacts.CONTENT_URI)
				.withValue(ContactsContract.RawContacts.ACCOUNT_TYPE, null)
				.withValue(ContactsContract.RawContacts.ACCOUNT_NAME, null)
				.build());

		// ------------------------------------------------------ Names
		if (DisplayName != null) {
			ops.add(ContentProviderOperation
					.newInsert(ContactsContract.Data.CONTENT_URI)
					.withValueBackReference(
							ContactsContract.Data.RAW_CONTACT_ID, 0)
					.withValue(
							ContactsContract.Data.MIMETYPE,
							ContactsContract.CommonDataKinds.StructuredName.CONTENT_ITEM_TYPE)
					.withValue(
							ContactsContract.CommonDataKinds.StructuredName.DISPLAY_NAME,
							DisplayName).build());
		}

		// ------------------------------------------------------ Mobile Number
		if (MobileNumber != null) {
			ops.add(ContentProviderOperation
					.newInsert(ContactsContract.Data.CONTENT_URI)
					.withValueBackReference(
							ContactsContract.Data.RAW_CONTACT_ID, 0)
					.withValue(
							ContactsContract.Data.MIMETYPE,
							ContactsContract.CommonDataKinds.Phone.CONTENT_ITEM_TYPE)
					.withValue(ContactsContract.CommonDataKinds.Phone.NUMBER,
							MobileNumber)
					.withValue(ContactsContract.CommonDataKinds.Phone.TYPE,
							ContactsContract.CommonDataKinds.Phone.TYPE_MOBILE)
					.build());
		}

		// ------------------------------------------------------ Home Numbers
		if (HomeNumber != null) {
			ops.add(ContentProviderOperation
					.newInsert(ContactsContract.Data.CONTENT_URI)
					.withValueBackReference(
							ContactsContract.Data.RAW_CONTACT_ID, 0)
					.withValue(
							ContactsContract.Data.MIMETYPE,
							ContactsContract.CommonDataKinds.Phone.CONTENT_ITEM_TYPE)
					.withValue(ContactsContract.CommonDataKinds.Phone.NUMBER,
							HomeNumber)
					.withValue(ContactsContract.CommonDataKinds.Phone.TYPE,
							ContactsContract.CommonDataKinds.Phone.TYPE_HOME)
					.build());
		}

		// ------------------------------------------------------ Work Numbers
		if (WorkNumber != null) {
			ops.add(ContentProviderOperation
					.newInsert(ContactsContract.Data.CONTENT_URI)
					.withValueBackReference(
							ContactsContract.Data.RAW_CONTACT_ID, 0)
					.withValue(
							ContactsContract.Data.MIMETYPE,
							ContactsContract.CommonDataKinds.Phone.CONTENT_ITEM_TYPE)
					.withValue(ContactsContract.CommonDataKinds.Phone.NUMBER,
							WorkNumber)
					.withValue(ContactsContract.CommonDataKinds.Phone.TYPE,
							ContactsContract.CommonDataKinds.Phone.TYPE_WORK)
					.build());
		}

		// ------------------------------------------------------ Email
		if (emailID != null) {
			ops.add(ContentProviderOperation
					.newInsert(ContactsContract.Data.CONTENT_URI)
					.withValueBackReference(
							ContactsContract.Data.RAW_CONTACT_ID, 0)
					.withValue(
							ContactsContract.Data.MIMETYPE,
							ContactsContract.CommonDataKinds.Email.CONTENT_ITEM_TYPE)
					.withValue(ContactsContract.CommonDataKinds.Email.DATA,
							emailID)
					.withValue(ContactsContract.CommonDataKinds.Email.TYPE,
							ContactsContract.CommonDataKinds.Email.TYPE_WORK)
					.build());
		}

		// ------------------------------------------------------ Organization
		if (company != null && jobTitle != null) {
			ops.add(ContentProviderOperation
					.newInsert(ContactsContract.Data.CONTENT_URI)
					.withValueBackReference(
							ContactsContract.Data.RAW_CONTACT_ID, 0)
					.withValue(
							ContactsContract.Data.MIMETYPE,
							ContactsContract.CommonDataKinds.Organization.CONTENT_ITEM_TYPE)
					.withValue(
							ContactsContract.CommonDataKinds.Organization.COMPANY,
							company)
					.withValue(
							ContactsContract.CommonDataKinds.Organization.TYPE,
							ContactsContract.CommonDataKinds.Organization.TYPE_WORK)
					.withValue(
							ContactsContract.CommonDataKinds.Organization.TITLE,
							jobTitle)
					.withValue(
							ContactsContract.CommonDataKinds.Organization.TYPE,
							ContactsContract.CommonDataKinds.Organization.TYPE_WORK)
					.build());
		}

		if (null != bm) {
			ByteArrayOutputStream stream = new ByteArrayOutputStream();
			bm.compress(Bitmap.CompressFormat.JPEG, 75, stream);
			ops.add(ContentProviderOperation
					.newInsert(ContactsContract.Data.CONTENT_URI)
					.withValue(ContactsContract.Data.RAW_CONTACT_ID, 0)
					.withValue(ContactsContract.Data.IS_SUPER_PRIMARY, 1)
					.withValue(
							ContactsContract.Data.MIMETYPE,
							ContactsContract.CommonDataKinds.Photo.CONTENT_ITEM_TYPE)
					.withValue(ContactsContract.CommonDataKinds.Photo.PHOTO,
							stream.toByteArray()).build());
		}
		// Asking the Contact provider to create a new contact
		try {
			ContentProviderResult[] res = context.getContentResolver()
					.applyBatch(ContactsContract.AUTHORITY, ops);

			if (null != bm) {
				ByteArrayOutputStream stream = new ByteArrayOutputStream();
				bm.compress(Bitmap.CompressFormat.JPEG, 75, stream);
				ops.add(ContentProviderOperation
						.newInsert(ContactsContract.Data.CONTENT_URI)
						.withValue(ContactsContract.Data.RAW_CONTACT_ID,
								res[0].uri.getPath().substring(14))
						.withValue(ContactsContract.Data.IS_SUPER_PRIMARY, 1)
						.withValue(
								ContactsContract.Data.MIMETYPE,
								ContactsContract.CommonDataKinds.Photo.CONTENT_ITEM_TYPE)
						.withValue(
								ContactsContract.CommonDataKinds.Photo.PHOTO,
								stream.toByteArray()).build());
			}

			context.getContentResolver().applyBatch(ContactsContract.AUTHORITY,
					ops);

			Log.v("View Flipper", "id: " + res[0].uri.getPath().substring(14));

			Toast.makeText(context, "Contact Added", Toast.LENGTH_SHORT).show();
		} catch (Exception e) {
			e.printStackTrace();
			Toast.makeText(context, "Exception: " + e.getMessage(),
					Toast.LENGTH_SHORT).show();
		}

	}

}