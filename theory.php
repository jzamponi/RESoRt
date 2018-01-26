<!DOCTYPE html>
<html>
<head>
	<title>	Theory - RESoRt </title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="style.css" type="text/css">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<meta name="viewport" content="width=device-width, initial-scale=1"> 
	<link rel="shortcut icon" href="img/RESoRt.ico" />
</head>
<body>

<?php include_once("header.php"); ?>
 
<!-- CONTENT -->
<div class="main-container">
<div class="main-content">
<div class="center-results" style="width: 1000px;">
<h4> <b>Detailed model description</b> </h4>
<p style="text-align:justify;"> A detailed description of the underlying model of RESoRt is presented in
		<ul style="list-style-type: none;">
		<li><em><a href="http://adsabs.harvard.edu/abs/2017MNRAS.468..946S" target="_blank">Schober, Schleicher &amp; Klessen 2017 (MRAS, 458, 1).</a></em></li>
		</ul>
	 Any publication resulting from the usage RESoRt should refer to this paper and acknowledge the web calculator.
</p>

<br>

<h4> <b>Background and basic idea</b> </h4>
<p style="text-align:justify;"> Typical local star-forming galaxies lie on the far-infrared (FIR) - radio correlaton, a linear relation between the 1.4 GHz and the 60 &mu;m luminosity (<em>Yun et al. 2001</em>). This can be leveraged to derive a relation betweeen the radio flux and the SFR (<em>Murphy et al. 2011</em>) by using well established far infrared-SFR relations (e.g., <em>Leitherer et al. 1999</em>). It is, however, unclear if such correlations hold in all types of galaxies and at different cosmic times. Therefore, in <em> Schober, Schleicher &amp; Klessen 2017</em> radio-SFR relations are derived from a semi-analytical galaxy model.</p>
<p style="text-align:justify;">
The model is based on the connection between star formation and galactic radio emission which is presented schematically in the figure below. The SFR is connected with the rate of supernovae, the final explossion of massive stars. Supernovae produce cosmic rays, highly energetic charged particles, which lose energy on one side via free-freee emission when interaction with the ionized gas, and on the other side via synchrotron radiation in the magnetized interstellar medium. Additionally, supernova shocks drive turbulence which affects the interstellar magnetic field (see magneto-hydrodynamical turbulence aand dynamo theory). Especially at low frequencies and high gas densities, the radio flux can be suppressed as a result of free-free absoprtion.
<figure style="position:relative">
<a href="img/flowchart.jpg"><img style="width:40%;height:auto;display:block;position:relative;margin:auto;top:0;bottom:0;right:0;left:0;" src="img/flowchart.jpg"></a>
<figcaption>
<!-- fill with figure caption -->
</figcaption>
</figure>
</p>
<p style="text-align:justify;">
The total galactic radio depends on a number of freee parameters, including the gas density, the interstellar raditation field, the ionization degree, the galactic magnetic field, galactic winds, and of course the star formation rate (see <a href="glossary.php">glossary</a> section). RESoRt allows to calulate the radio flux for a set of given galactic parameters, taking into account also free-free absorption at low frequencies.
</p>

<br>

<h4> <b>Related literature</b> </h4>
<p style="text-align:justify;"> <em><a href="http://adsabs.harvard.edu/abs/2017MNRAS.468..946S" target="_blank">Schober, Schleicher &amp; Klessen 2017 (MRAS, 458, 1).</a></em>
	<ul style="list-style-type: none;">
	<li>Description of the galaxy model which pedicts the galactic radio emission for a given SFR. Derivation of the critical frequency above which radio emission can be used a SFR tracer. Relation between observer radio fluxes and the SFR for fiducial galaxy models.</li>
	</ul>

<p style="text-align:justify;"> <em><a href="http://adsabs.harvard.edu/abs/2016ApJ...827..109S" target="_blank">Schober, Schleicher &amp; Klessen 2016 (ApJ, 827, 2).</a></em>
	<ul style="list-style-type: none;">
	<li>A theoretical explanation for the FIR-radio correlation. Prediction for the evolution of the FIR-radio correlation with redshift under various conditions.</li>
	</ul>
<p style="text-align:justify;"> <em><a href="http://adsabs.harvard.edu/abs/2015MNRAS.446....2S" target="_blank">Schober, Schleicher &amp; Klessen 2015 (MRAS, 446, 1).</a></em>
	<ul style="list-style-type: none;">
	<li>Cosmic rays emit photons mostly at radio wavelengths when synchrotron emission is dominant. In presence of strong radiaton fields, however, inverse compton scattering becomes the main cosmic ray energy loss channel, and X-ray photons are produced. Using a semi-analytical galaxy model the importance of X-ray losses is estimated for different galaxy types and redshifts..</li>
	</ul>
<p style="text-align:justify;"> <em><a href="http://adsabs.harvard.edu/abs/2016A%26A...593A..77S" target="_blank">Schleicher &amp; Beck 2016 (A &amp; A, 593).</a></em>
	<ul style="list-style-type: none;">
	<li>Modelling the connection between cosmic rays, magnetic fields and star formation in dwarf galaxies. Conditions for the maintenance of a FIR-radio correlation in dwarf galaxies are derived.</li>
	</ul>
	
</p>

</div>
</div>
</div>

<?php	include_once("footer.php");	?>

</body>
</html>
