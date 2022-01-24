package com.smartmindx.visitingcardsender;

import android.content.Context;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteOpenHelper;

public class DatabaseHandler extends SQLiteOpenHelper {

	// Database Version
	private static final int DATABASE_VERSION = 1;
	// Database Name
	private static final String DATABASE_NAME = "information";
	// Contacts table name
	private static final String TABLE_NAME = "info";

	// Contacts Table Columns names
	private static final String KEY_ID = "id";
	private static final String KEY_NAME = "name";
	private static final String KEY_EMAIL = "email";
	private static final String KEY_PHONE = "phone";
	private static final String KEY_FACEBOOK = "facebook";
	private static final String KEY_ADDRESS = "address";
	private static final String KEY_IMAGE = "cardimage";

	public DatabaseHandler(Context context) {
		super(context, DATABASE_NAME, null, DATABASE_VERSION);
	}

	// Creating Tables

	public void onCreate(SQLiteDatabase db) {
		// TODO Auto-generated method stub
		String CREATE_TABLE = "CREATE TABLE " + TABLE_NAME + "(" + KEY_ID
				+ " INTEGER PRIMARY KEY," + KEY_NAME + " TEXT," + KEY_PHONE
				+ " INT," + KEY_EMAIL + " TEXT," + KEY_FACEBOOK + " TEXT,"
				+ KEY_ADDRESS + " TEXT," + KEY_IMAGE + " BLOB" + ");";
		db.execSQL(CREATE_TABLE);

	}

	public void onUpgrade(SQLiteDatabase db, int oldVersion, int newVersion) {
		// TODO Auto-generated method stub
		db.execSQL("DROP TABLE IF EXISTS " + TABLE_NAME);
		// Create tables again
		onCreate(db);

	}

}