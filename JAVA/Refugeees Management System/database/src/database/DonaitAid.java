/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package database;

/**
 *
 * @author Bilal
 */
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */



import java.awt.BorderLayout;
import java.awt.Color;
import java.awt.FlowLayout;
import java.awt.GridLayout;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
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
public class DonaitAid extends JFrame implements ActionListener, Runnable{
private JPanel p1;
private JPanel p2;
private JPanel panel1;
private JPanel panel2;
private JLabel l;
private JLabel l1;
private JLabel l2;
private JLabel l3;
private JLabel l4;
private JTextField txa_snum;
private JTextField txa_type;
private JTextField txa;
private JTextField txreg_id;
private JButton AddBtn;
private JButton ResetBtn;
private JButton ExitBtn;
private ImageIcon image;
static Camps c;
String A_snum;
String A_type;
String A_amount;
String Reg_id;
static Thread t;
Connection conn;

    ClassConnection connect = new ClassConnection();

DonaitAid(){

super("Donate To " + c.str);
this.setLayout(new BorderLayout());
 t=new Thread(this);
        t.start();
    p1= new JPanel();
    p1.setBackground(Color.BLACK);
    image=new ImageIcon("donation.jpg");
    l=new JLabel(image);
    p1.add(l);

    p2 = new JPanel();

     panel1 = new JPanel();
    	panel1.setLayout(new GridLayout(4,2));

	    l1 = new JLabel(" Aid Serial number :");
	    l2 = new JLabel(" Aid Type :");
	    l3 = new JLabel(" Amount of Aid :");
            l4=new JLabel(" NGO Registration_ID  :");
	    txa_snum = new JTextField(20);

       	txa_snum.addActionListener(this);

	    txa_type = new JTextField(20);
            txa_type.addActionListener(this);
            txa = new JTextField(20);
	    txa.addActionListener(this);
            txreg_id=new JTextField(20);
            txa.addActionListener(this);
	    panel1.add(l1);
	    panel1.add(txa_snum);
   
	    panel1.add(l2);
	    panel1.add(txa_type);

	    panel1.add(l3);
	    panel1.add(txa);

	    panel1.add(l4);
	    panel1.add(txreg_id);

	    panel1.setOpaque(true);

        panel2 = new JPanel();
   		panel2.setLayout(new FlowLayout());
   		AddBtn = new JButton("Donate");
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
       // throw new UnsupportedOperationException("Not supported yet.");

        if(e.getActionCommand().equals("Reset")){

ResetRecord();
}

if(e.getActionCommand().equals("Exit")){
System.exit(0);
}


        if(e.getActionCommand().equals("Donate"))
				 {




		A_snum= txa_snum.getText().trim();
		A_type =txa_type.getText().trim();
		A_amount = txa.getText().trim();
               Reg_id=txreg_id.getText().trim();

		 try {
                conn = connect.setConnection(conn);
            }
            catch(Exception E)
            {
            }
          try {
                    Statement stmt = (Statement) conn.createStatement();

                    String qtoe = "INSERT INTO aid VALUES ('"+A_snum +"','"+A_type +"','"+A_amount +"','"+Reg_id+"')";
                    int rs=stmt.executeUpdate(qtoe);


                                 if ( rs == 1 )
                                 {
                           			JOptionPane.showMessageDialog(null,"Record Added in Aid Table", "Information",JOptionPane.INFORMATION_MESSAGE);
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
		txa_snum.setText("");
	   txa_type.setText("");
	    txa.setText("");
           txreg_id.setText("");


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
                   if(x1==280){
                         x1=10;}
            }

            catch (InterruptedException ex) {

            }
}

    }
    }



/**
 *
 * @author Bilal
 */

