<?php

/*This file contains a function that, given a result set from a SQL query, prints out the result set into an HTML 
table. Note: the beginning <table> and ending </table> tags are not printed by this function, nor are any table
headers. This gives the function flexibility, allowing any result set of arbitrary size and contents to be printed
by the function. The user opens the table tag and prints the headers before calling the function, and closes the 
table tag after the function call.*/


function print_result_table($sql_result){

	//parse the result set as an associative array: get the next row.
	while ($row = @ mysqli_fetch_array( $sql_result, MYSQL_ASSOC )){

		//make a new table row
		print("<tr>\n");


		//take each column and print as it's own table element
		foreach ($row as $col) {

			//get last 4 chars in column for use in later if statement
			$last4 = substr($col, -4);


	
			print("\t<td>");
			
			//check if we have hyperlink in result (if 1st 4 chars are "http") :
			if ( substr($col, 0, 4) == "http" ){

				//print in anchor tag
				print("<a href=\"{$col}\" >{$col}</a>");

			}

			//check if we have image in result (last 4 chars are .jpg, .gif, or .png:
			else if ( $last4 == ".jpg" or $last4 == ".gif" or $last4 == ".png"){

				//print as image src
				print("<img src=\"images/{$col}\" >");

			}

			//check for null values
			else if ($col == ""){

				print("N/A");

			}

			//otherwise just text, print without special markup
			else{

				print($col);

			}

			
			print("</td>\n");

		}

		//all columns printed, end the row
		//make a new table row
		print("</tr>\n");


	}





}




?>