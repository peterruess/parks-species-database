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
		<title>Contribute to the Growth of Our Database</title>

		<!-- General style sheet -->
		<link rel="stylesheet" type="text/css" href="gen.css">

		<!-- Specific style sheet -->
		<link rel="stylesheet" type="text/css" href="contribute_style.css">

	
		

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


			<!-- All info for a new entry in the researchers table in the database. Will use HTML 5 elements to enforce 
			required fields, and will check these with the php script that processes the registration. -->

			<h2>Register Here:</h2>

			<form action="register.php" method="post" autocomplete>


				<div class="col1">	<!-- first col of form elements -->


					<div>
						<label for="username">Username:</label>
						<input id="username" name="username" required type="text" maxlength="30">
					</div>

					<div>
						<label for="password">Password:</label>
						<input id="password" name="password" required type="password" maxlength="20">
					</div>

					<div>
						<label for="f_name">First Name:</label>
						<input id="f_name" name="f_name" required type="text" maxlength="30">
					</div>

					<div>
						<label for="l_name">Last Name:</label>
						<input id="l_name" name="l_name" required type="text" maxlength="30">
					</div>

					<div>
						<label for="email">Email:</label>
						<input id="email" name="email" required type="email" maxlength="30">
					</div>

					<div>
						<label for="phone">Phone:</label>
						<input id="phone" name="phone" type="text" maxlength="10">
					</div>



					

				</div>	<!-- end first col -->



				<div class="col2"> <!-- second col of form elements -->
				

					<div>
						<label for="addr">Address:</label>
						<input id="addr" name="addr" type="text" maxlength="70">
					</div>

					<div>
						<label for="city">City:</label>
						<input id="city" name="city" type="text" maxlength="50">
					</div>

					<div>
						<label for="state">State:</label>
						<input id="state" name="state" type="text"s maxlength="25">
					</div>

					<div>
						<label for="zip">Zip:</label>
						<input id="zip" name="zip" type="text" maxlength="9">
					</div>



					<div>

						<label for="interest">

							<!-- Cull kingdom names from the database for the user to input as their main interest -->
							Interest:

						</label>

						<?php

							//connect to the database
							if ( ! ($connection = @ mysqli_connect($db_server, $db_user, $db_pwd, $db_name) ) ){

								//warn user of error
								die("<span style=\"color:red;\">Cannot connect to databse. Try refreshing page.</span>");

							}
							
							//create the query to select all kingdom names
							$query = "SELECT kingdom_name FROM kingdoms ORDER BY kingdom_name";

							//run the query
							if ( ! ($result = @ mysqli_query($connection, $query) )){

								die("<span style=\"color:red;\">Problems with databse. Try refreshing page.</span>");
							
							}

							//create a select box for the kingdom name options
							print("<select id =\"interest\" name=\"interest\" > \n");

							//fetch the results of the query, using associative array
							while ($row = @ mysqli_fetch_array( $result, MYSQL_ASSOC )){

								//take each common name listed and make it an option for the select box
								foreach ($row as $cname) {
							
									//value must be the kingdom name from the table for use in new researcher table entry
									print("\t\t\t\t\t\t\t<option value=\"{$cname}\" > ");

									//the kingdom name from the table is also suitable for display to user
									print($cname);

									print(" </option>\n");

								}


							}

							//close the select box when done
							print("\t\t\t\t\t\t</select> \n");


						?>

					</div>
				
					<div class="button">

						<span class="align">&nbsp;</span>
						<input type="submit" id="sub_button" value="Register" />
					
					</div>
				
				</div> <!-- end second col -->

			


			</form>
			



		</section>





	</body>

</html>


