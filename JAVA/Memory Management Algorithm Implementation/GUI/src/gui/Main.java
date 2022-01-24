/*
* To change this template, choose Tools | Templates
* and open the template in the editor.
*/

package gui;

import java.awt.BorderLayout;
import java.awt.Color;
import java.awt.Dimension;
import java.awt.FlowLayout;
import java.awt.Font;
import java.awt.Graphics;
import java.awt.Graphics2D;
import java.awt.GridLayout;
import java.awt.LayoutManager;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.awt.event.MouseAdapter;
import java.awt.event.MouseEvent;
import java.awt.event.MouseListener;
import java.awt.geom.Rectangle2D;
import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.DataInputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileReader;
import java.io.FileWriter;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.Writer;
import java.net.URL;
import java.util.Date;
import java.util.logging.Level;
import java.util.logging.Logger;
import javax.swing.BorderFactory;
import javax.swing.BoxLayout;
import javax.swing.Icon;
import javax.swing.ImageIcon;
import javax.swing.JButton;
import javax.swing.JComboBox;
import javax.swing.JFrame;
import javax.swing.JLabel;
import javax.swing.JOptionPane;
import javax.swing.JPanel;
import javax.swing.JTextField;
import javax.swing.border.BevelBorder;
import javax.swing.border.Border;
import javax.swing.border.LineBorder;

/**
*
* @author Usman
*/
public class Main extends JPanel {


//*********************FRAMES*************************************
static JFrame win=new JFrame("Window"); // FRame
static JFrame start=new JFrame("SELECT"); //startting Frame

//****************************************************************


//*********************Panels*************************************
static JPanel overall_panel=new JPanel(); //Panel with RAM and Processes
static JPanel block_p=new JPanel();     //Panel Contain moving proccess and RAM
static JPanel welcom_comnt=new JPanel(); //Panel At End Contain Comments
static JPanel ram_panel=new JPanel(); //Panel with RAM
static JPanel obj=new JPanel();
//****************************************************************




//*********************JButtons*************************************
static JButton best=new JButton();  //Button for Best FIt
static JButton first=new JButton(); // Button for First fit
static JButton worst=new JButton(); //Button for worst fit
static JButton load_b=new JButton(); //button for loading processes from RAM
static JButton compact=new JButton(); //Button for Compaction
static JButton stat=new JButton(); //Button to view Statistic
static JButton []but=new JButton[20];  //Array of buttons served as Blocks of RAM .20 indicates that maximum 20 blocks can b thers
static JButton []but_c=new JButton[20];  //Array of buttons used for compaction


//****************************************************************


//*********************Int*************************************
static  int process[];                                 //size of each process
static int process_alc[]; //array used for compacttion,contain size of processes

static int usage[];                                    //check block is already filled or not

static int p;                                                //number of processes;
static int  b; //number of blocks
static int  used; // Total space used by blobks
static int free;  // Size that remained free *used for compaction
static int c;     //number of blocks used
static int bf=0;        //total number of blocks
static int space;     //total space used by processes

static int []blk;   //size of each block of RAM
static int []final_pos;   //final position of each process after particular technique
private static int space_left; // Free Space
private static int sz; //total size of blocks used
static int select; //select the Pattern
//****************************************************************

//**************************OTHER OBJECT*****************************
static Move rect[];                                    //arry of blocks equala to number of processe
static   JLabel status=new JLabel("Ready");  //Label Served as a Status Bar;
//two Seperate font styles
public static Font myFont1=new Font("Serif",Font.BOLD,39);


static Icon back=new ImageIcon("C:/Users/Usman/Documents/NetBeansProjects/GUI/src/gui/logo.gif");

//**********************************************************************





//**********************RESET************************
static void reset() //Function For Reseting Values
{
p=0;
b=0;  used=0;
free=0;
sz=0;
             ; c=0;
bf=0;
space=0;
}
//**********************************************************




static void compact() //DO Compaction
{
    
final JFrame com=new JFrame("Comapaction state of Current Ram"); //create Frame
com.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE); //Do Exit
JLabel head=new JLabel("                 COMPACTION"); // Compaction Heading
Icon pic=new ImageIcon("C:/Users/Usman/Documents/NetBeansProjects/GUI/src/gui/c.gif"); // create image icon
head.setIcon(pic);
head.setBackground(Color.WHITE);   //set background to Black
  

JButton ok=new JButton("OK");       // Button at End for OK
ok.addActionListener(new ActionListener()
{

public void actionPerformed(ActionEvent e) {
com.hide();  //Do hide the Frame
}


})

;
head.setFont(myFont1);  //Setting font for heading
com.add(ok,BorderLayout.SOUTH);
com.add(head,BorderLayout.NORTH);


com.setBounds(250,0,600, 600); //setting bounds
int x=p+1;



process_alc[p]=space_left; //last element equal to the space left
JPanel c=new JPanel();
c=create_compact(process_alc,p+1); //callind process to create compact RAM
com.add(c); // add it to Frame
com.setVisible(true); //Allow to view
}
//**************************************************


