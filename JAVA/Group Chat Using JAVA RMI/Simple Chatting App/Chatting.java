import java.rmi.*;
import java.util.*;

public interface Chatting extends Remote
{
	public void sendPublicMessage(String keyword, String username, String message) throws RemoteException;
	public ArrayList getClientList() throws RemoteException;
	public void connect(String username) throws RemoteException;
	public void disconnect(String username) throws RemoteException;
}