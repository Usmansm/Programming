/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package database;

import java.io.FileInputStream;
import java.sql.Connection;
import java.sql.DriverManager;
import java.util.Properties;

/**
 *
 * @author ARSLAN and Bilal
 */
public class ClassConnection {



    public Connection setConnection(Connection conn)

    {
    	try
	{

            conn = null;
            Class.forName("org.postgresql.Driver");

          conn = DriverManager.getConnection("jdbc:postgresql://localhost:5432/postgres","postgres", "123");
          
    }catch(Exception e)
		{

		}


    		return conn;

    }

}