//*****************DISPLAY***************************
static void display() //Show it on Console
{
int total;
total=used+free;
System.out.println("Blocks used = "+c);
System.out.println("Total used space = "+used);
System.out.println("Blocks free = "+(b-c));
System.out.println("Total free space = "+(total-used));
}
//***************************************************

//******************BEST FIT*************************
static void b_fit()
{
int i,j,size,best;
for(i=0;i<p;i++)  //runs total upto total processes
{
size=32967;      //given a maximum number
best=-1;         // best postion of this process
for(j=0;j<b;j++)   //runs for each block
{
if(process[i]<=blk[j]&&usage[j]==0&&(blk[j]-process[i])<size) //less thean < condition must be noticed
{
size=blk[j]-process[i]; //change value of size
best=j; //now best is block num j
}
}
if(size<32967&&best!=-1) //Ensuring a best fit.
{
usage[best]=1;   //block is now reserved
used=used+blk[best]; //increased used space
c++;   //increment number of block used
System.out.println("Process "+(i+1)+" is in block "+(best+1)); //show on console

final_pos[i]=best+1;   //final position of process is stored in another array

}
}
}
//***********************************************************************


//******************************Wotst FIT********************************
static void w_fit() //put process in maximum size available
{
int i,j,size,worst;
for(i=0;i<p;i++)
{
size=0;
worst=-1; // give a surpass value
for(j=0;j<b;j++)
{
if(process[i]<=blk[j]&&usage[j]==0&&(blk[j]-process[i])>size) //greater than > condition must be noticed
{
size=blk[j]-process[i]; //size will be changed
worst=j;   //now worst is at block j
}
}
if(worst!=-1) //Ensuring a worst fit.
{
usage[worst]=1; //block is reserved
used=used+blk[worst]; //used space is inctease
c++; //block is used
System.out.println("Process "+(i+1)+" is in block "+(worst+1)); //show on console
final_pos[i]=worst+1;  //change final position

}
}
}

//***************************************************************************


//******************FIRST Fit*****************************
static void f_fit() //simple add where you find extra position
{
int i,j;
for(i=0;i<p;i++) //Processes.
for(j=0;j<b;j++) //Blocks.
{
if(process[i]<=blk[j]&&usage[j]==0)
{
usage[j]=1; //block is reserved
used=used+blk[j]; //total used space
c++; //increment used blocks
System.out.println("Process "+(i+1)+" is in block "+(j+1));
final_pos[i]=j+1; //final postion is changed
break;
}
}
}
//*****************************************************************

//*****************FILE_HANDLING************************************



static void block_size()
{
////////////////////////////////SIZE OF BLOCK/////////////////////////
try{
// Open the file that is the first
// command line parameter
FileInputStream fstream = new FileInputStream("size_of_block.txt");
// Get the object of DataInputStream
DataInputStream in = new DataInputStream(fstream);
BufferedReader br = new BufferedReader(new InputStreamReader(in));
String strLine;
//Read File Line By Line
int i=0,j=0;
while ((strLine = br.readLine()) != null)   {
if(i==0)                       //read first line that contain number of blocks
{
bf=Integer.parseInt(strLine);
        b=bf;
System.out.print(strLine);
blk =new int[bf];
i++;
}
else
{
blk[j]=Integer.parseInt(strLine); //store size of blocks in blk array
j++;
// Print the content on the console
System.out.println (strLine);

}
}
//Close the input stream
in.close();
}catch (Exception e){//Catch exception if any
System.err.println("Error: " + e.getMessage());}
//////////////////////////////////////////////////////////////////////////////////
}


