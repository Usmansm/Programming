# include <iostream>
using namespace std;

# include <conio.h>



void main()
{
    int mat1[2][2],i,j,r,mat2[2][2],prod[2][2];
  
        cout<<"Enter The values For  First matrix \n";
        
        for(i=0;i<2;i++)
        {
            for(j=0;j<2;j++)
                cin>>mat1[i][j];
        }
        
        cout<<"Enter The Values for Second matrix \n";
        for(i=0;i<2;i++)
        {
            for(j=0;j<2;j++)
                cin>>mat2[i][j];
        }
        
        
                cout<<"The result of the  MatriX Multiplication is as follows:\n";
     
        for(i=0;i<2;i++)
        {
            for(j=0;j<2;j++)
            {
                prod[i][j]=0;
                for(r=0;r<2;r++)
                {
                   prod[i][j]=prod[i][j]+(mat1[i][r]*mat2[r][j]);

                
                }
                cout<<"\t"<<prod[i][j];
            }
            cout<<"\n";
			
        }
		cout<<"\n\n************THANKS***************";
        getch();
    
    
}