//------------------------------------------------------------------------------
// <copyright file="SkeletalViewer.cpp" company="Microsoft">
//     Copyright (c) Microsoft Corporation.  All rights reserved.
// </copyright>
//------------------------------------------------------------------------------

// This module provides sample code used to demonstrate Kinect NUI processing

// Note: 
//     Platform SDK lib path should be added before the VC lib
//     path, because uuid.lib in VC lib path may be older

#include "stdafx.h"
#include <strsafe.h>
#include "SkeletalViewer.h"
#include "resource.h"

#include "stdlib.h"
#include "stdio.h"

#include "gl/glut.h"
#include "tga.h"
# include <math.h>

extern int select;

//typedef enum _NUI_SKELETON_POSITION_INDEX
//{
//    NUI_SKELETON_POSITION_HIP_CENTER = 0,
//    NUI_SKELETON_POSITION_SPINE,
//    NUI_SKELETON_POSITION_SHOULDER_CENTER,
//    NUI_SKELETON_POSITION_HEAD,
//    NUI_SKELETON_POSITION_SHOULDER_LEFT,
//    NUI_SKELETON_POSITION_ELBOW_LEFT,
//    NUI_SKELETON_POSITION_WRIST_LEFT,
//    NUI_SKELETON_POSITION_HAND_LEFT,
//    NUI_SKELETON_POSITION_SHOULDER_RIGHT,
//    NUI_SKELETON_POSITION_ELBOW_RIGHT,
//    NUI_SKELETON_POSITION_WRIST_RIGHT,
//    NUI_SKELETON_POSITION_HAND_RIGHT,
//    NUI_SKELETON_POSITION_HIP_LEFT,
//    NUI_SKELETON_POSITION_KNEE_LEFT,
//    NUI_SKELETON_POSITION_ANKLE_LEFT,
//    NUI_SKELETON_POSITION_FOOT_LEFT,
//    NUI_SKELETON_POSITION_HIP_RIGHT,
//    NUI_SKELETON_POSITION_KNEE_RIGHT,
//    NUI_SKELETON_POSITION_ANKLE_RIGHT,
//    NUI_SKELETON_POSITION_FOOT_RIGHT,
//    NUI_SKELETON_POSITION_COUNT
//} NUI_SKELETON_POSITION_INDEX;



    //Values of x range from approximately −2.2 to 2.2.
    //Values of y range from approximately −1.6 to 1.6.
    //Values of z range from 0.0 to 4.0.


// Global Variables:
CSkeletalViewerApp  g_skeletalViewerApp;  // Application class

#define INSTANCE_MUTEX_NAME L"SkeletalViewerInstanceCheck"

//-------------------------------------------------------------------
// _tWinMain
//
// Entry point for the application
//-------------------------------------------------------------------

//............My code goes here......................................
float light_diffuse[]   = { 0.8, 0.8, 0.8, 1.0 };
float light_ambient[]   = { 0.1, 0.1, 0.1, 1.0 };
float light_specular[]  = { 0.5, 0.5, 0.5, 1.0 };
float light_position[]  = { 0.0, 10.0, 0.0, 1.0 };

char  g_SelectedColor = 'w';
int   g_Width;
int   g_Height;

static float lookY=0.0;
static float lookZ=600.0;
static float logo=0.0;
static int start=0;
static float frontC=0.0;
static int startfront=0;

int glmain();
void init();
void myMouseFunction( int button, int state, int mouseX, int mouseY );
void myKeyboardFunction( unsigned char key, int mouseX, int mouseY );
void Reshape( int width, int height );
void timer( int val );
void display();
//...........brrrrrrrrrr.............................................