static void size_p()
{
///////////////////////SIZE OF EACH PROCESS//////////
try{
// Open the file that is the first
// command line parameter
FileInputStream fstream = new FileInputStream("size_of_process.txt");
// Get the object of DataInputStream
DataInputStream in = new DataInputStream(fstream);
BufferedReader br = new BufferedReader(new InputStreamReader(in));
String strLine;
//Read File Line By Line
int k=0,i=0;
while ((strLine = br.readLine()) != null)   {
if(i==0)
{                                //read first line that contain number of processes
p=Integer.parseInt(strLine);
process=new int[p];  //initialize array to number of process

//Initializtio of each array
usage=new  int[p];
final_pos=new int[p];
process_alc=new int[p+1];
for(int t=0;t<p;t++)
{

//initailize to zero

final_pos[t]=0;
process_alc[t]=0;
}
for(int j=0;j<bf;j++)
{
//block item find total available space
used=used+blk[j];

}

i++;
}
else
{
process[k]=Integer.parseInt(strLine); //get process size
k++;
}
// Print the content on the console
System.out.println (strLine);
}
//Close the input stream
in.close();
}catch (Exception e){//Catch exception if any
System.err.println("Error: " + e.getMessage());}

}
//**************************************************************************
/**
* @param args the command line arguments
*/
//***********************Statistics********************************************

static void stat() throws IOException
{
    BufferedWriter output = null;

  File file = new File("write.txt");
  output = new BufferedWriter(new FileWriter(file));
final JFrame list=new JFrame("Statistics"); //create A JFrame
JLabel []arr=new JLabel[7]; //LAbel array
JTextField []text=new JTextField[7]; //Textfield Array
list.setSize(400, 400);
output.write(" ***********************PROCESS ALLOCATION LOG FILE***************"); //top heading
output.newLine(); //inseritng new line
output.newLine();
output.newLine();
list.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
JPanel stat=new JPanel(new GridLayout(7,7,3,3));
for(int i=0;i<7;i++)
{
//Initializations
arr[i]=new JLabel();
text[i]=new JTextField();
}
//Set Text oF Label and Add statistical data to textFields and writing it to File
arr[0].setText("Total Number Of Blocks");
text[0].setText(Integer.toString(bf));
output.write("\n Total Number Of Blocks "+Integer.toString(bf));
output.newLine();
output.newLine();
arr[1].setText("Total Number Of Process");
text[1].setText(Integer.toString(p));
output.write("\nTotal Number Of Processes  "+Integer.toString(p));
output.newLine();
output.newLine();
arr[2].setText("Total Size Of Blocks");

text[2].setText(Integer.toString(sz)+" KB");
output.write("\nTotal Size Of Blocks "+Integer.toString(sz)+" KB");
output.newLine();
output.newLine();
arr[3].setText("Total Size Of Process");
int pz=0;
for(int i=0;i<p;i++)
{
pz=pz+process[i]; //Total size of processes
}
text[3].setText(Integer.toString(pz)+" KB");
output.write("Total Size Of Process "+Integer.toString(pz)+" KB");
output.newLine();
output.newLine();
arr[4].setText("\nTotal Space Used");

text[4].setText(Integer.toString(space)+" KB");
output.write("Total Space Used" + Integer.toString(space)+" KB");
output.newLine();
output.newLine();
arr[5].setText("Total Space Free");

space_left=sz-space;

text[5].setText(Integer.toString(sz-space)+" KB");
output.write("\nTotal Space Free "+ Integer.toString(sz-space)+" KB");
output.newLine();
output.newLine();
arr[6].setText("Number of Blocks unused");
text[6].setText(Integer.toString(b-c));
output.write("\nNumber of Blocks unused "+Integer.toString(b-c));
output.newLine();
output.newLine();

  output.write("Thankyou");
  output.close();
  System.out.println("Your file has been written");
stat.add(arr[0]);
stat.add(text[0]);

stat.add(arr[1]);
stat.add(text[1]);

stat.add(arr[2]);
stat.add(text[2]);

stat.add(arr[3]);
stat.add(text[3]);

stat.add(arr[4]);
stat.add(text[4]);

stat.add(arr[5]);
stat.add(text[5]);

stat.add(arr[6]);
stat.add(text[6]);


JButton ok=new JButton("OK"); //JButton for Ok
ok.addActionListener(new ActionListener() {

public void actionPerformed(ActionEvent e) {
list.hide(); //Do hide
}
});



list.add(stat);
list.add(ok,BorderLayout.SOUTH);

list.setVisible(true);
}

