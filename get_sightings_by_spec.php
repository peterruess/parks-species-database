<!DOCTYPE html>

<!-- This program is passed either a genus/species name pair, or the common name of the species, along with info on how
to order the result set and whether to condense results. The program will determine if it is submitting a query using 
the genus/species name pair, or if it is is using the common name, and will also determine the received criteria for 
the results. It will then chose the proper query to create based on the input it receives, submit the query, and present
the result in an HTML table. -->

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
				$order_array = array("ORDER BY park_name ASC, borough ASC", "ORDER BY park_name DESC, borough DESC", 
									 "ORDER BY borough ASC, park_name ASC", "ORDER BY borough DESC, park_name DESC");

				//get the way to order the result that the user specified
				$order_num = (int) $_GET["order_type"];

				$order_str = $order_array[$order_num];



				//2.
				//get whether the displayed results should be condensed to show only distinct sightings
				$display_pref = $_GET["display_type"];

				$distinct_str = ""; //this will contain the word "DISTINCT" if user wants unique results
				

				/*In addition, if the user does not want unique results, the date and count of species sightings
				must also be displayed. */

				//these will both be empty if user wants unique results
				$date_str = ",si.s_date";
				$count_str = ",si.count";    

				if ($display_pref == "unique"){

					//user wants unique results
					$distinct_str = "DISTINCT";
					$date_str = "";
					$count_str = "";

				}
				//otherwise user does not want only distinct results. Keep the $distinct_str empty, and the $date_str
				//and $count_str as they are

				



				//3.
				//determine if the user gave us the genus-species name combo, or the common name

				$common_name = $_GET["c_name"];

				$gen_spec_name = $_GET["g_s_name"];

				


				/* B. Build the Query */


				//open connection to database, as will be need to run short query before determining our main query
				if ( ! ($connection = @ mysqli_connect($db_server, $db_user, $db_pwd, $db_name) ) ){

					//warn user of error
					die("<h1 style=\"color:red;\">Cannot connect to databse. Try refreshing page.</h1>");

				}


				$query = "";


				if ( !isset($common_name)){ //user wants to use genus/species name combo in query
					
					
					//separate the genus name and species name
					$split_names = explode("--", $gen_spec_name);

					//genus name is 1st substr and species name is 2nd substr
					$genus_name = $split_names[0];
					$species_name = $split_names[1];

					//print header of genus/species name
					print("<h2>Sightings of {$genus_name} {$species_name}:</h2>\n");



					/*If user doesn't want only unique results, we must determine if the species they provided is 
					microscopic. If so, we need to change the $count_str variable */
					if ($count_str != ""){

						//run query to see if species is microscopic
						$prelim_query = "SELECT common_name FROM species".
						" WHERE genus_name = \"{$genus_name}\" AND species_name = \"{$species_name}\"".
						" AND microscopic = 1";

						
						if ( ! ($micro_result = @ mysqli_query($connection, $prelim_query) )){

						die("<h1 style=\"color:red;\">Problems with databse. Try refreshing page.</h1>");
					
						}


						//if result set is not empty species is microscopic
						if ( mysqli_num_rows($micro_result) > 0 ){

							//it is microscopic. Set $count_str to display count per sq. cm. rather than an integer count
							$count_str = ",si.count_per_sq_cm";

						}


					}



					$query = "SELECT {$distinct_str} p.park_name, p.street, p.borough,".
					" p.info_url {$date_str} {$count_str} ".
					" FROM parks p".
					" INNER JOIN sightings si USING(park_id)".
					" INNER JOIN species sp USING(genus_name, species_name)".
					" WHERE sp.genus_name = \"{$genus_name}\" AND sp.species_name = \"{$species_name}\"".
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
						print("\t<th>Park Name</th>\n");
						print("\t<th>Address</th>\n");
						print("\t<th>Borough</th>\n");
						print("\t<th>Info</th>\n");
						

						//print date and correct count headers, if unique results not requested

						if ($date_str == ",si.s_date"){
							print("\t<th>Date</th>\n");
						}

						if ($count_str == ",si.count"){
							print("\t<th>Count</th>\n");
						}
						else if($count_str == ",si.count_per_sq_cm"){
							print("\t<th>Count per cm<sup>2</sup></th>\n");
						}

						print("</tr>\n"); //close the header row

						//call function to print table data
						print_result_table($result);

						//finally close table element
						print("</table>\n");
						

					}


				} //end processing genus/species name request



				/* B. Build the Query */

				else{ //user wants to use common name in query

					//print header of common name
					print("<h2>Sightings of {$common_name}:</h2>\n");

	
					//already have db connection open
					
					/*If user doesn't want only unique results, we must determine if the species they provided is 
					microscopic. If so, we need to change the $count_str variable */
					if ($count_str != ""){

						//run query to see if species is microscopic
						$prelim_query = "SELECT common_name FROM species".
						" WHERE common_name = \"{$common_name}\" ".
						" AND microscopic = 1";

						
						if ( ! ($micro_result = @ mysqli_query($connection, $prelim_query) )){

						die("<h1 style=\"color:red;\">Problems with databse. Try refreshing page.</h1>");
					
						}


						//if result set is not empty species is microscopic
						if ( mysqli_num_rows($micro_result) > 0 ){

							//it is microscopic. Set $count_str to display count per sq. cm. rather than an integer count
							$count_str = ",si.count_per_sq_cm";

						}


					}

					$query = "SELECT {$distinct_str} p.park_name, p.street, p.borough,".
					" p.info_url {$date_str} {$count_str} ".
					" FROM parks p".
					" INNER JOIN sightings si USING(park_id)".
					" INNER JOIN species sp USING(genus_name, species_name)".
					" WHERE sp.common_name = \"{$common_name}\"".
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
						print("\t<th>Park Name</th>\n");
						print("\t<th>Address</th>\n");
						print("\t<th>Borough</th>\n");
						print("\t<th>Info</th>\n");
						

						//print date and correct count headers, if unique results not requested

						if ($date_str == ",si.s_date"){
							print("\t<th>Date</th>\n");
						}

						if ($count_str == ",si.count"){
							print("\t<th>Count</th>\n");
						}
						else if($count_str == ",si.count_per_sq_cm"){
							print("\t<th>Count per cm<sup>2</sup></th>\n");
						}
						print("</tr>\n"); //close the header row

						//call function to print table data
						print_result_table($result);

						//finally close table element
						print("</table>\n");
						

					}

	
					
				} //end processing common name request

				

				//free result set
				mysqli_free_result($result);

				//finally close link to database, since we will no longer need it
				mysqli_close($connection);


			

			?>

		</section>





	</body>

</html>