int APIENTRY _tWinMain(HINSTANCE hInstance, HINSTANCE hPrevInstance, LPTSTR lpCmdLine, int 

nCmdShow)
{
//	printf(".");
	//char *t="Hello";
	//g_Width = g_Height = 400;

 //   glutInitDisplayMode( GLUT_DOUBLE | GLUT_RGBA | GLUT_DEPTH);
 //   glutInitWindowSize( g_Width, g_Height ); 
 //   glutInitWindowPosition( 50, 50 );
 //   glutCreateWindow( t );


 //   glClearColor( 0.0, 0.0, 0.0, 0.0 );

 //   glMatrixMode( GL_PROJECTION );
 //   glLoadIdentity();

 //   gluPerspective( 50.0, 1.0, 200, 1000 );
 //   //glOrtho( -5.0, +5.0, -5.0, +5.0, +5.0, -5.0 );

 //   glLightfv(GL_LIGHT0, GL_DIFFUSE,  light_diffuse);
 //   glLightfv(GL_LIGHT0, GL_AMBIENT,  light_ambient );
 //   glLightfv(GL_LIGHT0, GL_SPECULAR, light_specular );
 //   glLightfv(GL_LIGHT0, GL_POSITION, light_position);

 //   glShadeModel(GL_SMOOTH);
 //   //glShadeModel(GL_FLAT);

 //   glEnable(GL_LIGHTING);
 //   glEnable(GL_LIGHT0);
 //   glEnable(GL_DEPTH_TEST);

	////glCullFace(GL_FRONT_AND_BACK );
	//glDisable(GL_CULL_FACE );

 //   glMatrixMode( GL_MODELVIEW );

 //   glutMouseFunc( myMouseFunction );
 //   glutKeyboardFunc( myKeyboardFunction );

 //   glutReshapeFunc( Reshape );
 //   glutDisplayFunc( display ); 

 //   glutMainLoop();





	

///////////////////////////////////////////////////////////////////////////////////////////////

////
    MSG       msg;
    WNDCLASS  wc;
      
    // unique mutex, if it already exists there is already an instance of this app running
    // in that case we want to show the user an error dialog
    HANDLE hMutex = CreateMutex( NULL, FALSE, INSTANCE_MUTEX_NAME );
    if ( (hMutex != NULL) && (GetLastError() == ERROR_ALREADY_EXISTS) ) 
    {
        TCHAR szAppTitle[256] = { 0 };
        TCHAR szRes[512] = { 0 };

        //load the app title
        LoadString( hInstance, IDS_APPTITLE, szAppTitle, _countof(szAppTitle) );

        //load the error string
        LoadString( hInstance, IDS_ERROR_APP_INSTANCE, szRes, _countof(szRes) );

        MessageBox( NULL, szRes, szAppTitle, MB_OK | MB_ICONHAND );

        CloseHandle(hMutex);
        return -1;
    }

    // Store the instance handle
    g_skeletalViewerApp.m_hInstance = hInstance;

    // Dialog custom window class
    ZeroMemory( &wc,sizeof(wc) );
    wc.style = CS_HREDRAW | CS_VREDRAW;
    wc.cbWndExtra = DLGWINDOWEXTRA;
    wc.hInstance = hInstance;
    wc.hCursor = LoadCursor(NULL,IDC_ARROW);
    wc.hIcon = LoadIcon(hInstance,MAKEINTRESOURCE(IDI_SKELETALVIEWER));
    wc.lpfnWndProc = DefDlgProc;
    wc.lpszClassName = SZ_APPDLG_WINDOW_CLASS;
    if( !RegisterClass(&wc) )
    {
        return 0;
    }

	
    // Create main application window
    HWND hWndApp = CreateDialogParam(
        hInstance,
        MAKEINTRESOURCE(IDD_APP),
        NULL,
        (DLGPROC) CSkeletalViewerApp::MessageRouter, 
        reinterpret_cast<LPARAM>(&g_skeletalViewerApp));

    // Show window
    ShowWindow(hWndApp,nCmdShow); 
	glmain();
    // Main message loop:
    while( GetMessage( &msg, NULL, 0, 0 ) ) 
    {
        // If a dialog message will be taken care of by the dialog proc
        if ( (hWndApp != NULL) && IsDialogMessage(hWndApp, &msg) )
        {
            continue;
        }

        // otherwise do our window processing
        TranslateMessage(&msg);
        DispatchMessage(&msg);
    }

    CloseHandle( hMutex );
	

    return static_cast<int>(msg.wParam);
}

//-------------------------------------------------------------------
// Constructor
//-------------------------------------------------------------------
CSkeletalViewerApp::CSkeletalViewerApp() : m_hInstance(NULL)
{
    ZeroMemory(m_szAppTitle, sizeof(m_szAppTitle));
    LoadString(m_hInstance, IDS_APPTITLE, m_szAppTitle, _countof(m_szAppTitle));

    m_fUpdatingUi = false;
    Nui_Zero();

    // Init Direct2D
    D2D1CreateFactory( D2D1_FACTORY_TYPE_SINGLE_THREADED, &m_pD2DFactory );
}

//-------------------------------------------------------------------
// Destructor
//-------------------------------------------------------------------
CSkeletalViewerApp::~CSkeletalViewerApp()
{
    // Clean up Direct2D
    SafeRelease( m_pD2DFactory );

    Nui_Zero();
    SysFreeString(m_instanceId);
}