Main()
{

}

public static void main(String[] args) throws IOException, InterruptedException {

splash open=new splash(); //create splash object
open.splashInit();   //calling function
open.appInit();


//************************BORDER CHNAGES AT FRAME***********************************
//Setting Texts
best.setToolTipText("Select Best Fit to Perform");
first.setToolTipText("Select First Fit to Perform");
worst.setToolTipText("Select Worst Fit to Perform");
stat.setToolTipText("Check Statisics of current processes");




JPanel bord=new JPanel(new GridLayout(3,1,3,3)); //Panel at left side contain buttons


/////////Button OF FITS/////////////////////

best.setText("BEST FIT");
first.setText("FIRST FIT");
worst.setText("WORST FIT");
bord.add(best);
bord.add(first);
bord.add(worst);

first.addActionListener(new ActionListener()
{


public void actionPerformed(ActionEvent e) {


status.setText("First Fit is Running");

block_p.remove(obj); //remove welcome obj from panel
f_fit(); //call first fit
               
                try {
                    //call first fit
                    drawing(); // draw processes
                } catch (InterruptedException ex) {
                    Logger.getLogger(Main.class.getName()).log(Level.SEVERE, null, ex);
                }
               
win.repaint(); //repaint the frame
display();  //Display on console
}



}     );
best.addActionListener(new ActionListener()
{

public void actionPerformed(ActionEvent e) {
block_p.remove(obj);  //REmove welcome msg
status.setText("Best Fit is Running"); //change text
b_fit(); //call Best Fit
               
                try {
                    //call Best Fit
                    drawing(); //draw Processes
                } catch (InterruptedException ex) {
                    Logger.getLogger(Main.class.getName()).log(Level.SEVERE, null, ex);
                }
               

win.repaint(); //repaint the Frames

display(); //display result on console

}
}     );
worst.addActionListener(new ActionListener()
{

public void actionPerformed(ActionEvent e) {
block_p.remove(obj); //remve welcome msg
status.setText("Worst Fit is Running"); //set status
w_fit(); //call worstfit
                
                try {
                    //call worstfit
                    drawing(); //call drawing function
                } catch (InterruptedException ex) {
                    Logger.getLogger(Main.class.getName()).log(Level.SEVERE, null, ex);
                }
                
win.repaint(); // repaint the Frame

display(); //Display result on console

}
}     );
//****************************************************************






//******************LOADING RAM AND OTHER PROCESS***************

JPanel p2=new JPanel(new GridLayout(3,1,3,3)); //Panel at right side

load_b.setText("Load RAM and Processes"); //set Text
load_b.addActionListener(new ActionListener()
{

public void actionPerformed(ActionEvent e) {
try {
    
  

    block_p.removeAll(); //remove everything from the Panel
    reset(); //reset everything to initial value
//                   no_b();

    block_size(); //read from file blocksize
 //   no_p();
    size_p(); //call from File Process Size
block_p.repaint();
   
    status.setText("Processes and Blocks are Loaded Successfully");
    block_p.add(create(blk,bf),BoxLayout.X_AXIS); //add RAM to Panel Again
    block_p.add(obj,BoxLayout.X_AXIS); //ADd welcome message
    System.out.println("Done");
} 

 catch (Exception ex) {
    Logger.getLogger(Main.class.getName()).log(Level.SEVERE, null, ex);
}

}

}
);

//***********************************************************************

//********************COMPACTION**************************************
compact.setText("Compaction (Extra Credit)");
compact.addActionListener(new ActionListener()
{

public void actionPerformed(ActionEvent e) {
status.setText("Compaction on RAM (Reminder Extra Credit)");
compact(); //call Function

}

});
//***************************************************************




//*******************STATISTIC************************************

stat.setText("Statistics"); //set text
stat.addActionListener(new ActionListener()
{

public void actionPerformed(ActionEvent e) {
status.setText("This Feature Contain Information of RAM an Processes after Allocation Technique");
                try {
                    stat(); //call Function
                } catch (IOException ex) {
                    Logger.getLogger(Main.class.getName()).log(Level.SEVERE, null, ex);
                }
}

});


p2.add(load_b);
load_b.setToolTipText("Load from File");
compact.setToolTipText("After Compaction RAM View");
p2.add(compact);
p2.add(stat);

//**************************************************************************


//************WELCOME MESSAGE AT BORDER*************************************


JLabel wel=new JLabel("Welcome to our end Semester Project of Operating System supervised by Mr Hamid Mukhtar");
wel.setToolTipText("Click to view Supervisor Profile"); //set text
final String[] cmd = new String[4]; //constant string
cmd[0] = "cmd.exe";
cmd[1] = "/C";
cmd[2] = "start";
cmd[3] = "http://seecs.nust.edu.pk/faculty/hamidmukhtar.html"; //hyperlink
wel.addMouseListener(new MouseAdapter(){


public void mouseClicked(MouseEvent e) {
  try {
    Process pc = Runtime.getRuntime().exec(cmd);
} catch (IOException ex) {
    Logger.getLogger(Main.class.getName()).log(Level.SEVERE, null, ex);
}
}





public void mouseEntered(MouseEvent e) {
status.setText("Click to view Supervisor Profile");
}

public void mouseExited(MouseEvent e) {
status.setText("Ready");
}
}


);

wel.setBorder(BorderFactory.createLineBorder(Color.yellow));

welcom_comnt.setBorder(BorderFactory.createTitledBorder("Info"));
welcom_comnt.add(wel);
JPanel last=new JPanel(); //panel contain buttons and comments
last.setLayout(new BoxLayout(last,BoxLayout.X_AXIS));
last.add(bord);
last.add(welcom_comnt);
last.add(p2);
JPanel end=new JPanel(new BorderLayout()); //Panel at end
end.add(last);
status.setPreferredSize(null);
Border border = LineBorder.createGrayLineBorder();
status.setBorder(border);
end.add(status,BorderLayout.SOUTH);
end.setBorder(BorderFactory.createTitledBorder("Options"));
end.setBackground(Color.cyan);
block_p.setLayout(new BoxLayout(block_p,BoxLayout.X_AXIS));

//****************************************************************************

//************************RAM PANEL***************************************
ram_panel=create(blk,bf); //Create RAM
ram_panel.setBorder(BorderFactory.createTitledBorder("Random Access Memory"));

obj.setLayout(new FlowLayout(FlowLayout.CENTER)); //seeting layout
JLabel im=new JLabel(); //label for gif image
im.setIcon(back); //setting background
im.setBounds(100,40, 500, 500);
obj.add(im);
obj.setBackground(Color.BLACK); //set background to black
block_p.add(ram_panel,BoxLayout.X_AXIS); //add created Ram to the PAnel
block_p.add(obj,BoxLayout.X_AXIS); //ADd Welcome Message to Panel

overall_panel.setLayout(new GridLayout(1,1));

overall_panel.add(block_p);
overall_panel.setBorder(BorderFactory.createTitledBorder("PROCESS AREA"));
win.add(overall_panel);

//Top Heading

 
        Icon icon = new ImageIcon("C:/Users/Usman/Documents/NetBeansProjects/GUI/src/gui/new5.gif");
        JLabel top = new JLabel(icon);
top.setBorder(BorderFactory.createLineBorder(Color.black));
top.setBackground(Color.red);
top.setFont(myFont1);
top.setForeground(Color.blue);

win.setSize(1300, 768); //setting Size
win.setBackground(Color.blue);
win.add(end,BorderLayout.SOUTH); //add end at bottom
win.add(top,BorderLayout.NORTH); //add heading at the top
win.setVisible(true); // set visible to true
win.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE); //exit on close


