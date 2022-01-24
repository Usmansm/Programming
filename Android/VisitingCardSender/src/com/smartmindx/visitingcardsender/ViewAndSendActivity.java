package com.smartmindx.visitingcardsender;

import java.io.BufferedOutputStream;
import java.io.BufferedReader;
import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.io.UnsupportedEncodingException;
import java.util.Date;
import java.util.Timer;
import java.util.TimerTask;

import org.apache.commons.io.FileUtils;
import org.json.JSONException;
import org.json.JSONObject;

import com.smartmindx.visitingcardsender.R;

import android.app.Activity;
import android.app.ProgressDialog;
import android.bluetooth.BluetoothAdapter;
import android.bluetooth.BluetoothDevice;
import android.content.ContentResolver;
import android.content.ContentValues;
import android.content.Context;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.net.Uri;
import android.os.Bundle;
import android.os.Environment;
import android.os.Handler;
import android.os.Message;
import android.provider.MediaStore;
import android.provider.MediaStore.Images;
import android.util.Log;
import android.view.Menu;
import android.view.MenuInflater;
import android.view.MenuItem;
import android.view.View;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.SlidingDrawer;
import android.widget.TextView;
import android.widget.Toast;

public class ViewAndSendActivity extends Activity implements
		View.OnClickListener {

	// Debugging
	private static final String TAG = "ViewAndSendActivity";
	private static final boolean D = true;

	// Message types sent from the BluetoothChatService Handler
	public static final int MESSAGE_STATE_CHANGE = 1;
	public static final int MESSAGE_READ = 2;
	public static final int MESSAGE_WRITE = 3;
	public static final int MESSAGE_DEVICE_NAME = 4;
	public static final int MESSAGE_TOAST = 5;
	public static final int MESSAGE_COMPLETE = 6;
	public static final int MESSAGE_FILE_NAME = 7;
	public static final int MESSAGE_FILE_READ_COMPLETE = 8;
	public static final int MESSAGE_FILE_READ = 9;

	// Key names received from the BluetoothChatService Handler
	public static final String DEVICE_NAME = "device_name";
	public static final String DEVICE_ADDRESS = "device_address";
	public static final String TOAST = "toast";

	// Intent request codes
	private static final int REQUEST_CONNECT_DEVICE = 1;
	private static final int REQUEST_ENABLE_BT = 2;

	// Name of the connected device
	private String mConnectedDeviceName = null;
	// String buffer for outgoing messages
	private StringBuffer mOutStringBuffer;
	// Local Bluetooth adapter
	private BluetoothAdapter mBluetoothAdapter = null;
	// Member object for the chat services
	private BluetoothCardsService mCardsService = null;

	public final static String APP_PATH_SD_CARD = "/download/";
	public final static String APP_THUMBNAIL_PATH_SD_CARD = "thumbnails";
	public ImageView imageViewer;
	Button btnView, btnSaveImage, btnSendImage, btnEdit, btnOwnCard;
	File rootsd = Environment.getExternalStorageDirectory();
	int imageNumber = 0;
	String imageName = "";
	String deviceAddress;
	private TextView mStatusTextView;
	private boolean isBitmap = false;

	private String file_name = "unknown";
	private boolean connected;

	private ProgressDialog dialog;
	private ProgressDialog receivingDialog;

	private ByteArrayOutputStream bosImage;
	private ByteArrayOutputStream bosFile;

	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.view);

		imageViewer = (ImageView) findViewById(R.id.image);
		btnEdit = (Button) findViewById(R.id.btnEdit);
		btnView = (Button) findViewById(R.id.btnView);
		btnSendImage = (Button) findViewById(R.id.btnSend);
		btnOwnCard = (Button) findViewById(R.id.btnOwnCard);
		mStatusTextView = (TextView) findViewById(R.id.statusTxtView);

		btnEdit.setOnClickListener(this);
		btnView.setOnClickListener(this);
		btnSendImage.setOnClickListener(this);
		btnOwnCard.setOnClickListener(this);

		connected = false;
		bosImage = new ByteArrayOutputStream();
		bosFile = new ByteArrayOutputStream();
		try {
			String infoPath = rootsd.getAbsolutePath()
					+ "/DCIM/contact_info.txt";
			FileInputStream fIs = new FileInputStream(infoPath);

			String str, fileText = "";
			InputStreamReader isr = new InputStreamReader(fIs);
			BufferedReader bufRead = new BufferedReader(isr);
			while ((str = bufRead.readLine()) != null) {
				fileText += str;
				Log.v("ViewFlipper", "json: " + str);
			}

			JSONObject contactJsonObject = new JSONObject(fileText);

			String name = contactJsonObject.getString("name");
			Bitmap myBitmap = BitmapFactory.decodeFile(Environment
					.getExternalStorageDirectory().getAbsolutePath()
					+ "/DCIM/"
					+ name + ".jpg");

			imageViewer.setImageBitmap(myBitmap);
		} catch (Exception e) {
			e.printStackTrace();
		}

		infoAddActivity.createDirIfNotExists(Environment
				.getExternalStorageDirectory().getAbsolutePath()
				+ "/downloads/bluetooth/");

		// Get local Bluetooth adapter
		mBluetoothAdapter = BluetoothAdapter.getDefaultAdapter();

		// If the adapter is null, then Bluetooth is not supported
		if (mBluetoothAdapter == null) {
			Toast.makeText(this, "Bluetooth is not available",
					Toast.LENGTH_LONG).show();
			finish();
			return;
		}

	}

	public void onClick(View v) {
		if (v == btnEdit) {
			infoAddActivity.VIEWMODE = "EDIT";
			Intent intent = new Intent(getApplicationContext(),
					infoAddActivity.class);
			startActivity(intent);
		} else if (v == btnView) {
			Intent intent = new Intent(getApplicationContext(),
					ViewFlipperActivity.class);
			startActivity(intent);
		} else if (v == btnSendImage) {
			if (connected) {
				dialog = new ProgressDialog(ViewAndSendActivity.this);
				dialog.setIndeterminate(true);
				dialog.setMessage("Sending...");
				dialog.show();

				String infoPath = rootsd.getAbsolutePath()
						+ "/DCIM/contact_info.txt";

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

					imageName = contactJsonObject.getString("name");

					infoPath = rootsd.getAbsolutePath() + "/DCIM/";

					OutputStream fOut1 = null;
					File file1 = new File(infoPath, imageName + ".txt");
					file1.createNewFile();
					fOut1 = new FileOutputStream(file1);
					fOut1.write(fileText.getBytes());
					fOut1.flush();
					fOut1.close();

					File rootsd = Environment.getExternalStorageDirectory();
					infoPath = rootsd.getAbsolutePath() + "/DCIM/" + imageName
							+ ".txt";
					final File file = new File(infoPath);

					rootsd = Environment.getExternalStorageDirectory();
					String imagePath = rootsd.getAbsolutePath() + "/DCIM/"
							+ imageName + ".jpg";
					final File file2 = new File(imagePath);
					final Handler handler3 = new Handler();
					Timer t3 = new Timer();
					t3.schedule(new TimerTask() {
						public void run() {
							handler3.post(new Runnable() {
								public void run() {
									String name = imageName + "ABCDEFG";
									sendMessage(name);
									Log.e(TAG, "Sending name... " + name);
								}
							});
						}
					}, 100);
					final Handler handler4 = new Handler();
					Timer t4 = new Timer();
					t4.schedule(new TimerTask() {
						public void run() {
							handler4.post(new Runnable() {
								public void run() {
									try {
										sendMessage(FileUtils
												.readFileToByteArray(file));
										Log.e(TAG, "Sending file... ");
									} catch (IOException e) {
										// TODO Auto-generated catch block
										e.printStackTrace();
									}

								}
							});
						}
					}, 500);

					final Handler handler6 = new Handler();
					Timer t6 = new Timer();
					t6.schedule(new TimerTask() {
						public void run() {
							handler6.post(new Runnable() {
								public void run() {
									sendMessage("file_complete");
									Log.e(TAG, "Sending file complete... ");

								}
							});
						}
					}, 4000);

					final Handler handler = new Handler();
					Timer t = new Timer();
					t.schedule(new TimerTask() {
						public void run() {
							handler.post(new Runnable() {
								public void run() {
									try {
										byte[] bytes = FileUtils
												.readFileToByteArray(file2);
										sendMessage(bytes);
										Log.e(TAG, "Sending image... ");
									} catch (IOException e) {
										// TODO Auto-generated catch block
										e.printStackTrace();
									}
								}
							});
						}
					}, 4500);
					final Handler handler2 = new Handler();
					Timer t2 = new Timer();
					t2.schedule(new TimerTask() {
						public void run() {
							handler2.post(new Runnable() {
								public void run() {
									sendMessage("complete");
									Log.e(TAG, "Complete... ");
									if (dialog.isShowing())
										dialog.dismiss();
								}
							});
						}
					}, 8000);
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
			} else {
				Toast.makeText(getBaseContext(),
						"Please connect to another Android device first!",
						Toast.LENGTH_SHORT).show();
			}

		} else if (v == btnOwnCard) {
			Intent intent = new Intent(getApplicationContext(),
					ViewOwnCard.class);
			startActivity(intent);
		}
	}

	@Override
	protected void onActivityResult(int requestCode, int resultCode, Intent data) {
		// TODO Auto-generated method stub
		super.onActivityResult(requestCode, resultCode, data);
		if (D)
			Log.d(TAG, "onActivityResult " + resultCode);
		switch (requestCode) {
		case REQUEST_CONNECT_DEVICE:
			// When DeviceListActivity returns with a device to connect
			if (resultCode == Activity.RESULT_OK) {
				// Get the device MAC address
				String address = data.getExtras().getString(
						DeviceListActivity.EXTRA_DEVICE_ADDRESS);
				// Get the BLuetoothDevice object
				BluetoothDevice device = mBluetoothAdapter
						.getRemoteDevice(address);
				// Attempt to connect to the device
				mCardsService.connect(device);
			}
			break;
		case REQUEST_ENABLE_BT:
			// When the request to enable Bluetooth returns
			if (resultCode == Activity.RESULT_OK) {
				// Bluetooth is now enabled, so set up a chat session
				setupChat();
			} else {
				// User did not enable Bluetooth or an error occured
				Log.d(TAG, "BT not enabled");
				Toast.makeText(this, R.string.bt_not_enabled_leaving,
						Toast.LENGTH_SHORT).show();
				finish();
			}
		}
		// if (requestCode == 1) {
		//
		// Intent i = new Intent(Intent.ACTION_SEND);
		// i.setType("image/jpeg");
		// File rootsd = Environment.getExternalStorageDirectory();
		// String imagePath = rootsd.getAbsolutePath() + "/DCIM/" + imageName
		// + ".jpg";
		// i.putExtra(Intent.EXTRA_STREAM, Uri.parse(imagePath));
		// startActivity(Intent.createChooser(i, "Send Image"));
		// }
	}

	@Override
	public void onStart() {
		super.onStart();
		if (D)
			Log.e(TAG, "++ ON START ++");

		// If BT is not on, request that it be enabled.
		// setupChat() will then be called during onActivityResult
		if (!mBluetoothAdapter.isEnabled()) {
			Intent enableIntent = new Intent(
					BluetoothAdapter.ACTION_REQUEST_ENABLE);
			startActivityForResult(enableIntent, REQUEST_ENABLE_BT);
			// Otherwise, setup the chat session
		} else {
			if (mCardsService == null)
				setupChat();
		}
	}

	@Override
	public synchronized void onResume() {
		super.onResume();
		if (D)
			Log.e(TAG, "+ ON RESUME +");

		// Performing this check in onResume() covers the case in which BT was
		// not enabled during onStart(), so we were paused to enable it...
		// onResume() will be called when ACTION_REQUEST_ENABLE activity
		// returns.
		if (mCardsService != null) {
			// Only if the state is STATE_NONE, do we know that we haven't
			// started already
			if (mCardsService.getState() == BluetoothCardsService.STATE_NONE) {
				// Start the Bluetooth chat services
				mCardsService.start();
			}
		}
	}

	private void setupChat() {
		Log.d(TAG, "setupChat()");

		// Initialize the BluetoothChatService to perform bluetooth connections
		mCardsService = new BluetoothCardsService(this, mHandler);

		// Initialize the buffer for outgoing messages
		mOutStringBuffer = new StringBuffer("");
	}

	@Override
	public synchronized void onPause() {
		super.onPause();
		if (D)
			Log.e(TAG, "- ON PAUSE -");
	}

	@Override
	public void onStop() {
		super.onStop();
		if (D)
			Log.e(TAG, "-- ON STOP --");
	}

	@Override
	public void onDestroy() {
		super.onDestroy();
		// Stop the Bluetooth chat services
		connected = false;
		if (mCardsService != null)
			mCardsService.stop();
		if (D)
			Log.e(TAG, "--- ON DESTROY ---");
	}

	private void ensureDiscoverable() {
		if (D)
			Log.d(TAG, "ensure discoverable");
		if (mBluetoothAdapter.getScanMode() != BluetoothAdapter.SCAN_MODE_CONNECTABLE_DISCOVERABLE) {
			Intent discoverableIntent = new Intent(
					BluetoothAdapter.ACTION_REQUEST_DISCOVERABLE);
			discoverableIntent.putExtra(
					BluetoothAdapter.EXTRA_DISCOVERABLE_DURATION, 300);
			startActivity(discoverableIntent);
		}
	}

	// The Handler that gets information back from the BluetoothChatService
	private final Handler mHandler = new Handler() {
		@Override
		public void handleMessage(Message msg) {
			switch (msg.what) {
			case MESSAGE_STATE_CHANGE:
				if (D)
					Log.i(TAG, "MESSAGE_STATE_CHANGE: " + msg.arg1);
				switch (msg.arg1) {
				case BluetoothCardsService.STATE_CONNECTED:
					mStatusTextView.setText(R.string.title_connected_to);
					mStatusTextView.append(mConnectedDeviceName);
					connected = true;

					break;
				case BluetoothCardsService.STATE_CONNECTING:
					mStatusTextView.setText(R.string.title_connecting);
					break;
				case BluetoothCardsService.STATE_LISTEN:
				case BluetoothCardsService.STATE_NONE:
					mStatusTextView.setText(R.string.title_not_connected);
					connected = false;
					break;
				}
				break;
			case MESSAGE_WRITE:
				byte[] writeBuf = (byte[]) msg.obj;
				// construct a string from the buffer
				String writeMessage = new String(writeBuf);
				break;
			case MESSAGE_FILE_NAME:
				byte[] nameBuff = (byte[]) msg.obj;
				String string = new String(nameBuff, 0, msg.arg1);
				file_name = string.substring(0, string.length() - 7);
				Log.i(TAG, "FILE NAME ==== >> " + file_name);
				receivingDialog = new ProgressDialog(ViewAndSendActivity.this);
				receivingDialog.setIndeterminate(true);
				receivingDialog.setMessage("Receiving data...");
				receivingDialog.show();
				break;
			case MESSAGE_COMPLETE:
				Log.i(TAG, "IMAGE TRANSFER COMPLETE");
				Log.i(TAG, "IMAGE COUNT: " + bosImage.toByteArray().length);
				try {
					OutputStream fOut = null;
					byte[] array = bosImage.toByteArray();
					String imgName = file_name + ".jpg";
					File file1 = new File(Environment
							.getExternalStorageDirectory().getAbsolutePath()
							+ "/downloads/bluetooth/",imgName) ;
					file1.createNewFile();
					Bitmap bitmap = BitmapFactory.decodeByteArray(array, 0,
							array.length);
//					fOut = new FileOutputStream(file1);
//					fOut.write(array);
//					fOut.flush();
//					fOut.close();
					if (bitmap != null) {
						fOut = new FileOutputStream(file1);
						bitmap.compress(Bitmap.CompressFormat.PNG, 100, fOut);

						// fOut.write(array);
						fOut.flush();
						fOut.close();
						ContentValues values = new ContentValues(7);
						values.put(Images.Media.TITLE, imgName);
						values.put(Images.Media.DISPLAY_NAME, imgName);
						values.put(Images.Media.DATE_TAKEN,
								new Date().getTime());
						values.put(Images.Media.MIME_TYPE, "image/jpeg");
						values.put(Images.ImageColumns.BUCKET_ID,
								file1.hashCode());
						values.put(Images.ImageColumns.BUCKET_DISPLAY_NAME,
								imgName);
						values.put("_data", Environment
								.getExternalStorageDirectory()
								.getAbsolutePath()
								+ "/downloads/bluetooth/" + imgName);
						ContentResolver contentResolver = getApplicationContext()
								.getContentResolver();
						Uri uri = contentResolver.insert(
								Images.Media.EXTERNAL_CONTENT_URI, values);
						MediaStore.Images.Media.insertImage(
								getContentResolver(), file1.getAbsolutePath(),
								file_name + ".jpg", file_name + ".jpg");

					} else {
						Log.e(TAG, "bitmap is null");
						Toast.makeText(getBaseContext(), "Error occured with bluetooth - please try to send again", Toast.LENGTH_LONG).show();
					}
					bosImage.reset();
					bosImage = null;
					bosImage = new ByteArrayOutputStream();
					if (receivingDialog.isShowing())
						receivingDialog.dismiss();
				} catch (IOException e) {
					// TODO Auto-generated catch block
					e.printStackTrace();
				}
				break;
			case MESSAGE_FILE_READ_COMPLETE:
				Log.i(TAG, "FILE TRANSFER COMPLETE");
				Log.i(TAG, "FILE COUNT: " + bosFile.toByteArray().length);
				try {
					OutputStream fOut = null;
					byte[] array = bosFile.toByteArray();
					File file1 = new File(Environment
							.getExternalStorageDirectory().getAbsolutePath()
							+ "/downloads/bluetooth/", file_name + ".txt");
					file1.createNewFile();
					fOut = new FileOutputStream(file1);
					fOut.write(array);
					fOut.flush();
					fOut.close();
					bosFile.reset();
					bosFile = null;
					bosFile = new ByteArrayOutputStream();
				} catch (IOException e) {
					// TODO Auto-generated catch block
					e.printStackTrace();
				}
				break;
			case MESSAGE_FILE_READ:
				byte[] readBuffFile = (byte[]) msg.obj;
				try {
					bosFile.write(readBuffFile);
					bosFile.flush();
				} catch (IOException e) {
					e.printStackTrace();
				}
				break;
			case MESSAGE_READ:
				byte[] readBuf = (byte[]) msg.obj;
				try {
					bosImage.write(readBuf);
					bosImage.flush();
				} catch (IOException e) {
					e.printStackTrace();
				}
				break;
			case MESSAGE_DEVICE_NAME:
				// save the connected device's name
				mConnectedDeviceName = msg.getData().getString(DEVICE_NAME);
				deviceAddress = msg.getData().getString(DEVICE_ADDRESS);
				Toast.makeText(getApplicationContext(),
						"Connected to: " + mConnectedDeviceName,
						Toast.LENGTH_SHORT).show();
				break;
			case MESSAGE_TOAST:
				Toast.makeText(getApplicationContext(),
						msg.getData().getString(TOAST), Toast.LENGTH_SHORT)
						.show();
				break;
			}
		}
	};

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {
		MenuInflater inflater = getMenuInflater();
		inflater.inflate(R.menu.option_menu, menu);
		return true;
	}

	@Override
	public boolean onOptionsItemSelected(MenuItem item) {
		switch (item.getItemId()) {
		case R.id.scan:
			// Launch the DeviceListActivity to see devices and do scan
			Intent serverIntent = new Intent(this, DeviceListActivity.class);
			startActivityForResult(serverIntent, REQUEST_CONNECT_DEVICE);
			return true;
		case R.id.discoverable:
			// Ensure this device is discoverable by others
			ensureDiscoverable();
			return true;
		}
		return false;
	}

	/**
	 * Sends a message.
	 * 
	 * @param message
	 *            A string of text to send.
	 */
	private void sendMessage(String message) {
		// Check that we're actually connected before trying anything
		if (mCardsService.getState() != BluetoothCardsService.STATE_CONNECTED) {
			Toast.makeText(this, R.string.not_connected, Toast.LENGTH_SHORT)
					.show();
			return;
		}

		// Check that there's actually something to send
		if (message.length() > 0) {
			// Get the message bytes and tell the BluetoothChatService to write
			byte[] send = message.getBytes();
			mCardsService.write(send);

			// Reset out string buffer to zero and clear the edit text field
			mOutStringBuffer.setLength(0);
		}
	}

	/**
	 * Sends a message.
	 * 
	 * @param message
	 *            A string of text to send.
	 */
	private void sendMessage(byte[] message) {
		// Check that we're actually connected before trying anything
		if (mCardsService.getState() != BluetoothCardsService.STATE_CONNECTED) {
			Toast.makeText(this, R.string.not_connected, Toast.LENGTH_SHORT)
					.show();
			return;
		}

		// Check that there's actually something to send
		if (message.length > 0) {
			mCardsService.write(message);

			// Reset out string buffer to zero and clear the edit text field
			mOutStringBuffer.setLength(0);
		}
	}

}
