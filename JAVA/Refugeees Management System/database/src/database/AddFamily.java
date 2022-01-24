/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package database;

import java.awt.BorderLayout;
import java.awt.Color;
import java.awt.FlowLayout;
import java.awt.GridLayout;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
//import java.beans.Statement;
//import java.sql.Connection;
import java.util.logging.Level;
import java.util.logging.Logger;
import javax.swing.ImageIcon;
import javax.swing.JButton;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.JPanel;
import javax.swing.JTextField;
import java.sql.*;
import javax.swing.JOptionPane;
/**
 *
 * @author Bilal and Arslan
 */
public class AddFamily extends JFrame implements ActionListener, Runnable{

    private JPanel p;
    private JPanel p1;
private JPanel p2;
private JPanel panel1;
private JPanel panel2;
private JLabel l;
private JLabel l1;
private JLabel l2;
private JLabel l3;
private JTextField txf_id;
private JTextField txa_by;
private JTextField txc_id;
private JButton AddBtn;
private JButton ResetBtn;
private JButton ExitBtn;
private ImageIcon image;
static Camps c;
String F_id;                           // Strings in which we have to get text
String A_type;                         // ...
String C_id;                           // .....
Connection conn;
static Thread t;

    ClassConnection connect = new ClassConnection();
AddFamily(){

super("Add Family in " + c.str);
this.setLayout(new BorderLayout());
 t=new Thread(this);
        t.start();

    p1= new JPanel();
    p1.setBackground(Color.BLACK);
    image=new ImageIcon("line.jpg");
    l=new JLabel(image);
    p1.add(l);

    p2 = new JPanel();

     panel1 = new JPanel();
    	panel1.setLayout(new GridLayout(3,2));

	    l1 = new JLabel(" Family ID :");
	    l2 = new JLabel(" Affected BY :");
	    l3 = new JLabel(" Camp ID :");

	    txf_id = new JTextField(20);

       	txf_id.addActionListener(this);

	   txa_by = new JTextField(20);
            txa_by.addActionListener(this);
            txc_id = new JTextField(20);
            C_id =c.id;
            txc_id.setText(C_id);
	    txc_id.addActionListener(this);

	    panel1.add(l1);
	    panel1.add( txf_id);

	    panel1.add(l2);
	    panel1.add(txa_by);

	    panel1.add(l3);
	    panel1.add(txc_id);


	    panel1.setOpaque(true);

        panel2 = new JPanel();
   		panel2.setLayout(new FlowLayout());
   		AddBtn = new JButton("Add");
        ResetBtn = new JButton("Reset");
		ExitBtn = new JButton("Exit");


   		panel2.add(AddBtn);
   		AddBtn.addActionListener(this);
   		panel2.add(ResetBtn);
   		ResetBtn.addActionListener(this);
        panel2.add(ExitBtn);
        ExitBtn.addActionListener(this);
        panel2.setOpaque(true);
        p2.setBackground(Color.BLACK);
p2.add(p1,BorderLayout.NORTH);
p2.add(panel1,BorderLayout.CENTER);
p2.add(panel2,BorderLayout.SOUTH);

this.add(p2);
this.setBounds(600, 50, 600, 400);
    this.setVisible(true);

}

    public void actionPerformed(ActionEvent e) {

if(e.getActionCommand().equals("Reset")){

ResetRecord();
}

if(e.getActionCommand().equals("Exit")){
System.exit(0);
}

				if(e.getActionCommand().equals("Add"))
				 {

			F_id = "";
			A_type = "";
			

                        
                        


		F_id = txf_id.getText().trim();
		A_type = txa_by.getText().trim();
		

		 try {
                conn = connect.setConnection(conn);
            }
            catch(Exception E)
            {
            }
          try {
                    Statement stmt = (Statement) conn.createStatement();
                    
                    String qtoe = "INSERT INTO family VALUES ('"+F_id +"','"+A_type +"','"+C_id +"' )";
                    int rs=stmt.executeUpdate(qtoe);
                   
                      
                                 if ( rs == 1 )
                                 {
                           			JOptionPane.showMessageDialog(null,"Record Added in Family Table", "Information",JOptionPane.INFORMATION_MESSAGE);
                           			ResetRecord();


                                 }
                                 else {
                                 		//dialogmessage = "Failed To Insert";
                    JOptionPane.showMessageDialog(null, "Failed To Insert in DataBase",
                            "WARNING!!",JOptionPane.WARNING_MESSAGE);


                                 }
                      conn.close();
                } catch (Exception ex) {

          	JOptionPane.showMessageDialog(null,"GENERAL EXCEPTION", "WARNING!!!",JOptionPane.INFORMATION_MESSAGE);
                }

}


    }



private void ResetRecord()
    {
		txf_id.setText("");
	   txa_by.setText("");
	    txc_id.setText("");
	     }

    public void run() {
       // throw new UnsupportedOperationException("Not supported yet.");
      int x1 =0;
    int y1=0;

while(true){
            try {
                t.sleep(30);
                x1++;
                p1.setLocation(x1, y1);
                   if(x1==260){
                         x1=10;}
            }

            catch (InterruptedException ex) {
                Logger.getLogger(AllData.class.getName()).log(Level.SEVERE, null, ex);
            }
}


    }
}
