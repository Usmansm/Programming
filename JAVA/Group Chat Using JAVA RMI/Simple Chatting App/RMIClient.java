import java.rmi.*;
import java.io.*;
import java.net.*;
import java.awt.*;
import java.util.*;
import javax.swing.*;
import java.awt.event.*;
import javax.swing.event.*;

public class RMIClient extends JFrame implements ActionListener, Runnable
{
	private Chatting c;
	
	private static String ipAddress;
	private int port;
    private BufferedReader in = null;
	private Thread thread;
	
    private JButton jButtonConnect;
    private JButton jButtonSend;
    private JLabel jLabelUserList;
    private JList jListUser;
    private JScrollPane jScrollPaneListUser;
    private JScrollPane jScrollPaneMessage;
    private JTextArea jTextAreaMessage;
    private JTextField jTextSendMessage;
    private JTextField jTextUserName;    
    
	private Socket socket = null;
    private DefaultListModel listClient;
    
	private String message;

	private final String SEPARATOR = "\\*\\*\\*";
	private final String PUBLICMESSAGE = "PUBLICMESSAGE";
	private final String ONLINE = "ONLINE";
	private final String OFFLINE = "OFFLINE";

    public RMIClient()
    {
        initComponents();
    	thread = new Thread(this);
    }

    @SuppressWarnings("unchecked")
    private void initComponents()
    {
        listClient = new DefaultListModel();
        jScrollPaneMessage = new JScrollPane();
        jTextAreaMessage = new JTextArea();
        jTextUserName = new JTextField();
        jButtonConnect = new JButton();
        jScrollPaneListUser = new JScrollPane();
        jListUser = new JList(listClient);
        jTextSendMessage = new JTextField();
        jButtonSend = new JButton();
        jLabelUserList = new JLabel();

        setDefaultCloseOperation(WindowConstants.EXIT_ON_CLOSE);
        setTitle("Chat Client");
        setResizable(false);
        getContentPane().setLayout(null);

        jTextAreaMessage.setColumns(20);
        jTextAreaMessage.setEditable(false);
        jTextAreaMessage.setRows(5);
        jTextAreaMessage.setAutoscrolls(false);
        jScrollPaneMessage.setViewportView(jTextAreaMessage);

        getContentPane().add(jScrollPaneMessage);
        jScrollPaneMessage.setBounds(10, 10, 340, 240);

        jTextUserName.addActionListener(this);
        getContentPane().add(jTextUserName);
        jTextUserName.setBounds(360, 10, 100, 20);

        jButtonConnect.setFont(new java.awt.Font("Times New Roman", 0, 11));
        jButtonConnect.setText("Connect");
        
        getContentPane().add(jButtonConnect);
        jButtonConnect.setBounds(470, 10, 80, 21);

        jListUser.setToolTipText("List of User");
        jScrollPaneListUser.setViewportView(jListUser);

        getContentPane().add(jScrollPaneListUser);
        jScrollPaneListUser.setBounds(360, 50, 190, 200);

        jTextSendMessage.addActionListener(this);
        
        getContentPane().add(jTextSendMessage);
        jTextSendMessage.setBounds(10, 260, 340, 30);

        jButtonSend.setFont(new java.awt.Font("Times New Roman", 0, 11));
        jButtonSend.setText("Send");
        jButtonSend.addActionListener(this);
        
        getContentPane().add(jButtonSend);
        jButtonSend.setBounds(360, 261, 70, 30);

        jLabelUserList.setFont(new java.awt.Font("Arial Black", 0, 11));
        jLabelUserList.setText("User List");
        getContentPane().add(jLabelUserList);
        jLabelUserList.setBounds(360, 30, 55, 17);

        java.awt.Dimension screenSize = java.awt.Toolkit.getDefaultToolkit().getScreenSize();
        setBounds((screenSize.width-570)/2, (screenSize.height-330)/2, 570, 330);        
        
        jButtonConnect.addActionListener(this);
        jButtonSend.setEnabled(false);
    }
    
