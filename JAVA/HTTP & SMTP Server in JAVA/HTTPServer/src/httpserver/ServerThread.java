/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package httpserver;

/**
 *
 * @author Qaisar
 */
import java.io.BufferedReader;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.io.RandomAccessFile;
import java.io.UnsupportedEncodingException;
import java.net.Socket;
import java.net.URLDecoder;
import java.util.StringTokenizer;

public class ServerThread  implements Runnable{
    Socket socket;
    OutputStream UPStream;
    InputStream IPStream;
    RandomAccessFile file;
    String fileName = "";
    EmailData email;
    private String smtpReply="";
    String operation="page";

  
    
    ServerThread(Socket socket) {
        this.socket = socket;
        try {
            UPStream = socket.getOutputStream();
        } catch (Exception ex) {
            System.out.println("Exception in ServerThread.Constructor  =  " + ex.getMessage());
        }
    }

    public void run() {

        System.out.println("A Client connected with Name = " + socket/*.getInetAddress().getHostName()*/ + " and Port =" + socket.getPort());
        processRequest();
        write();
    }

    private void sendMail(String from, String to, String subject, String message, String time,String smtp) {
        try {
            
            Socket smtpSocket = new Socket("localhost", 20000);
            InputStream smtpInputStream = smtpSocket.getInputStream();
            OutputStream smtpOutputStream = smtpSocket.getOutputStream();
                        
            smtpOutputStream.write((operation+":::"+from+":::"+to+":::"+subject+":::"+smtp+":::"+message+":::"+time).getBytes());
            smtpOutputStream.flush();         
            
            BufferedReader in = new BufferedReader(new InputStreamReader(smtpInputStream));
            
            String reply=null ;
            while((reply=in.readLine())==null){}
            smtpReply=reply;
        } catch (Exception ex) {
            System.out.println("Exception in ServerThread.sendMail() = " + ex.getMessage());
            smtpReply="Dear user follwing exception occured while processing :<b>"+ex.getMessage()+"</b>";
        }
   }

