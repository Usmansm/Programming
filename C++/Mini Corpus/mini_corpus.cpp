#include <iostream>
using namespace std;
#include <iomanip>
#include <windows.h>
#include <fstream>
#include <stdlib.h>
#include <conio.h>
#include <time.h>
//----------------------------------- FUNCTION DECLARATION ---------------------------------------
void com();
void del();
void ins();
void menu();
void header();
void option();
void exit_confirm();
void combo_option();
void insert_option();
void delete_option();
void part_selection();
//__________________________________ INSERTION CLASS STARTS ______________________________________
class insertion
{
private:
	char input_text[1000];
	char default_text[5000];
	int input_character_count;
	int default_character_count;
	int word_count;
	int percentage;
	ifstream input_infile;
	ifstream default_infile;

public:
	insertion()
	{
		strcpy (input_text, " ");
		strcpy (default_text, " ");
		input_character_count		= 0;
		default_character_count		= 0;
		word_count					= 0;
		percentage					= 0;
	}
	//-------------------------------- READING ORIGNAL FILE --------------------------------------
	void input_read()
	{
		int counter = 0;
		char file_name[25];
		cout << "Enter File Name: (\"path\\:file name.txt\") ";
		cin >> file_name;
		input_infile.open (file_name);

		while (input_infile)
		{
			input_text[input_character_count] = input_infile.get();
			input_character_count++;
		}
		
		input_text[input_character_count+1] = '\0';
			
			for( int i=input_character_count ; i<5000 ; i++)
			{
				input_text[input_character_count] = ' ';
			}
			
			for (int c=0 ; c<input_character_count ; c++)
			{
				if (input_text[c] == ' ' && input_text[c+1] != ' ')
				{
					word_count ++;
				}
			}
			attack_percentage (word_count);
	}
	//------------------------------- READIND DEFAULT FILE ---------------------------------------
	void default_read()
	{
		default_infile.open ("pool.txt");
		while (default_infile)
		{
			default_text[default_character_count] = default_infile.get();
			default_character_count ++;
		}
		default_text[default_character_count+1] = '\0';
		input_read();
	}
	//----------------------------- GENERATE RANDOM LOCATIONS ------------------------------------
	int random_generator (int r)
	{
		int random;
		srand (time(NULL));
		random = rand () % r;
		srand (1);
		return random;
	}
	//-------------------------------- CALCULATE PERCENTAGE --------------------------------------
	void attack_percentage (int w)
	{
		float per;
		cout << "Enter % of insertion attack (1-100) ";
		cin >> per;
		percentage = (per/100) * w;
		insert();
	}
	//--------------------------------- INSERTION OPERATION --------------------------------------
	void insert()
	{
		int s = 0;
		ofstream outfile;
		outfile.open ("insertion.txt");
		int numb = 0;
		int serial = 0;
		int random[5000];
		char *default_ptr;
		int counter = 0;
		int random_check = 0;
		int array = 0;
		
		for (int n=0 ; n<percentage; n++)
		{
			numb ++;
			random[n] = random_generator (numb);
		}
		
		while (serial<input_character_count)
		{
			outfile << input_text[serial];
			if (input_text[serial] == ' ')
			{
				default_ptr = strtok (default_text, " ");
				while (default_ptr != NULL)
				{
					if(random_check == random[array])
					{
						s ++;
						outfile << ' ' << default_ptr << ' ';
						counter = 1;
						default_ptr = strtok (NULL , " ");
						random_check ++;
						serial ++;
						
						while(input_text[serial] != ' ')
						{
							outfile << input_text[serial];
							serial ++;
						}
					}
					else
					{
						default_ptr = strtok (NULL , " ");
						random_check ++;
					}
					if (counter == 1 )
					{
						array++;
						counter = 0;
						random_check = 0;
					}
				}
			}
			serial ++;
		}
		outfile.close();
		cout << "File Created" << endl;
		system ("pause");
		ins();
	}
};
//________________________________ INSERTION CLASS ENDS __________________________________________
//------------------------------------------------------------------------------------------------
//________________________________ DELETION CLASS STARTS _________________________________________
class deletion
{
private:
	char input_text[5000];
	int character_count;
	int word_count;
	int percentage;
public:
	deletion()
	{
		strcpy (input_text, " ");
		character_count				= 0;
		word_count					= 0;
		percentage					= 0;
	}
	//----------------------------- READING ORIGNAL FILE -----------------------------------------
	void input_read()
	{
		ifstream infile;
		char file_name[25];
		cout << "Enter File Name: (\"path\\:file name.txt\") ";
		cin >> file_name;
		infile.open (file_name);

		while (infile)
		{
			input_text[character_count] = infile.get();
			character_count++;

			if (input_text[character_count] == ' ')
			{
				word_count ++;
			}
		}
		input_text[character_count] = '\0';

		for (int i=character_count ; i<5000 ; i++)
		{
			input_text[character_count+1] = ' ';
		}
		attack_percentage (word_count);
	}
	//---------------------------- GENERATE RANDOM LOCATIONS -------------------------------------
	int random_generator (int r)
	{
		int random;
		srand (time(NULL));
		random = rand () % r;
		srand (1);
		return random;
	}
	//------------------------------- CALCULATE PERCENTAGE ---------------------------------------
	void attack_percentage (int w)
	{
		float per;
		cout << "Enter % of deletion attack (1-80) ";
		cin >> per;
		percentage = (per/100) * w;
		dele();
	}
	//--------------------------------- DELETION OPERATION ---------------------------------------
	void dele()
	{
		ofstream outfile;
		outfile.open("deletion.txt");
		int s = 0;
		int random[5000];
		int numb = 0;
		char *default_ptr;
		int array = 0;
		int random_check = 0;
		int counter = 0;

		for (int n=0 ; n<percentage ; n++)
		{
			numb ++;
			random[n] = random_generator (numb);
			
			if(numb >= 0)
			{
				numb = 0;
			}
		}
		
		default_ptr = strtok (input_text, " ");
		
		while (default_ptr != NULL)
		{
			if (random_check == random[array])
			{
				s ++;
				strcpy (default_ptr, " ");
				random_check = 0;
				array ++;
				counter ++;
			}
			else
			{
				random_check ++;
			}
			outfile << default_ptr << " ";
			default_ptr = strtok (NULL, " ");
		}
		outfile.close();
		cout << "File Created" << endl;
		system ("pause");
		del();
	}
};
//_________________________________ DELETION CLASS ENDS __________________________________________
//------------------------------------------------------------------------------------------------
//_______________________________ STATISTICS CLASS STARTS ________________________________________
class statistics
{
private:
	int word_count;
	int sentence_count;
	int preposition_count;
	int double_count;
	int character_count;
	int special_count;
	int digit_count;
public:
	statistics()
	{
	word_count			= 1;
	sentence_count		= 0;
	preposition_count	= 0;
	double_count		= -1;
	character_count		= -3;
	special_count		= 0;
	digit_count			= 0;
	}
	void stat()
	{
		ifstream infile;
		
		char file_name[25];
		char text[5000];
		
		int array = 0;
		
		cout << "Enter File Name: (\"path\\:file name.txt\") ";
		cin >> file_name;
		
		infile.open(file_name);
		
		while(infile)
		{
			text[array] = infile.get();
			
			if (text[array] == ' ')
			{
				word_count ++;
			}
			
			if (text[array] == '.')
			{
				sentence_count ++;
			}
			character_count ++;
			array ++;
		}
		
		for( int count=0 ; count<array ; count++)
		{
			if (text[count] == text[count+1])
			{
				double_count ++;
			}
			
			if (text[count] == '!' || text[count] == '@' || text[count] == '#' || text[count] == '$' || text[count] == '%' || text[count] == '&' || text[count] == '^')
			{
				special_count ++;
			}
		}
//-------------------------------- STATISTICS DISPLAY ON SCREEN ----------------------------------
		system ("cls");
		header();
		cout << "________________________________________________________________________________" << endl;
		cout << setw(57) << "-------------------------------" << endl;
		cout << setw(57) << "S T A T I S T I C S   T A B L E" << endl;
		cout << setw(57) << "-------------------------------" << endl;
		cout << "________________________________________________________________________________" << endl;
		cout << setw(51) << "*---------------------------+" << "-------*"								 << endl;
		cout << setw(51) << "| Word Count:               |" << setw(6)		<< word_count		 << " |" << endl;
		cout << setw(51) << "|---------------------------|" << "-------|"								 << endl;
		cout << setw(51) << "| Sentence Count:           |" << setw(6)		<< sentence_count	 << " |" << endl;
		cout << setw(51) << "|---------------------------|" << "-------|"								 << endl;
		cout << setw(51) << "| Preposition Count:        |" << setw(6)		<< preposition_count << " |" << endl; 
		cout << setw(51) << "|---------------------------|" << "-------|"								 << endl;
		cout << setw(51) << "| Double Letter Word Count: |" << setw(6)		<< double_count		 << " |" << endl;
		cout << setw(51) << "|---------------------------|" << "-------|"								 << endl;
		cout << setw(51) << "| Character Count:          |" << setw(6)		<< character_count	 << " |" << endl;
		cout << setw(51) << "|---------------------------|" << "-------|"								 << endl;
		cout << setw(51) << "| Special Character Count:  |" << setw(6)		<< special_count	 << " |" << endl;
		cout << setw(51) << "|---------------------------|" << "-------|"						    	 << endl;
		cout << setw(51) << "| Digit Count:              |" << setw(6)		<< digit_count		 << " |" << endl;
		cout << setw(51) << "*---------------------------+" << "-------*"								 << endl;
		cout << endl;
//---------------------------------- STATISTICS SAVED ON FILE ------------------------------------	
		ofstream stats;
		stats.open("statistics.txt");
		stats << setw(62) << "-------------------------------" << endl;
		stats << setw(62) << "S T A T I S T I C S   T A B L E" << endl;
		stats << setw(62) << "-------------------------------" << endl;
		stats << endl;
		stats << setw(57) << "*---------------------------+" << "-------*"								 << endl;
		stats << setw(57) << "| Word Count:               |" << setw(6)		<< word_count		 << " |" << endl;
		stats << setw(57) << "|---------------------------|" << "-------|"								 << endl;
		stats << setw(57) << "| Sentence Count:           |" << setw(6)		<< sentence_count	 << " |" << endl;
		stats << setw(57) << "|---------------------------|" << "-------|"								 << endl;
		stats << setw(57) << "| Preposition Count:        |" << setw(6)		<< preposition_count << " |" << endl; 
		stats << setw(57) << "|---------------------------|" << "-------|"								 << endl;
		stats << setw(57) << "| Double Letter Word Count: |" << setw(6)		<< double_count		 << " |" << endl;
		stats << setw(57) << "|---------------------------|" << "-------|"								 << endl;
		stats << setw(57) << "| Character Count:          |" << setw(6)		<< character_count	 << " |" << endl;
		stats << setw(57) << "|---------------------------|" << "-------|"								 << endl;
		stats << setw(57) << "| Special Character Count:  |" << setw(6)		<< special_count	 << " |" << endl;
		stats << setw(57) << "|---------------------------|" << "-------|"						    	 << endl;
		stats << setw(57) << "| Digit Count:              |" << setw(6)		<< digit_count		 << " |" << endl;
		stats << setw(57) << "*---------------------------+" << "-------*"								 << endl;
	}
};
//_________________________________ STATISTICS CLASS ENDS ________________________________________
//------------------------------------------------------------------------------------------------
//_______________________________ COMPARATIVE CLASS STARTS _______________________________________
//------------------------------------------------------------------------------------------------
class comparison
{
private:
	int org_word_count		,	att_word_count;
	int org_character_count	,	att_character_count;
	int org_digit_count		,	att_digit_count;
	int org_the				,	att_the;
	int org_of				,	att_of;
	int org_and				,	att_and;
	int org_to				,	att_to;
	int org_a				,	att_a;
public:
	comparison()
	{
		org_word_count			= 1		,	att_word_count			= 1;
		org_character_count		= -3	,	att_character_count		= -3;
		org_digit_count			= 0		,	att_digit_count			= 0;
		org_the					= 0		,	att_the					= 0;
		org_of					= 0		,	att_of					= 0;
		org_and					= 0		,	att_and					= 0;
		org_to					= 0		,	att_to					= 0;
		org_a					= 0		,	att_a					= 0;
	}
	void compare()
	{
		ifstream org_infile;
		ifstream att_infile;
		
		char org_file[25];
		char att_file[25];
		
		char org_text[5000];
		char att_text[5000];

		int array = 0;
		
		cout << "Enter Orignal File Name : (\"path\\:file name.txt\") ";
		cin >> org_file;

		cout << "Enter Attacked File Name: (\"path\\:file name.txt\") ";
		cin >> att_file;
		
		org_infile.open(org_file);
		
		while (org_infile)
		{
			org_text[array] = org_infile.get();
// ===================================== WORD COUNT ==============================================
			if (org_text[array] == ' ')
			{
				org_word_count ++;
			}
			org_character_count ++;
			array ++;
		}
//====================================== DIGIT COUNT =============================================
			for (int x=0 ; x<array ; x++)
			{
				if (org_text[x] == '1' || org_text[x] == '2' || org_text[x] == '3' || org_text[x] == '4' || org_text[x] == '5' || org_text[x] == '6' || org_text[x] == '7' || org_text[x] == '8' || org_text[x] == '9' || org_text[x] == '0')
				{
					org_digit_count ++;
				}
			}
//================================ FREQUENCY COUNT OF "THE" ======================================
			for (int y=0 ; y<array ; y++)
			{
				if (org_text[y] == ' ')
				{
					if (org_text[y+1] == 't' || org_text[y+1] == 'T')
					{
						if (org_text[y+2] == 'h' || org_text[y+2] == 'H')
						{
							if (org_text[y+3] == 'e' || org_text[y+3] == 'E')
							{
								if (org_text[y+4] == ' ' || org_text[y+4] == '.' || org_text[y+4] == '?' || org_text[y+4] == '!' || org_text[y+4] == ',')
								{
									org_the ++;
								}
							}
						}
					}
				}
//================================= FREQUENCY COUNT OF "OF" ======================================
				if (org_text[y] == ' ')
				{
					if (org_text[y+1] == 'o' || org_text[y+1] == 'O')
					{
						if (org_text[y+2] == 'f' || org_text[y+2] == 'F')
						{
							if (org_text[y+3] == ' ' || org_text[y+3] == '.' || org_text[y+3] == '?' || org_text[y+3] == '!' || org_text[y+3] == ',')
							{
								org_of ++;
							}
						}
					}
				}
//================================= FREQUENCY COUNT OF "AND" =====================================
				if (org_text[y] == ' ')
				{
					if (org_text[y+1] == 'a' || org_text[y+1] == 'A')
					{
						if (org_text[y+2] == 'n' || org_text[y+2] == 'N')
						{
							if (org_text[y+3] == 'd' || org_text[y+3] == 'D')
							{
								if (org_text[y+4] == ' ' || org_text[y+4] == '.' || org_text[y+4] == '?' || org_text[y+4] == '!' || org_text[y+4] == ',')
								{
									org_and ++;
								}
							}
						}
					}
				}
//================================== FREQUENCY COUNT OF "TO" =====================================
				if (org_text[y] == ' ')
				{
					if (org_text[y+1] == 't' || org_text[y+1] == 'T')
					{
						if (org_text[y+2] == 'o' || org_text[y+2 ]== 'O')
						{
							if (org_text[y+3] == ' ' || org_text[y+3] == '.' || org_text[y+3] == '?' || org_text[y+3] == '!' || org_text[y+3] == ',')
							{
								org_to ++;
							}
						}
					}
				}
//==================================== FREQUENCY COUNT OF "A" ====================================
				if (org_text[y] == ' ')
				{
					if (org_text[y+1] == 'a' || org_text[y+1] == 'A')
					{
						if (org_text[y+2] == ' ' || org_text[y+2] == '.' || org_text[y+2] == '?' || org_text[y+2] == '!' || org_text[y+2] == ',')
						{
							org_a ++;
						}
					}
				}
			}
//------------------------------------------------------------------------------------------------
//------------------------------------------------------------------------------------------------
			att_infile.open(att_file);
			
			while (att_infile)
			{
				att_text[array] = att_infile.get();
// ===================================== WORD COUNT ==============================================
				if (att_text[array] == ' ')
				{
					att_word_count ++;
				}
				att_character_count ++;
				array ++;
			}
//====================================== DIGIT COUNT =============================================
			for (int x=0; x<array ; x++)
			{
				if (att_text[x] == '1' || att_text[x] == '2' || att_text[x] == '3' || att_text[x] == '4' || att_text[x] == '5' || att_text[x] == '6' || att_text[x] == '7' || att_text[x] == '8' || att_text[x] == '9' || att_text[x] == '0')
				{
					att_digit_count ++;
				}
			}
//================================ FREQUENCY COUNT OF "THE" ======================================
			for (int y=0 ; y<array ; y++)
			{
				if (att_text[y] == ' ')
				{
					if (att_text[y+1] == 't' || att_text[y+1] == 'T')
					{
						if (att_text[y+2] == 'h' || att_text[y+2] == 'H')
						{
							if (att_text[y+3] == 'e' || att_text[y+3] == 'E')
							{
								if (att_text[y+4] == ' ' || att_text[y+4] == '.' || att_text[y+4] == '?' || att_text[y+4] == '!' || att_text[y+4] == ',')
								{
									att_the ++;
								}
							}
						}
					}
				}
//================================= FREQUENCY COUNT OF "OF" ======================================
				if (att_text[y] == ' ')
				{
					if (att_text[y+1] == 'o' || att_text[y+1] == 'O')
					{
						if (att_text[y+2] == 'f' || att_text[y+2] == 'F')
						{
							if (att_text[y+3] == ' ' || att_text[y+3] == '.' || att_text[y+3] == '?' || att_text[y+3] == '!' || att_text[y+3] == ',')
							{
								att_of ++;
							}
						}
					}
				}
//================================= FREQUENCY COUNT OF "AND" =====================================
				if (att_text[y] == ' ')
				{
					if (att_text[y+1] == 'a' || att_text[y+1] == 'A')
					{
						if (att_text[y+2] == 'n' || att_text[y+2] == 'N')
						{
							if (att_text[y+3] == 'd' || att_text[y+3] == 'D')
							{
								if (att_text[y+4] == ' ' || att_text[y+4] == '.' || att_text[y+4] == '?' || att_text[y+4] == '!' || att_text[y+4] == ',')
								{
									att_and ++;
								}
							}
						}
					}
				}
//================================== FREQUENCY COUNT OF "TO" =====================================
				if (att_text[y] == ' ')
				{
					if (att_text[y+1] == 't' || att_text[y+1] == 'T')
					{
						if (att_text[y+2] == 'o' || att_text[y+2 ]== 'O')
						{
							if (att_text[y+3] == ' ' || att_text[y+3] == '.' || att_text[y+3] == '?' || att_text[y+3] == '!' || att_text[y+3] == ',')
							{
								att_to ++;
							}
						}
					}
				}
//==================================== FREQUENCY COUNT OF "A" ====================================
				if (att_text[y] == ' ')
				{
					if (att_text[y+1] == 'a' || att_text[y+1] == 'A')
					{
						if (att_text[y+2] == ' ' || att_text[y+2] == '.' || att_text[y+2] == '?' || att_text[y+2] == '!' || att_text[y+2] == ',')
						{
							att_a ++;
						}
					}
				}
			}
			print();
			}
//------------------------------------------------------------------------------------------------
void print()
{
	system ("cls");
	header();
	cout << "________________________________________________________________________________" << endl;
	cout << setw(62) << "-------------------------------------------" << endl;
	cout << setw(62) << "C O M P A R A T I V E   S T A T I S T I C S" << endl;
	cout << setw(62) << "-------------------------------------------" << endl;
	cout << "________________________________________________________________________________"			<< endl;
	cout << setw(35) << "*------------------+"				<< "--------------+" << "---------------*"	<< endl;
	cout << setw(35) << "|                  |"				<< " Orignal Text |" << " Attacked Text |"	<< endl;
	cout << setw(35) << "|------------------|"				<< "--------------|" << "---------------|"	<< endl;
	cout << setw(35) << "| Attack Type:     |" << "                              |" << endl;
	cout << setw(35) << "|------------------|"				<< "--------------|" << "---------------|"	<< endl;
	cout << setw(35) << "| Words            |" << setw(13) << org_word_count << " |" << setw(14) << att_word_count << " |" << endl;
	cout << setw(35) << "|------------------|"				<< "--------------|" << "---------------|"	<< endl;
	cout << setw(35) << "| Digit Count      |" << setw(13) << org_digit_count << " |" << setw(14) << att_digit_count << " |" << endl;
	cout << setw(35) << "|------------------|"				<< "--------------|" << "---------------|"	<< endl;
	cout << setw(35) << "| Character Count  |" << setw(13) << org_character_count << " |" << setw(14) << att_character_count << " |" << endl;
	cout << setw(35) << "|------------------|"				<< "--------------|" << "---------------|"	<< endl;
	cout << setw(35) << "| Frequency Count: |"				<<	"                              |" << endl;
	cout << setw(35) << "|------------------|"				<< "--------------|" << "---------------|"	<< endl;
	cout << setw(35) << "| the              |" << setw(13) << org_the << " |" << setw(14) << att_the << " |" << endl;
	cout << setw(35) << "|------------------|"				<< "--------------|" << "---------------|"	<< endl;
	cout << setw(35) << "| of               |" << setw(13) << org_of << " |" << setw(14) << att_of << " |" << endl;
	cout << setw(35) << "|------------------|"				<< "--------------|" << "---------------|"	<< endl;
	cout << setw(35) << "| and              |" << setw(13) << org_and << " |" << setw(14) << att_and << " |" << endl;
	cout << setw(35) << "|------------------|"				<< "--------------|" << "---------------|"	<< endl;
	cout << setw(35) << "| to               |" << setw(13) << org_to << " |" << setw(14) << att_to << " |" << endl;
	cout << setw(35) << "|------------------|"				<< "--------------|" << "---------------|"	<< endl;
	cout << setw(35) << "| a                |" << setw(13) << org_a << " |" << setw(14) << att_a << " |" << endl;
	cout << setw(35) << "*------------------+"				<< "--------------+" << "---------------*"	<< endl;
	cout << endl;
	output();
}
	//--------------------------------------------------------------------------------------------
void output()
{
	ofstream outfile;
	outfile.open ("comparative_statistics.txt");
	outfile << setw(67) << "-------------------------------------------" << endl;
	outfile << setw(67) << "C O M P A R A T I V E   S T A T I S T I C S" << endl;
	outfile << setw(67) << "-------------------------------------------" << endl;
	outfile << endl;
	outfile << setw(40) << "*------------------+"				<< "--------------+" << "---------------*"	<< endl;
	outfile << setw(40) << "|                  |"				<< " Orignal Text |" << " Attacked Text |"	<< endl;
	outfile << setw(40) << "|------------------|"				<< "--------------|" << "---------------|"	<< endl;
	outfile << setw(40) << "| Attack Type:     |" << "                              |" << endl;
	outfile << setw(40) << "|------------------|"				<< "--------------|" << "---------------|"	<< endl;
	outfile << setw(40) << "| Words            |" << setw(13) << org_word_count << " |" << setw(14) << att_word_count << " |" << endl;
	outfile << setw(40) << "|------------------|"				<< "--------------|" << "---------------|"	<< endl;
	outfile << setw(40) << "| Digit Count      |" << setw(13) << org_digit_count << " |" << setw(14) << att_digit_count << " |" << endl;
	outfile << setw(40) << "|------------------|"				<< "--------------|" << "---------------|"	<< endl;
	outfile << setw(40) << "| Character Count  |" << setw(13) << org_character_count << " |" << setw(14) << att_character_count << " |" << endl;
	outfile << setw(40) << "|------------------|"				<< "--------------|" << "---------------|"	<< endl;
	outfile << setw(40) << "| Frequency Count: |"				<<	"                              |" << endl;
	outfile << setw(40) << "|------------------|"				<< "--------------|" << "---------------|"	<< endl;
	outfile << setw(40) << "| the              |" << setw(13) << org_the << " |" << setw(14) << att_the << " |" << endl;
	outfile << setw(40) << "|------------------|"				<< "--------------|" << "---------------|"	<< endl;
	outfile << setw(40) << "| of               |" << setw(13) << org_of << " |" << setw(14) << att_of << " |" << endl;
	outfile << setw(40) << "|------------------|"				<< "--------------|" << "---------------|"	<< endl;
	outfile << setw(40) << "| and              |" << setw(13) << org_and << " |" << setw(14) << att_and << " |" << endl;
	outfile << setw(40) << "|------------------|"				<< "--------------|" << "---------------|"	<< endl;
	outfile << setw(40) << "| to               |" << setw(13) << org_to << " |" << setw(14) << att_to << " |" << endl;
	outfile << setw(40) << "|------------------|"				<< "--------------|" << "---------------|"	<< endl;
	outfile << setw(40) << "| a                |" << setw(13) << org_a << " |" << setw(14) << att_a << " |" << endl;
	outfile << setw(40) << "*------------------+"				<< "--------------+" << "---------------*"	<< endl;
	outfile << endl;
	outfile.close();
}
};
//________________________________ COMPARATIVE CLASS ENDS ________________________________________
//------------------------------------------------------------------------------------------------
//________________________________ HEADER FUNCTION STARTS ________________________________________
void header()
{
	cout << "________________________________________________________________________________" << endl;
//	cout << setw(51) << "---------------------" << endl;
	cout << setw(56) << "M I N I   T E X T   C O R P U S" << endl;
	cout << setw(56) << "-------------------------------" << endl;
	cout << "________________________________________________________________________________" << endl;
}
//_________________________________ HEADER FUNCTION ENDS _________________________________________
//------------------------------------------------------------------------------------------------
//_________________________________ MENU FUNCTION STARTS _________________________________________
void menu()
{
	cout << setw(49) << "-----------------" << endl;
	cout << setw(49) << "M A I N   M E N U" << endl;
	cout << setw(49) << "-----------------" << endl;
    cout << "________________________________________________________________________________" << endl;
	cout << setw(57) << "----------------------------------" << endl;
	cout << setw(57) << "| Press I for insertation attack |" << endl;
	cout << setw(57) << "* Press D for deletion attack    *" << endl;
	cout << setw(57) << "* Press C for combine attack     *" << endl;
	cout << setw(57) << "* Press P for part selection     *" << endl;
	cout<<  setw(57) << "| Press X for exit the program   |" << endl;
	cout << setw(57) << "----------------------------------" << endl;
	cout << "________________________________________________________________________________" << endl;
	option();
}
//____________________________________ MENU FUNCTION ENDS ________________________________________
//------------------------------------------------------------------------------------------------
//__________________________________ OPTION FUNCTION STARTS ______________________________________
void option()
{
	char opt;
	cout << "Enter your destination ";
	cin >> opt;
	switch (opt)
	{
	case 'i':
		{
			system ("cls");
			header();
			ins();
			break;
		}
	case 'I':
		{
			system ("cls");
			header();
			ins();
			break;
		}
	case 'd':
		{
			system ("cls");
			header();
			del();
			break;
		}
	case 'D':
		{
			system ("cls");
			header();
			del();
			break;
		}
	case 'c':
		{
			system ("cls");
			header();
			com();
			break;
		}
	case 'C':
		{
			system ("cls");
			header();
			com();
			break;
		}
	case 'p':
		{
			system ("cls");
			header();
			part_selection();
			break;
		}
	case 'P':
		{
			system ("cls");
			header();
			part_selection();
			break;
		}
	case 'x':
		{
			system ("cls");
			header();
			exit_confirm();
			break;
		}
	case 'X':
		{
			system ("cls");
			header();
			exit_confirm();
			break;
		}
	default:
		{
			system ("cls");
			header();
			menu();
		}
	}
}
//__________________________________ OPTION FUNCTION ENDS ________________________________________
//------------------------------------------------------------------------------------------------
//_______________________________ INSERTION FUNCTION STARTS ______________________________________
void ins()
{
	system ("cls");
	header();
	cout << setw(61) << "-----------------------------------------" << endl;
	cout << setw(61) << "I N S E R T I O N   A T T A C K   M E N U" << endl;
	cout << setw(61) << "-----------------------------------------" << endl;
    cout << "________________________________________________________________________________" << endl;
	cout << setw(57) << "----------------------------------" << endl;
	cout << setw(57) << "| Press C for continue attack    |" << endl;
	cout << setw(57) << "* Press S for file statistics    *" << endl;
	cout << setw(57) << "* Press B for previous menu      *" << endl;
	cout<<  setw(57) << "| Press X for exit the program   |" << endl;
	cout << setw(57) << "----------------------------------" << endl;
	cout << "________________________________________________________________________________" << endl;
	insert_option();
}
//________________________________ INSERTION FUNCTION ENDS _______________________________________
//------------------------------------------------------------------------------------------------
// _______________________________ DELETION FUNCTION STARTS ______________________________________
void del()
{
	system ("cls");
	header();
	cout << setw(60) << "---------------------------------------" << endl;
	cout << setw(60) << "D E L E T I O N   A T T A C K   M E N U" << endl;
	cout << setw(60) << "---------------------------------------" << endl;
	cout << "________________________________________________________________________________" << endl;
	cout << setw(57) << "----------------------------------" << endl;
	cout << setw(57) << "| Press C for continue attack    |" << endl;
	cout << setw(57) << "* Press S for file statistics    *" << endl;
	cout << setw(57) << "* Press B for previous menu      *" << endl;
	cout<<  setw(57) << "| Press X for exit the program   |" << endl;
	cout << setw(57) << "----------------------------------" << endl;
	cout << "________________________________________________________________________________" << endl;
	delete_option();
}
//________________________________ DELETION FUNCTION ENDS ________________________________________
//------------------------------------------------------------------------------------------------
//_______________________________ COMBINE FUNCTION STARTS ________________________________________
void com()
{
	system ("cls");
	header();
	cout << setw(59) << "-------------------------------------" << endl;
	cout << setw(59) << "C O M B I N E   A T T A C K   M E N U" << endl;
	cout << setw(59) << "-------------------------------------" << endl;
	cout << "________________________________________________________________________________" << endl;
	cout << setw(57) << "----------------------------------" << endl;
	cout << setw(57) << "| Press C for continue attack    |" << endl;
	cout << setw(57) << "* Press S for file statistics    *" << endl;
	cout << setw(57) << "* Press B for previous menu      *" << endl;
	cout<<  setw(57) << "| Press X for exit the program   |" << endl;
	cout << setw(57) << "----------------------------------" << endl;
	cout << "________________________________________________________________________________" << endl;
	combo_option();
}
//_________________________________ COMBINE FUNCTION ENDS ________________________________________
//------------------------------------------------------------------------------------------------
//____________________________ INSERTION OPTION FUNCTION STARTS __________________________________
void insert_option()
{
	char opt;
	cout << "Enter your destination ";
	cin >> opt;
	switch (opt)
	{
	case 'c':
		{
			insertion i;
			system ("cls");
			header();
			i.default_read();
			break;
		}
	case 'C':
		{
			insertion i;
			system ("cls");
			header();
			i.default_read();
			break;
		}
	case 's':
		{
			statistics s;
			system ("cls");
			header();
			s.stat();
			system ("pause");
			system ("cls");
			header();
			ins();
			break;
		}
	case 'S':
		{
			statistics s;
			system ("cls");
			header();
			s.stat();
			system ("pause");
			system ("cls");
			header();
			ins();
			break;
		}
	case 'b':
		{
			system ("cls");
			header();
			menu();
			break;
		}
	case 'B':
		{
			system ("cls");
			header();
			menu();
			break;
		}
	case 'x':
		{
			system ("cls");
			header();
			exit_confirm();
			break;
		}
	case 'X':
		{
			system ("cls");
			header();
			exit_confirm();
			break;
		}
	default:
		{
			system ("cls");
			header();
			ins();
		}
	}
}
//______________________________ INSERTION OPTION FUNCTION ENDS __________________________________
//------------------------------------------------------------------------------------------------
//______________________________ DELETION OPTION FUNCTION STARTS__________________________________
void delete_option()
{
	char opt;
	cout << "Enter your destination ";
	cin >> opt;
	switch (opt)
	{
	case 'c':
		{
			deletion d;
			system ("cls");
			header();
			d.input_read();
			break;
		}
	case 'C':
		{
			deletion d;
			system ("cls");
			header();
			d.input_read();
			break;
		}
	case 's':
		{
			statistics s;
			system ("cls");
			header();
			s.stat();
			system ("pause");
			system ("cls");
			header();
			del();
			break;
		}
	case 'S':
		{
			statistics s;
			system ("cls");
			header();
			s.stat();
			system ("pause");
			system ("cls");
			header();
			del();
			break;
		}
	case 'b':
		{
			system ("cls");
			header();
			menu();
			break;
		}
	case 'B':
		{
			system ("cls");
			header();
			menu();
			break;
		}
	case 'x':
		{
			system ("cls");
			header();
			exit_confirm();
			break;
		}
	case 'X':
		{
			system ("cls");
			header();
			exit_confirm();
			break;
		}
	default:
		{
			system ("cls");
			header();
			del();
		}
	}
}
//________________________________ DELETION OPTION FUNCTION ENDS _________________________________
//------------------------------------------------------------------------------------------------
//________________________________ COMBINE OPTION FUNCTION STARTS ________________________________
void combo_option()
{
	char opt;
	cout << "Enter your destination ";
	cin >> opt;
	switch (opt)
	{
	case 'c':
		{
			system ("cls");
			header();
			cout << "Combine Attack Functionality Does Not Exisit" << endl;
			system ("pause");
			com();
			break;
		}
	case 'C':
		{
			system ("cls");
			header();
			cout << "Combine Attack Functionality Does Not Exisit" << endl;
			system ("pause");
			com();
			break;
		}
	case 's':
		{
			statistics s;
			system ("cls");
			header();
			s.stat();
			system ("pause");
			system ("cls");
			header();
			com();
			break;
		}
	case 'S':
		{
			statistics s;
			system ("cls");
			header();
			s.stat();
			system ("pause");
			system ("cls");
			header();
			com();
			break;
		}
	case 'b':
		{
			system ("cls");
			header();
			menu();
			break;
		}
	case 'B':
		{
			system ("cls");
			header();
			menu();
			break;
		}
	case 'x':
		{
			system ("cls");
			header();
			exit_confirm();
			break;
		}
	case 'X':
		{
			system ("cls");
			header();
			exit_confirm();
			break;
		}
	default:
		{
			system ("cls");
			header();
			com();
		}
	}
}
//________________________________ COMBINE OPTION FUNCTION ENDS __________________________________
//------------------------------------------------------------------------------------------------
//_____________________________ EXIT CONFIRMATION FUNCTION STARTS ________________________________
void exit_confirm()
{
	char opt;
	cout << setw(59) << "Are You Sure To Exit Program (Y / N) ";
	cin >> opt;
	switch (opt)
	{
	case 'y':
		{
			system ("cls");
			header();
			cout << setw(54) << "Program Closed Successfully" << endl;
			cout << endl;
			cout << setw(49) << "Copy Rights 2010" << endl;
			cout << endl;
			system ("pause");
			exit(0);
			break;
		}
	case 'Y':
		{
			system ("cls");
			header();
			cout << setw(54) << "Program Closed Successfully" << endl;
			cout << endl;
			cout << setw(49) << "Copy Rights 2010" << endl;
			cout << endl;
			system ("pause");
			exit(0);
			break;
		}
	case 'n':
		{
			system ("cls");
			header();
			menu();
			break;
		}
	case 'N':
		{
			system ("cls");
			header();
			menu();
			break;
		}
	default:
		{
			system ("cls");
			header();
			exit_confirm();
		}
	}
}
//______________________________ EXIT CONFIRMATION FUNCTION ENDS _________________________________
//------------------------------------------------------------------------------------------------
void part_selection()
{
	system ("cls");
	cout << "________________________________________________________________________________" << endl;
	cout << setw(57) << "+++++++++++++++++++++++++++++++" << endl;
	cout << setw(57) << "+ I S L A M I C  U N I V E R S I T Y"  << endl;
	cout << setw(57) << "+++++++++++++++++++++++++++++++" << endl;
	cout << endl;
	cout << setw(57) << "-------------------------------" << endl;
	cout << setw(57) << "S E M E S T E R   P R O J E C T" << endl;
	cout << setw(57) << "===============================" << endl;
	cout << setw(57) << "M I N I   T E X T   C O R P U S" << endl;
	cout << setw(57) << "-------------------------------" << endl;
	cout << "________________________________________________________________________________" << endl;
	cout << setw(53) << "------------------------" << endl;
	cout << setw(53) << "+ Press 1 for PART ONE +" << endl;
	cout << setw(53) << "+ Press 2 for PART TWO +" << endl;
	cout << setw(53) << "------------------------" << endl;
	cout << "________________________________________________________________________________" << endl;
	char opt;
	cout << "Enter your destination ";
	cin >> opt;
	switch (opt)
	{
	case '1':
		{
			system ("cls");
			header();
			menu();
			break;
		}
	case '2':
		{
			comparison c;
			system ("cls");
			header();
			cout << setw(66) << "-----------------------------------------------------" << endl;
			cout << setw(66) << "C O M P A R A T I V E   S T A T I S T I C S   M E N U" << endl;
			cout << setw(66) << "-----------------------------------------------------" << endl;
			cout << "________________________________________________________________________________" << endl;
			c.compare();
			system ("pause");
			part_selection();
			break;
		}
	default:
		{
			system ("cls");
			part_selection();
		}
	}
}
//___________________________________ MAIN FUNCTION STARTS _______________________________________
void main()
{
	system ("color 0E");
	part_selection();
}
//_____________________________________ MAIN FUNCTION ENDS _______________________________________
//------------------------------------------------------------------------------------------------
//_________________________________________ END OF FILE __________________________________________