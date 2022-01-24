
package httpserver;

import java.net.ServerSocket;
import java.net.Socket;

/**
 *
 * @author Qaisar
 */
public class HttpServer {
    /**
     * @param args the command line arguments
     */
    ServerSocket server;
    int port=8080;

    public int getPort() {
        return port;
    }

    public void setPort(int port) {
        this.port = port;
    }
    HttpServer()
    {
    
    }
    HttpServer(String port)
    {
     try{     
        this.port= Integer.parseInt(port);
     }catch(Exception ex){System.out.println("Wrong Port format.. :"+ex.getMessage());System.exit(0);}
    
    }
    public void start()
    {
    try {
            server = new ServerSocket(port); 
            System.out.println("Server Started..at port "+port);
            while(true)
            {
            Socket clientSoc = server.accept();
            ServerThread thread = new ServerThread(clientSoc);
            Thread Curthread = new Thread(thread);
            Curthread.start();
            }

        } catch (Exception ex) {
            System.out.println("Can't listen at "+port+" coz it is used by some other program  " + ex.getMessage());
        }
    
    }
    public static void main(String[] args) {
        System.out.println("Starting HTTPServer please wait....");
        HttpServer server=null;
        if(args.length!=0)
        server= new HttpServer(args[0]);
        else
        server= new HttpServer();
        
        server.start();
            }

}
