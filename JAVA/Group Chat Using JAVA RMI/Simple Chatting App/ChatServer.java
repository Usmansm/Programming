import java.rmi.*;
import java.rmi.server.*;
import java.net.*;
import java.io.*;
import java.util.*;
import javax.swing.JOptionPane;

public class ChatServer
{		
	private static HashMap<String, Socket> connectedUser = new HashMap<String, Socket>();	
	private static Socket ClientSocket = null;
	private static ServerSocket serverSocket;
	private static String username = null;
	private static PrintWriter output;
	
	private static final String PUBLICMESSAGE = "PUBLICMESSAGE";
	private static final String ONLINE = "ONLINE";
	private static final String OFFLINE = "OFFLINE";
	
	public ChatServer()
	{
		try
		{
			ChatImpl csi = new ChatImpl(this);
			Naming.rebind("rmi://localhost:1099/ChatService", csi);
		}		
	 	catch(java.rmi.ConnectException ce)
	 	{
	 		JOptionPane.showMessageDialog(null, "Trouble : Please run rmiregistry.", "Connect Exception", JOptionPane.ERROR_MESSAGE);
	 		System.exit(-1);
	 	}
	 	catch (IOException ioe)
	 	{
	   		JOptionPane.showMessageDialog(null, "Trouble : Please run rmiregistry.", "Exception", JOptionPane.ERROR_MESSAGE);
	   		System.exit(-1);
	 	}
	 	catch (Exception e)
	 	{
	   		JOptionPane.showMessageDialog(null, "Trouble : Please run rmiregistry.", "Exception", JOptionPane.ERROR_MESSAGE);
	   		System.exit(-1);
	 	}
	}
	
	public static void main (String[] args)
	{
		try
		{
			int port = Integer.parseInt(args[0]);
			ChatServer cs = new ChatServer();
			cs.processConnection(port);
		}
		catch (ArrayIndexOutOfBoundsException ae)
		{
			JOptionPane.showMessageDialog(null, "Please insert the port", "ATTENTION", JOptionPane.INFORMATION_MESSAGE);
			System.exit(-1);
		}		
    }
    
    private void processConnection(int port)
	{
		try
		{
			serverSocket = new ServerSocket(port);
			System.out.println("Server is running on port " + port + " .... ");
		}
		catch (IOException ioe)
		{
			JOptionPane.showMessageDialog(null, "Could not listen port " + port, "ERROR", JOptionPane.ERROR_MESSAGE);
			System.exit(-1);
		}

		try
		{
			while (true)
			{
				addClient(serverSocket.accept());
				String username = getUsername();
				sendPublicMessage(PUBLICMESSAGE, "SERVER", "[" + username + "] is now online");
			}
		}
		catch (IOException ioe)
		{
			JOptionPane.showMessageDialog(null, "Could not accept connection.", "ERROR", JOptionPane.ERROR_MESSAGE);
			System.exit(-1);
		}
	}
    
    public void connect(String username)
	{
		this.username = username;
	}
	
	public String getUsername()
	{
		return username;
	}
	
	public ArrayList getClientList()
	{
		ArrayList myUser = new ArrayList();
		
		Iterator i = connectedUser.keySet().iterator();
		String user = null;
		
		while(i.hasNext())
		{
			user = i.next().toString();
			myUser.add(user);
		}
		
		return myUser;
	}
	
	public void addClient(Socket clientSocket) throws RemoteException
	{
		connectedUser.put(getUsername(), clientSocket);
		sendPublicMessage(ONLINE, getUsername(), "CLIENT");
	}
	
	public void sendPublicMessage(String keyword, String username, String message) throws RemoteException
	{
		Iterator i = connectedUser.keySet().iterator();
		String user = null;
		while(i.hasNext())
		{
			try
			{
				user = i.next().toString();
				ClientSocket =  connectedUser.get(user);
				output = new PrintWriter(ClientSocket.getOutputStream(), true);
				output.println(keyword + "***" + username + "***" + message);
				output.flush();
			}
			catch(IOException ioe)
			{
				connectedUser.remove(user);
				sendPublicMessage(OFFLINE, user, user + " has been left the conversation");
			}
		}
	}
	
	public void disconnect(String username) throws RemoteException
	{
		connectedUser.remove(username);
		sendPublicMessage(OFFLINE, username, username + " has been left the conversation");
		sendPublicMessage(PUBLICMESSAGE, "SERVER", username + " has been left the conversation");
	}
}