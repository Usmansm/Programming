# include "iostream"
using namespace std;

class hash
{
public:
	int a[10];
	hash()
	{
		for(int i=0;i<10;i++)
			a[i]=-1;
	}
	int hsh_func(int key)
	{
		int location=key%10;
		return location;
	}
	void insert()
	{
		int key;
		cout<<"\nEnter Num to Insert";
		cin >>key;
		int loc=hsh_func(key);
		while (a[loc]!=-1)
		{
			loc=(loc+1)%10;
		}
		a[loc]=key;
		cout<<"\nValue Inserted Successfully";
	}
	void search(int*it,int key)
	{
		int loc=hsh_func(key);
int st=loc;
while(1)
{
	if(a[loc]==key)
	{
		cout<<"\nvalue found at Index " << loc;
		it=&a[loc];
		break;
	}
	if(a[loc]==-1)
	{
		cout<<"\nNumber not Exist";
		break;
	}
	loc=(loc+1)%10;
	if(st==loc)
		break;
}
	}


	
	void print()
	{
		int loc=0;
		for (int i=0;i<10;i++)
		{
			cout<<"\n Value at Index "<<i<<" is " << a[i];
		}
	}
};
void main()
{
	hash obj;
	int *it=NULL;
	cout<<"\nPress 1 to Insert";
	cout<<"\nPress 2 to Search";
	cout<<"\nPress 3 to Print";

	int op;
	cin>>op;
	while(1)
	{
	
	switch(op)
	{
	case 1:
		{
			obj.insert();
			break;
		}
	
case 2:
	{
int s;
cout<<"\nEnter Num To search ";
cin>>s;
obj.search(it,s);\
break;
	}
case 3:
	{
		obj.print();
		break;
	}
	}

	cout<<"\nWant more or 0 to exit";
		cout<<"\nPress 1 to Insert";
	cout<<"\nPress 2 to Search";
	cout<<"\nPress 3 to Print";
	cin >>op;
	}
}






