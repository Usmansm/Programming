/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package p2pfailuredectector;

import java.util.StringTokenizer;
import javax.swing.JOptionPane;

/**
 *
 * @author qaisar
 */
public class P2PFailureDectector {

    /**
     * @param args the command line arguments
     */
    public static void main(String[] args) {
        // TODO code application logic here
       
        Peer peer = new Peer(args[0], Integer.parseInt(args[1]));
        parseArguments(peer, args);
        peer.startPeer();

    }

    static void parseArguments(Peer peer, String[] args) {
        for (int i = 2; i < args.length; i++) {
            StringTokenizer tokens = new StringTokenizer(args[i], ":");
            String host = tokens.nextToken();
            int port = Integer.parseInt(tokens.nextToken());
            peer.setOtherPeer(host,port);
            peer.initializeHeartbeatData(args[i]);
        }
    }
}
