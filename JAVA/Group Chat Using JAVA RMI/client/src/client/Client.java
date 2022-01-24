package client;


import java.rmi.*;
import java.rmi.server.*;
import client_board.*;
import java.net.*;
/**
 * this class implements the remote interface methods
 */
public class Client extends UnicastRemoteObject
        implements ClientBoard{
    public static ClientUI ui=null;
    String clientName="";

    public Client() throws RemoteException {
       super();
    }

    /**
     * passing a nick name we try to create a new client and bind it into the naming
     * service
     */
    public void createClient(String name){
        if (System.getSecurityManager() == null) {
           System.setSecurityManager(new RMISecurityManager());
       }
       try {
           ClientBoard client = new Client();
           Naming.rebind(name, client);
           System.out.println("Client bound");
           clientName=name;
       } catch (Exception e) {
           System.err.println("Client exception: " + e.getMessage());
           e.printStackTrace();
       }

    }

    /**
     * when client disconnects, it's no longer needed so  detach from naming service
     */

    public void unbind(){
        try {
            Naming.unbind(clientName);
        } catch (Exception ex){
        }
    }

    /**
     * recive a message from others(server) and print it
     * @param msg String
     * @return Object
     * @throws RemoteException
     */
    public Object recieveMsg(String msg) throws RemoteException {
        //noitfy all
        ui.msgs.append(msg+"\n");
        return "ok";
    }

}
