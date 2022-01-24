#include <iostream.h>
void main()
{
int n;
cout<<"Enter The Value Of n:";
cin>>n;
for(int i=0;i<n;i++)
{
for(int j=n;j>i;j--)
cout<<" ";
for(int k=0;k<i;k++)
cout<<" *";
cout<<"\n";
}
for(i=n;i>0;i--)
{
for(int j=n;j>i;j--)
cout<<" ";
for(int k=0;k<i;k++)
cout<<" *";
cout<<"\n";
}
}