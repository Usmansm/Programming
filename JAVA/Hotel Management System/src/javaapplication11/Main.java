package javaapplication11;

import java.io.IOException;
import java.sql.*;
import java.awt.BorderLayout;
import java.awt.Color;
import java.awt.GridLayout;
import java.awt.event.KeyAdapter;
import java.awt.event.KeyEvent;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.awt.event.MouseAdapter;
import java.util.logging.Level;
import java.util.logging.Logger;
import javax.swing.JButton;
import javax.swing.JComboBox;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.JMenu;
import javax.swing.JMenuBar;
import javax.swing.JMenuItem;
import javax.swing.JOptionPane;
import javax.swing.JPanel;
import javax.swing.JTextField;
import java.sql.Driver;

public class Main implements ActionListener{
    static Main obj;
    static Connection connection=null;
    static Statement stmt = null;
    static JFrame cr=new JFrame("Create Room");
   static JFrame fc=new JFrame("Find Customer");
    static JFrame cm=new JFrame("Create Motel");
    static JFrame op=new JFrame("Cancel Reservation");
   static JFrame f=new JFrame("Customer Reservation Form");
    static JButton b1=new JButton("Make Reservation");

static JButton b2=new JButton("Cancel Reservetion");
static JButton b3=new JButton("Find Customer");
static JButton b5=new JButton("Go to Web");
static JButton b4=new JButton("Quit");
static JButton con_room=new JButton("Create");
static JButton con_motel=new JButton("Create");
static JMenuItem res=new JMenuItem("Make Reservation");
static String cid_f,fname_f,lname_f,motel_name,resrv_roomid,cat_textr,motel_namer,motel_room,reserv_f,cidc,fnamec,lnamec,reservc,cid_r,fname_r,lname_r,addres_r,lnum_r,mnum_r,email_r,motel_r,reserv_r,arrvldt_r,deprtrdt_r,price_r,rid_crroom,price_crroom,mid_crmotel,phn_crroom,fax_crroom,loc_crroom;
   static  JMenuItem cancel=new JMenuItem("Cancel Reservation");
JMenuItem f_cust=new JMenuItem("Find Customer");

JMenuItem web=new JMenuItem("Web Support");
JMenuItem about=new JMenuItem("About");

JMenuItem c_room=new JMenuItem("Create Room");
JMenuItem c_motel=new JMenuItem("Create Motel");
    static JMenuItem quit=new JMenuItem("Quit");
    static String arr[]={"Naran","Kaghan","Murree","Ayubia","Sakardu","Kalam","Malamjabba"};
    static JButton can=new JButton("Cancel reservation");
    static JButton very=new JButton("Verify");
    static JButton fin_c=new JButton("Find Customer");
    
