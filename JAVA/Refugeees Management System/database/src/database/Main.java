/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package database;

import javax.swing.JFrame;



/**
 *
 * @author ARSLAN and bilal
 */
public class Main {

    /**
     * @param args the command line arguments
     */



    public static void main(String[] args) {
        // TODO code application logic here
        Camps c = new Camps();
        
        c.setDefaultCloseOperation(JFrame.EXIT_ON_CLOSE);
        c.setSize(900, 600);
        c.setVisible(true);
    }

}
