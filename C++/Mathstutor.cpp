# include <iostream>
using namespace std;
void main ()
{
int c,ch1,ch2,a,b;
char chq='y';
cout<<"+++++++++++++++++MATHS TUTUOR+++++++++++++++++++++++";
cout<<"\n Enter your class";
cin>>c;
if(c>=1 && c<=2)
	while (chq!='n')
	{
		cout<<"\n LESSONS";
	cout<<"\n+++++++++++++++SECTION 1+++++++++++++++++++";
cout<<"\n 1) FOR ADDITION";
cout<<"\n 2) FOR SUBTRACTION";
cout<<"\n 3) FOR MULTIPLICATION";
cout<<"\n 4) FOR DIVISION";
cout<<"ENTER THE CHOICE";
cin>>ch1;
switch (ch1)
{
case 1:
	{
		cout<<"ENTER FIRST NUMBER";
		cin>>a;
cout<<"ENTER SECOND NUMBER";
		cin>>b;
cout<<"THE ADDITION IS "<<a+b;
break;
	}
	case 2:
	{
		cout<<"ENTER FIRST NUMBER";
		cin>>a;
cout<<"ENTER SECOND NUMBER";
		cin>>b;
cout<<"THE RESULT IS "<<b-a;
break;
	}
	case 3:
	{
		cout<<"ENTER FIRST NUMBER ";
		cin>>a;
cout<<"ENTER SECOND NUMBER ";
		cin>>b;
cout<<"THE PRODUCT IS "<<a*b;
break;
	}
	case 4:
	{
		cout<<"ENTER FIRST NUMBER ";
		cin>>a;
cout<<"ENTER SECOND NUMBER ";
		cin>>b;
		if(b!=0)
cout<<"THE DIVISION IS "<<a/b;
		else
			cout<<"RESULT IS INFINITY";
break;
	}
}
	cout<<"\nDO YOU WANT TO LEARN MORE LESSONS OR WANT TO QUIT (y/n)";
	cin>>chq;
}
if (c>=3 && c<=7)
	while (chq!='n')
	{
	cout<<"\n+++++++++++++++SECTION 2+++++++++++++++++++";
	cout<<"\n LESSONS";
cout<<"\n 1) FOR ADDITION";
cout<<"\n 2) FOR SUBTRACTION";
cout<<"\n 3) FOR MULTIPLICATION";
cout<<"\n 4) FOR DIVISION";
cout<<"\n 5) FOR AREA OF SQUARE";
cout<<"\n 6) FOR AREA OF CIRCLE";
cout<<"\n 7) FOR AREA OF TRIANGLE";
cout<<"\nENTER THE CHOICE";
cin>>ch2;
switch(ch2)
{
case 1:
	{
		cout<<"ENTER FIRST NUMBER";
		cin>>a;
cout<<"ENTER SECOND NUMBER";
		cin>>b;
cout<<"THE ADDITION IS "<<a+b;
break;
	}
	case 2:
	{
		cout<<"ENTER FIRST NUMBER";
		cin>>a;
cout<<"ENTER SECOND NUMBER";
		cin>>b;
cout<<"THE RESULT IS "<<b-a;
break;
	}
	case 3:
	{
		cout<<"ENTER FIRST NUMBER";
		cin>>a;
cout<<"ENTER SECOND NUMBER";
		cin>>b;
cout<<"THE PRODUCT IS "<<a*b;
break;
	}
	case 4:
	{
		cout<<"ENTER FIRST NUMBER";
		cin>>a;
cout<<"ENTER SECOND NUMBER";
		cin>>b;
		if(b!=0)
cout<<"THE DIVISION IS "<<a/b;
		else
			cout<<"RESULT IS INFINITY";
break;
	}
	case 5:
		{
cout<<"ENTER LENGTH OF FIRsT SIDE";
		cin>>a;
		cout<<"THE AREA IS "<<4*a;
		break;
}
case 6:
		{
cout<<"ENTER RADIUS OF CIRCLE";
		cin>>a;
		cout<<"THE AREA IS "<<3.14*a*a;
		break;
}
		case 7:
		{
			cout<<"ENTER LENGTH";
		cin>>a;
cout<<"ENTER WIDTH";
		cin>>b;
cout<<"THE AREA OF TRIANGLE IS  "<<a*b;
break;
}
}
		cout<<"\nDO YOU WANT TO LEARN MORE LESSONS OR WANT TO QUIT (y/n)";
	cin>>chq;
}
}
	
