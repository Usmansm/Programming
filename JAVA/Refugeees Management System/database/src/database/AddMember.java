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
public class AddMember extends JFrame implements ActionListener, Runnable{
private JPanel p1;
private JPanel p2;
private JPanel panel1;
private JPanel panel2;
private JLabel l;
private JLabel l1;
private JLabel l2;
private JLabel l3;
private JLabel l4;
private JLabel l5;
private JTextField txf_id;
private JTextField txsex;
private JTextField txid_num;
private JTextField txage;
private JTextField txname;
private JButton AddBtn;
private JButton ResetBtn;
private JButton ExitBtn;
private ImageIcon image;
static Camps c;
String f_id;
String sex;
String id_num;
String age;
String name;
static Thread t;
Connection conn;
ClassConnection connect = new ClassConnection();

AddMember(){

super("Add Member in " + c.str);
this.setLayout(new BorderLayout());
 t=new Thread(this);
        t.start();
    p1= new JPanel();
    p1.setBackground(Color.BLACK);
    image=new ImageIcon("llittle girl2.jpg");
    l=new JLabel(image);
    p1.add(l);

    p2 = new JPanel();

     panel1 = new JPanel();
    	panel1.setLayout(new GridLayout(5,2));

	    l1 = new JLabel(" Family ID :");
	    l2 = new JLabel(" Gender :");
	    l3 = new JLabel(" Id_num :");
            l4 = new JLabel(" Age :");
            l5 = new JLabel(" Name :");
	   txf_id = new JTextField(20);     //firsttext field

       	txf_id.addActionListener(this);         //registring 1st tf

	    txsex = new JTextField(20);        //2nd
            txsex.addActionListener(this);     //2nd reg
            txid_num = new JTextField(20);     //3rd
	    txid_num.addActionListener(this);   //reg 3rd
             txage = new JTextField(20);          // 4th
	     txage.addActionListener(this);       //reg 4th
             txname = new JTextField(20);
              txname.addActionListener(this);     //reg 5
             panel1.add(l1);           //labl 1
	    panel1.add(txf_id);
	    panel1.add(l2);
	    panel1.add(txsex);
             panel1.add(l3);
	    panel1.add(txid_num);
              panel1.add(l4);
              panel1.add(txage);
              panel1.add(l5);
              panel1.add(txname);

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
       // throw new UnsupportedOperationException("Not supported yet.");

        if(e.getActionCommand().equals("Reset")){

ResetRecord();
}

if(e.getActionCommand().equals("Exit")){
System.exit(0);
}


				if(e.getActionCommand().equals("Add"))
				 {




		f_id = txf_id.getText().trim();
		sex = txsex.getText().trim();
		id_num = txid_num.getText().trim();
                age=txage.getText().trim();
                name=txname.getText().trim();
		 try {
                conn = connect.setConnection(conn);
            }
            catch(Exception E)
            {
            }
          try {
                    Statement stmt = (Statement) conn.createStatement();

                    String qtoe = "INSERT INTO members VALUES ('"+f_id +"','"+sex +"','"+id_num +"','"+age+"','"+name+"')";
                    int rs=stmt.executeUpdate(qtoe);


                                 if ( rs == 1 )
                                 {
                                                JOptionPane.showMessageDialog(null,"Record Added in Members Table", "Information",JOptionPane.INFORMATION_MESSAGE);
                           			
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
	   txsex.setText("");
	    txid_num.setText("");
           txage.setText("");
           txname.setText("");

}

    public void run() {
        //throw new UnsupportedOperationException("Not supported yet.");
     int x1 =0;
    int y1=0;

while(true){
            try {
                t.sleep(30);
                x1++;
                p1.setLocation(x1, y1);
                   if(x1==300){
                         x1=10;}
            }

            catch (InterruptedException ex) {

            }
}

    }
    }