//***************************************************************************
}
//}


//***************PROCESS DRAWING*****************************************
static void drawing() throws InterruptedException
{

rect=new Move[p]; // create array of maximum num of processes
for(int i=0;i<p;i++)
{
int temp;
temp=final_pos[i];  //find final postion of this process i-e in which block?
if(temp>0)   // ensuring allocation
{
for(int j=0;j<bf;j++)
{
if(Integer.parseInt(but[j].getText().substring(0,1))==temp) //Matching text with Buttons
{
int x=but[j].getY(); //get Y cordinates of Button
int hit=process[i]/2; //Setting height of process
process_alc[i]=process[i]; //Process is allocated
int wid=but[j].getWidth();  //width wil be equal to jButton width
but[j].setToolTipText("Process "+(i+1) +"  has reached "+ " free= "+(blk[j]-process[i]));
space=space+process[i]; //space reserved is changed

proc_come(x,i,wid,hit,j);//call function



}

}
}
else if(i <b)
{
free=free+blk[i]; //increment Free space
}
}
for(int it=0;it<bf;it++)
{
sz=sz+blk[it];  //calcaulatin total space
}
space_left=sz-space; //space remaining
}

//***********************************************************************

//*************PROCESS COMING*********************************************
static void proc_come(int y,int num,int wid,int hit,int button_num) throws InterruptedException
{

{
rect[num]=new Move(y,wid,hit,button_num); //MOve a process of width wid ,height hit to Postion Y
block_p.add(rect[num],BoxLayout.X_AXIS); //Process to BLock

}

}
//***********************************************************************

