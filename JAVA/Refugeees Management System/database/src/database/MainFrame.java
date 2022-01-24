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
import javax.swing.BorderFactory;
import javax.swing.ImageIcon;
import javax.swing.JButton;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.JPanel;
import javax.swing.border.Border;
import javax.swing.border.TitledBorder;



/**
 *
 * @author ARSLAN and Bilal
 */
public class MainFrame extends JFrame implements ActionListener{

	private JPanel p;
        private JPanel p1;
        private JPanel p2;
        
     
        private JButton b1;
        private JButton b2;
        private JButton b3;
        private JButton b4;
        private JButton b5;
        private JButton b6;
    
        private JLabel lblpic1;
        private JLabel lblpic2;
        private JLabel lblpic3;
        private JLabel lblpic4;
        private JLabel lblpic5;
        private JLabel lblpic6;
        private JLabel lblpic7;
        private JLabel lblpic8;
        private JLabel lblpic9;
        
        private static ImageIcon image1;
        private static ImageIcon image2;
        private static ImageIcon image3;
        private static ImageIcon image4;
        private static ImageIcon image5;
        private static ImageIcon image6;
        private static ImageIcon image7;
        private static ImageIcon image8;
        private static ImageIcon image9;

       // Border.createRaisedBevelBorder();
      // public static TitledBorder createTitledBorder();


        MainFrame(){
        super("Charity sees the need, not the cause.");
        p = new JPanel();
        p.setBackground(Color.BLACK);
        this.setLayout(new BorderLayout());
     
        p1 = new JPanel();
        p2 = new JPanel();
        TitledBorder brdr= new TitledBorder("OPTIONS");
         brdr.setTitleJustification(TitledBorder.CENTER);
    brdr.setTitleColor(Color.LIGHT_GRAY);
        p1.setBorder(brdr);
       // p1.setBorder(new TitledBorder("Options"));
     
        b1 = new JButton("Show all IDPs data");
        b1.addActionListener(this);
        
        b2 = new JButton("Add Family");
        b2.addActionListener(this);
        b3 = new JButton("Remove Family");
        b3.addActionListener(this);
        b4 = new JButton("Donate Aid");
        b4.addActionListener(this);
        b5 = new JButton("Add Member");
        b5.addActionListener(this);
        b6 = new JButton("Remove Member");
        b6.addActionListener(this);
        p1.setLayout(new GridLayout(2,3));
       //  p1.setLayout(new FlowLayout());
        
        p1.add(b1);
        p1.add(b2);
        p1.add(b3);
        p1.add(b4);
        p1.add(b5);
        p1.add(b6);
       p1.setBackground(Color.BLACK);
       p2.setLayout(new GridLayout(3,3));
       p2.setBackground(Color.BLACK);
 
        image1=new ImageIcon("pakistan.jpg");
        lblpic1= new JLabel(image1);
        image2=new ImageIcon("little girl1.jpg");
        lblpic2= new JLabel(image2);
        image3=new ImageIcon("camp1.jpg");
        lblpic3= new JLabel(image3);
        image4=new ImageIcon("woman.jpg");
        lblpic4= new JLabel(image4);
        image5=new ImageIcon("Capture.PNG");
        lblpic5= new JLabel(image5);
        image6=new ImageIcon("unicef.jpg");
        lblpic6= new JLabel(image6);
        image7=new ImageIcon("edhi.jpg");
        lblpic7= new JLabel(image7);
        image8=new ImageIcon("desperation.jpg");
        lblpic8= new JLabel(image8);
        image9=new ImageIcon("tire.jpg");
        lblpic9= new JLabel(image9);



        p2.add(lblpic1);
        p2.add(lblpic2);
        p2.add(lblpic3);
        p2.add(lblpic4);
        p2.add(lblpic5);
        p2.add(lblpic6);
        p2.add(lblpic7);
        p2.add(lblpic8);
        p2.add(lblpic9);

    
   
       // p2.setBackground(Color.BLACK);
        p.add(p1,BorderLayout.NORTH);
        p.add(p2,BorderLayout.CENTER);
        
     //   p.add(p3,BorderLayout.SOUTH);
        

        this.add(p);
        this.setVisible(true);
        this.setSize(950, 750);
        }

    public void actionPerformed(ActionEvent e) {
       // throw new UnsupportedOperationException("Not supported yet.");
    if(e.getActionCommand().equals("Show all IDPs data")){
    AllData AD = new AllData();

    }

    if(e.getActionCommand().equals("Add Family")){
    AddFamily AF = new AddFamily();

    }

    if(e.getActionCommand().equals("Remove Family")){
    RemoveFamily RF=new RemoveFamily();

    }

    if(e.getActionCommand().equals("Donate Aid")){
     DonaitAid DN=new DonaitAid();

    }

    if(e.getActionCommand().equals("Add Member")){
      AddMember AM=new AddMember();

    }

    if(e.getActionCommand().equals("Remove Member")){
RemoveMember Rm=new RemoveMember();

    }

    }
}