void CSkeletalViewerApp::ClearComboBox()
{
    for ( long i = 0; i < SendDlgItemMessage(m_hWnd, IDC_CAMERAS, CB_GETCOUNT, 0, 0); i++ )
    {
        SysFreeString( reinterpret_cast<BSTR>( SendDlgItemMessage(m_hWnd, IDC_CAMERAS, 

CB_GETITEMDATA, i, 0) ) );
    }
    SendDlgItemMessage(m_hWnd, IDC_CAMERAS, CB_RESETCONTENT, 0, 0);
}

void CSkeletalViewerApp::UpdateComboBox()
{
    m_fUpdatingUi = true;
    ClearComboBox();

    int numDevices = 0;
    HRESULT hr = NuiGetSensorCount(&numDevices);

    if ( FAILED(hr) )
    {
        return;
    }

    EnableWindow(GetDlgItem(m_hWnd, IDC_APPTRACKING), numDevices > 0);

    long selectedIndex = 0;
    for ( int i = 0; i < numDevices; i++ )
    {
        INuiSensor *pNui = NULL;
        HRESULT hr = NuiCreateSensorByIndex(i,  &pNui);
        if (SUCCEEDED(hr))
        {
            HRESULT status = pNui ? pNui->NuiStatus() : E_NUI_NOTCONNECTED;
            if (status == E_NUI_NOTCONNECTED)
            {
                pNui->Release();
                continue;
            }
            
            WCHAR kinectName[MAX_PATH];
            StringCchPrintfW( kinectName, _countof(kinectName), L"Kinect %d", i);
            long index = static_cast<long>( SendDlgItemMessage(m_hWnd, IDC_CAMERAS, 

CB_ADDSTRING, 0, reinterpret_cast<LPARAM>(kinectName)) );
            SendDlgItemMessage( m_hWnd, IDC_CAMERAS, CB_SETITEMDATA, index, 

reinterpret_cast<LPARAM>(pNui->NuiUniqueId()) );
            if (m_pNuiSensor && pNui == m_pNuiSensor)
            {
                selectedIndex = index;
            }
            pNui->Release();
        }
    }

    SendDlgItemMessage(m_hWnd, IDC_CAMERAS, CB_SETCURSEL, selectedIndex, 0);
    m_fUpdatingUi = false;
}

void CSkeletalViewerApp::UpdateTrackingComboBoxes()
{
    SendDlgItemMessage(m_hWnd, IDC_TRACK0, CB_RESETCONTENT, 0, 0);
    SendDlgItemMessage(m_hWnd, IDC_TRACK1, CB_RESETCONTENT, 0, 0);

    SendDlgItemMessage(m_hWnd, IDC_TRACK0, CB_ADDSTRING, 0, reinterpret_cast<LPARAM>(L"0"));
    SendDlgItemMessage(m_hWnd, IDC_TRACK1, CB_ADDSTRING, 0, reinterpret_cast<LPARAM>(L"0"));
    
    SendDlgItemMessage(m_hWnd, IDC_TRACK0, CB_SETITEMDATA, 0, 0);
    SendDlgItemMessage(m_hWnd, IDC_TRACK1, CB_SETITEMDATA, 0, 0);

    bool setCombo0 = false;
    bool setCombo1 = false;
    
    for ( int i = 0 ; i < NUI_SKELETON_COUNT ; i++ )
    {
        if ( m_SkeletonIds[i] != 0 )
        {
            WCHAR trackingId[MAX_PATH];
            StringCchPrintfW(trackingId, _countof(trackingId), L"%d", m_SkeletonIds[i]);
            LRESULT pos0 = SendDlgItemMessage(m_hWnd, IDC_TRACK0, CB_ADDSTRING, 0, 

reinterpret_cast<LPARAM>(trackingId));
            LRESULT pos1 = SendDlgItemMessage(m_hWnd, IDC_TRACK1, CB_ADDSTRING, 0, 

reinterpret_cast<LPARAM>(trackingId));

            SendDlgItemMessage(m_hWnd, IDC_TRACK0, CB_SETITEMDATA, pos0, m_SkeletonIds[i]);
            SendDlgItemMessage(m_hWnd, IDC_TRACK1, CB_SETITEMDATA, pos1, m_SkeletonIds[i]);

            if ( m_TrackedSkeletonIds[0] == m_SkeletonIds[i] )
            {
                SendDlgItemMessage(m_hWnd, IDC_TRACK0, CB_SETCURSEL, pos0, 0);
                setCombo0 = true;
            }

            if ( m_TrackedSkeletonIds[1] == m_SkeletonIds[i] )
            {
                SendDlgItemMessage(m_hWnd, IDC_TRACK1, CB_SETCURSEL, pos1, 0);
                setCombo1 = true;
            }
        }
    }
    
    if ( !setCombo0 )
    {
        SendDlgItemMessage( m_hWnd, IDC_TRACK0, CB_SETCURSEL, 0, 0 );
    }

    if ( !setCombo1 )
    {
        SendDlgItemMessage( m_hWnd, IDC_TRACK1, CB_SETCURSEL, 0, 0 );
    }
}

