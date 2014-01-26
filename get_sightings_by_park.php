<!DOCTYPE html>

<!-- This program is passed a park name, along with info on how to order the result set and whether to condense results. The program will then use this information to submit a query to the database to get all sightings of any
species within the park provided by the user. It will finally display this information in an HTML table. -->

<?php

//include database vars.
include("/home/pcr241/dbFiles/db_info.php");

//include function to print result set in a table
include("print_result_table.php");

?>

<html lang="en">

	<head>
		<meta charset="utf-8" />
		<title>Find Species in NYC's Parks</title>

		<!-- General style sheet -->
		<link rel="stylesheet" type="text/css" href="gen.css">

		<!-- Specific style sheet -->
		<link rel="stylesheet" type="text/css" href="spec_sightings_style.css">

		<!-- Extra stylesheet to correct the dimensions of the main section and header to fit the larger table of all species sighted at a specific park, which is larger than the table of all parks that a species has been sighted at. -->  
		<link rel="stylesheet" type="text/css" href="dimen_corrections.css">
	
		

	</head>

	<body>


		<!-- Header with simple title and navigation for the 3 different parts of the assignment-->
		<header role="banner">
			<h1>Discover What Can Be Found in NYC's Parks</h1>
			

			<nav role="navigation">
				<ul>
					<li><a href="index.html">Home</a></li>
					<li><a href="discover.php">Discover</a></li>
					<li><a href="contribute.php">Contribute</a></li>
				</ul>
			</nav>

		</header>




		<section id="main" role="main">

		

			<?php

				/* A. Getting user input */

				//1.
				//array with all possible ways to order the result
				$order_array = array("ORDER BY genus_name ASC, species_name ASC", "ORDER BY genus_name DESC, species_name DESC", "ORDER BY common_name ASC", "ORDER BY common_name DESC", "ORDER BY kingdom ASC, genus_name ASC, species_name ASC", "ORDER BY kingdom DESC, genus_name DESC, species_name DESC");

				//get the way to order the result that the user specified
				$order_num = (int) $_GET["order_type"];

				$order_str = $order_array[$order_num];



				//2.
				//get whether the displayed results should be condensed to show only distinct sightings
				$display_pref = $_GET["display_type"];

				$distinct_str = ""; //this will contain the word "DISTINCT" if user wants unique results
				

				/*In addition, if the user does not want unique results, the date and count of species sightings
				must also be displayed. Since we won't know whether the park contains microscopic species before running the query, we must query for both the count and count/sq. cm. (which is used for microscopic species).  */

				//these will both be empty if user wants unique results
				$date_str = ",si.s_date";
				$count_str = ",si.count, si.count_per_sq_cm";    

				if ($display_pref == "unique"){

					//user wants unique results
					$distinct_str = "DISTINCT";
					$date_str = "";
					$count_str = "";

				}
				//otherwise user does not want only distinct results. Keep the $distinct_str empty, and the $date_str
				//and $count_str as they are

				



				//3.
				//get the park name from the user
				$park_name = $_GET["p_name"];

				


				/* B. Build the Query */


				//open connection to database
				if ( ! ($connection = @ mysqli_connect($db_server, $db_user, $db_pwd, $db_name) ) ){

					//warn user of error
					die("<h1 style=\"color:red;\">Cannot connect to databse. Try refreshing page.</h1>");

				}


				$query = "";
			
				

				//print header of park name
				print("<h2>Sightings at {$park_name}:</h2>\n");


				//the query
				$query = "SELECT {$distinct_str} sp.genus_name, sp.species_name, sp.common_name,".
				" sp.kingdom, sp.description_url, sp.picture_url {$date_str} {$count_str} ".
				" FROM species sp".
				" INNER JOIN sightings si USING(genus_name, species_name)".
				" INNER JOIN parks p USING(park_id)".
				" WHERE p.park_name = \"{$park_name}\"".
				" {$order_str}"; 

				

				//run the main query
				if ( ! ($result = @ mysqli_query($connection, $query) )){

					die("<h1 style=\"color:red;\">Problems with databse. Try refreshing page.</h1>");
				
				}


				/* C. Process Results */


				if ( mysqli_num_rows($result) == 0){ //no sightings yet

					print("<h3>Sorry but there have not been any sightings yet.\n</h3>");
				}

				else{

					//print out the result set to a table, using the imported function


					//must first open table element
					print("<table>\n");


					//print headers
					print("<tr>\n");
					print("\t<th>Genus</th>\n");
					print("\t<th>Species</th>\n");
					print("\t<th>Common Name</th>\n");
					print("\t<th>Kingdom</th>\n");
					print("\t<th>More Info</th>\n");
					print("\t<th>Picture</th>\n");
					

					//print date and count headers, if unique results not requested

					if ($date_str != ""){

						print("\t<th>Date</th>\n");
					}

					if ($count_str != ""){

						print("\t<th>Count</th>\n");
						print("\t<th>Count per cm<sup>2</sup></th>\n");
					}
					
					print("</tr>\n"); //close the header row

					//call function to print table data
					print_result_table($result);

					//finally close table element
					print("</table>\n");
					

				}


				//free result set
				mysqli_free_result($result);

				//finally close link to database, since we will no longer need it
				mysqli_close($connection);


			

			?>

		</section>





	</body>

</html>


