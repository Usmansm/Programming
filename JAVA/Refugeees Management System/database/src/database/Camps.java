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
import javax.swing.ImageIcon;
import javax.swing.JFrame;
import javax.swing.JButton;
import javax.swing.JLabel;
import javax.swing.JPanel;
import javax.swing.border.TitledBorder;

/**
 *
 * @author ARSLAN ansd Bilal
 */
public class Camps extends JFrame implements ActionListener {
    private JPanel p;
    private JPanel p1;
    private JPanel p2;
    private JPanel p3;
    private JButton b1;
    private JButton b2;
    private JButton b3;
    private JButton b4;
    private JButton b5;
    private JButton b6;
    private JButton b7;
    private JLabel l;
    private JLabel l1;
    private JLabel l2;
    private JLabel l3;
    private JLabel l4;
    private JLabel l5;
    private JLabel l6;
    private static ImageIcon image1;
    private static ImageIcon image2;
    private static ImageIcon image3;
    private static ImageIcon image4;
    private static ImageIcon image5;
    private static ImageIcon image6;
    static String str;
    static String id;

    Camps(){
    super("DataBase for IDP Camps");
    p=new JPanel();
    p.setLayout(new BorderLayout());
    p1=new JPanel();
    l= new JLabel("If you can't feed a hundred people, then feed just one.  ~Mother Teresa");
    l.setForeground(Color.LIGHT_GRAY);
    p1.add(l);
    p1.setBackground(Color.BLACK);
    p2=new JPanel();
    TitledBorder Br = new TitledBorder("CAMPS");
    Br.setTitleJustification(TitledBorder.CENTER);
    Br.setTitleColor(Color.LIGHT_GRAY);
    p2.setBorder(Br);
    b1= new JButton("SWAT");
    b1.addActionListener(this);
    b2= new JButton("CHARSADA");
    b2.addActionListener(this);
    b3= new JButton("LARKANA");
    b3.addActionListener(this);
     b4= new JButton("BAJOUR");
    b4.addActionListener(this);
     b5= new JButton("NAUSHERA");
    b5.addActionListener(this);
     b6= new JButton("HASILPUR");
    b6.addActionListener(this);
     b7= new JButton("DG.KHAN");
    b7.addActionListener(this);
    p2.add(b1,FlowLayout.LEFT);
    p2.add(b2,FlowLayout.CENTER);
    p2.add(b6,FlowLayout.RIGHT);
    p2.add(b5,FlowLayout.RIGHT);
    p2.add(b3,FlowLayout.RIGHT);
    p2.add(b4,FlowLayout.RIGHT);
    p2.add(b7,FlowLayout.RIGHT);
    p2.setBackground(Color.BLACK);
    p3=new JPanel();
    image1=new ImageIcon("camp.jpg");
    l1=new JLabel(image1);
     image2=new ImageIcon("little girl.jpg");
    l2=new JLabel(image2);
    image3=new ImageIcon("mardan.jpg");
    l3=new JLabel(image3);
    image4=new ImageIcon("need.jpg");
    l4=new JLabel(image4);
    image5=new ImageIcon("night.jpg");
    l5=new JLabel(image5);
    image6=new ImageIcon("swat.jpg");
    l6=new JLabel(image6);
    p3.setLayout(new GridLayout(2,3));
    p3.add(l1);
    p3.add(l2);
    p3.add(l3);
    p3.add(l4);
    p3.add(l5);
    p3.add(l6);
    p3.setBackground(Color.BLACK);
    p.add(p1,BorderLayout.NORTH);
    p.add(p2,BorderLayout.SOUTH);
    p.add(p3,BorderLayout.CENTER);
    this.add(p);
    }

    public void actionPerformed(ActionEvent e) {
       // throw new UnsupportedOperationException("Not supported yet.");
        if(e.getActionCommand().equals("SWAT")){
        
            str="SWAT";
            id="55101";
            LoginFrame frame = new LoginFrame();
        
        this.setVisible(false);
       // this.dispose();
        }

        if(e.getActionCommand().equals("CHARSADA")){
        
            str="CHARSADA";
            id="55107";
            LoginFrame frame = new LoginFrame();
        
        this.setVisible(false);
       // this.dispose();
        }

    if(e.getActionCommand().equals("LARKANA")){
        str="LARKANA";
        id="55106";
        LoginFrame frame = new LoginFrame();
        
        this.setVisible(false);
       // this.dispose();
        }

         if(e.getActionCommand().equals("BAJOUR")){
        str="BAJOUR";
        id="55102";
             LoginFrame frame = new LoginFrame();
        
        this.setVisible(false);
       // this.dispose();
        }

        if(e.getActionCommand().equals("NAUSHERA")){
        str="NAUSHERA";
        id="55104";
            LoginFrame frame = new LoginFrame();
        
        this.setVisible(false);
       // this.dispose();
        }

         if(e.getActionCommand().equals("HASILPUR")){
        str="HASILPUR";
        id="55105";
             LoginFrame frame = new LoginFrame();
        
        this.setVisible(false);
      //  this.dispose();
        }

         if(e.getActionCommand().equals("DG.KHAN")){
        str="DG.KHAN";
        id="55103";
             LoginFrame frame = new LoginFrame();
        
        this.setVisible(false);
      //  this.dispose();
        }

    }
}