void CSkeletalViewerApp::UpdateTrackingFromComboBoxes()
{
    LRESULT trackingIndex0 = SendDlgItemMessage(m_hWnd, IDC_TRACK0, CB_GETCURSEL, 0, 0);
    LRESULT trackingIndex1 = SendDlgItemMessage(m_hWnd, IDC_TRACK1, CB_GETCURSEL, 0, 0);

    LRESULT trackingId0 = SendDlgItemMessage(m_hWnd, IDC_TRACK0, CB_GETITEMDATA, 

trackingIndex0, 0);
    LRESULT trackingId1 = SendDlgItemMessage(m_hWnd, IDC_TRACK0, CB_GETITEMDATA, 

trackingIndex1, 0);

    Nui_SetTrackedSkeletons(static_cast<int>(trackingId0), static_cast<int>(trackingId1));
}

LRESULT CALLBACK CSkeletalViewerApp::MessageRouter( HWND hwnd, UINT uMsg, WPARAM wParam, LPARAM 

lParam )
{
    CSkeletalViewerApp *pThis = NULL;
    
    if ( WM_INITDIALOG == uMsg )
    {
        pThis = reinterpret_cast<CSkeletalViewerApp*>(lParam);
        SetWindowLongPtr(hwnd, GWLP_USERDATA, reinterpret_cast<LONG_PTR>(pThis));
        NuiSetDeviceStatusCallback( &CSkeletalViewerApp::Nui_StatusProcThunk, pThis );
    }
    else
    {
        pThis = reinterpret_cast<CSkeletalViewerApp*>(::GetWindowLongPtr(hwnd, GWLP_USERDATA));
    }

    if ( NULL != pThis )
    {
        return pThis->WndProc( hwnd, uMsg, wParam, lParam );
    }
	
    return 0;
}

//-------------------------------------------------------------------
// WndProc
//
// Handle windows messages
//-------------------------------------------------------------------
LRESULT CALLBACK CSkeletalViewerApp::WndProc(HWND hWnd, UINT message, WPARAM wParam, LPARAM 

lParam)
{
    switch(message)
    {
        case WM_INITDIALOG:
        {
            LOGFONT lf;

            // Clean state the class
            Nui_Zero();

            // Bind application window handle
            m_hWnd = hWnd;

            // Set the font for Frames Per Second display
            GetObject( (HFONT) GetStockObject(DEFAULT_GUI_FONT), sizeof(lf), &lf );
            lf.lfHeight *= 4;
            m_hFontFPS = CreateFontIndirect(&lf);
            SendDlgItemMessage( hWnd, IDC_FPS, WM_SETFONT, (WPARAM) m_hFontFPS, 0 );

            UpdateComboBox();
            SendDlgItemMessage(m_hWnd, IDC_CAMERAS, CB_SETCURSEL, 0, 0);

            // Initialize and start NUI processing
            Nui_Init();
        }
        break;

        case WM_USER_UPDATE_FPS:
        {
            ::SetDlgItemInt( m_hWnd, static_cast<int>(wParam), static_cast<int>(lParam), FALSE 

);
        }
        break;

        case WM_USER_UPDATE_COMBO:
        {
            UpdateComboBox();
        }
        break;

        case WM_COMMAND:
        {
            if( HIWORD( wParam ) == CBN_SELCHANGE )
            {
                switch (LOWORD(wParam))
                {
                    case IDC_CAMERAS:
                    {
                        LRESULT index = ::SendDlgItemMessage(m_hWnd, IDC_CAMERAS, CB_GETCURSEL, 

0, 0);

                        // Don't reconnect as a result of updating the combo box
                        if ( !m_fUpdatingUi )
                        {
                            Nui_UnInit();
                            Nui_Zero();
                            Nui_Init(reinterpret_cast<BSTR>(::SendDlgItemMessage(m_hWnd, 

IDC_CAMERAS, CB_GETITEMDATA, index, 0)));
                        }
                    }
                    break;

                    case IDC_TRACK0:
                    case IDC_TRACK1:
                    {
                        UpdateTrackingFromComboBoxes();
                    }
                    break;
                }
            }
            else if ( HIWORD( wParam ) == BN_CLICKED )
            {
                switch (LOWORD(wParam))
                {
                    case IDC_APPTRACKING:
                    {
                        bool checked = IsDlgButtonChecked(m_hWnd, IDC_APPTRACKING) == 

BST_CHECKED;
                        m_bAppTracking = checked;

                        EnableWindow( GetDlgItem(m_hWnd, IDC_TRACK0), checked );
                        EnableWindow( GetDlgItem(m_hWnd, IDC_TRACK1), checked );

                        if ( checked )
                        {
                            UpdateTrackingComboBoxes();
                        }

                        Nui_SetApplicationTracking(checked);

                        if ( checked )
                        {
                            UpdateTrackingFromComboBoxes();
                        }
                    }
                    break;
                }
            }
        }
        break;

        // If the titlebar X is clicked destroy app
        case WM_CLOSE:
            DestroyWindow(hWnd);
            break;

        case WM_DESTROY:
            // Uninitialize NUI
            Nui_UnInit();

            // Other cleanup
            ClearComboBox();
            DeleteObject(m_hFontFPS);

            // Quit the main message pump
            PostQuitMessage(0);
            break;
    }

    return FALSE;
}

