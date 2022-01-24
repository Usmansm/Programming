package client_board;

import java.rmi.Remote;
import java.rmi.RemoteException;
/**
 * one method to recieve messages from others(server)
 */
public interface ClientBoard extends Remote{
   public Object recieveMsg(String msg) throws RemoteException;
}
