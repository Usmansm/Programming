# include <iostream>
# include <conio.h>
using namespace std;
void main ()
{
	int a[2][2],sum=0,row;
	for(int i=0;i<2;i++)
		for(int j=0;j<2;j++)
		{
			cout<<"\nEnter the Elements For "<<i+1<<" row =";
			cin>>a[i][j];
			sum=sum+a[i][j];
		}
		for( i=0;i<2;i++)
		{
			row=0;
		for( int j=0;j<2;j++)
		{
			row=row+a[i][j];
			cout<<"\t"<<a[i][j];
			
		}
		cout<<"\t"<<row/2;
		cout<<"\n";
		}
		
	cout<<"\nTotal Average is ="<<sum/(4);
	cout<<"\n\n************THANKS***************";
	getche();
}