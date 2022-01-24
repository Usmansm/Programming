/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package gui;

/**
 *
 * @author Usman
 */
import java.awt.*;
import java.awt.Graphics2D;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.awt.geom.Ellipse2D;
import java.awt.geom.Rectangle2D;
import java.util.logging.Level;
import java.util.logging.Logger;
import javax.swing.*;
public class Move extends JPanel implements ActionListener{
   public Timer t=new Timer(15,this); //Timer acting like thread
    public double x,y,stop,Vx=2,Vy=2; //positon x and y and velocity of process
    public double w,h; //width and height
    public int c1,c2,c3;
      int col; //three integers
    

   public  Color bck; //color

    Move(int z,int wid,int hit,int chnge_cl)
    {
        w=wid;     // width
        h=hit;    //height
stop=z; //at loction where it should stop
col=chnge_cl;

if (Math.random()!=0)     // generating different colors
            {
c1=     (int) (Math.random()*255);
c2=     (int) (Math.random() * 255);
c3=     (int) (Math.random() * 255);
        }
else
{
    c1=19;
    c2=76;
    c3=56;
}
    }
    public void paintComponent(Graphics g)
    {


      super.paintComponent(g);
      Graphics2D g2=(Graphics2D) g;
     Rectangle2D cir=new Rectangle2D.Double(100,y,w,h);
 bck=new Color(c1,c2,c3); //change color

g2.setColor(bck);

      g2.fill(cir);
      
      g2.drawString("Process "+ h*2 +" KB", 100, (int)y);

      t.start();
    }
 
    public void actionPerformed(ActionEvent e)
    {

       
        if(y<stop)
        {
             x+=Vx;
        
        
           y+=Vy;
            try {
                Thread.currentThread().sleep(30);
            } catch (InterruptedException ex) {
                Logger.getLogger(Move.class.getName()).log(Level.SEVERE, null, ex);
            }
        repaint();
        }
        else
        {
            
            Main.but[col].setBackground(bck);
Main.but[col].setForeground(Color.white);


        }


}



    

}