//-------------------------------------------------------------------
// MessageBoxResource
//
// Display a MessageBox with a string table table loaded string
//-------------------------------------------------------------------
int CSkeletalViewerApp::MessageBoxResource( UINT nID, UINT nType )
{
    static TCHAR szRes[512];

    LoadString( m_hInstance, nID, szRes, _countof(szRes) );
    return MessageBox( m_hWnd, szRes, m_szAppTitle, nType );
}
//...........My rest of the code.......................................
void showMenuItem (int val)
{
    printf ("Menu Item: %d\n", val);
}

int glmain()
{
	int menu,submenu;

	submenu = glutCreateMenu(showMenuItem);
	glutAddMenuEntry("Head", 1);
	glutAddMenuEntry("Left Hand", 2);
	glutAddMenuEntry("Right Hand", 3);
	glutAddMenuEntry("Left Foot", 4);
	glutAddMenuEntry("Right Foot", 5);

	menu = glutCreateMenu(showMenuItem);
	glutAddSubMenu("Tracking",submenu);
	glutAttachMenu(GLUT_RIGHT_BUTTON);

	g_Width =800;
	g_Height = 600;

    glutInitDisplayMode( GLUT_DOUBLE | GLUT_RGBA | GLUT_DEPTH);
    glutInitWindowSize( g_Width, g_Height ); 
    glutInitWindowPosition( 50, 50 );
    glutCreateWindow( "My OpenGL Appliction" );

    init();

    glutMouseFunc( myMouseFunction );
    glutKeyboardFunc( myKeyboardFunction );

    glutReshapeFunc( Reshape );
    glutDisplayFunc( display ); 

    glutMainLoop();

    return 0;   

    return 0;
}

static float cameraY=0.0;
static int rotationAngle=0;
void init(void) 
{



    glClearColor( 1.0, 0.1, 0.0, 0.3 );

    glMatrixMode( GL_PROJECTION );
    glLoadIdentity();

    gluPerspective( 50.0, 1.0, 200, 1000 );
    //glOrtho( -5.0, +5.0, -5.0, +5.0, +5.0, -5.0 );

    glLightfv(GL_LIGHT0, GL_DIFFUSE,  light_diffuse);
    glLightfv(GL_LIGHT0, GL_AMBIENT,  light_ambient );
    glLightfv(GL_LIGHT0, GL_SPECULAR, light_specular );
    glLightfv(GL_LIGHT0, GL_POSITION, light_position);

    glShadeModel(GL_SMOOTH);
    //glShadeModel(GL_FLAT);

    glEnable(GL_LIGHTING);
    glEnable(GL_LIGHT0);
    glEnable(GL_DEPTH_TEST);

	//glCullFace(GL_FRONT_AND_BACK );
	glDisable(GL_CULL_FACE );

    glMatrixMode( GL_MODELVIEW );

    if ( loadTGA ("fish1.tga", 10 ) == false )
        printf ("\nError: File myQuakeTexture.tga not found!");
	
}