    Main() throws IOException
    {
         splash open=new splash();
        open.splashInit();
        open.appInit();
                // splashInit();           // initialize splash overlay drawing parameters
       // appInit();              // simulate what an application would do before starting
        if (splash.mySplash != null)   // check if we really had a spash screen
           splash. mySplash.close();
        JFrame main_fr = new JFrame("Pakistan Tourism Development Coporation");
        JPanel main_pan=new JPanel(new BorderLayout());
        main_pan.setBackground(Color.WHITE);
        main_fr.setBounds(450, 200, 325, 450);
     JMenuBar menu=new JMenuBar();
     menu.setSize(10, 10);
     JMenu file=new JMenu("File");
     JMenu search=new JMenu("Search");
     JMenu create=new JMenu("Create");
     JMenu help=new JMenu("Help");
     create.add(c_room);
     create.add(c_motel);


JPanel but_pan=new JPanel(new GridLayout(5,1,5,15));

but_pan.add(b1);
b1.addActionListener(this);
but_pan.add(b2);
b2.addActionListener(this);
but_pan.add(b3);
b3.addActionListener(this);
but_pan.add(b5);
b5.addActionListener(this);
but_pan.add(b4);
b4.addActionListener(this);
JLabel status=new JLabel("Ready");
status.setPreferredSize(null);
      search.add(f_cust);
c_room.addActionListener(this);
c_motel.addActionListener(this);
cancel.addActionListener(this);
quit.addActionListener(this);
f_cust.addActionListener(this);
      file.add(res);
      res.addActionListener(this);
     final String[] cmd = new String[4];
cmd[0] = "cmd.exe";
cmd[1] = "/C";
cmd[2] = "start";
cmd[3] = "http://www.tourism.gov.pk";
      b5.addActionListener(new ActionListener()
      {
public void actionPerformed(ActionEvent e)
          {
                try {
                    Process pc = Runtime.getRuntime().exec(cmd);
                } catch (IOException ex) {
                    Logger.getLogger(Main.class.getName()).log(Level.SEVERE, null, ex);
                }

            }
        });
         web.addActionListener(new ActionListener()
      {
public void actionPerformed(ActionEvent e)
          {
                try {
                    Process pc = Runtime.getRuntime().exec(cmd);
                } catch (IOException ex) {
                    Logger.getLogger(Main.class.getName()).log(Level.SEVERE, null, ex);
                }

            }
        });

     file.add(cancel);
     file.add(quit);
     help.add(web);
     help.add(about);
     menu.add(file);
     menu.add(search);
     menu.add(create);
     menu.add(help);
     main_pan.add(status,BorderLayout.SOUTH);
     main_pan.add(but_pan,BorderLayout.CENTER);
     main_pan.add(menu,BorderLayout.NORTH);
        main_fr.add(main_pan);

        main_fr.setVisible(true);
        main_fr.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
    }
    public static void main(String[] args) throws IOException{

      

        obj=new Main();
        try
        {
            Class.forName("org.postgresql.Driver");
            connection = DriverManager.getConnection("jdbc:postgresql://localhost:5432/postgres","localhost", "pakistan");
            System.out.print("hh");
            
        }
        catch(Exception x)
        {
            System.out.println("exception"+x);
        }
    }

     
    public void insertQuery(String a, String b, String c, String d, String e, String f, String g, String h, String i, String j, String k, String l)
    {
        try
        {
            ResultSet ts=stmt.executeQuery("select * from customer");
            while(ts.next())
            {
                System.out.print(ts.getString(1)+"\t");
                System.out.print(ts.getString(2)+"\t"+"\n");
                System.out.print(ts.getString(3)+"\t"+"\n");
                System.out.print(ts.getString(4)+"\n");
            }
        }
        catch(Exception x)
        {
            System.out.println("exception"+x);
        }
    }
    public void cancelQuery(String a, String b, String c, String d)
    {
        try
        {
            ResultSet ts=stmt.executeQuery("select * from customer");
            while(ts.next())
            {
                System.out.print(ts.getString(1)+"\t");
                System.out.print(ts.getString(2)+"\t"+"\n");
                System.out.print(ts.getString(3)+"\t"+"\n");
                System.out.print(ts.getString(4)+"\n");
            }
        }
        catch(Exception e)
        {
            System.out.println("exception"+e);
        }
    }
    public void findQuery(String a, String b, String c, String d)
    {
        try
        {
            stmt=  connection.createStatement();
            ResultSet ts=stmt.executeQuery("select * from customer c, hotels h, reservation r where c.c_id="+a+" and c.cfname="+b+" and c.clname="+c+" and r.arrival_date="+d+" and r.h_id=h.h_id and r.c_id=c.c_id");
            while(ts.next())
            {
                System.out.print(ts.getString(1)+"\t");
                System.out.print(ts.getString(2)+"\t"+"\n");
                System.out.print(ts.getString(3)+"\t"+"\n");
                System.out.print(ts.getString(4)+"\n");
            }
        }
        catch(Exception e)
        {
            System.out.println("exception"+e);
        }
    }
    static void find_c()
    {
        fc.setBounds(450,200,450,350);
        fc.setDefaultCloseOperation(JFrame.DISPOSE_ON_CLOSE);

        JPanel pan=new JPanel(new GridLayout(5,1,15,15));
        JLabel cid=new JLabel("Customer ID");
        JTextField cidtext=new JTextField();
        JLabel cfname=new JLabel("First Name");
        JTextField ftext=new JTextField();
        JLabel clname=new JLabel("Last Name");
        JTextField ltext=new JTextField();
        JLabel res_date=new JLabel("Reservation Date");
        JTextField res_text=new JTextField();
        JLabel emp=new JLabel();
        pan.add(cid);
        pan.add(cidtext);
        pan.add(cfname);
        pan.add(ftext);
        pan.add(clname);
        pan.add(ltext);
        pan.add(res_date);
        pan.add(res_text);
        pan.add(emp);
        fin_c.addActionListener(obj);
        pan.add(fin_c);
        fc.add(pan);
cidtext.addKeyListener(new KeyAdapter() {
public void keyReleased(KeyEvent e) {
JTextField textField = (JTextField) e.getSource();
cid_f = textField.getText();
System.out.println(cid_f);
}});
ftext.addKeyListener(new KeyAdapter() {
public void keyReleased(KeyEvent e) {
JTextField textField = (JTextField) e.getSource();
fname_f = textField.getText();
System.out.println(fname_f);
}});
ltext.addKeyListener(new KeyAdapter() {
public void keyReleased(KeyEvent e) {
JTextField textField = (JTextField) e.getSource();
lname_f = textField.getText();
System.out.println(lname_f);
}});
res_text.addKeyListener(new KeyAdapter() {
public void keyReleased(KeyEvent e) {
JTextField textField = (JTextField) e.getSource();
reserv_f = textField.getText();
System.out.println(reserv_f);
}});
fc.setVisible(true);
    }

