/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package database;

import java.awt.BorderLayout;
import java.awt.Color;
import java.sql.Connection;
import java.sql.ResultSet;
import java.sql.Statement;
import java.util.logging.Level;
import java.util.logging.Logger;
import javax.swing.ImageIcon;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.JOptionPane;
import javax.swing.JPanel;
import javax.swing.JTextArea;

/**
 *
 * @author Bilal and Arslan
 */
public class AllData extends JFrame implements Runnable {

    private JPanel p;
    private JPanel p1;
    private JPanel p2;
    static JTextArea ta;
    private JLabel l;
    private static ImageIcon image;
    Connection co;
    ClassConnection connect = new ClassConnection();
    static Camps c1;
    
    static Thread t;

    AllData(){
    super("Data of Every Member in "+c1.str+"");
   
    this.setLayout(new BorderLayout());
    p= new JPanel();
    p1= new JPanel();
    p1.setBackground(Color.BLACK);
    image=new ImageIcon("camp2.jpg");
    l=new JLabel(image);
    p1.add(l);


    p2= new JPanel();
    ta= new JTextArea();

    
    ta.setBackground(Color.BLACK);
    ta.setForeground(Color.WHITE);
    
     try {
                co = connect.setConnection(co);
            }
            catch(Exception e)
            {
            }
             try{


                Statement stmt = co.createStatement();
               

                String query = "SELECT * FROM members WHERE family_id in (select family_id from family where camp_id = (select camp_id from camp where location ='"+c1.str+"'))";
                ResultSet rs = stmt.executeQuery(query);
                 
                

                 while(rs.next()){
                
                
               
                ta.setText(ta.getText()+"Family_ID: "+rs.getString(1)+"  Sex: "+rs.getString(2)+"  Member_ID: "+rs.getString(3)+"  Age: "+rs.getString(4)+"  Name: "+rs.getString(5)+"\n");
                
                
                }
                
                co.close();
             }
             catch(Exception ex)
          {
          	JOptionPane.showMessageDialog(null,"GENERAL EXCEPTION", "WARNING!!!",JOptionPane.INFORMATION_MESSAGE);
          	}
      
    p2.add(ta,BorderLayout.CENTER);
    
    p2.setBackground(Color.BLACK);
    p.add(p1,BorderLayout.NORTH);
    p.add(p2,BorderLayout.CENTER);
   t=new Thread(this);
        t.start();
        p.setBackground(Color.BLACK);
    this.add(p);
   // this.setSize(600, 500);
    this.setBounds(600, 0, 600, 800);
    this.setVisible(true);
    


    }

    public void run(){
    
        int x1 =0;
    int y1=0;
   
while(true){
            try {
                t.sleep(30);
                x1++;
                p1.setLocation(x1, y1);
                   if(x1==320){
                         x1=10;}
            }
            
            catch (InterruptedException ex) {
                Logger.getLogger(AllData.class.getName()).log(Level.SEVERE, null, ex);
            }
}


  
    

    }

}
