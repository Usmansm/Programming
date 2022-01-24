/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package database;

import java.awt.Color;
import java.awt.Component;
import java.awt.Container;
import java.awt.Dimension;
import java.awt.FlowLayout;
import java.awt.GridLayout;
import java.awt.Toolkit;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.awt.event.WindowAdapter;
import java.awt.event.WindowEvent;
import java.sql.Connection;
import java.sql.ResultSet;
import java.sql.Statement;
import java.util.Date;
import javax.swing.JButton;
import javax.swing.JFrame;
import javax.swing.JPanel;
import javax.swing.JLabel;
import javax.swing.JOptionPane;
import javax.swing.JPasswordField;
import javax.swing.JTextField;
/**
 *
 * @author ARSLAN and Bilal
 */
public class LoginFrame extends JFrame implements ActionListener {

    static JFrame frame;
   
    private static JPanel panel1;
    private static JPanel panel2;
    private static JPanel panel3;
    private JButton loginBtn;
    private JButton exitBtn;
    int dialogtype = JOptionPane.PLAIN_MESSAGE;
    String dialogmessage;
    String dialogs = "Every little help matters!!";
    private JLabel nameLbl;
    private JLabel userLbl;
    private JLabel passwordLbl;
    private static JTextField userTxt;
    private static JPasswordField passwordTxt;

    public String loginname;
    public String loginpass;

    // class Veriables
    ClassConnection connect = new ClassConnection();
    //Connection variable

    Connection conn;
    	Dimension screen 	= 	Toolkit.getDefaultToolkit().getScreenSize();

    static Camps c;
    public static String st;

    public LoginFrame()
    {

    st=c.str;

   panel1 = new JPanel();
   panel1.setLayout(new FlowLayout());
   nameLbl = new JLabel("Welcome to the Camp for Internally Displaced People in "+st);

   panel2 = new JPanel();
   panel2.setLayout(new GridLayout(2,2));
   userLbl = new JLabel("NGO NAME :");
   userTxt = new JTextField(20);

   passwordLbl = new JLabel("REG_ID :");

   passwordTxt = new JPasswordField(20);

   panel3 = new JPanel();
   panel3.setLayout(new FlowLayout());

   loginBtn = new JButton("Login");

   loginBtn.addActionListener(this);
   exitBtn = new JButton("Exit");

   exitBtn.addActionListener(this);
	panel1.add(nameLbl);
	panel1.setOpaque(true);
    panel2.add(userLbl);
	panel2.add(userTxt);
	panel2.add(passwordLbl);
	panel2.add(passwordTxt);
	panel2.setOpaque(true);
   	panel3.add(loginBtn);
	panel3.add(exitBtn);
	panel3.setOpaque(true);
        panel1.setBackground(Color.lightGray);
        panel2.setBackground(Color.lightGray);
        panel3.setBackground(Color.lightGray);
	frame = new JFrame("NGO Login");
        
       frame.setSize(400,200);

	Container pane = frame.getContentPane();

    pane.setLayout(new GridLayout(3,1));
	pane.add(panel1);
	pane.add(panel2);
	pane.add(panel3);
        
	frame.setLocation((screen.width - 500)/2,((screen.height-350)/2));
    frame.setVisible(true);
    frame.addWindowListener(new WindowAdapter()
        {
            public void windowClosing(WindowEvent e)
            {
                System.exit(0);
            }
        });



    }








    public void actionPerformed(ActionEvent e) {
        //throw new UnsupportedOperationException("Not supported yet.");
         //Object source = event.getSource();
        if(e.getActionCommand().equals("Login"))
        {
           login();


        }
        else if(e.getActionCommand().equals("Exit"))
        {
            		System.exit(0);
        }
    }
        
        public void login()
        {
        	loginname = userTxt.getText().trim();
           	loginpass = passwordTxt.getText().trim();


            try {
                conn = connect.setConnection(conn);
            }
            catch(Exception e)
            {
            }
            try{


                Statement stmt = conn.createStatement();

                String query = "SELECT * FROM ngo WHERE n_name='" + loginname +
                        "'AND registration_id='"+loginpass+"' AND registration_id in( SELECT registration_id FROM camp)";
                ResultSet rs = stmt.executeQuery(query);
                boolean recordfound = rs.next();
                if (recordfound  || 1==1)
                {

                	dialogmessage = "Welcome - " +loginname;
                    dialogtype = JOptionPane.INFORMATION_MESSAGE;
                    JOptionPane.showMessageDialog((Component)null, dialogmessage, dialogs, dialogtype);
                    userTxt.setText("");
                    passwordTxt.setText("");
                    frame.setVisible(false);
                    frame.dispose();
                   
                    MainFrame menu = new MainFrame();
                    


                }
                else
                {
                	dialogmessage = "Login Failed!";
                    JOptionPane.showMessageDialog(null, "INVALID ID OR PASSWORD!",
                            "WARNING!!",JOptionPane.WARNING_MESSAGE);

                    userTxt.setText("");
                    passwordTxt.setText("");
                }
                conn.close();
        }
        catch(Exception ex)
          {
          	JOptionPane.showMessageDialog(null,"GENERAL EXCEPTION", "WARNING!!!",JOptionPane.INFORMATION_MESSAGE);
          	}
    }

}