    private void processRequest() {
        try {
            IPStream = socket.getInputStream();
            BufferedReader in = new BufferedReader(new InputStreamReader(IPStream));
            String requestData = in.readLine();
            System.err.println("Request: " + requestData);
            StringTokenizer tokens = new StringTokenizer(requestData, " ");
            System.out.println("Method = " + tokens.nextToken());
            fileName = tokens.nextToken();
            //String line=fileName;
            //while(line!=null)
            //System.out.println("HAHAH = "+(line=in.readLine()));
           
            
            System.out.println(fileName);
            if (fileName.startsWith("/processEmail")) 
            {
                operation = "email";
                processGet(fileName.substring(fileName.indexOf("?") + 1));
                sendMail(email.from, email.to,email.subject, email.message,email.scheduleTime,email.smtp);
            }
            else if (fileName.startsWith("/processStaus"))
            {
                
                operation = "status";
                processStatus(fileName.substring(fileName.indexOf("?") + 1));
                sendStatusReq(email.from, email.to,email.subject,email.scheduleTime);
            }

        } catch (Exception ex) {
            System.out.println("Exception in ServerThread.read()  =  " + ex.getMessage());
        }
    }
 void sendStatusReq(String from, String to, String subject,String time)
 {
   try {
            Socket smtpSocket = new Socket("localhost", 20000);
            InputStream smtpInputStream = smtpSocket.getInputStream();
            OutputStream smtpOutputStream = smtpSocket.getOutputStream();
                        
            smtpOutputStream.write((operation+":::"+from+":::"+to+":::"+subject+":::"+time).getBytes());
            smtpOutputStream.flush();         
            
            BufferedReader in = new BufferedReader(new InputStreamReader(smtpInputStream));
            
            String reply=null ;
            while((reply=in.readLine())==null){}
            smtpReply=reply;
        } catch (Exception ex) {
            System.out.println("Exception in ServerThread.sendMail() = " + ex.getMessage());
            smtpReply="Dear user follwing exception occured while processing :<b>"+ex.getMessage()+"</b>";
        }      
 }
void processStatus(String data)
{
try {    
            String decodedData = URLDecoder.decode(data, "UTF-8");
            StringTokenizer tokens = new StringTokenizer(decodedData, "&");
            
            String temp = tokens.nextToken();
            email=new EmailData(null, null, null, null,null);
            email.from=temp.substring(temp.indexOf("=")+1);
            temp=tokens.nextToken();
            email.to=temp.substring(temp.indexOf("=")+1);
            temp=tokens.nextToken();
            email.subject=temp.substring(temp.indexOf("=")+1);
            temp=tokens.nextToken();
            email.scheduleTime=temp.substring(temp.indexOf("=")+1);
            
            System.out.println("decodedData =" + decodedData);
            }catch (UnsupportedEncodingException ex){
            System.out.println("Exception in ServerThread.processStatus()  =  " + ex.getMessage());
            System.out.println("UnsupportedEncodingException");
        }

}
    public void write() {
        try {
            
            if (this.operation.equals("email") || this.operation.equals("status")){
                UPStream.write(("<center>"+smtpReply+"</center>").getBytes());      
            }
            else{
            
                FileInputStream inputStream = new FileInputStream(new File("." + fileName));
                byte bytes[] = new byte[inputStream.available()];
                inputStream.read(bytes);
                UPStream.write(bytes);
               }            
            UPStream.flush();            
            UPStream.close();
            IPStream.close();
            socket.close();

        } catch (FileNotFoundException ex) {
            try {
                System.out.println("File " + fileName + " not found");
                String errorFile = "<html>" +
                        "<title>File Not Found</title>" +
                        "<body><center><h2>Your Required file " + fileName + " not found </h2><center></body>" +
                        "</html>";
                UPStream.write(errorFile.getBytes());
                UPStream.flush();
                socket.close();
            } catch (Exception e) {
            }
        } catch (Exception ex) {
            System.out.println("Exception in ServerThread.write  =  " + ex.getMessage());
        }
    }

    private void processGet(String data) {
        try {
            String decodedData = URLDecoder.decode(data, "UTF-8");
            StringTokenizer tokens = new StringTokenizer(decodedData, "&");
            String temp = tokens.nextToken();
            email=new EmailData(null, null, null, null,null);
            email.from=temp.substring(temp.indexOf("=")+1);
            temp=tokens.nextToken();
            email.to=temp.substring(temp.indexOf("=")+1);
            temp=tokens.nextToken();
            email.subject=temp.substring(temp.indexOf("=")+1);            
            temp=tokens.nextToken();
            email.scheduleTime=temp.substring(temp.indexOf("=")+1);            
            temp=tokens.nextToken();
            temp.trim();
            if((temp.substring(temp.indexOf("=")+1)).equals(""))
            {
            email.smtp="null";
            System.out.println("email.smtp at HTTP= " +email.smtp);
            }
            else
            {
                email.smtp=temp.substring(temp.indexOf("=")+1);
                System.out.println("email.smtp at HTTP= " +email.smtp);
            }
            temp=tokens.nextToken();
            email.message=temp.substring(temp.indexOf("=")+1);
            
            System.out.println("From = "+email.from+"To = "+email.to+"sub = "+email.subject+"Message = "+email.message+" Time = "+email.scheduleTime+" SMTP = "+email.smtp);

            System.out.println("decodedData =" + decodedData);
            }catch (UnsupportedEncodingException ex) {
            System.out.println("Exception in ServerThread.processGet()  =  " + ex.getMessage());
            System.out.println("UnsupportedEncodingException");
            }catch(Exception ex){
            System.out.println("Exception in ServerThread.processGet()  =  " + ex.getMessage());
            }
    }

    private void processPost() {

    }
}
