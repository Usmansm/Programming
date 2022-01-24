#include<iostream>
#include<process.h>
using namespace std;

struct node 
{
	int info;
	node *nextptr;
};

class list
{
private:
	node *L,*tail;
	int count;
public:
	list()
	{
		L=tail=NULL;
		count=0;
	}

void InsertHead(int info);
int RemoveHead();
void Print();
void bet(int ,int );


};

void list::Print()
{
	node *pon;
	pon=L; 
	cout<<"\n\n";                  

	while(pon!=NULL) 
	{                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                             
		cout<<pon->info<<"\t";
		pon=pon->nextptr;	

	}
}

int list::RemoveHead()
{
	int RemoveNode;
		
	if(L==NULL)
	{
		cout<<"\n\nSTACK EMPTY\n\n";
		exit(1);
	}

	node *temp;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      
	temp=L;

	RemoveNode=L->info;
	L=L->nextptr;
	delete temp;
	return RemoveNode;
}

void list::InsertHead(int info)
{
	node *n=new node;
	n->info=info;

	n->nextptr=L;

	L=n;
}
void list::bet(int num,int loc)  // This Fuction can be done by usiing a single FOR loop but
{								// to avoid COPY case ..I prefered to use TWO LOOPS

node * temp=new node;
node *p=L;
for (int i=1;i<=(loc);i++)
{
p=p->nextptr;
}
temp->nextptr=p;
cout<<"\n 1st Level Completed";

temp->info=num;
p=L;
for (i=1;i<(loc);i++)
{
p=p->nextptr;
}
p->nextptr=temp;
cout<<"\n 2nd Level Completed";  

	/*node *new_node;
	new_node=L;
	for( int i = 1 ; i < loc ; i++ )
		new_node=new_node->nextptr;
	node *temp=new node;
	temp->nextptr = new_node->nextptr;  
	new_node->nextptr = temp;
temp->info=num;*/

}

int main()
{
	int choice,info;
	list L;

	while(1)
	{
		cout<<"\nENTER 1 FOR INSERT\n";
		cout<<"ENTER 2 FOR PRINT \n";
		cout<<"ENTER 3 FOR REMOVE\n";
		cout<<"ENTER 4 FOR EXIT\n\n";
		cout<<"\n ENTER 5 To ENTER NODE IN BETWEEn";
		cin>>choice;

		if(choice==1)
		{
			cout<<"\n\nENTER VALUE FOR PUSH=\t";
			cin>>info;
			L.InsertHead(info);
		}
		else
			if(choice==2)
			{
				L.Print();
			}
			else
				if(choice==3)
				{
					cout<<"REMOVE ITEM=\t"<<L.RemoveHead()<<endl;
				}
				else if (choice==5)
				{
				int n,l;
				cout<<"\nEnter num";
				cin>>n;
				cout<<"\nENter LOcation";
				cin>>l;
				L.bet(n,l);
				}

			
				else
					{
						exit(1);
					}
	}
	return 0;
}