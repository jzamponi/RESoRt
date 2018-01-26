<!DOCTYPE html>
<html>
<head>
	<title>	Contact - RESoRt </title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="style.css" type="text/css">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<meta name="viewport" content="width=device-width, initial-scale=1"> 
	<link rel="shortcut icon" href="img/RESoRt.ico" />
        <style>
            .card {
                    border-radius: 8px;
                    display: inline-block;
                    width: 40%;
                    /* Add shadows to create the "card" effect */
                    box-shadow: 0 0px 8px 0 rgba(0,0,0,0.3);
                    transition: 0.3s;
            }
            /* On mouse-over, add a deeper shadow */
            .card:hover {
                    box-shadow: 0 8px 16px 0 rgba(0,0,0,0.6);
            }
            /* Add some padding inside the card container */
            .container {
                    padding: 2px 16px;
            }
	
        </style>

</head>
<body>

<!-- HEADER -->
<?php include_once("header.php"); ?>
	
 
<!-- PAGE CONTENT -->
<div class="main-container">
<div class="main-content"> 
	<div style="width:900px; margin:0 auto;">

		<!-- CONCEPCION CONTACT -->
		<div class="card">
			<div style="margin-left: 13%;">
			<br>
			<a href="http://www.astro.udec.cl" target="_blank" title="Astronomy Department at Universidad de Concepci&oacute;n">
				<img src="img/astroudec.jpg" alt="Astroudec logo unavailable" style="margin-left: 10%"width="200px" heigth="90px">
			</a>
			<br><br>
		    <div class="container">
			<h4><b>Joaquin Zamponi F.</b></h4>
			<p><a href="mailto:jzamponi@udec.cl?Subject=Webmodule RESoRt"><i class="fa fa-envelope"></i> jzamponi@udec.cl</a></p>
			<br>
			<h4><b>Dominik Schleicher</b></h4>
			<p><a href="mailto:dschleicher@astro-udec.cl?Subject=Webmodule RESoRt"><i class="fa fa-envelope"></i> dschleicher@astro-udec.cl</a></p>
			</div>
		</div>
	</div>

		<span style="display:inline-block;width:15%;"></span>

		<!--HAMBURG CONTACT -->
		<div class="card" >
			<div style="margin-left: 13%;">
			<br>&nbsp;&nbsp;&nbsp;&nbsp;
			<a href="http://www.nordita.org" target="_blank" title="Nordic Institute for Theoretical Physics">
				<img src="img/nordita_logo.png" alt="Nordita logo unavailable" width="200px" height="80px">
			</a>
			<br><br>
		    <div class="container">
			<h4><b>Jennifer Schober</b></h4>
			<p><a href="mailto:schober.jen@gmail.com?Subject=Webmodule RESoRt"><i class="fa fa-envelope"></i> schober.jen@gmail.com</a></p>
			<br>&nbsp;&nbsp;&nbsp;&nbsp;
			<br>&nbsp;&nbsp;&nbsp;&nbsp;
			<br>&nbsp;&nbsp;

			</div>
			</div>
		</div>

</div>
</div>
</div>

<?php	include_once("footer.php");	?>

</body>
</html>
