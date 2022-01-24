/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package p2pfailuredectector;

/**
 *
 * @author qaisar
 */
public class PeerInfo {
    int port;
    String hostName;

    
    public void setHostName(String hostName) {
        this.hostName = hostName;
    }

    public void setPort(int port) {
        this.port = port;
    }

    public String getHostName() {
        return hostName;
    }

    public int getPort() {
        return port;
    }
    
}
