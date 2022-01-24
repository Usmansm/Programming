#include <iostream>
#include <string>
# include <conio.h>

using namespace std;

void main()
{
char inp[50];
int flag=1,x;

cout << "Enter word :";
cin>>inp;
 x = strlen(inp)-1;
for(int i = 0; i <= x; i++)
{
if (inp[i] == inp[x-i])
flag=0;
else
{
	flag=1;
break;
}
}
if (flag==1)
{
cout<<"Not a palidrome"<<endl;
}
if(flag==0)
cout << "Yes It is a Palidrome"<<endl;
cout<<"\n\t***********THANKS FOR USING PROGRAM***************";
getche();
}