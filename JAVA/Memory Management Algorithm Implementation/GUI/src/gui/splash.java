
package gui;

import java.awt.Color;
import java.awt.Dimension;
import java.awt.Font;
import java.awt.Graphics2D;
import java.awt.SplashScreen;
import java.awt.geom.Rectangle2D;

// For the splash screen we had taken help from java docs
public class splash
{
    public static SplashScreen mySplash;                   // instantiated by JVM we use it to get graphics
    public static Graphics2D splashGraphics;               // graphics context for overlay of the splash image
    public static Rectangle2D.Double splashTextArea;       // area where we draw the text
    public static Rectangle2D.Double splashProgressArea;   // area where we draw the progress bar
    public static Font font;                               // used to draw our text

  
    public  void appInit()
    {
        for (int i = 1; i <= 10; i++)
        {   // pretend we have 10 things to do
            int pctDone = i * 10;       // this is about the only time I could calculate rather than guess progress
            //splashText("Loading Please wait");     // tell the user what initialization task is being done
            splashProgress(pctDone);            // give them an idea how much we have completed
            try
            {
                Thread.sleep(500);             // wait a second
            }
            catch (InterruptedException ex)
            {
                break;
            }
        }
    }

    /**
     * Prepare the global variables for the other splash functions
     */
    public void splashInit()
    {
        // the splash screen object is created by the JVM, if it is displaying a splash image

        mySplash = SplashScreen.getSplashScreen();
        // if there are any problems displaying the splash image
        // the call to getSplashScreen will returned null

        if (mySplash != null)
        {
            // get the size of the image now being displayed
            Dimension ssDim = mySplash.getSize();
            int height = ssDim.height;
            int width = ssDim.width;

            // stake out some area for our status information
            splashTextArea = new Rectangle2D.Double(12, height*0.88, width * .45, 32);
            splashProgressArea = new Rectangle2D.Double(width * .55, height*.92, width*.4, 12 );

            // create the Graphics environment for drawing status info
            splashGraphics = mySplash.createGraphics();
            font = new Font("Dialog", Font.BOLD, 14);
            splashGraphics.setFont(font);

            // initialize the status info
            splashText("Starting");
            splashProgress(0);
        }
    }
    /**
     * Display text in status area of Splash.  Note: no validation it will fit.
     - text to be displayed
     */
    public  void splashText(String str)
    {
        if (mySplash != null && mySplash.isVisible())
        {   // important to check here so no other methods need to know if there
            // really is a Splash being displayed

            // erase the last status text
            splashGraphics.setPaint(Color.LIGHT_GRAY);
           // splashGraphics.fill(splashTextArea);

            // draw the text
            splashGraphics.setPaint(Color.BLACK);
            splashGraphics.drawString(str, (int)(splashTextArea.getX() + 10),(int)(splashTextArea.getY() + 15));

            // make sure it's displayed
            mySplash.update();
        }
    }
    /**
     * Display a (very) basic progress bar
  
     */
    public void splashProgress(int pct)
    {
        if (mySplash != null && mySplash.isVisible())
        {

            // Note: 3 colors are used here to demonstrate steps
            // erase the old one
            //splashGraphics.setPaint(Color.LIGHT_GRAY);
           // splashGraphics.fill(splashProgressArea);

            // draw an outline
            //splashGraphics.setPaint(Color.BLUE);
            //splashGraphics.draw(splashProgressArea);

            // Calculate the width corresponding to the correct percentage
            int x = (int) splashProgressArea.getMinX();
            int y = (int) splashProgressArea.getMinY()+25;
            int wid = (int) splashProgressArea.getWidth();
            int hgt = (int) splashProgressArea.getHeight();

            int doneWidth = Math.round(pct*wid/100.f);
            doneWidth = Math.max(0, Math.min(doneWidth, wid-1));  // limit 0-width

            // fill the done part one pixel smaller than the outline
            splashGraphics.setPaint(Color.white);
            splashGraphics.fillRect(x+1, y+1, doneWidth+1, hgt-1);

            // make sure it's displayed
            mySplash.update();
        }
    }

}