void myMouseFunction( int button, int state, int mouseX, int mouseY ) 
{

}




void myKeyboardFunction( unsigned char key, int mouseX, int mouseY )
{
    switch( key )
    {
    case 'r':
		rotationAngle=0;
		cameraY=0.0;
		frontC=0.0;
		break;
    case 'R':
		rotationAngle=0;
		cameraY=0.0;
		frontC=0.0;
		break;
	case 's':
		start=1;
		break;
	case 'S':
		start=1;
		break;
	case 'q':
		lookZ--;
		break;
	case 'Q':
		lookZ++;
		break;
    case 'g':
    case 'G':
	case 'h':
		select=11;
		break;
    case 'b':
		select=7;
		break;
    case 'B':
		select=3;
		break;
    case 'w':
		select=15;
		break;
	case 'W':
		select=19;

        g_SelectedColor = key;

        break;

    case '1':
		rotationAngle++;
		break;
    case '2':
		rotationAngle--;
		break;
    case '3':
		lookY--;
		break;
    case '4':
		lookY++;
		break;
    case '5':
        break;

	case 'f':
		//frontC++;
		startfront=1;
		break;
	case 'F':
		startfront=0;
		break;

    case 27:  // Esc key
        exit(0);
        break;  // redundant

    default:
        break;
    }
}


void Reshape( int width, int height )
{
    glClear(GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);

    g_Width  = width; 
    g_Height = height;

    glViewport (0, 0, g_Width, g_Height);

    glMatrixMode (GL_PROJECTION);
    glLoadIdentity ();

    gluPerspective( 50.0, 1.0, 200, 1000 );
    //glOrtho( -5.0, +5.0, -5.0, +5.0, +5.0, -5.0 );
}

void timer( int val )
{
    display();
}




unsigned char *imageData = NULL;
unsigned char *imageData1 = NULL;
 BYTE buff[640*480*4];
 BYTE  my[320*240*4];
 Vector4 trap_vector;
 Vector4 trap_vector1;

 
 float x,x1;
 float y,y2;
 float z,z1;

  void drawScreenFront()
{
	  	glEnable(GL_TEXTURE_2D);
		
	imageData=buff;

      glPixelStorei (GL_UNPACK_ALIGNMENT, 1);
    glTexParameteri (GL_TEXTURE_2D, GL_TEXTURE_WRAP_S, GL_REPEAT);
    glTexParameteri (GL_TEXTURE_2D, GL_TEXTURE_WRAP_T, GL_REPEAT);
    glTexParameteri (GL_TEXTURE_2D, GL_TEXTURE_MAG_FILTER, GL_NEAREST);
    glTexParameteri (GL_TEXTURE_2D, GL_TEXTURE_MAG_FILTER, GL_LINEAR);
    glTexParameteri (GL_TEXTURE_2D, GL_TEXTURE_MIN_FILTER, GL_NEAREST);
    glTexParameteri (GL_TEXTURE_2D, GL_TEXTURE_MIN_FILTER, GL_LINEAR);
    glTexEnvf (GL_TEXTURE_ENV, GL_TEXTURE_ENV_MODE, GL_MODULATE);
    glTexImage2D (GL_TEXTURE_2D, 0, GL_RGBA, 640, 480, 0, GL_RGBA, GL_UNSIGNED_BYTE, 

imageData);
	

	
	glPushMatrix();

	glRotatef( rotationAngle, 0.0, 1.0, 0.0 );
	
	if(startfront==1 && frontC <=90.0)
	{
		glRotatef( frontC++, 1.0, 0.0, 0.0 );
	}
	else if(startfront==0 && frontC >=0.0)
		glRotatef( frontC--, 1.0, 0.0, 0.0 );
	else if(startfront==1 && frontC >90.0)
		glRotatef( 90, 1.0, 0.0, 0.0 );
	
	
	glTranslatef(-76.0,-112.0,0.0);

	glBegin(GL_POLYGON);
	//glVertex3f(0.0,0.0,0.0);
	glTexCoord2d(0,0);
	glVertex3f(0.0,150.0,0.0);
	glTexCoord2d(1,0);
	glVertex3f(200.0,150.0,0.0);
	glTexCoord2d(1,1);

	glVertex3f(200.0,0.0,0.0);
	glTexCoord2d(0,1);
	glVertex3f(0.0,0.0,0.0);
	glEnd();
	glPopMatrix();



}

  
void drawScreenBack()
{
	glEnable(GL_TEXTURE_2D);
	
		imageData1=my;
      glPixelStorei (GL_UNPACK_ALIGNMENT, 1);
    glTexParameteri (GL_TEXTURE_2D, GL_TEXTURE_WRAP_S, GL_REPEAT);
    glTexParameteri (GL_TEXTURE_2D, GL_TEXTURE_WRAP_T, GL_REPEAT);
    glTexParameteri (GL_TEXTURE_2D, GL_TEXTURE_MAG_FILTER, GL_NEAREST);
    glTexParameteri (GL_TEXTURE_2D, GL_TEXTURE_MAG_FILTER, GL_LINEAR);
    glTexParameteri (GL_TEXTURE_2D, GL_TEXTURE_MIN_FILTER, GL_NEAREST);
    glTexParameteri (GL_TEXTURE_2D, GL_TEXTURE_MIN_FILTER, GL_LINEAR);
    glTexEnvf (GL_TEXTURE_ENV, GL_TEXTURE_ENV_MODE, GL_MODULATE);
	glTexImage2D (GL_TEXTURE_2D, 0, GL_RGBA, 320, 240, 0, GL_RGBA, GL_UNSIGNED_BYTE, 

imageData1);
	

	glPushMatrix();
	glRotatef( rotationAngle, 0.0, 1.0, 0.0 );
	//glRotatef( 45, 0.0, 1.0, 0.0 );
//	glTranslatef(-76.0,-112.0,20.0);
	glTranslatef(-76.0,-112.0,0.0);
	glTranslatef(0.0,50.0,-200.0);
	
	glBegin(GL_POLYGON);
	//glVertex3f(0.0,0.0,0.0);
	glTexCoord2d(0,0);
	glVertex3f(0.0,150.0,0.0);
	glTexCoord2d(1,0);
	glVertex3f(200.0,150.0,0.0);
	glTexCoord2d(1,1);
	glVertex3f(200.0,0.0,0.0);
	glTexCoord2d(0,1);
	glVertex3f(0.0,0.0,0.0);
	glEnd();

	glPopMatrix();

}