//**************************CREATE RAM***********************************
static JPanel create(int []h,int num)  //for creating RAM
{
JPanel x2=new JPanel();
x2.setBorder(BorderFactory.createTitledBorder("Random Access Memory"));
x2.setLayout(null);
x2.setBounds(0,0,200,300); //setting Bounds
x2.setBackground(Color.GREEN);
int t=20;

for(int i=0;i<num;i++) //run till total number of blocks
{

if(i==0)
{
but[i]=new JButton();
but[i].setBackground(Color.WHITE);
System.out.println(but[i].getBackground());
but[i].repaint();
but[i].setBounds(15, t, 200, h[i]/2);
}
else
{
t=t+(h[i-1]/2); //calculating Y-axis location
but[i]=new JButton();
but[i].setBackground(Color.WHITE);
System.out.println(but[i].getBackground());
but[i].repaint();
but[i].setBounds(15, t, 200, h[i]/2);


}

but[i].setText(Integer.toString(i+1)+ " BLOCK \n "+ "Size="+blk[i]); // set text
but[i].setToolTipText("Block "+(i+1)+" is empty");
x2.add(but[i]); //add it to Panel
}
return x2; //return
}
//**************************************************************************

//********************COMPACTION*******************************************
static JPanel create_compact(int []h,int num)  // Doing Compaction
{
JPanel x3=new JPanel(); // Create another Panel
//x2.setLayout(new BoxLayout(x2,BoxLayout.Y_AXIS));
x3.setLayout(null);
x3.setBounds(0,0,200,300);
x3.setBackground(Color.GREEN);
int t=50;

for(int i=0;i<num;i++)
{

if(i==0)
{
but_c[i]=new JButton();

but_c[i].setBounds(100, t, 200, h[i]);
if(h[i]!=space_left) //if it is not equal to remaining space then
{
but_c[i].setBackground(Color.GREEN); //changing color
but_c[i].setText(Integer.toString(i+1)+ " Process \n "+ "Size="+h[i]);
}
else
{
but_c[i].setBackground(Color.WHITE); //white for not reserved
but_c[i].setText(Integer.toString(i+1)+ " Block \n "+ "Size="+space_left);
}
}
else
{
t=t+(h[i-1]); //creating y-axis
but_c[i]=new JButton();

but_c[i].setBounds(100, t, 200, h[i]);
if(h[i]!=space_left)
{
but_c[i].setBackground(Color.GREEN);
but_c[i].setText(Integer.toString(i+1)+ " Process \n "+ "Size="+h[i]);
}
else
{
but_c[i].setBackground(Color.WHITE);
but_c[i].setText(Integer.toString(i+1)+ " Block \n "+ "Size="+space_left);
}

}
//but[i].setMaximumSize(d);


x3.add(but_c[i]);
}
return x3;
}
  

}
//********************************************************************







