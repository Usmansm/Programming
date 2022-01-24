#include <iostream>
using namespace std;
int height; 

int main() {  
    while(true)
	{
    	cout <<"Enter the height: ";
    	cin >>height;
        for(int i=0; i<height; i++)
		{
cout <<"\n";
                for(int j=0;j<height-i;j++)
				{
                     cout<<" ";
                 }
                 for(int k=0;k<i*2+1;k++)
{
                     cout<<"*";
                 }

        }  
    }
    return 0;
}