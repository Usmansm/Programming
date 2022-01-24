# include <iostream>
using namespace std;

class node
{
public:
	node(int n,node *p=0)
{
	info=n;
	current=p;
}
	int info; //contain info of node
	node * current; //pointer connected with each node ...contain address ov current node

};
class list
{
public :
	node * head; //top node
	node * last_node; // last node
	list()
	{
		head=0; //set the head_node to zero
		last_node=0; //set the last_node to zero
	}

	void insert_at_head(int n)
	{
	
		node *temp=new node(n);		//create a new node apart from link list
		temp->current=head;			//points the new node toward head
		head=temp;			//give the new node "HEAD title" mean we have changed the head node
		cout<<"\n Nodes inserted Successfully";
		if (last_node==0)			//if there is only single node
		{
			last_node=head; //head and last_node will b same
			cout<<"\n Nodes inserted Successfully";
		}
	}
	void insert_at_last_node(int n)
	{
		node *temp=new node(n); // create a new node apart from link list
		if(last_node==0)		//check whether the link list is empty or not
		{
			last_node=temp;			//temp will be the first and last node
			head=temp;
			cout<<"\nNode INserted At LAst Successfully";
			return;
		}
		last_node->current=temp;
		last_node=temp;
		cout<<"\nNode INserted At LAst Successfully";
	}

	void del_head()
	{
		if(last_node==0) //check the link list is empty or not
		{
			cerr<<"\nNO NODE IS PRESENT ";
			exit(0);
		}
		if(head==last_node) // if there is single node ion link list
		{
			cout<<"\nNODE deleted From HEAD  SUCCESSFULLY";
			delete head;
			head=last_node=0;
		}
		else
		{
			node *temp=head; //to create a new node that stores address of head
			head=head->current; //head stores the address of very next node
			delete temp;		
			cout<<"\nNODE deleted From HEAD  SUCCESSFULLY";
		}
	}
	void del_last_node()
	{
		if(last_node==0)  //to check link list is empty or not
		{
			cerr<<"\n No node is present";
		}
		if (head==last_node) //if there is single node
		{
			delete last_node;
			cout<<"\nNODE deleted from LAST Successfully";
head=last_node=0;
		}
		else // the main issue is to find the address of second last node
		{
			node *temp=head; // a new node will be created
			while(temp->current !=last_node) //loop continues until it reaches the second last node
			{
				temp=temp->current;   
			}
			delete last_node; //delete last node
			last_node=temp;
			last_node->current=0; 
			cout<<"\nNODE deleted from LAST Successfully";
		}
	}
};
	void main()
	{
		list l1;
		 l1.insert_at_head(12);
		 l1.insert_at_head(14);
		 l1.insert_at_head(14);
		 l1.insert_at_last_node(25);
		l1.del_last_node();
		l1.del_head();
system("\npause");

	}