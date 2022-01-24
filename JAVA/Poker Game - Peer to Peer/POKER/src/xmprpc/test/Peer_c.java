/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package xmprpc.test;
import java.awt.Color;
import java.awt.FlowLayout;
import java.awt.GridLayout;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.awt.event.WindowAdapter;
import java.awt.event.WindowEvent;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.net.MalformedURLException;
import java.util.ArrayList;
import java.util.List;
import java.util.Vector;
import java.util.logging.Level;
import java.util.logging.Logger;
import javax.swing.ImageIcon;
import javax.swing.JButton;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.JOptionPane;
import javax.swing.JPanel;
import javax.swing.JTextField;
import javax.swing.border.TitledBorder;
import javax.xml.soap.Text;
import org.apache.xmlrpc.XmlRpcClient;
import org.apache.xmlrpc.XmlRpcException;
/**
 *
 * @author SmartMindx
 */
public class Peer_c {


   static int my_cash=100;

 /////////////////////////////Voting//////////////////////////////////////////
    
    public static void main (String [] Args) throws FileNotFoundException, MalformedURLException
    {

      
        int online_now=0;
    

     String port= JOptionPane.showInputDialog(null,"Please Enter Your GUID/Identifying Port For Other Peers To Access You\n Available ports=1 to 6");
     final int port_int=Integer.parseInt(port);

     int[] Online_Peers = new int[7];  //For time being, we are only working with 6 peers
     Online_Peers[port_int]= port_int;

     String opt= JOptionPane.showInputDialog(null,"Press 1 to suppose you are selected as voted peer \nPress 2 to suppose you are not selected as voted peer");
     int op=Integer.parseInt(opt);

     if(op==1)
     {
         ////////Goes To the Server Program and handles all peers requests/////////
         Peer_s server = new Peer_s();
         server.startServer(port_int);
        }
 else

     //////////////////////////////////////Client Side///////////////////////////////////
  try{

      for(int j=1; j<=6; j++)
      {

try{

     XmlRpcClient comHandler = getRpcObject("localhost:"+j);
     Vector params = new Vector();
     params.add(1);
     Object o = comHandler.execute("server.im_alive", params);

//     String ret=(String) o;
//     int ret_val=Integer.parseInt(ret);
     int ret_val =Integer.parseInt( o.toString() );

     System.out.println("Result: "+o+"   A Peer is alive to play");


     if(ret_val==1)
     {

          online_now=j;

     }
          }
catch (Exception ex)
    {
    System.out.println("Unable to Find Other Live Peer" + ex.toString());
    }

      }


       JOptionPane.showMessageDialog(null,"Welcome User!! You have following Available Online Rooms with Ports: "+online_now);
//      int[] Online_Rooms = new int[20];
//      for(int i=0;i<20;i++)
//      {
//          if((Online_Peers[i]== i) && (Online_Peers[i]!=0))
//
//          {
//              Online_Rooms[i]=Online_Peers[i];
//
//              JOptionPane.showMessageDialog(null, Online_Rooms[i]);
//              System.out.println(""+Online_Peers[i]);
////              str=":"+ Online_Rooms[i];
//
//          }
//
//      }
//

      String port_joined= JOptionPane.showInputDialog(null,"Please Enter Any Port Given Above To Join A Room");
      int join_port=Integer.parseInt(port_joined);

    ///////////////////GUI//////////////////////////
      JFrame fr=new JFrame("Peer To Peer Poker ");
      fr.setDefaultLookAndFeelDecorated(true);
      fr.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
      fr.setLayout(new GridLayout(3,1));

      JPanel panel1=new JPanel();
      final JPanel panel2=new JPanel();
      JPanel panel3=new JPanel();

      fr.add(panel1);
      fr.add(panel2);
      fr.add(panel3);
      JLabel status=new JLabel("Status :");
      final JTextField status_txt=new JTextField(25);
      status_txt.setBackground(Color.GREEN);
      status_txt.setText("Game Started");
      status_txt.setEditable(false);

      JLabel cash=new JLabel("Your Cash :");
      final JTextField cash_txt=new JTextField(8);
      String my_cash_string = Integer.toString(my_cash);
      cash_txt.setText(my_cash_string);
      cash_txt.setBackground(Color.YELLOW);
      cash_txt.setEditable(false);



      panel1.setLayout(new FlowLayout());
      panel1.add(status);
      panel1.add(status_txt);

      panel1.add(cash);
      panel1.add(cash_txt);


      JLabel l1=new JLabel();
      JLabel l2=new JLabel();
      final JLabel l3=new JLabel();//Separation Label
      final JLabel l4=new JLabel();
      final JLabel l5=new JLabel();
      final JLabel l6=new JLabel();
      final JLabel l7=new JLabel();
      final JLabel l8=new JLabel();
      final JLabel l9=new JLabel();

      panel2.setLayout(new FlowLayout());
      panel2.add(l1);
      panel2.add(l2);
      panel2.add(l3);
      l3.setBackground(Color.red);
      panel2.add(l4);
      panel2.add(l5);
      panel2.add(l6);
      panel2.add(l7);
      panel2.add(l8);
      panel2.add(l9);
panel2.setBackground(Color.black);

      JLabel bottom =new JLabel("Your Cards :");
      JButton ok=new JButton("OK");
      JButton call=new JButton("CALL");
      JButton raise=new JButton("Raise");
      JButton sr=new JButton("Show Results");
      JButton fold=new JButton("FOLD");

      panel3.setLayout(new FlowLayout());
      panel3.add(bottom);
      panel3.add(ok);
      panel3.add(call);
       panel3.add(raise);
       panel3.add(sr);
      panel3.add(fold);




      if(join_port==online_now&&join_port!=0)

      {
          
     final XmlRpcClient comHandler = getRpcObject("localhost:"+join_port);

     fr.setBounds(250, 200, 800, 400);

      JFrame frame1 = new JFrame("P2P Poker");
		frame1.setLocation(150,100);
		frame1.setSize(700, 550);
		frame1.addWindowListener(new WindowAdapter(){
		public void windowClosing(WindowEvent e) {
		System.exit(0);}});
                AnimationBanner banner = new AnimationBanner();
                frame1.setContentPane(banner.getAnimation());
                frame1.setVisible(true);
                AnimationBanner.initAnimation(AnimationBanner.animation);
                banner.getAnimation().start();


Thread t1=null;
t1.sleep(10000);
frame1.dispose();








     fr.setVisible(true);
     final Vector params = new Vector();
     params.add(port_int);

     final Thread t = null;
     try{
     t.sleep(5000);
          }
     catch(InterruptedException f){}
     
     status_txt.setText("10$ Cash Extracted");
     my_cash=my_cash-10;
     my_cash_string = Integer.toString(my_cash);
     cash_txt.setText(my_cash_string);
     cash_txt.setBackground(Color.LIGHT_GRAY);
     comHandler.execute("server.getCash", params);
     

  
     t.sleep(5000);
        

     status_txt.setText("Receiving Your 2 Cards");


    final Object obj= comHandler.execute("server.throw2Cards", params);
    String cards2=(String)obj;

String[] strArray = cards2.split("-");
String part1=strArray[0];
final String part2=strArray[1];



l1.setIcon( new ImageIcon("D:\\Images\\"+part1));
t.sleep(5000);
l2.setIcon( new ImageIcon("D:\\Images\\"+part2));

t.sleep(2000);

status_txt.setText("Press Ok to Receive 2 Table Cards for $20");



 ok.addActionListener(new ActionListener() {

                    @Override
                    public void actionPerformed(ActionEvent e) {
my_cash=my_cash-20;
                        cash_txt.setText(Integer.toString(my_cash));
                        cash_txt.setBackground(Color.CYAN);
                   
                        l3.setIcon( new ImageIcon("D:\\Images\\l3.png"));
                        try {
                            Object obj1 = comHandler.execute("server.throw_tableCards1", params);



                             String cards2=(String)obj1;

String[] strArray = cards2.split("-");
String part1=strArray[0];
final String part2=strArray[1];

System.out.println("Received"+part1 +"And"+part2);

l4.setIcon( new ImageIcon("D:\\Images\\"+part1));
                        try {
                            t.sleep(3000);
                        } catch (InterruptedException ex) {
                            Logger.getLogger(Peer_c.class.getName()).log(Level.SEVERE, null, ex);
                        }
l5.setIcon( new ImageIcon("D:\\Images\\"+part2));





                        } catch (XmlRpcException ex) {
                            Logger.getLogger(Peer_c.class.getName()).log(Level.SEVERE, null, ex);
                        } catch (IOException ex) {
                            Logger.getLogger(Peer_c.class.getName()).log(Level.SEVERE, null, ex);
                        }
                      

                    }
                });

 


     try {
                            t.sleep(4000);
                        } catch (InterruptedException ex) {
                           System.out.println(ex);
                        }
                        status_txt.setText("Note: You Can Press Fold any time to Quit");

                          try {
                            t.sleep(5000);
                        } catch (InterruptedException ex) {
                            Logger.getLogger(Peer_c.class.getName()).log(Level.SEVERE, null, ex);
                        }
status_txt.setText("Press Call button to submit 20$ and Get 2 more Cards");






 call.addActionListener(new ActionListener() {

                    @Override
                    public void actionPerformed(ActionEvent e) {
my_cash=my_cash-20;
                        cash_txt.setText(Integer.toString(my_cash));
                        cash_txt.setBackground(Color.ORANGE);
                        try {
                            t.sleep(2000);
                        } catch (InterruptedException ex) {
                           System.out.println(ex);
                        }
                        status_txt.setText("Note: You Can Press Fold any time to Quit");
                       
                        try {
                            Object obj2 = comHandler.execute("server.throw_tableCards2", params);

                                String cards2=(String)obj2;

String[] strArray = cards2.split("-");
String part1=strArray[0];
final String part2=strArray[1];

l6.setIcon( new ImageIcon("D:\\Images\\"+part1));
l7.setIcon( new ImageIcon("D:\\Images\\"+part2));

                        } catch (XmlRpcException ex) {
                            Logger.getLogger(Peer_c.class.getName()).log(Level.SEVERE, null, ex);
                        } catch (IOException ex) {
                            Logger.getLogger(Peer_c.class.getName()).log(Level.SEVERE, null, ex);
                        }



                        try {
                            t.sleep(3000);
                        } catch (InterruptedException ex) {
                            Logger.getLogger(Peer_c.class.getName()).log(Level.SEVERE, null, ex);
                        }
                        
                        status_txt.setText("TIP: Only Play full game when you have maxinmum matching cards");
                        try {
                            t.sleep(3000);
                        } catch (InterruptedException ex) {
                            Logger.getLogger(Peer_c.class.getName()).log(Level.SEVERE, null, ex);
                        }
                         status_txt.setText("Press Raise to submit $30");


                    }
                });






 raise.addActionListener(new ActionListener() {


                    @Override
                    public void actionPerformed(ActionEvent e) {
my_cash=my_cash-30;
                        cash_txt.setText(Integer.toString(my_cash));
                        cash_txt.setBackground(Color.red);
                    
                        

                        try {
                            Object obj2 = comHandler.execute("server.throw_tableCards3", params);

                                String cards2=(String)obj2;

String[] strArray = cards2.split("-");
String part1=strArray[0];
final String part2=strArray[1];

l8.setIcon( new ImageIcon("D:\\Images\\"+part1));
l9.setIcon( new ImageIcon("D:\\Images\\"+part2));

                        } catch (XmlRpcException ex) {
                            Logger.getLogger(Peer_c.class.getName()).log(Level.SEVERE, null, ex);
                        } catch (IOException ex) {
                            Logger.getLogger(Peer_c.class.getName()).log(Level.SEVERE, null, ex);
                        }

                        try {
                            t.sleep(3000);
                        } catch (InterruptedException ex) {
                            Logger.getLogger(Peer_c.class.getName()).log(Level.SEVERE, null, ex);
                        }
                        status_txt.setText("You can press Fold to Quit");
                       
                        try {
                            t.sleep(2000);
                        } catch (InterruptedException ex) {
                            Logger.getLogger(Peer_c.class.getName()).log(Level.SEVERE, null, ex);
                        }
                        status_txt.setText("Press Show Result For Results!!!");

                        ////////////////////////////////////////////////
                       





                    }
                });



 
 sr.addActionListener(new ActionListener() {

                    @Override
                    public void actionPerformed(ActionEvent e) {
                        
                        
                        
                        
                        
                         
 
  try {
     
      
       try {
                            t.sleep(2000);
                        } catch (InterruptedException ex) {
                            Logger.getLogger(Peer_c.class.getName()).log(Level.SEVERE, null, ex);
                        }
                        status_txt.setText("Calculating Results!!!");
                            
                            Object obj3 = comHandler.execute("server.who_won", params);

                              String res=(String)obj3;
                              int result=Integer.parseInt(res);
                              System.out.println(result+"Won");
                              
                              JFrame f=new JFrame("STATUS  :::::: NUST P2P Poker!!!!!!!!!!!!!");
                              JPanel p=new JPanel();
                              JLabel l=new JLabel();
                              p.add(l);
                              f.add(p);
                              f.setDefaultLookAndFeelDecorated(true);
                              f.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
                              f.setBounds(325, 225, 550, 350);

                              if(result==port_int){
                                  
                                  JLabel l1=new JLabel();
                                  JPanel p1=new JPanel();
                                  p1.add(l1);
                                  f.add(p1);
                                  f.setLayout(new GridLayout(2,1,1,3));

                                  l.setIcon( new ImageIcon("D:\\Images\\you-win.gif"));
                                  l1.setIcon( new ImageIcon("D:\\Images\\win.gif"));
                                  f.setVisible(true);
                              

                              }

                               else{

                                  l.setIcon( new ImageIcon("D:\\Images\\Lost.jpg"));
                                  f.setVisible(true);


                               }


                        } catch (XmlRpcException ex) {
                            Logger.getLogger(Peer_c.class.getName()).log(Level.SEVERE, null, ex);
                        } catch (IOException ex) {
                            Logger.getLogger(Peer_c.class.getName()).log(Level.SEVERE, null, ex);
                        }




                        
                        
                        
                        
                    }
                });



                try {
                                Thread.currentThread().sleep(8000);
                            } catch (InterruptedException ex) {
                                Logger.getLogger(Peer_c.class.getName()).log(Level.SEVERE, null, ex);
                            }
                              try {
                                Thread.currentThread().sleep(500);
                                panel2.setBackground(Color.red);
                            } catch (InterruptedException ex) {
                                Logger.getLogger(Peer_c.class.getName()).log(Level.SEVERE, null, ex);
                            }

                               try {
                                Thread.currentThread().sleep(500);
                                panel2.setBackground(Color.GREEN);
                            } catch (InterruptedException ex) {
                                Logger.getLogger(Peer_c.class.getName()).log(Level.SEVERE, null, ex);
                            }
                               try {
                                Thread.currentThread().sleep(500);
                                panel2.setBackground(Color.blue);
                            } catch (InterruptedException ex) {
                                Logger.getLogger(Peer_c.class.getName()).log(Level.SEVERE, null, ex);
                            }
                               try {
                                Thread.currentThread().sleep(500);
                                panel2.setBackground(Color.BLUE);
                            } catch (InterruptedException ex) {
                                Logger.getLogger(Peer_c.class.getName()).log(Level.SEVERE, null, ex);
                            }

                 try {
                                Thread.currentThread().sleep(500);
                            } catch (InterruptedException ex) {
                                Logger.getLogger(Peer_c.class.getName()).log(Level.SEVERE, null, ex);
                            }
                              try {
                                Thread.currentThread().sleep(500);
                                panel2.setBackground(Color.red);
                            } catch (InterruptedException ex) {
                                Logger.getLogger(Peer_c.class.getName()).log(Level.SEVERE, null, ex);
                            }

                               try {
                                Thread.currentThread().sleep(500);
                                panel2.setBackground(Color.GREEN);
                            } catch (InterruptedException ex) {
                                Logger.getLogger(Peer_c.class.getName()).log(Level.SEVERE, null, ex);
                            }
                               try {
                                Thread.currentThread().sleep(500);
                                panel2.setBackground(Color.blue);
                            } catch (InterruptedException ex) {
                                Logger.getLogger(Peer_c.class.getName()).log(Level.SEVERE, null, ex);
                            }
                               try {
                                Thread.currentThread().sleep(500);
                                panel2.setBackground(Color.BLUE);
                            } catch (InterruptedException ex) {
                                Logger.getLogger(Peer_c.class.getName()).log(Level.SEVERE, null, ex);
                            }

                 try {
                                Thread.currentThread().sleep(500);
                            } catch (InterruptedException ex) {
                                Logger.getLogger(Peer_c.class.getName()).log(Level.SEVERE, null, ex);
                            }
                              try {
                                Thread.currentThread().sleep(500);
                                panel2.setBackground(Color.red);
                            } catch (InterruptedException ex) {
                                Logger.getLogger(Peer_c.class.getName()).log(Level.SEVERE, null, ex);
                            }

                               try {
                                Thread.currentThread().sleep(500);
                                panel2.setBackground(Color.GREEN);
                            } catch (InterruptedException ex) {
                                Logger.getLogger(Peer_c.class.getName()).log(Level.SEVERE, null, ex);
                            }
                               try {
                                Thread.currentThread().sleep(500);
                                panel2.setBackground(Color.blue);
                            } catch (InterruptedException ex) {
                                Logger.getLogger(Peer_c.class.getName()).log(Level.SEVERE, null, ex);
                            }
                               try {
                                Thread.currentThread().sleep(500);
                                panel2.setBackground(Color.BLUE);
                            } catch (InterruptedException ex) {
                                Logger.getLogger(Peer_c.class.getName()).log(Level.SEVERE, null, ex);
                            }

                 try {
                                Thread.currentThread().sleep(500);
                            } catch (InterruptedException ex) {
                                Logger.getLogger(Peer_c.class.getName()).log(Level.SEVERE, null, ex);
                            }
                              try {
                                Thread.currentThread().sleep(500);
                                panel2.setBackground(Color.red);
                            } catch (InterruptedException ex) {
                                Logger.getLogger(Peer_c.class.getName()).log(Level.SEVERE, null, ex);
                            }

                               try {
                                Thread.currentThread().sleep(500);
                                panel2.setBackground(Color.GREEN);
                            } catch (InterruptedException ex) {
                                Logger.getLogger(Peer_c.class.getName()).log(Level.SEVERE, null, ex);
                            }
                               try {
                                Thread.currentThread().sleep(500);
                                panel2.setBackground(Color.blue);
                            } catch (InterruptedException ex) {
                                Logger.getLogger(Peer_c.class.getName()).log(Level.SEVERE, null, ex);
                            }
                               try {
                                Thread.currentThread().sleep(500);
                                panel2.setBackground(Color.BLUE);
                            } catch (InterruptedException ex) {
                                Logger.getLogger(Peer_c.class.getName()).log(Level.SEVERE, null, ex);
                            }

                 try {
                                Thread.currentThread().sleep(500);
                            } catch (InterruptedException ex) {
                                Logger.getLogger(Peer_c.class.getName()).log(Level.SEVERE, null, ex);
                            }
                              try {
                                Thread.currentThread().sleep(500);
                                panel2.setBackground(Color.red);
                            } catch (InterruptedException ex) {
                                Logger.getLogger(Peer_c.class.getName()).log(Level.SEVERE, null, ex);
                            }

                               try {
                                Thread.currentThread().sleep(500);
                                panel2.setBackground(Color.GREEN);
                            } catch (InterruptedException ex) {
                                Logger.getLogger(Peer_c.class.getName()).log(Level.SEVERE, null, ex);
                            }
                               try {
                                Thread.currentThread().sleep(500);
                                panel2.setBackground(Color.blue);
                            } catch (InterruptedException ex) {
                                Logger.getLogger(Peer_c.class.getName()).log(Level.SEVERE, null, ex);
                            }
                               try {
                                Thread.currentThread().sleep(500);
                                panel2.setBackground(Color.BLUE);
                            } catch (InterruptedException ex) {
                                Logger.getLogger(Peer_c.class.getName()).log(Level.SEVERE, null, ex);
                            }

                 try {
                                Thread.currentThread().sleep(500);
                            } catch (InterruptedException ex) {
                                Logger.getLogger(Peer_c.class.getName()).log(Level.SEVERE, null, ex);
                            }
                              try {
                                Thread.currentThread().sleep(500);
                                panel2.setBackground(Color.red);
                            } catch (InterruptedException ex) {
                                Logger.getLogger(Peer_c.class.getName()).log(Level.SEVERE, null, ex);
                            }

                               try {
                                Thread.currentThread().sleep(500);
                                panel2.setBackground(Color.GREEN);
                            } catch (InterruptedException ex) {
                                Logger.getLogger(Peer_c.class.getName()).log(Level.SEVERE, null, ex);
                            }
                               try {
                                Thread.currentThread().sleep(500);
                                panel2.setBackground(Color.blue);
                            } catch (InterruptedException ex) {
                                Logger.getLogger(Peer_c.class.getName()).log(Level.SEVERE, null, ex);
                            }
                               try {
                                Thread.currentThread().sleep(500);
                                panel2.setBackground(Color.BLUE);
                            } catch (InterruptedException ex) {
                                Logger.getLogger(Peer_c.class.getName()).log(Level.SEVERE, null, ex);
                            }




 fold.addActionListener(new ActionListener() {

                    @Override
                    public void actionPerformed(ActionEvent e) {

                        final JFrame f=new JFrame("EXIT??");
                        JButton yes=new JButton("YES");
                        JButton no=new JButton("NO");
                        f.setLayout(new FlowLayout());
                        f.add(yes);
                        f.add(no);
                        f.setBounds(520, 350, 200, 80);


                        f.setVisible(true);




 yes.addActionListener(new ActionListener() {

                    @Override
                    public void actionPerformed(ActionEvent e) {
                        System.exit(1);

     }});



      no.addActionListener(new ActionListener() {

                    @Override
                    public void actionPerformed(ActionEvent e) {
                       // JOptionPane.showMessageDialog(null, "Wajjo");
                        f.dispose();

     }});





                    }
                });





      }



 else

 {


          JOptionPane.showMessageDialog(null, "Illegal Port");

 }


    }catch (Exception ex)
    {
    System.err.println("XMLRPCClient.getRpcObject: " + ex.toString());
    }
    }


    static XmlRpcClient getRpcObject(String entryPoint) {
        try {

            XmlRpcClient comHandler = new XmlRpcClient("http://" + entryPoint);
            return comHandler;
        } catch (Exception exception) {
            System.err.println("XMLRPCClient.getRpcObject: " + exception.toString());
           return null;
        }

    }

    
}