void drawScreenLeft()
{
		glEnable(GL_TEXTURE_2D);
	glBindTexture(GL_TEXTURE_2D,10);

	

	glPushMatrix();
	glRotatef( rotationAngle, 0.0, 1.0, 0.0 );

	glTranslatef(-76.0,-112.0,0.0);
	glTranslatef(-25.0,20.0,-150.0);
	
	glBegin(GL_POLYGON);
	glVertex3f(0.0,0.0,0.0);
	glTexCoord2d(0,0);
	glVertex3f(0.0,150.0,0.0);
glTexCoord2d(1,0);
	glVertex3f(0.0,150.0,200.0);
glTexCoord2d(1,1);	
	glVertex3f(0.0,0.0,200.0);
glTexCoord2d(0,1);
	glVertex3f(0.0,0.0,0.0);
	glEnd();

	glPopMatrix();


}


void drawScreenRight()
{

		glPushMatrix();

	glRotatef( rotationAngle, 0.0, 1.0, 0.0 );

	glTranslatef(-76.0,-112.0,0.0);
	glTranslatef(225.0,20.0,-150.0);
	
	glBegin(GL_POLYGON);
	glVertex3f(0.0,0.0,0.0);
	glTexCoord2d(0,0);
	
	glVertex3f(0.0,150.0,0.0);
	glTexCoord2d(1,0);
	
	glVertex3f(0.0,150.0,200.0);
	glTexCoord2d(1,1);	
	
	glVertex3f(0.0,0.0,200.0);
	glTexCoord2d(0,1);
	
	glVertex3f(0.0,0.0,0.0);
	
	glEnd();

	glPopMatrix();

}

void output(GLfloat x, GLfloat y, char* text)
{
    glPushMatrix();
    glTranslatef(x,y,4);
    glScalef( 0.1, 0.1, 1); // change values of the parameters to make     text big or small
    for(char* p=text; *p!='\0'; p++ )
		glutStrokeCharacter(GLUT_STROKE_ROMAN, *p );
    glPopMatrix();
}

