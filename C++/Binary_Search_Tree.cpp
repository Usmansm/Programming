# include <iostream>
using namespace std;
class bnode
{
public:
	int info;
	bnode *left;
	bnode *right;
	bnode(int n,bnode *l=0,bnode *r=0)
	{
		left=l;
		right=r;
		info=n;
	}
};
class BST
{
public:
	bnode *root;
   bnode* Search(int n);
   void insert(int n);
	BST()
	{
		root=0;
	}
};
bnode * BST::Search(int n)
{
	bnode *p=root;
	while (p!=NULL)
	{
		if(p->info=n)
		{
			return p;
			break;
		}
		else if (p->info>n)
			p=p->left;
		else
			p=p->right;
	}
	return NULL;

}
void BST::insert(int n)
{
if(root==NULL)
{
	root=new bnode(n);
	cout<<"\nNode ADDED sUUCESSFULLY";
	return;
}
else 
{
bnode *temp=root;
while (1)
{
	if(temp->info<n)
	{
		if(temp->right==NULL)
		{
			temp->right=new bnode(n);
			cout<<"\nNode ADDED sUUCESSFULLY";
		break;
		}
		else 
			temp=temp->right;
	}
	if(temp->info>n)
	{
		if(temp->left==NULL)
		{
			temp->left=new bnode(n);
			cout<<"\nNode ADDED sUUCESSFULLY";
		break;
		}
	else
temp=temp->left;
	}
}

}
}

void main()
{
	BST l;
	l.insert(1);
}
