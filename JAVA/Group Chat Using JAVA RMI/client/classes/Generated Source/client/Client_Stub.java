// Stub class generated by rmic, do not edit.
// Contents subject to change without notice.

package client;

public final class Client_Stub
    extends java.rmi.server.RemoteStub
    implements client_board.ClientBoard, java.rmi.Remote
{
    private static final long serialVersionUID = 2;
    
    private static java.lang.reflect.Method $method_recieveMsg_0;
    
    static {
	try {
	    $method_recieveMsg_0 = client_board.ClientBoard.class.getMethod("recieveMsg", new java.lang.Class[] {java.lang.String.class});
	} catch (java.lang.NoSuchMethodException e) {
	    throw new java.lang.NoSuchMethodError(
		"stub class initialization failed");
	}
    }
    
    // constructors
    public Client_Stub(java.rmi.server.RemoteRef ref) {
	super(ref);
    }
    
    // methods from remote interfaces
    
    // implementation of recieveMsg(String)
    public java.lang.Object recieveMsg(java.lang.String $param_String_1)
	throws java.rmi.RemoteException
    {
	try {
	    Object $result = ref.invoke(this, $method_recieveMsg_0, new java.lang.Object[] {$param_String_1}, 2903897470876804266L);
	    return ((java.lang.Object) $result);
	} catch (java.lang.RuntimeException e) {
	    throw e;
	} catch (java.rmi.RemoteException e) {
	    throw e;
	} catch (java.lang.Exception e) {
	    throw new java.rmi.UnexpectedException("undeclared checked exception", e);
	}
    }
}
