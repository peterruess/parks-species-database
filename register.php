<!DOCTYPE html>

<!-- This program is designed to create a web page with 3 forms: the first for getting all parks that a species has been
sighted in by its genus and species name, the second for doing the same but by the species' common name, and the third
for getting all species sighted in a particular park, by the park name. -->

<?php

/*This file receives a total of 11 different fields to be used to create a new record in the researchers table. It first checks that the user has provided information for the required fields: username, password, f_name, l_name, and email. It then builds the insert query using the information provided by the user, and submits the query. If at any time a query fails, it prints an error message to the user. Finally, if the query does not fail, it gathers all fields and all records from the researchers table and prints them as an HTML table to the page. */

ini_set('display_errors', true);
ini_set('display_startup_errors', true);
error_reporting(E_ALL);

//include database vars.
include("/home/pcr241/dbFiles/db_info.php");

//include function to print result set in a table
include("print_result_table.php");

?>

<html lang="en">

	<head>
		<meta charset="utf-8" />
		<title>Contribute to the Growth of Our Database</title>

		<!-- General style sheet -->
		<link rel="stylesheet" type="text/css" href="gen.css">

		<!-- Specific style sheet -->
		<link rel="stylesheet" type="text/css" href="spec_sightings_style.css">

		<!-- Another specific style sheet with a few more additions -->
		<link rel="stylesheet" type="text/css" href="register_extra_styles.css">

	
		

	</head>

	<body>


		<!-- Header with simple title and navigation for the 3 different parts of the assignment-->
		<header role="banner">
			<h1>Register To Become a Researcher For This Database</h1>
			

			<nav role="navigation">
				<ul>
					<li><a href="index.html">Home</a></li>
					<li><a href="discover.php">Discover</a></li>
					<li><a href="contribute.php">Contribute</a></li>
				</ul>
			</nav>

		</header>




		<section id="main" role="main">

			<h2>The Researchers table is displayed below, including the new user.</h2>
			<p class="note">Note: if this website dealt with a real database, it would of course not display the entire researchers table after each registration. However, for the purposes of this assignment, it must do so.</p>



			<?php



			//get the required fields
			$username = $_POST["username"];
			$password = $_POST["password"];
			$f_name = $_POST["f_name"];
			$l_name = $_POST["l_name"];
			$email = $_POST["email"];


			//if any of the required fields are absent, send user error message and abort
			if ( empty($username) || empty($password) || empty($f_name) || empty($l_name) || empty($email) ){

				//tell user that they are missing a required field
				die("<h3 style=\"color:red;\">Missing required information. Must provide username, password, first and last name, and email.</h3>");


			}

			//get the other, non-required fields
			$phone = $_POST["phone"];
			$addr = $_POST["addr"];
			$city = $_POST["city"];
			$state = $_POST["state"];
			$zip = $_POST["zip"];
			$interest = $_POST["interest"];


			//if a field is empty, replace it's empty value with NULL, for insertion into the database
			$phone = ( empty($phone) ) ? "NULL" : $phone;
			$addr = ( empty($addr) ) ? "NULL" : $addr;
			$city = ( empty($city) ) ? "NULL" : $city;
			$state = ( empty($state) ) ? "NULL" : $state;
			$zip = ( empty($zip) ) ? "NULL" : $zip;
			$interest = ( empty($interest) ) ? "NULL" : $interest;


			//trim all whitespace from left and right ends of strings provided by user
			$username = trim($username);
			$password = trim($password);
			$f_name = trim($f_name);
			$l_name = trim($l_name);
			$email = trim($email);
			$phone = trim($phone);
			$addr = trim($addr);
			$city = trim($city);
			$state = trim($state);
			$zip = trim($zip);
			$interest = trim($interest);

			//connect to the database
			if ( ! ($connection = @ mysqli_connect($db_server, $db_user, $db_pwd, $db_name) ) ){

				//warn user of connection error
				die("<h3 style=\"color:red;\">Cannot connect to databse. Try refreshing page.</h3>");

			}


			//use mysqli escape string to help guard against sql injection and other database insecurities
			$username = mysqli_real_escape_string($connection, $username);
			$password = mysqli_real_escape_string($connection, $password);
			$f_name = mysqli_real_escape_string($connection, $f_name);
			$l_name = mysqli_real_escape_string($connection, $l_name);
			$email = mysqli_real_escape_string($connection, $email);
			$phone = mysqli_real_escape_string($connection, $phone);
			$addr = mysqli_real_escape_string($connection, $addr);
			$city = mysqli_real_escape_string($connection, $city);
			$state = mysqli_real_escape_string($connection, $state);
			$zip = mysqli_real_escape_string($connection, $zip);
			$interest = mysqli_real_escape_string($connection, $interest);


			//build the insert query
			$query= "INSERT INTO researchers VALUES(\"{$username}\", \"{$password}\", \"{$f_name}\", \"{$l_name}\", \"{$email}\", \"{$phone}\", \"{$addr}\", \"{$city}\", \"{$state}\", \"{$zip}\", \"{$interest}\")";



			//run the query
			if ( ! ($result = @ mysqli_query($connection, $query) )){


				//if the query failed, it is most likely the case that the username was taken. Tell user
				die("<h3 style=\"color:red;\">Failed to register. It is likely that this username is already taken. Please enter a different username.</h3>");

			}


			//otherwise the query has succeeded, so now display all data from the researchers table to the user
			$newQuery = "SELECT username, password, first_name, last_name, email, phone, street_addr, city, state, zip, interest FROM researchers ORDER BY username";


			//run the query
			if ( ! ($newResult = @ mysqli_query($connection, $newQuery) )){


				//if the query failed, it is most likely the case that the username was taken. Tell user
				die("<h3 style=\"color:red;\">Failed to access researcher records.</h3>");

			}

			//print out the researchers


			//must first open table element
			print("<table>\n");

			//print headers
			print("<tr>\n");
			print("\t<th>Username</th>\n");
			print("\t<th>Password</th>\n");
			print("\t<th>First Name</th>\n");
			print("\t<th>Last Name</th>\n");
			print("\t<th>Email</th>\n");
			print("\t<th>Phone</th>\n");
			print("\t<th>Address</th>\n");
			print("\t<th>City</th>\n");
			print("\t<th>State</th>\n");
			print("\t<th>Zip</th>\n");
			print("\t<th>Interest</th>\n");
			print("</tr>\n");


			//call function to print table data
			print_result_table($newResult);

			//finally close table element
			print("</table>\n");
			

	
			//free result set
			mysqli_free_result($newResult);

			//finally close link to database, since we will no longer need it
			mysqli_close($connection);

			




			?>
			



		</section>


	</body>

</html>


