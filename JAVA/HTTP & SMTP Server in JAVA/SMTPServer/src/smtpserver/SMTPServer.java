/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package smtpserver;

import java.net.ServerSocket;
import java.net.Socket;
/**
 *
 * @author Qaisar
 */
public class SMTPServer {
    ServerSocket server;

    public void start()
    {
    try {
            server = new ServerSocket(20000);
            System.out.println("SMTP Server started...");
            while(true)
            {
            Socket clientSoc = server.accept();
            SMTPThread thread = new SMTPThread(clientSoc);
            thread.start();
            }

        } catch (Exception ex) {
            System.out.println("SMTP Can't listen at 20000 coz it is used by some other program" + ex.getMessage());
        }
    
    }
  public static void main(String[] args) {
        
        SMTPServer smtp = new SMTPServer();
        smtp.start();
        
    }



}