    public static void main(String args[])
    {
        JFrame.setDefaultLookAndFeelDecorated(true);
        try
		{
			RMIClient rm = new RMIClient();
			rm.setIPAddress(args[0]);
			rm.setPort(Integer.parseInt(args[1]));
			rm.setServer(ipAddress);
			rm.setVisible(true);
		}
		catch (ArrayIndexOutOfBoundsException ae)
		{
			JOptionPane.showMessageDialog(null, "Please insert the port", "ATTENTION", JOptionPane.INFORMATION_MESSAGE);
			System.exit(-1);
		}
    }
    
    private void setIPAddress(String ipAddress)
    {
    	this.ipAddress = ipAddress;
    }
    
    private void setPort(int port)
    {
    	this.port = port;
    }
    
    private String getIPAddress()
    {
    	return ipAddress;
    }
    
    private int getPort()
    {
    	return port;
    }
    
    private void setServer(String ipAddress)  
    {
    	try
    	{
    		c = (Chatting) Naming.lookup("rmi://" + ipAddress + "/ChatService");
    	}
    	catch (MalformedURLException murle)
		{
			System.out.println();
			System.out.println("MalformedURLException");
			System.out.println(murle);
		}
		catch (RemoteException re)
		{
			System.out.println();
			System.out.println("RemoteException");
			System.out.println(re);
		}
		catch (NotBoundException nbe)
		{
			System.out.println();
			System.out.println("NotBoundException");
			System.out.println(nbe);
		}
        catch (Exception e)
        {
            System.out.println(e);
        }
    }    

    public void updateMessage(String username, String message) throws RemoteException
	{
		jTextAreaMessage.append(username + " >> " + message + "\n");
	}
	
	public void run()
    {
    	System.out.println(socket);
    	try
    	{
			in = new BufferedReader(new InputStreamReader(socket.getInputStream()));

			while((message = in.readLine()) != null)
			{
				System.out.println(message);
				String[] fromServer = message.split(SEPARATOR);
				if (fromServer[0].equals(ONLINE) || fromServer[0].equals(OFFLINE))
				{
					updateClient(c.getClientList());
				}
				else if (fromServer[0].equals(PUBLICMESSAGE))
				{
					String sender = fromServer[1];
					String content = fromServer[2];
					updateMessage(sender, content);
				}
			}

    		in.close();
			socket.close();
		}
    	catch(java.net.UnknownHostException e)
    	{}
    	catch(IOException e)
    	{}
    }
    
    public void updateClient(ArrayList allClientList) throws RemoteException
	{
		listClient.clear();
		int i = 0;
		String username;

		for(i=0; i<allClientList.size(); i++)
		{
			username = allClientList.get(i).toString();
			listClient.addElement(username);
		}
	}
    
    public void actionPerformed(ActionEvent ae)
    {
    	if (ae.getActionCommand().equals("Connect") && !jTextUserName.getText().equals(""))
		{
			try
			{
				System.out.println(jTextUserName.getText());
				c.connect(jTextUserName.getText());
				socket = new Socket(ipAddress, port);
				jTextUserName.setEditable(false);
				jButtonConnect.setText("Disconnect");
				System.out.println("You are connect to server");
				thread.start();
				jButtonSend.setEnabled(true);
			}
			catch (RemoteException re)
			{}
	    	catch(java.net.UnknownHostException e)
	    	{
	    		JOptionPane.showMessageDialog(null, "Can not connect to server " + ipAddress ,"WARNING", JOptionPane.WARNING_MESSAGE);
	 			System.exit(-1);
	    	}
	    	catch(IOException e)
	    	{
	    		JOptionPane.showMessageDialog(null, "The server " + ipAddress + " on port " + port + " is not found!", "ERROR",JOptionPane.ERROR_MESSAGE);
	 			System.exit(-1);
	    	}
		}
		else if (ae.getActionCommand().equals("Disconnect"))
		{
			try
			{
				c.disconnect(jTextUserName.getText());
				jTextUserName.setText("");
				jTextUserName.setEditable(true);
				listClient.clear();				
				jButtonConnect.setText("Connect");
				thread.interrupt();
			}
			catch (RemoteException re)
			{}
		}
		else if (ae.getSource() == jButtonSend && !jTextSendMessage.getText().equals(""))
		{
			try
			{
				c.sendPublicMessage(PUBLICMESSAGE, jTextUserName.getText(), jTextSendMessage.getText());
				jTextSendMessage.setText("");
			}
			catch (RemoteException re)
			{}
		}
    }
}
