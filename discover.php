<!DOCTYPE html>

<!-- This program is designed to create a web page with 3 forms: the first for getting all parks that a species has been
sighted in by its genus and species name, the second for doing the same but by the species' common name, and the third
for getting all species sighted in a particular park, by the park name. -->

<?php

//include database vars.
include("/home/pcr241/dbFiles/db_info.php");

?>

<html lang="en">

	<head>
		<meta charset="utf-8" />
		<title>Discover All of the Exciting Species in NYC's Parks</title>

		<!-- General style sheet -->
		<link rel="stylesheet" type="text/css" href="gen.css">

		<!-- Specific style sheet -->
		<link rel="stylesheet" type="text/css" href="discover_style.css">

	
		

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

		<!-- h2 headers separate the different types of search queries to be made: searches by 
		species or searches by park -->

		<h2>Here you can find all sightings of a specific species</h2>

		<p class="note">Note: there may be multiple sightings of a given species in a given park, each made at a
		   different date. These records are kept in the database for research purposes, in order to
		   track species’ population changes. If you are only looking for the parks that a given 
		   species has been sighted in, we recommend you chose the "Unique Sightings" option. Otherwise you
		   will see each sighting of the species in a particular park, along with the sighting date and the
		   count of the species, if available.</p>





		<!-- For finding all sightings of a given species, by genus/species name -->
		<h3>Search by genus and species name:</h3>

		<form action="get_sightings_by_spec.php" method="get"> 
		
		<div>
		<!-- All search options, including drop-down menu of genus/species name -->

			<label>

				Genus/Species Name:

				<?php

					//connect to the database
					if ( ! ($connection = @ mysqli_connect($db_server, $db_user, $db_pwd, $db_name) ) ){

						//warn user of error
						die("<span style=\"color:red;\">Cannot connect to databse. Try refreshing page.</span>");

					}
					
					//create the query to select all genus and species names from table
					$query = "SELECT genus_name, species_name FROM species ORDER BY genus_name, species_name";

					//run the query
					if ( ! ($result = @ mysqli_query($connection, $query) )){

						die("<span style=\"color:red;\">Problems with databse. Try refreshing page.</span>");
					
					}

					//create a select box for the genus/species name options
					print("<select name=\"g_s_name\" > \n");

					//fetch the results of the query, using numeric array
					while ($row = @ mysqli_fetch_array( $result, MYSQL_NUM )){

							//must be only two columns per row in this query
						
					
							//create value as genus name and species name separated by "--", can parse
							//this in the php file that processes search query
							print("\t\t\t\t\t<option value=\"{$row[0]}--{$row[1]}\" > ");

							//print genus and species name sep. by space for user
							print($row[0] . " " . $row[1]);

							print(" </option>\n");


					}

					//close the select box when done
					print("\t\t\t\t</select> \n");

			

				?>

			</label>

			<label>

				List results by:
				<select name="order_type">
					<option value="0">Park Ascending</option>
					<option value="1">Park Descending</option>
					<option value="2">Borough Ascending</option>
					<option value="3">Borough Descending</option>
				</select>
				
			</label>

			<label>
				Display:

				<select name="display_type">
					<option value="unique">Unique Sightings</option>
					<option value="all">All Sightings</option>
				</select>

			</label>

		</div>


		<div class="button">
			
			<input type="submit" value="Search" />

		</div>	



		</form>

		




		<!-- For finding all sightings of a given species, by common name -->

		<h3>Or by common name:</h3>

		<form action="get_sightings_by_spec.php" method="get">

		<div>
		<!-- All search options, including drop-down menu of common name -->

			<label>

				Common Name:

				

				<?php

					//connect to the database
					if ( ! ($connection = @ mysqli_connect($db_server, $db_user, $db_pwd, $db_name) ) ){

						//warn user of error
						die("<span style=\"color:red;\">Cannot connect to databse. Try refreshing page.</span>");

					}
					
					//create the query to select all species common names from table
					$query = "SELECT common_name FROM species ORDER BY common_name";

					//run the query
					if ( ! ($result = @ mysqli_query($connection, $query) )){

						die("<span style=\"color:red;\">Problems with databse. Try refreshing page.</span>");
					
					}

					//create a select box for the common name options
					print("<select name=\"c_name\" > \n");

					//fetch the results of the query, using associative array
					while ($row = @ mysqli_fetch_array( $result, MYSQL_ASSOC )){

						//take each common name listed and make it an option for the select box
						foreach ($row as $cname) {
					
							//value must be the common name from the table for use in databse search
							print("\t\t\t\t\t<option value=\"{$cname}\" > ");

							//the common name from the table is also suitable for display to user
							print($cname);

							print(" </option>\n");

						}


					}

					//close the select box when done
					print("\t\t\t\t</select> \n");


				?>

				

			</label>

			<label>

				List results by:
				<select name="order_type">
					<option value="0">Park Ascending</option>
					<option value="1">Park Descending</option>
					<option value="2">Borough Ascending</option>
					<option value="3">Borough Descending</option>
				</select>
				
			</label>

			<label>
				Display:

				<select name="display_type">
					<option value="unique">Unique Sightings</option>
					<option value="all">All Sightings</option>
				</select>

			</label>

		</div>


		<div class="button">
			
			<input type="submit" value="Search" />

		</div>	
			

		</form>
		





		<!-- For finding all sightings in a particular park -->

		<h2>Or, you can discover all species sightings in a particular park</h2>
		
		<p class="note">If you are only looking for all of the species found in a given park, we
		   recommend you chose the "Unique Sightings" option. Otherwise you will see each sighting of
		   a particular species in this park, along with the sighting date and a count of that species,
		   if available.</p>

		<form action="get_sightings_by_park.php" method="get">


			<div>

				Park Name:

				<?php

					//use old connection
					
					//create the query to select all park names from the table
					$query = "SELECT park_name FROM parks ORDER BY park_name";

					//run the query
					if ( ! ($result = @ mysqli_query($connection, $query) )){

						die("<span style=\"color:red;\">Problems with databse. Try refreshing page.</span>");
					
					}

					//create a select box for the park name options
					print("<select name=\"p_name\" > \n");

					//fetch the results of the query, using numeric array
					while ($row = @ mysqli_fetch_array( $result, MYSQL_NUM )){

							//must be only one column per row for this query
						
					
							//the value is the park name
							print("\t\t\t\t\t<option value=\"{$row[0]}\" > ");

							//park name is also shown to user
							print($row[0]);

							print(" </option>\n");


					}

					//close the select box when done
					print("\t\t\t\t</select> \n");

					//free result set
					mysqli_free_result($result);

					//finally close link to database, since we will no longer need it
					mysqli_close($connection);


				?>


				<label>

					List results by:
					<select name="order_type">
						<option value="0">Genus/Species Asc</option>
						<option value="1">Genus/Species Desc</option>
						<option value="2">Common Name Asc</option>
						<option value="3">Common Name Desc</option>
						<option value="4">Kingdom Asc</option>
						<option value="5">Kingdom Descending</option>
					</select>
					
				</label>

				<label>
					Display:

					<select name="display_type">
						<option value="unique">Unique Sightings</option>
						<option value="all">All Sightings</option>
					</select>

				</label>
				

			</div>



		<div class="button">
			
			<input type="submit" value="Search" />

		</div>	
			

		</form>



		</section>





	</body>

</html>


