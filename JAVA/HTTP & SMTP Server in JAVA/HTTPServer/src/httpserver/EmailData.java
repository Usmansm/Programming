/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

package httpserver;
/**
 *
 * @author Qaisar
 */
public class EmailData {
    String from;
    String to;
    String subject;
    String smtp="smtp.kth.se";
    String message;
    String scheduleTime;

    public EmailData(String from, String to, String subject, String message, String scheduleTime)
    {
        this.from = from;
        this.to = to;
        this.subject = subject;
        this.message = message;
        this.scheduleTime = scheduleTime;
    }

    public String getFrom() {
        return from;
    }

    public void setFrom(String from) {
        this.from = from;
    }

    public String getMessage() {
        return message;
    }

    public void setMessage(String message) {
        this.message = message;
    }

    public String getScheduleTime() {
        return scheduleTime;
    }

    public void setScheduleTime(String scheduleTime) {
        this.scheduleTime = scheduleTime;
    }

    public String getSubject() {
        return subject;
    }

    public void setSubject(String subject) {
        this.subject = subject;
    }

    public String getTo() {
        return to;
    }

    public void setTo(String to) {
        this.to = to;
    }

}
