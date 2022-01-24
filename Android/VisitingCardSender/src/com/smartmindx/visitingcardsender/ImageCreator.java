package com.smartmindx.visitingcardsender;

import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.Canvas;
import android.graphics.Paint;

public class ImageCreator {
	String Name = "", Email = "", Phone = "", Facebook = "", Address = "";

	public ImageCreator(String Name, String Email, String Phone,
			String facebook, String address) {
		this.Name = Name;
		this.Email = Email;
		this.Phone = Phone;
		this.Facebook = facebook;
		this.Address = address;
	}

	public Bitmap CreateBitmap() {

		float height = 400;
		float width = 600;

		BitmapFactory.Options bmpFactoryOptions = new BitmapFactory.Options();
		bmpFactoryOptions.inJustDecodeBounds = true;
		Bitmap dest = BitmapFactory.decodeFile(
				infoAddActivity.selectedImagePath, bmpFactoryOptions);

		int heightRatio = (int) Math.ceil(bmpFactoryOptions.outHeight
				/ (float) height);
		int widthRatio = (int) Math.ceil(bmpFactoryOptions.outWidth
				/ (float) width);

		if (heightRatio > 1 || widthRatio > 1) {
			if (heightRatio > widthRatio) {
				bmpFactoryOptions.inSampleSize = heightRatio;
			} else {
				bmpFactoryOptions.inSampleSize = widthRatio;
			}
		}

		bmpFactoryOptions.inJustDecodeBounds = false;
		dest = BitmapFactory.decodeFile(infoAddActivity.selectedImagePath,
				bmpFactoryOptions);
		Bitmap mutableBitmap = dest.copy(Bitmap.Config.ARGB_8888, true);
		
		Canvas cs = new Canvas(mutableBitmap);
		Paint tPaint = new Paint();
		
		

		tPaint.setTextSize(30);
		cs.drawBitmap(mutableBitmap, 0f, 0f, null);

		cs.drawText("Name : ", 50, 50, tPaint);
		cs.drawText(this.Name, 170, 50, tPaint);

		cs.drawText("Email : ", 55, 100, tPaint);
		cs.drawText(this.Email, 170, 100, tPaint);

		cs.drawText("Mobile : ", 37, 150, tPaint);
		cs.drawText(this.Phone, 170, 150, tPaint);

		cs.drawText("Facebook : ", 0, 200, tPaint);
		cs.drawText(this.Facebook, 170, 200, tPaint);

		cs.drawText("Address : ", 20, 250, tPaint);
		cs.drawText(this.Address, 170, 250, tPaint);

		return mutableBitmap;

	}
}