     void  cancel_option()
     {

        op.setBounds(450,200,450,350);
        op.setDefaultCloseOperation(JFrame.DISPOSE_ON_CLOSE);
        op.setVisible(true);
        JPanel pan=new JPanel(new GridLayout(5,1,15,15));
        JLabel cid=new JLabel("Customer ID");
    JTextField cidtext=new JTextField();
    JLabel cfname=new JLabel("First Name");
      JTextField ftext=new JTextField();
    JLabel clname=new JLabel("Last Name");
    JTextField ltext=new JTextField();
    JLabel res_date=new JLabel("Reservation Date");
    JTextField res_text=new JTextField();

    can.addActionListener(obj);
    JLabel emp=new JLabel();
    pan.add(cid);
    pan.add(cidtext);
    pan.add(cfname);
    pan.add(ftext);
    pan.add(clname);
    pan.add(ltext);
    pan.add(res_date);
    pan.add(res_text);
    pan.add(emp);
    pan.add(can);
    op.add(pan);
    cidtext.addKeyListener(new KeyAdapter() {
    public void keyReleased(KeyEvent e) {
    JTextField textField = (JTextField) e.getSource();
    cidc = textField.getText();
    System.out.println(cidc);
    }});
    ftext.addKeyListener(new KeyAdapter() {
    public void keyReleased(KeyEvent e) {
    JTextField textField = (JTextField) e.getSource();
    fnamec = textField.getText();
    System.out.println(fnamec);
    }});
    ltext.addKeyListener(new KeyAdapter() {
    public void keyReleased(KeyEvent e) {
    JTextField textField = (JTextField) e.getSource();
    lnamec = textField.getText();
    System.out.println(lnamec);
    }});
    res_text.addKeyListener(new KeyAdapter() {
    public void keyReleased(KeyEvent e) {
    JTextField textField = (JTextField) e.getSource();
    reservc = textField.getText();
    System.out.println(reservc);
    }});
    }
     void res_form()
    {

        f.setBounds(450,200,700,425);
    JPanel res_pan=new JPanel(new GridLayout(10,2,10,20));

    JLabel cid=new JLabel("Customer ID");
    JTextField cidtext=new JTextField();
    JLabel cfname=new JLabel("First Name");
    JTextField ftext=new JTextField();
    JLabel clname=new JLabel("Last Name");
    JTextField ltext=new JTextField();
    JLabel cadres=new JLabel("Address");
    JTextField addtext=new JTextField();
    JLabel land_num=new JLabel("Landline Number");
    JTextField landnumtext=new JTextField();
       JLabel mob_num=new JLabel("Mobile NUmber");
    JTextField mobnumtext=new JTextField();
       JLabel email=new JLabel("Email Address");
    JTextField emailtext=new JTextField();
    JLabel visit=new JLabel("Visiting to");
    JComboBox visitto=new JComboBox(arr);
    JLabel motid=new JLabel("Motel ID");
    JTextField mottext=new JTextField();
    JLabel mot_name=new JLabel("Motel Name");
    JComboBox motname=new JComboBox();
     JLabel roomid=new JLabel("Room ID");
    JTextField roomtext=new JTextField();
    JLabel res_date=new JLabel("Reservation Date");
    JTextField res_text=new JTextField();
     JLabel arr_date=new JLabel("Arrival Date");
    JTextField arr_text=new JTextField();
     JLabel dep_date=new JLabel("Departure Date");
    JTextField dep_text=new JTextField();
    JLabel tot_text=new JLabel("Total Rooms");
    JComboBox tot_room=new JComboBox();
    JLabel price=new JLabel("Price");
    JLabel cat_name=new JLabel("Category ");
    JComboBox cat_box=new JComboBox();
    JTextField pr_text=new JTextField();
    res_pan.add(cid);
    res_pan.add(cidtext);
     res_pan.add(cfname);
 res_pan.add(ftext);
  res_pan.add(clname);
   res_pan.add(ltext);
    res_pan.add(cadres);
 res_pan.add(addtext);
  res_pan.add(land_num);
  res_pan.add(landnumtext);
   res_pan.add(mob_num);
    res_pan.add(mobnumtext);
     res_pan.add(email);
      res_pan.add(emailtext);
      res_pan.add(visit);
       res_pan.add(visitto);
        res_pan.add(motid);

         res_pan.add(mottext);
         res_pan.add(roomid);
         res_pan.add(roomtext);
         res_pan.add(mot_name);
          res_pan.add(motname);
           res_pan.add(res_date);
            res_pan.add(res_text);
             res_pan.add(arr_date);
              res_pan.add(arr_text);
               res_pan.add(dep_date);
                res_pan.add(dep_text);
                res_pan.add(tot_text);
                 res_pan.add(tot_room);
                  res_pan.add(price);
                   res_pan.add(pr_text);
                   res_pan.add(cat_name);
                   res_pan.add(cat_box);
                   very.addActionListener(obj);
                   res_pan.add(very);
       f.add(res_pan);
cidtext.addKeyListener(new KeyAdapter() {
public void keyReleased(KeyEvent e) {
JTextField textField = (JTextField) e.getSource();
cid_r = textField.getText();
System.out.println(cid_r);
}});
roomtext.addKeyListener(new KeyAdapter() {
public void keyReleased(KeyEvent e) {
JTextField textField = (JTextField) e.getSource();
resrv_roomid = textField.getText();
System.out.println(resrv_roomid);
}});
ftext.addKeyListener(new KeyAdapter() {
public void keyReleased(KeyEvent e) {
JTextField textField = (JTextField) e.getSource();
fname_r = textField.getText();
System.out.println(fname_r);
}});
ltext.addKeyListener(new KeyAdapter() {
public void keyReleased(KeyEvent e) {
JTextField textField = (JTextField) e.getSource();
lname_r = textField.getText();
System.out.println(lname_r);
}});
addtext.addKeyListener(new KeyAdapter() {
public void keyReleased(KeyEvent e) {
JTextField textField = (JTextField) e.getSource();
addres_r = textField.getText();
System.out.println(addres_r);
}});
landnumtext.addKeyListener(new KeyAdapter() {
public void keyReleased(KeyEvent e) {
JTextField textField = (JTextField) e.getSource();
lnum_r = textField.getText();
System.out.println(lnum_r);
}});
mobnumtext.addKeyListener(new KeyAdapter() {
public void keyReleased(KeyEvent e) {
JTextField textField = (JTextField) e.getSource();
mnum_r = textField.getText();
System.out.println(mnum_r);
}});
emailtext.addKeyListener(new KeyAdapter() {
public void keyReleased(KeyEvent e) {
JTextField textField = (JTextField) e.getSource();
email_r = textField.getText();
System.out.println(email_r);
}});
mottext.addKeyListener(new KeyAdapter() {
public void keyReleased(KeyEvent e) {
JTextField textField = (JTextField) e.getSource();
motel_r = textField.getText();
System.out.println(motel_r);
}});
res_text.addKeyListener(new KeyAdapter() {
public void keyReleased(KeyEvent e) {
JTextField textField = (JTextField) e.getSource();
reserv_r = textField.getText();
System.out.println(reserv_r);
}});
arr_text.addKeyListener(new KeyAdapter() {
public void keyReleased(KeyEvent e) {
JTextField textField = (JTextField) e.getSource();
arrvldt_r = textField.getText();
System.out.println(arrvldt_r);
}});
dep_text.addKeyListener(new KeyAdapter() {
public void keyReleased(KeyEvent e) {
JTextField textField = (JTextField) e.getSource();
deprtrdt_r = textField.getText();
System.out.println(deprtrdt_r);
}});
pr_text.addKeyListener(new KeyAdapter() {
public void keyReleased(KeyEvent e) {
JTextField textField = (JTextField) e.getSource();
price_r = textField.getText();
System.out.println(price_r);
}});
       f.setVisible(true);
       f.setDefaultCloseOperation(JFrame.DISPOSE_ON_CLOSE);
    }
   static  void cr_room()
    {
          cr.setBounds(450,200,450,350);
        cr.setDefaultCloseOperation(JFrame.DISPOSE_ON_CLOSE);
        cr.setVisible(true);
        JPanel pan=new JPanel(new GridLayout(5,1,15,15));
        JLabel rid=new JLabel("Room ID");
    JTextField ridtext=new JTextField();
    JLabel mot_name=new JLabel("Motel ID");
    JTextField motel_text=new JTextField();
  JLabel cat_name=new JLabel("Category ");
  JTextField cat_text=new JTextField();
    
    JLabel price=new JLabel("Price");
    JTextField price_text=new JTextField();


    JLabel emp=new JLabel();
    pan.add(rid);
    pan.add(ridtext);
    pan.add(mot_name);
    pan.add(motel_text);
    pan.add(cat_name);
    pan.add(cat_text);
    pan.add(price);
    pan.add(price_text);

  pan.add(emp);
  pan.add(con_room);
ridtext.addKeyListener(new KeyAdapter() {
public void keyReleased(KeyEvent e) {
JTextField textField = (JTextField) e.getSource();
rid_crroom = textField.getText();
System.out.println(rid_crroom);
}});
cat_text.addKeyListener(new KeyAdapter() {
public void keyReleased(KeyEvent e) {
JTextField textField = (JTextField) e.getSource();
cat_textr = textField.getText();
System.out.println(cat_textr);
}});
motel_text.addKeyListener(new KeyAdapter() {
public void keyReleased(KeyEvent e) {
JTextField textField = (JTextField) e.getSource();
motel_namer = textField.getText();
System.out.println(motel_namer);
}});
price_text.addKeyListener(new KeyAdapter() {
public void keyReleased(KeyEvent e) {
JTextField textField = (JTextField) e.getSource();
price_crroom = textField.getText();
System.out.println(price_crroom);
}});
cr.add(pan);
     }
   static void cr_motel()
    {
        cm.setBounds(450,200,475,350);
       cm.setDefaultCloseOperation(JFrame.DISPOSE_ON_CLOSE);

        JPanel pan_m=new JPanel(new GridLayout(7,1,12,12));
        JLabel mid=new JLabel("Motel ID");
    JTextField midtext=new JTextField();
    JLabel mot_name=new JLabel("Motel Name");
    JTextField motname=new JTextField();
   
    JLabel loc=new JLabel("Location");
    JTextField loc_text_text=new JTextField();
 JLabel tot_text=new JLabel("Total Rooms");
 JTextField tot_room=new JTextField();
   
     JLabel phn_land=new JLabel("LandLine Number");
    JTextField phn_text=new JTextField();
  JLabel fax=new JLabel("Fax Number");
    JTextField fax_text=new JTextField();



    JLabel emp=new JLabel();
    pan_m.add(mid);
    pan_m.add(midtext);
    pan_m.add(mot_name);
    pan_m.add(motname);
    pan_m.add(loc);
    pan_m.add(loc_text_text);
    pan_m.add(tot_text);
    pan_m.add(tot_room);
    pan_m.add(phn_land);
    pan_m.add(phn_text);
    pan_m.add(fax);
    pan_m.add(fax_text);
    pan_m.add(emp);
    pan_m.add(con_motel);
midtext.addKeyListener(new KeyAdapter() {
public void keyReleased(KeyEvent e) {
JTextField textField = (JTextField) e.getSource();
mid_crmotel = textField.getText();
System.out.println(mid_crmotel);
}});
motname.addKeyListener(new KeyAdapter() {
public void keyReleased(KeyEvent e) {
JTextField textField = (JTextField) e.getSource();
motel_name = textField.getText();
System.out.println(motel_name);
}});
tot_room.addKeyListener(new KeyAdapter() {
public void keyReleased(KeyEvent e) {
JTextField textField = (JTextField) e.getSource();
motel_room = textField.getText();
System.out.println(motel_room);
}});

phn_text.addKeyListener(new KeyAdapter() {
public void keyReleased(KeyEvent e) {
JTextField textField = (JTextField) e.getSource();
phn_crroom = textField.getText();
System.out.println(phn_crroom);
}});
fax_text.addKeyListener(new KeyAdapter() {
public void keyReleased(KeyEvent e) {
JTextField textField = (JTextField) e.getSource();
fax_crroom = textField.getText();
System.out.println(fax_crroom);
}});
loc_text_text.addKeyListener(new KeyAdapter() {
public void keyReleased(KeyEvent e) {
JTextField textField = (JTextField) e.getSource();
loc_crroom = textField.getText();
System.out.println(loc_crroom);
}});
cm.add(pan_m);
cm.setVisible(true);
   }
   public void actionPerformed(ActionEvent e)
    {
        if(e.getSource()==can)
        {
            cancelQuery(cidc, fnamec, lnamec, reservc);
            JOptionPane.showMessageDialog(null,"Reservetion Canelled Successfully","PTDC",JOptionPane.INFORMATION_MESSAGE);
            op.dispose();
        }
        else if(e.getSource() == b1 || e.getSource() == res)
        {

            res_form();
        }
        else if(e.getSource() == b2 || e.getSource()==cancel)
        {
            cancel_option();
        }
        else if(e.getSource()==c_room )
        {
            cr_room();

        }
        else if(e.getSource()==c_motel)
        {
            cr_motel();
        }
        else if(e.getSource()==f_cust || e.getSource()==b3)
        {
            find_c();
        }
        else if(e.getSource()==quit || e.getSource()==b4)
        {
            System.exit(1);
        }
        else if(e.getSource()==very)
        {
            insertQuery(cid_r, fname_r, lname_r, addres_r, lnum_r, mnum_r, email_r, motel_r, reserv_r, arrvldt_r, deprtrdt_r, price_r);
            JOptionPane.showMessageDialog(null,"Reservetion Done Successfully","PTDC",JOptionPane.INFORMATION_MESSAGE);
            f.dispose();
        }
        else if(e.getSource()==fin_c)
        {
            findQuery(cidc, fnamec, lnamec, reservc);
            JOptionPane.showMessageDialog(null,"Required Data Found","PTDC",JOptionPane.INFORMATION_MESSAGE);
        }
    }



}