char str[10];
void drawArrow()
{
	   if ( loadTGA ("fish1.tga", 10 ) == false )
        printf ("\nError: File myQuakeTexture.tga not found!");

	glEnable(GL_TEXTURE_2D);            // enable texture mapping
    glBindTexture(GL_TEXTURE_2D, 10);

	
	glPushMatrix();



	
	float arrowMaterial[4]   = { 0.4, 0.4, 0.2, 1.0 };

	if(trap_vector.x!=0 || trap_vector.y!=0 || trap_vector.z!=0)
	{
		x=trap_vector.x*50;
		y=trap_vector.y*50;
		z=trap_vector.z*50;
	}

	if((trap_vector1.x)!=0 || (trap_vector1.y)!=0 || (trap_vector1.z)!=0)
	{
		x1=trap_vector1.x*50;
		y2=trap_vector1.y*50;
		z1=trap_vector1.z*50;
	}
	int xa=abs(x);
	int xa1=abs(x1);
	if(xa==xa1 && xa!=0)
	{
		//exit(0);
	}
	
	glTranslatef(x,y,z);
	int i=x;

//itoa(i, str, 10);
//output( x+26, y+112, str );
//i=y;
//itoa(i, str, 10);
//output( x+26, y+100, str );
//i=z;
//itoa(i, str, 10);
//output( x+26, y+88, str );
//	

_itoa(i, str, 10);
output( x+6, y+42, str );
i=y;
_itoa(i, str, 10);
output( x+6, y+30, str );
i=z;
_itoa(i, str, 10);
output( x+6, y+18, str );



	glColor3f(1.0, 0.5, 0.0);
	glTranslatef(-76.0,-112.0,0.0);
	glTranslatef(98.0,210.0,0.0);
	
	glBegin(GL_POLYGON);
	
	glTexCoord2d(0,1);
	glVertex3f(-12.5, -12.5, 1.0);
	glTexCoord2d(1,1);
	glVertex3f(-12.5, 12.5, 1.0);
	glTexCoord2d(1,0);
	glVertex3f(1.5, 12.5, 1.0);
	glVertex3f(1.5, -12.5, 1.0);
	glTexCoord2d(0,0);
	glVertex3f(-5.5, -30.5, 1.0);
	glEnd();


	glPopMatrix();

	
}

void drawLogo()
{
    
  if ( loadTGA ("sm.tga", 22 ) == false )
        printf ("\nError: File sm.tga not found!");
	glEnable(GL_TEXTURE_2D);
	glBindTexture(GL_TEXTURE_2D,22);

	if(start==1 && logo <=90.0)
	{
		glRotatef( logo++, 1.0, 0.0, 0.0 );
	}
	else if(start==0 && logo <90.0)
		glRotatef( 0, 1.0, 0.0, 0.0 );
	else if(start==1 && logo >=90.0)
		glRotatef( 90, 1.0, 0.0, 0.0 );

	//glRotatef( rotationAngle, 0.0, 1.0, 0.0 );
	//glRotatef( cameraY, 1.0, 0.0, 0.0 );
	//glRotatef( -45, 0.0, 1.0, 0.0 );
	glPushMatrix();

	glTranslatef(-76.0,-112.0,58.0);
	glTranslatef(-24.0,12.0,58.0);

	
	glBegin(GL_POLYGON);
	//glVertex3f(0.0,0.0,0.0);
	glTexCoord2d(0,1);
	glVertex3f(0.0,200.0,0.0);
	glTexCoord2d(1,1);
	glVertex3f(250.0,200.0,0.0);
	glTexCoord2d(1,0);

	glVertex3f(250.0,0.0,0.0);
	glTexCoord2d(0,0);
	glVertex3f(0.0,0.0,0.0);
	glEnd();

	glPopMatrix();

}

void display()
{
	
    


    glClear( GL_COLOR_BUFFER_BIT | GL_DEPTH_BUFFER_BIT);
	//glutFullScreen();
	

    glMatrixMode(GL_MODELVIEW);
    glLoadIdentity();

	gluLookAt( 500*sin(lookY), 500*sin(lookY), lookZ, 
               2.5, 0.0,  0.0, 
               0.0, 1.0, 0.0 );
	

	glPushMatrix();
	output(100,260,"Right Hand=h");
	output(100,245,"Left  Hand=b");
	output(100,230,"Head    =B");
	output(100,215,"Left  Foot=w");
	output(100,200,"Right Foot=W");
	glPopMatrix();
	drawScreenFront();
	
	drawArrow();
		
	
	drawScreenBack();
		
	
	drawScreenLeft();

	
	drawScreenRight();
	
	drawLogo();
	
    
    // this tells glut to call the 'timer' function in 33 milliseconds
    // i.e. this way we will draw 1000/33 = 30 times a second
    glutTimerFunc( 33, timer, 0 ); 

    glutSwapBuffers();
    printf(".");
}


//...........Yeah Thats it...............................................