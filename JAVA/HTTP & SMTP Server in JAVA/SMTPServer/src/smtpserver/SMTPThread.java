/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package smtpserver;

import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.io.OutputStreamWriter;
import java.net.Socket;
import java.util.Date;
import java.util.StringTokenizer;
import java.util.Timer;
import java.util.TimerTask;
import java.util.Vector;
/**
 *
 * @author Qaisar
 */

public class SMTPThread extends Thread
{
Socket socket;
OutputStream UPStream;
InputStream IPStream;
String errorMessage;
EmailScheduler email= new EmailScheduler(); // this is object of inner class..
private static Vector StatusDB = new Vector();
String operation=null;

 class EmailScheduler extends TimerTask //this is inner class..
 {
    int timerId;
    String from;
    String to;
    String subject;
    String smtp;
    String message;
    String scheduledTime="imediately";
    String CurStatus="Pinding";
    
   EmailScheduler()
   {
    
   }
   public void run()
      {
       //doSomething();
       sendEmail(from, to , subject , message,smtp);
       sendEmail("noreply@qaisar.com",from , "Email Deliver Confirmation", "Your Email scheduled for  "+to+" is sent",smtp);// Confirmation reply to send...
       for (int i= 0;i<StatusDB.size();i++)
       {
       EmailScheduler curEmail = (EmailScheduler)StatusDB.elementAt(i);
       if (curEmail.from.equalsIgnoreCase(from) && curEmail.to.equalsIgnoreCase(to) && curEmail.subject.equalsIgnoreCase(subject) && curEmail.scheduledTime.equalsIgnoreCase(scheduledTime))
       { StatusDB.removeElementAt(i);
           curEmail.CurStatus= "Delivered";
           StatusDB.add(curEmail);
       }
       }
       
   }
  void scheduleEmail(int year,int month,int day, int hour, int minute)
  { 
         
         Timer timer = new Timer();
         Date time = new Date(year,month ,day,hour,minute,0);
         System.out.println("Date = "+time);
         timer.schedule(this, time);
         errorMessage="Dear User your email is schedule to be delivered to "+to+" at "+time;
        
  
  }
   
   } // end inner class ScheduleRunner


public SMTPThread(Socket socket){
    this.socket=socket;
    try{
    UPStream = socket.getOutputStream();
    IPStream = socket.getInputStream();
    }catch(Exception ex){System.out.println("Exception in ServerThread.Constructor  =  "+ex.getMessage());}
}

public void run()
{

        System.out.println("I am SMTP Server,a Client connected with Name = "+socket.getInetAddress().getHostName()+" and Port ="+socket.getPort());
        
        read();
        write();

}
private void read(){
 try{      
    IPStream = this.socket.getInputStream();
    
    int totalBytes=IPStream.available();
    byte [] bytes =new byte[totalBytes];
    int readBytes=0;
    while(readBytes != totalBytes)
    {
        readBytes += IPStream.read(bytes);
    }
    
        String temp = new String (bytes);
        //System.out.println("String received at SMTP from HTTP = "+temp);
        StringTokenizer tokens = new StringTokenizer(temp,":::");
        this.operation= tokens.nextToken();
        System.out.println("Operation = "+this.operation);
        this.email.from=tokens.nextToken();
        System.out.println("From = "+this.email.from);
        this.email.to=tokens.nextToken();
        System.out.println("To = "+this.email.to);
        this.email.subject=tokens.nextToken();
        System.out.println("Sub = "+this.email.subject);
        if(operation.equals("email"))
        {
        String smtp =null;
         if ((smtp=tokens.nextToken())!=null)
        {
            System.out.println("I am at point 1");
            System.out.println("SMTP = "+smtp);
            this.email.smtp=smtp;   
        }
        this.email.message= this.adjustMessage(tokens.nextToken());
        System.out.println("Msg = "+this.email.message);
        
        }
        this.email.scheduledTime=tokens.nextToken();
        System.out.println("Sche = "+this.email.scheduledTime);
        decideOperation();
    
    
     }catch(Exception ex){System.out.println("Exception in SMTPThread.read()  =  "+ex.getMessage());
     errorMessage="Dear User please fill in all fields..<br>Thanx";
     }
    
    }
private String adjustMessage(String message)
{
    StringTokenizer tokens = new StringTokenizer(message,"\n",true);
    String temp=tokens.nextToken();
    while(tokens.hasMoreTokens())
    {
        String st = tokens.nextToken();
        if(st.startsWith("."))
        temp=temp+"."+st;
        else
        temp=temp+st;
    }
    System.out.println("Message Adj = "+temp);
    return temp;
}
private void decideOperation()
{
    
    if (operation.equals("email"))
    {
    if (this.email.scheduledTime.equalsIgnoreCase("Imediately"))
    {
        sendEmail(this.email.from, this.email.to, this.email.subject,this.email.message,email.smtp);
       
    }
    else
    {
        try{
            
        boolean validAddress= checkAddress(this.email.from, this.email.to, this.email.subject,this.email.message,email.smtp);
        if (!validAddress) 
            return;
        System.out.println("Time String = ="+email.scheduledTime);
        StringTokenizer timeTokens = new StringTokenizer(email.scheduledTime,"/");
        int YEAR= Integer.parseInt(timeTokens.nextToken())-1900;
        int MONTH= Integer.parseInt(timeTokens.nextToken());
        int DAY= Integer.parseInt(timeTokens.nextToken());
        int HOUR= Integer.parseInt(timeTokens.nextToken());
        int MINUTE= Integer.parseInt(timeTokens.nextToken());
        email.scheduleEmail(YEAR,MONTH-1,DAY, HOUR,MINUTE);
        email.timerId = StatusDB.size();
        StatusDB.add(email); // putting email data for status quries..
        
        System.out.println("StatusDB size = "+StatusDB.size());
        }catch(Exception ex){System.out.println("Wrong time format used.. ");
        errorMessage="Wrong <b>'Time'</b> format used.. ";
        }
    }
    }
    else if (operation.equalsIgnoreCase("status"))
        checkStatus();
}
void checkStatus()
{
    boolean noSuchEmailFound=true;
for (int i= 0;i<StatusDB.size();i++)
{
EmailScheduler curEmail = (EmailScheduler)StatusDB.elementAt(i);
     /*System.out.println("From :"+curEmail.from+":"+this.email.from);
     System.out.println("From :"+curEmail.to+":"+this.email.to);
     System.out.println("From :"+curEmail.subject+":"+this.email.subject);
     System.out.println("From :"+curEmail.scheduledTime+":"+this.email.scheduledTime);
      */ 
 if (curEmail.from.equalsIgnoreCase(this.email.from) 
    && curEmail.to.equalsIgnoreCase(this.email.to) 
    && curEmail.subject.equalsIgnoreCase(this.email.subject) 
    && curEmail.scheduledTime.equalsIgnoreCase(this.email.scheduledTime))
  { 
     
     errorMessage = "Dear User your email is "+curEmail.CurStatus;
     //System.out.println("Status found..");
     noSuchEmailFound=false;
     break;
     
  }
   
 }
  if(noSuchEmailFound)    
     errorMessage = "Dear User No such email is scheduled..Please correct your query.. ";
       
}
public void write(){
    try{
        UPStream.write((errorMessage+"\n").getBytes());
        UPStream.close();
        IPStream.close();
        socket.close();
        
    }catch(Exception ex){System.out.println("Exception in SMTPThread.write  =  "+ex.getMessage());}
    }



private static int hear( BufferedReader in ) throws IOException {
     String line = null;
     int res = 0;

     while ( (line = in.readLine()) != null ) {
         System.out.println(line);
         String pfx = line.substring( 0, 3 );
         try {
            res = Integer.parseInt( pfx );
         }
         catch (Exception ex) {
            res = -1;
         }
         if ( line.charAt( 3 ) != '-' ) break;
     }

     return res;
     }

