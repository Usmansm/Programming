/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package xmprpc.test;
import org.apache.xmlrpc.WebServer;

import org.apache.xmlrpc.XmlRpcException;


/**
 *
 * @author qaisar
 */

///This class handles all the GUID of Peers and also updates the GUIDs of Peers
public class XMPRPCDATA {

    /**
     * @param args the command line arguments
     */
    public static void main(String[] args) {
        // TODO code application logic here
        XMPRPCDATA server = new XMPRPCDATA();
        server.startServer(8080);
    }

    int[] IP = new int[20];  //For time being, we are only working with 20 peers
    int[] GUID = new int[20];
    String[] resources = new String[20];//this array stores the updated resources for peers, and when peers request their updates, it sends them the updated resources posted by clients




    public String storeIP(int ip)
    {
        for(int i=0; i<11; i++){

            if(ip==i)
            {
                IP[i]=ip;

                return "Ip is Stored and Array Updated";


            }

        }


            return "You are already registered";

    
    }

    public String storeGUID(int guid)
    {
        for(int i=0; i<11; i++){
         if(guid==i)
            {
                GUID[i]=guid;

                return "GUID is Stored and Array Updated";

            }

        }
            return "You are already registered";
    }




     public String UpdatePeerLeafSet(int e)
    {

         if(GUID[e-1]!=0||GUID[e+1]!=0)
         {
    return ""+GUID[e-2]+":"+GUID[e-1]+":"+GUID[e+1]+":"+GUID[e+2];
        }
 else
     return ""+GUID[e-3]+":"+GUID[e-2]+":"+GUID[e+2]+":"+GUID[e+3];
    }




     public String UpdatePeerGUID(int e)
    {
         
    return "Slot 0 in Array :"+GUID[0]+"    Slot 1 in Array :"+GUID[1]+"\n Slot 2 in Array :"+GUID[2]+"    Slot 3 in Array :"+GUID[3]+" \nSlot 4 in Array :"+GUID[4]+"    Slot 5 in Array :"+GUID[5]+" \nSlot 6 in Array :"+GUID[6]+"    Slot 7 in Array :"+GUID[7]+" \nSlot 8 in Array :"+GUID[8]+"    Slot 9 in Array :"+GUID[9]+" \nSlot 10 in Array :"+GUID[10]+"\n Note: 0 means Array slot is not filled by any peer";
    }


     public String UpdatePeerIP()
    {
    return "Slot 0 in Array"+IP[0]+"    Slot 1 in Array :"+IP[1]+" \nSlot 2 in Array :"+IP[2]+" \nSlot 3 in Array :"+IP[3]+"    Slot 4 in Array :"+IP[4]+" \nSlot 5 in Array :"+IP[5]+"    Slot 6 in Array :"+IP[6]+" \nSlot 7 in Array :"+IP[7]+"    Slot 8 in Array :"+IP[8]+" \nSlot 9 in Array :"+IP[9]+"    Slot 10 in Array :"+IP[10]+" Note: 0 means Array slot is not filled by any peer";
    }


       public String GetResource(int g)
    {
    return resources[g];
       }



       public String SubmitUpdatedResource(String f)
    {
           String[] strArray = f.split("-");
        String part1=strArray[0];
        
       int given_guid=Integer.parseInt(part1);

      
        String part2=strArray[1];
        
        resources[given_guid]=part2;
        return "submitted";

       }


   /////////// ///////////Functions for Clients////////////////////////////







    public String get(int search_guid)
    {

        if(IP[search_guid]!=0)
        {

    return "The IP address associated with the requested GUID is :"+IP[search_guid];
        }
 else
     return "No peer with requested GUID exists";

    }






    public String check(int given_guid)
    {

        if(resources[given_guid].isEmpty())
        {

            return "No peer with requested GUID exists";

        }
 else

       return "The Resource is :"+resources[given_guid];

    }






    public String remove(int rem_guid)
    {

         if(GUID[rem_guid]!=0 || IP[rem_guid]!=0)
        {


     return" The peer with GUID :"+rem_guid+ "  and IP" +IP[rem_guid]+" has been removed from the system and its resource was :"+ resources[rem_guid];

         }
 else
     return "No peer with requested GUID exists";

    
     
    }



    

    public String publish(String resourceandguid) // for the  time being ,we are only publishing resources of strings on peers
    {
        String[] strArray = resourceandguid.split("-");
        String part1=strArray[0];
        int given_guid=Integer.parseInt(strArray[0]);

        String part2=strArray[1];

        resources[given_guid]=strArray[1];

        return "Resource :"+strArray[1]+" is saved on peer :"+strArray[0];


    }






    void startServer(int port)
    {
    
        WebServer server = new WebServer(port);
        server.addHandler("server", new XMPRPCDATA());
        server.start();
    }
}
