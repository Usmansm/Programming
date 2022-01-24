/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package xmprpc.test;

/**
 *
 * @author Rizwan
 */
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

import java.util.Vector;
import javax.swing.JOptionPane;
import org.apache.xmlrpc.XmlRpcClient;
/**
 *
 * @author qaisar
 */
public class User {

    public static void main (String [] Args)
    {
    try{
//
//     String guid=JOptionPane.showInputDialog(null,"Welcome User,Please enter your GUID to be enrolled\n Min=0 ,Max=10");
//     int int_guid= Integer.parseInt(guid);
//
//     JOptionPane.showMessageDialog(null,"Thank you!! Your IP address is automatically mapped, and it is :"+int_guid);
//
    XmlRpcClient comHandler = getRpcObject("localhost:8080");

//
//     comHandler.execute("server.storeGUID", params1);
//     Object o = comHandler.execute("server.storeIP", params1);
//
//     JOptionPane.showMessageDialog(null, "You are now enrolled, :" +o);
//
//

     String opt=JOptionPane.showInputDialog(null,"Welcome User :) ,Please Enter any of the options to Proceed\n 1 to Search Peer IP using GUID\n 2 to remove a peer and its resource from list\n 3 to Publish a resource on peer \n4 to check a resource on any Peer");
     int op= Integer.parseInt(opt);

     if(op==1)
     {


     String opt1=JOptionPane.showInputDialog(null,"Please Enter GUID to search");
     int op1= Integer.parseInt(opt1);
     Vector params1 = new Vector();
     params1.add(op1);
     Object o1=comHandler.execute("server.get",params1);
     JOptionPane.showMessageDialog(null, "Status :"+o1);
     }

     if(op==2)
     {
     String opt1=JOptionPane.showInputDialog(null,"Please Enter GUID to remove peer and resource");
     int op2= Integer.parseInt(opt1);
     Vector params1 = new Vector();
     params1.add(op2);
     Object o2=comHandler.execute("server.remove",params1);
     JOptionPane.showMessageDialog(null, "Status :"+o2);
     }

      if(op==3)
     {
    String opt1=JOptionPane.showInputDialog(null,"Please Enter a new String Resource to publish");
    String opt2_guid=JOptionPane.showInputDialog(null,"Now Enter the GUID to which you want the resource to be published ");

    int opt2_guidno=Integer.parseInt(opt2_guid);
     Vector params1 = new Vector();
     params1.add(opt2_guidno+"-"+opt1);
     Object o2=comHandler.execute("server.publish",params1);
     JOptionPane.showMessageDialog(null, "Status :"+o2);

      }


      if(op==4)
     {
    String opt1=JOptionPane.showInputDialog(null,"Please Enter GUID to search for resource");
    int opt1_=Integer.parseInt(opt1);
     Vector params1 = new Vector();
     params1.add(opt1_);
     Object o2=comHandler.execute("server.check",params1);
     JOptionPane.showMessageDialog(null, "Resource :"+o2);

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
