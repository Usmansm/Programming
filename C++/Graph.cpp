# include <iostream>
using namespace std;

class graph
{
public:
	int arr[10][10];
graph()
	{
	arr[10][10]=0;
	}

void addedge(int u,int v)
{
	arr[u][v]=1;
	arr[v][u]=1;
	cout<<"\nEdge Inserted Successfully between " << u <<" and " << v;
}
bool isthereanedge(int u ,int v)
{
	if(arr[u][v]==1)
	{
		cout<<"\nThere is an Edge Between " << u <<" and " << v;
		return true;
	}
	else 
	{
		cout<<"\nNo Edge Between " << u <<" and " << v;
		return false;

	}
}

 void isisolated(int u)
{
	int chk=1;
	for(int i=0;i<10;i++)
	{
		if(arr[u][i]==1)
		{
			cout<<"\n"<< u <<" is not isolated";
			chk=0;
			break;
		}
		
		
	}
	if(chk==1)
	cout<<"\n"<< u <<" is isolated";
}
	int highestdegree()
	{
		int sum[10]={0};
	for(int i=0;i<10;i++)
	{
		int count=0;
		for (int j=0;j<10;j++)
		{
			if(arr[i][j]==1)
				sum[i]=sum[i]+1;
		}
	}

int max=sum[0];
int high;
	for(int i=0;i<10;i++)
	{
		if(max<sum[i])
			max=sum[i];
		
	}
	for(int i=0;i<10;i++)
	{
		if(max==sum[i])
		{
			cout<<"\nThe Vertice with maximum node is "<<i;
			high=i;
			break;}
	}
	
	return high;
	}

};
void main(){


graph g1;
g1.addedge(1,5);
g1.addedge(2,5);
g1.addedge(2,6);
g1.addedge(2,3);
g1.addedge(5,9);
g1.addedge(5,8);
g1.addedge(5,7);
g1.isisolated(7);
g1.isthereanedge(3,5);
g1.isthereanedge(1,5);
int x=g1.highestdegree();
system("pause");
}