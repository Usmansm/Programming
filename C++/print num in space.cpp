# include <iostream>
using namespace std;
void main ()
{
	int n,a,b,c,d,e,f,g,h;
		cout<<"Enter the Num";
		cin>>n;
	a=n/10000;
	cout<<"\t"<<a;
		n=n%10000;
	b=n/1000;
	cout<<"\t"<<b;
		n=n%1000;
	c=n/100;
	cout<<"\t"<<c;
	n=n%100;
	d=n/10;
	cout<<"\t"<<d;
	n=n%10;
	cout<<"\t"<<n;
	e=a+b+c+d+n;
	f=e/10;
	g=e%10;
	h=f+g;
	cout<<h/10;
}