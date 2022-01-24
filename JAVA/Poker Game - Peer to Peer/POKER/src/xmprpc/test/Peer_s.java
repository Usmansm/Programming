/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package xmprpc.test;
import java.io.File;
import java.io.FileNotFoundException;
import java.util.Scanner;
import java.util.Vector;
import org.apache.xmlrpc.WebServer;

import org.apache.xmlrpc.XmlRpcException;


/**
 *
 * @author SmartMindx
 */
public class Peer_s {

    /**
     * @param args the command line arguments
     */

int c;
static int collected_cash=0; //cash being collected in case this peer handles the card throwing



 static String [] guessed_card_table=new String [7];
 static String [] guessed_card_player=new String [22];



    void startServer(int port) throws FileNotFoundException
    {




        String [] words= new String [60];   //array of words to store card names
      String current;    //String to be use in loop to reference next.
      int counter=0;
      int [] n=new int [7];



      Scanner text=new Scanner(new File("D:\\Images\\cards.txt"));
      while (text.hasNext())
      {
        current= text.next();
        words[counter]= current;
         counter++;
      }


      for(int i=1;i<=6;i++)
      {
          n[i] = (int) (Math.random() * 52.0) + 1; //Generating Random number and converting into properly int.
      guessed_card_table[i]=words[n[i]];

      while ((guessed_card_table[i].charAt(0) >= 'A'||guessed_card_table[i].charAt(0) >= 2) && (guessed_card_table[i].charAt(0) <= 'Z'||guessed_card_table[i].charAt(0) <= 10))
      {
          n[i] = (int) (Math.random()*52.0)+1;
          guessed_card_table[i]=words[n[i]];
      }

        }


      for(int j=1;j<7;j++){
     System.out.println("Card"+""+j+": "+guessed_card_table[j]);//Guessing 6 Random Cards
        }


        WebServer server = new WebServer(port);
        server.addHandler("server", new Peer_s());
        //Peer_c1 c=new Peer_c1();
      
        
        server.start();
    }



      public int im_alive(int i)
    {
    return 1;
    }


    public int getCash(int cash)
    {
        collected_cash=collected_cash+cash;
        return 3456;
    }


     public String throw2Cards(int in) throws FileNotFoundException
    {
        // cards_thrown_to[i]=i;



        String [] words= new String [60];   //array of words to store card names
        String current;    //String to be use in loop to reference next.
        int counter=0;
        int [] n=new int [22];

     

      Scanner text=new Scanner(new File("D:\\Images\\cards.txt"));
      while (text.hasNext())
      {
        current= text.next();
        words[counter]= current;
         counter++;
      }


      for(int i=1;i<=20;i++)
      {
          n[i] = (int) (Math.random() * 52.0) + 1; //Generating Random number and converting into properly int.
      guessed_card_player[i]=words[n[i]];

      while ((guessed_card_player[i].charAt(0) >= 'A'||guessed_card_player[i].charAt(0) >= 2) && (guessed_card_player[i].charAt(0) <= 'Z'||guessed_card_player[i].charAt(0) <= 10))
      {
          n[i] = (int) (Math.random()*52.0)+1;
          guessed_card_player[i]=words[n[i]];
      }

        }


      for(int j=1;j<21;j++){
     System.out.println("Player Card"+""+j+": "+guessed_card_player[j]);//Guessing 20 Random Cards
        }


if(in==1)
{
        
        return guessed_card_player[1] + "-" + guessed_card_player[2];
        }

     if(in==2)
{

         System.out.println("I ve sent these two cardz"+guessed_card_player[3]+ "And"+guessed_card_player[4]);
        return guessed_card_player[3] + "-" + guessed_card_player[4];
        }

      if(in==3)
{

        return guessed_card_player[5] + "-" + guessed_card_player[6];
        }

         if(in==4)
{

        return guessed_card_player[7] + "-" + guessed_card_player[8];
        }

         if(in==5)
{

        return guessed_card_player[9] + "-" + guessed_card_player[10];
        }

       if(in==6)
{

        return guessed_card_player[11] + "-" + guessed_card_player[12];
        }



            if(in==7)
{

        return guessed_card_player[13] + "-" + guessed_card_player[14];
        }


            if(in==8)
{

        return guessed_card_player[15] + "-" + guessed_card_player[16];
        }



            if(in==9)
{

        return guessed_card_player[17] + "-" + guessed_card_player[18];
        }


 else
{

        return guessed_card_player[19] + "-" + guessed_card_player[20];
        }


    }











    public String throw_tableCards1(int x) throws FileNotFoundException
    {


   System.out.println("I have sent these cards"+guessed_card_table[1]+"-"+guessed_card_table[2]);
    return guessed_card_table[1]+"-"+guessed_card_table[2];




    }


        public String throw_tableCards2(int x) throws FileNotFoundException
    {


   System.out.println("I have sent these cards"+guessed_card_table[3]+"-"+guessed_card_table[4]);
    return guessed_card_table[3]+"-"+guessed_card_table[4];




    }


              public String throw_tableCards3(int x) throws FileNotFoundException
    {


   System.out.println("I have sent these cards"+guessed_card_table[5]+"-"+guessed_card_table[6]);
    return guessed_card_table[5]+"-"+guessed_card_table[6];




    }


     public String who_won(int id) throws FileNotFoundException, InterruptedException
    {

         int stats[]=new int[10];
         for(int w=1;w<=6;w++)
         {
             if(((guessed_card_player[1].substring(1)).equalsIgnoreCase((guessed_card_table[w].substring(1))))||(((guessed_card_player[2]).substring(1)).equalsIgnoreCase((guessed_card_table[w].substring(1)))))
             {
          
                 stats[1]=stats[1]+1;
                 
             }

            if(((guessed_card_player[3].substring(1)).equalsIgnoreCase((guessed_card_table[w].substring(1))))||(((guessed_card_player[4]).substring(1)).equalsIgnoreCase((guessed_card_table[w].substring(1)))))
             {
                 stats[2]=stats[2]+1;
                 
             }

             if(((guessed_card_player[5].substring(1)).equalsIgnoreCase((guessed_card_table[w].substring(1))))||(((guessed_card_player[6]).substring(1)).equalsIgnoreCase((guessed_card_table[w].substring(1)))))
             {
                 stats[3]=stats[3]+1;
               
             }
             }

int maximum = stats[0];   // first value of the array
int index = 0;
for (int i=1; i<stats.length; i++) {
    if (stats[i] > maximum) {
        maximum = stats[i];   // maximum
        index = i; // comparing index
    }
}


String r=Integer.toString(index);
System.out.println(r+"Won ..I have sent this message to every one");
       return r;


    }


//
//    public String throwsecond2cardsof6randomCards()
//    {
//    return "testFunction2 is called with argument: ";
//    }
//
//
//    public String throwthird2cardsof6randomCards()
//    {
//    return "testFunction2 is called with argument: ";
//    }




}