   private static void say( BufferedWriter wr, String text )
      throws IOException {
     wr.write( text + "\r\n" );
     System.out.println(text);
     wr.flush();

     return;
     }
private boolean checkAddress(String from,String to,String subject,String message,String smtp)
   {
   try {
             
        if(smtp.equals("null"))
         {
             MXLookup mx =  new MXLookup();
             String domain= to.substring(to.indexOf ("@")+1);
             System.out.println("Domain  = "+domain);
             Vector MXServers = mx.doLookup(domain);
             smtp= (String) MXServers.lastElement();
             System.out.println("SMTP Server found is "+smtp);
             System.out.println("Trying to connect to SMTP Server: "+smtp);
         }
        
             int res;            
             Socket skt = new Socket(smtp, 25 );
             skt.setSoTimeout(1000*60);
             BufferedReader rdr = new BufferedReader
                ( new InputStreamReader( skt.getInputStream() ) );
             BufferedWriter wtr = new BufferedWriter
                ( new OutputStreamWriter( skt.getOutputStream() ) );

             res = hear( rdr );
             if ( res != 220 ) throw new Exception( "SMTP Server you are trying to connect to is temporarily busy.." );
             say( wtr, "HELO kth.se" );

             res = hear( rdr );
             if ( res != 250 ) throw new Exception( "Not ESMTP" );

                        
             say( wtr, "MAIL FROM: <"+from+">" );
             res = hear( rdr );
             if ( res != 250 ) throw new Exception( "Sender rejected" );

             say( wtr, "RCPT TO: <" + to + ">" );
             res = hear( rdr );
              if ( res != 250 )
                throw new Exception("Relay to the receiver's SMTP denied OR Receiver's Address does not exist!"  );
                   
             say( wtr, "RSET" ); hear( rdr );
             say( wtr, "QUIT" ); hear( rdr );
             
             rdr.close();
             wtr.close();
             skt.close();
             return true;
             
            }catch(Exception ex){
            errorMessage="Dear User following Exception occur while processing your email:<b> "+ex.getMessage()+"</b>";
                return false;
            }
   }
public void sendEmail(String from,String to,String subject,String message,String smtp)
{   
        
         
    try {
             
        if(smtp.equals("null"))
         {
             MXLookup mx =  new MXLookup();
             String domain= to.substring(to.indexOf ("@")+1);
             System.out.println("Domain  = "+domain);
             Vector MXServers = mx.doLookup(domain);
             smtp= (String) MXServers.lastElement();
             System.out.println("SMTP Server found is "+smtp);
             System.out.println("Trying to connect to SMTP Server: "+smtp);
         }
        
             int res;            
             Socket skt = new Socket(smtp, 25 );
             //skt.setSoTimeout(1000*60);
             System.out.println("Trying to connect to "+smtp);
             BufferedReader rdr = new BufferedReader
                ( new InputStreamReader( skt.getInputStream() ) );
             BufferedWriter wtr = new BufferedWriter
                ( new OutputStreamWriter( skt.getOutputStream() ) );

             res = hear( rdr );
             if ( res != 220 ) throw new Exception(  "SMTP Server you are trying to connect to is temporarily busy.." );
             say( wtr, "HELO kth.se" );

             res = hear( rdr );
             if ( res != 250 ) throw new Exception( "Not SMTP" );

                        
             say( wtr, "MAIL FROM: <"+from+">" );
             res = hear( rdr );
             if ( res != 250 ) throw new Exception( "Sender rejected" );

             say( wtr, "RCPT TO: <" + to + ">" );
             res = hear( rdr );
              if ( res != 250 )
                throw new Exception( "Relay to the receiver's SMTP denied OR Receiver's Address does not exist!" );
            
             say(wtr,"data");
             res=hear(rdr);
             say(wtr,"subject: "+subject);
             say(wtr,"From: "+from);
             say(wtr,"To: "+to+"\r\n");
             say(wtr,message+"  ");
             say(wtr,"\r\n.");
             res=hear(rdr);
             if (res==250)
                 errorMessage="Dear user your email to '<b>"+to+"'</b> is queued for delivery..";
             else
                 errorMessage="<b>Internal Server Error</b>";
             say( wtr, "RSET" ); hear( rdr );
             say( wtr, "QUIT" ); hear( rdr );
             
             rdr.close();
             wtr.close();
             skt.close();
         }
         catch (Exception ex) {
           errorMessage="Dear User following Exception occur while processing your email:<b> "+ex.getMessage()+"</b>";
         }
     }
}

