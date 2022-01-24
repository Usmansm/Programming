/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package xmprpc.test;
import java.util.Vector;
import javax.swing.JOptionPane;
import org.apache.xmlrpc.XmlRpcClient;
/**
 *
 * @author qaisar
 */
public class XMLRPCPeer {
    
    public static void main (String [] Args)
    {
    try{

     String guid=JOptionPane.showInputDialog(null,"You are a Peer,Please enter your GUID to be enrolled\n Min=0 ,Max=10");
     int int_guid= Integer.parseInt(guid);
     
     JOptionPane.showMessageDialog(null,"Thank you!! Your IP address is automatically mapped, and it is :"+int_guid);

     XmlRpcClient comHandler = getRpcObject("localhost:8080");
     Vector params1 = new Vector();
     params1.add(int_guid);

     comHandler.execute("server.storeGUID", params1);
     Object o = comHandler.execute("server.storeIP", params1);

     JOptionPane.showMessageDialog(null, "You are now enrolled, :" +o);



     String opt=JOptionPane.showInputDialog(null,"Enter 1 to check updated Peers GUID and\n 2 to check Upated Peer IP\n 3 to check your up to date resource\n 4 to change and update your resource\n5 to update your Leaf Set");
     int op= Integer.parseInt(opt);
    
     if(op==1)
     {
    Object o1=comHandler.execute("server.UpdatePeerGUID",params1);
    JOptionPane.showMessageDialog(null, "Updated GUID list :"+o1);
     }

     if(op==2)
     {
     Object o2=comHandler.execute("server.UpdatePeerIP", params1);
     JOptionPane.showMessageDialog(null, "Updated Peer IP list : "+o2);
     }

      if(op==3)
     {
     Object o3=comHandler.execute("server.GetResource", params1);
     JOptionPane.showMessageDialog(null, "You have received Resource : "+o3);
     }

     if(op==4)
     {
         String res=JOptionPane.showInputDialog(null,"Enter new String Resource");
        String ress=int_guid+"-"+res;
         Vector resource = new Vector();


        resource.add(ress);
         Object o4=comHandler.execute("server.SubmitUpdatedResource", resource );
         JOptionPane.showMessageDialog(null, "You have updated your Resource, Status : "+o4);
     }


      if(op==5)
     {

         Object o4=comHandler.execute("server.UpdatePeerLeafSet", params1 );
         JOptionPane.showMessageDialog(null, "You have updated your Leaf Set, Your upper and lower Peers Are "+o4+"\nNote: 0 means you have no upper ");
     }


    }catch (Exception ex)
    {
    System.err.println("XMLRPCClient.getRpcObject: " + ex.toString());
    }



    }
    static XmlRpcClient getRpcObject(String entryPoint) {
        try {

            XmlRpcClient comHandler = new XmlRpcClient("http://" + entryPoint);
            return comHandler;
        } catch (Exception exception) {
            System.err.println("XMLRPCClient.getRpcObject: " + exception.toString());
            return null;
        }

    }

    
}
