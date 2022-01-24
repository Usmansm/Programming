#include <iostream.h>
#include <conio.h>



void main()
{
	int m,y,d,r,s;
    cout << "Enter month : ";
	cin >> m;
	cout << "Enter year : ";
	cin >> y;
	cout << "Enter Date : ";
	cin >> d;
	s=m-1;
	r=d+((s+(s%2))/2+(s/8)*(1-(s%2)))*31+(s/2-(s/8)*(1-(s%2)))*30+((3*s-3)/(3*s-4))*(-2+1-(3*(y%4))/(3*(y%4)-1)-3*(y%400)/(3*(y%400)-1)+3*(y%100)/(3*(y%100)-1));
	cout << "\nThe total numbers of days from the Start of this year  are  " << r;
cout<<"\n******************THANKS FOR USING PRO*********************";
cin>>m;

}



