# include <iostream>
# include <conio.h>
using namespace std;
void main()
{
	float arr[100],n,*ptr,sum=0;
	ptr=&sum;
	cout<<"\nEnter the Total num";
	cin>>n;
		cout<<"\n**********WELCOME TO THE PROGRAM***************";
	for(int i=0;i<n;i++)
	{
		cout<<"\nEnter the NUm";
		cin>>arr[i];
		*ptr=*ptr+arr[i];
	}
	cout<<"\nThe Average is "<<*ptr/n;
	cout<<"\n**********THANKS FOR USING PROGRAM***************";
	getche();
}