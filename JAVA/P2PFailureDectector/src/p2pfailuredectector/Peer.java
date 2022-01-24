/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package p2pfailuredectector;

import java.net.DatagramPacket;
import java.net.DatagramSocket;
import java.net.InetAddress;
import java.util.Hashtable;
import java.util.Iterator;
import java.util.Set;
import java.util.Vector;

/**
 *
 * @author qaisar
 */
public class Peer {

    PeerInfo self;
    Vector otherPeers;
    DatagramPacket receivePacket;
    DatagramSocket serverSocket;
    Boolean peersHeartBeats[];
    byte[] receiveData = new byte[1024];
    String sendHBData = "HB";
    int HBSendingTime = 5000; //2 seconds
    int HBCheckTime = 5500; //2.5 seconds
    Hashtable heartbeatData = new Hashtable();

    public Peer(String host, int port) {


        self = new PeerInfo();
        this.self.setHostName(host);
        this.self.setPort(port);
        otherPeers = new Vector();

    }

    public void startPeer() {
        try {
            serverSocket = new DatagramSocket(this.self.port);
            /////New thread for sending priodically heartbeats...........1
            Thread sendingThread = new Thread(new Runnable() {

                @Override
                public void run() {
                    sendHeartBeat();

                }
            });
            sendingThread.start();
            //////////////END-----------------------------------------1

            /////New thread for priodically checking heartbeats...........2
            Thread heartBeatCheckingThread = new Thread(new Runnable() {

                @Override
                public void run() {

                    checkFailure();

                }
            });
            heartBeatCheckingThread.start();
            //////////////END-----------------------------------------2

        } catch (Exception ex) {
            printError("Exception in Peer.startPeer() while creating serverSocket is: " + ex.getMessage());
        }

        while (true) {
            try {

                receivePacket = new DatagramPacket(receiveData, receiveData.length);
                serverSocket.receive(receivePacket);
                String sentence = new String(receivePacket.getData());

                InetAddress IPAddress = receivePacket.getAddress();
 
                int port = receivePacket.getPort();
                print("RECEIVED: From: " + IPAddress.getHostName() + ":" + port + " Data: " + sentence);
                receiveHeartBeat(IPAddress.getHostName(), port);

            } catch (Exception ex) {
                printError("Exception in Peer.startPeer() is " + ex.getMessage());
            }
        }
    }

    public void initializeHeartbeatData(String client) {
        heartbeatData.put(client, false);
    }

    public void receiveHeartBeat(String host, int port) {
        // heartbeatData.remove(host + ":" + port);
        heartbeatData.put(host + ":" + port, true);
    }

    public void sendHeartBeat() {

        while (true) {
            try {

                for (int i = 0; i < otherPeers.size(); i++) {

                    PeerInfo currentPeer = (PeerInfo) otherPeers.get(i);
                    DatagramPacket sendPacket = new DatagramPacket(sendHBData.getBytes(),
                            sendHBData.getBytes().length, InetAddress.getByName(currentPeer.getHostName()),
                            currentPeer.getPort());
                    print("Going to send heartbeat to " + currentPeer.getHostName() + ":" + currentPeer.getPort());
                    serverSocket.send(sendPacket);
                    print("Heartbeat sent to " + currentPeer.getHostName() + ":" + currentPeer.getPort());
                }
                print("Going to sleep at " + System.currentTimeMillis() / 1000);
                Thread.currentThread().sleep(HBSendingTime);
                print("Awake from sleep at " + System.currentTimeMillis() / 1000);

            } catch (Exception ex) {
                printError("Exception in Peer.sendHeartBeat() is " + ex.getMessage());
            }
        }
    }

    void checkFailure() {
        while (true) {
            try {
                Thread.currentThread().sleep(HBCheckTime);
                Set keys = heartbeatData.keySet();
                for (Iterator i = keys.iterator(); i.hasNext();) {
                    String client = (String) i.next();
                    Boolean flag = (Boolean) heartbeatData.get(client);
                    if (flag == false) {
                        printError("*********************************"
                                + "\nClient: " + client + " has crashed"
                                + "\n*********************************\n\n");
                    }
                    heartbeatData.put(client, false);
                }
            } catch (Exception ex) {
                printError("Exception in Peer.checkFailure() is " + ex.getMessage());
            }
        }
    }

    public void setOtherPeer(String host, int port) {
        PeerInfo peer = new PeerInfo();
        peer.setHostName(host);
        peer.setPort(port);
        otherPeers.add(peer);

    }

    void printError(String str) {
        System.err.println(str);
    }

    void print(String str) {
        System.out.println(str);
    }
}
