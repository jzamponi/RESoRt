<!DOCTYPE html>
<html>
<head>
	<title>	Glossary - RESoRt </title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="style.css" type="text/css">
	<link rel="stylesheet" href="loader.css" type="text/css">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<meta name="viewport" content="width=device-width, initial-scale=1"> 
	<link rel="shortcut icon" href="img/RESoRt.ico" />
</head>
<body>

<?php include_once("header.php"); ?>
 
<!-- PAGE CONTENT -->
<div class="main-container">
<div class="main-content"> 
<div class="center-page" style="width:1000px;">
<br>
<h4>Here you will find the definition of the concepts involved in this webmodule for both kind of calculations. Each required parameters comes along with its description.</h4>
<br>

<table>
<tbody>
<tr height="35px">
<td width="25%">Frequency</td>
<td width="8%">[MHz]</td>
<td>Observed frequency given in Mega-Hertz.</td>
</tr>
<tr height="35px">
<td>Gas density</td>
<td>[cm<sup>-3</sup>]</td>
<td>Number density of the interstellar gas given in cubic centimeters. This number refers to the neutral gas and not to the cosmic ray particles.</td>
</tr>
<tr height="35px">
<td>Electron tempereature</td>
<td>[K]</td>
<td>Electron temperature of the interstellar medium given in Kelvin.</td>
</tr>
<tr height="35px">
<td>Galactic scale height</td>
<td>[pc]</td>
<td>Scale height of the galactic disk given in parsec. The scale height is also used as a proxy for the ion depth.</td>
</tr>
<tr height="35px">
<td>Ionization degree</td>
<td></td>
<td>The ionization degree of the interstellar medium. This is a dimesnionles factor which varies netween 0 and 1.</td>
</tr>
<tr height="35px">
<td>Filling factor</td>
<td></td>
<td>The filling factor describes the clumpiness of the interstellar medium. This is a dimensionless factor which varies between 0 and 1.</td>
</tr>
<tr height="35px">
<td>Magnetic field strength</td>
<td>[&mu;G]</td>
<td>The magnetic field strength in micro-Gauss.</td>
</tr>
<tr height="35px">
<td>Stellar radiation field</td>
<td>[erg/cm<sup>3</sup>]</td>
<td>The energy density of the interstellar radiation field given in erg per cubic centimeters.</td>
</tr>
<tr height="35px">
<td>Galactic winds</td>
<td>[km/s]</td>
<td>The velocity of the galactic outflows or winds given in kilometers per second.</td>
</tr>
<tr height="35px">
<td>Surface area</td>
<td>[kpc<sup>2</sup>]</td>
<td>The surface area of the galaxy given in square kiloparsec.</td>
</tr>
<tr height="35px">
<td>Redshift</td>
<td></td>
<td>Cosmological redshift of the galaxy. The redshift should be set to zero for local galaxies.</td>
</tr>
<tr height="35px">
<td>Distance</td>
<td>[Mpc]</td>
<td>Distance of the galaxy given in Megaparsec.</td>
</tr>
<tr height="35px">
<td>Slope of cosmic ray spectrum</td>
<td></td>
<td>Slope &chi; of the cosmic ray spectrum given as a dimensionless quantity. This refers to the slope of the electron energy spectrum at the time of injection, e.g., when the cosmic rays leave the site of acceleration. The injection spectrum is Q<sub>e</sub>&propto;&gamma;<sup>-&chi;</sup>, where &gamma; is the relativistic Lorentz factor. The resulting synchrotron flux is S<sub>&nu;</sub>&propto;&nu;<sup>-(&chi;-2)/2</sup>.</td>
</tr>
</tbody>
</table>

<br>
<h4>For Star Formation Rate calculation only:</h4>

<table>
<tbody>
<tr height="85px">
<td width="25%">Lower limit for SFR estimation</td>
<td width="8%">[M<sub>&odot;</sub>yr<sup>-1</sup>]</td>
<td>Lower boundary of the range used to apply the bisection method in the SFR estimation. The lower the limit the longer the algorihm takes. You are highly encouraged to use a narrow range in case you have close idea of the expected SFR for your source.</td>
</tr>
<tr height="95px">
<td>Upper limit for SFR estimation</td>
<td>[M<sub>&odot;</sub>yr<sup>-1</sup>]</td>
<td>Upper boundary of the range used to apply the bisection method in the SFR estimation. The higher the limit the longer the algorihm takes. You are highly encouraged to use a narrow range in case you have close idea of the expected SFR for your source.</td>
</tr>
<tr height="110px">
<td>Accuracy of the SFR estimation (&epsilon;)</td>
<td> &nbsp;&nbsp;&nbsp; % </td>
<td>This is the accuracy required for the bisection algorithm to consider a test SFR as a match. The lower the value the longer the algorithm takes. This &epsilon; accuracy is given as a percentage of the luminosity entered. The bisection approach performs consecutive evaluations of each luminosity from the corresponding SFR, and consider a match when the difference between the given luminosity (from the flux entered) and the currently evaluated luminosity is less or equal than &epsilon;.</td>
</tr>
</tbody> 
</table>


</div>
</div>
</div>


<?php	include_once("footer.php");	?>

</body>
</html>
