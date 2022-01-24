# include <iostream>
using namespace std;

# include <conio.h>
# include <string>




void main()
{
 int ch;

 int l=0,num=0;

 while((ch=cin.get())!=EOF)
 {
	 ++num;
	 l=l+1;
	 while((ch=cin.get())!='\n')
		 ++num;
 }
 cout<<"You entered "<<num<<"characters."<<endl;
 cout<<" \n total lines are"<<l;

 getche();
}