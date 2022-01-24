package smtpserver;

import java.util.Date;
/**
 *
 * @author Qaisar
 */
public class StatusInfo {
    
int id;
String from;
String to;
Date scheduledTime;
Date requestTime;
public enum status {SENT,PENDING}
status CurStatus;

    public StatusInfo() {
    
    }


}
