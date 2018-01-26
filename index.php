<?php
	if (!empty($_SERVER["REMOTE_ADDR"]) && $_SERVER["REMOTE_ADDR"]!="127.0.0.1" && $_SERVER["REMOTE_ADDR"]!="190.163.215.244"){	//avoid log if acces from localhost
	
		$filename="visitors.txt";
		$file = fopen($filename, "a+");

		date_default_timezone_set('America/Santiago');
		fwrite($file, date("F j, Y, g:i a")." [UTC-3]\n");

		fwrite($file, "VIEWER PROPERTIES\n");
		if (!empty($_SERVER["REMOTE_ADDR"])) fwrite($file, "IP:\t".$_SERVER["REMOTE_ADDR"]. "\n");
		if (!empty($_SERVER["HTTP_CLIENT_IP"]) && $_SERVER["HTTP_CLIENT_IP"] != $_SERVER["REMOTE_ADDR"]) {
			 fwrite($file, "IP2:\t".$_SERVER["HTTP_CLIENT_IP"]. "\n");}
		if (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) fwrite($file, "IP Behind Proxy:\t".$_SERVER["HTTP_X_FORWARDED_FOR"]. "\n");
		if (!empty($_SERVER["REMOTE_PORT"])) fwrite($file, "Port:\t".$_SERVER["REMOTE_PORT"]. "\n");
		if (!empty($_SERVER["HTTP_USER_AGENT"])) fwrite($file, "UAgent:\t".$_SERVER["HTTP_USER_AGENT"]. "\n");
		if (!empty($_SERVER["REMOTE_HOST"])) fwrite($file, "Remote Host:\t".$_SERVER["REMOTE_HOST"]. "\n");
		if (!empty($_SERVER["REMOTE_USER"])) fwrite($file, "Remote User:\t".$_SERVER["REMOTE_USER"]. "\n");
		if (!empty($_SERVER["REDIRECT_REMOTE_USER"])) fwrite($file, "(Redirect)Remote User:\t".$_SERVER["REDIRECT_REMOTE_USER"]. "\n");


		$query = @unserialize (file_get_contents('http://ip-api.com/php/'.$_SERVER["REMOTE_ADDR"]));
		if ($query && $query['status'] == 'success') {
		    fwrite($file,"Count:\t".$query['country']."\nCity:\t".$query['city']."\n");
		}

		fwrite($file, "-------------------------------------------------------\n");
		fclose($file);
	}
?>


<!-- BEGIN WEBPAGE CONTENT -->
<!DOCTYPE html>	
<html>
<head>
	<title>	Radio Emission Star fOrmation Rate </title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="style.css" type="text/css">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pretty-checkbox@3.0/dist/pretty-checkbox.min.css">
	<meta name="viewport" content="width=device-width, initial-scale=1"> 
	<link rel="shortcut icon" href="img/RESoRt.ico" />
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<!--For nice JS sweetAlert messages -->
	<script src="sweetalert.min.js"></script>
	<link rel="stylesheet" type="text/css" href="sweetalert.css">

</head>
<body>

<!-- HEADER -->
<?php include_once('header.php');?>

<div class="main-container">
<div class="main-content">

<!-- WELCOME MESSAGE -->
<br>
<h3 class="Welcome">Welcome to RES<span style="text-transform:lowercase">o</span>R<span style="text-transform:lowercase">t</span></h3>
<h4>
<br>
<p>
The online calculator <b>RES</b>o<b>R</b>t (<b>R</b>adio <b>E</b>mission <b>S</b>tar f<b>o</b>rmation <b>R</b>ate) is a tool for determining the star formation rate (<span title="Star Formation Rate">SFR</span>) from the non-thermal galactic radio flux or vice versa.
</p>
<p>
In short, the physical background for a connection between the <span title="Star Formation Rate">SFR</span> and the radio flux is the following:<br>
Non-thermal long-wavelength photons are emitted by highly energetic charged particles that travel in spiral motion around the interstellar magnetic field lines. These cosmic rays are connected to star formation as they are produced in supernova shock fronts.
The link between synchrotron emission and the <span title="Star Formation Rate">SFR</span> is, however, not direct because the cosmic ray spectrum is affected by various processes, such as inverse Compton scattering, bremsstrahlung, ionization of the interstellar medium, interaction with the interstellar radiation field, and galactic outflows. Especially in galaxies with high gas densities, cosmic rays lose a large fraction of their energy via ionization. As a result, the radio flux does not contain any information about the SFR below a critical radio frequency.
</p>
<p>
<b>RES</b>o<b>R</b>t is based on the semi-analytical model of star-forming galaxies presented in <a href="http://adsabs.harvard.edu/abs/2017MNRAS.468..946S" target="_blank" title="Tracing star formation with non-thermal radio emission">Schober et al. 2017</a> which takes into account all the physical processes discussed above. Any publications resulting from the usage of the online calculator should include a citation to <a href="http://adsabs.harvard.edu/abs/2017MNRAS.468..946S" target="_blank" title="Tracing star formation with non-thermal radio emission">Schober et al. 2017</a>.
</p>
</h4>


<!-- BEGGINING OF FORM -->
<div class="form-content">
<form id="SrcForm" name="SrcForm" method="POST" action="calc.php" required="true">

<!-- Calculator Mode Selector -->
<h3> <label>Select your calculator mode </label></h3>
	<input type="radio" onclick="ShowTr('Flux')" id="CalcModeFlux" name="CalcMode" value="Flux" form="SrcForm"/>
        <label> Compute radio flux</label>
	<input type="radio" onclick="ShowTr('SFR');" id="CalcModeSFR" name="CalcMode" value="SFR" form="SrcForm"/>
        <label> Compute star formation rate</label>

<br/>

<div style="display:none;" id="first_curtain"> <!-- Show next div only if calculator mode selected -->

<h3><label>Galaxy parameters</label></h3>
<!-- Source Selector -->
<h4><label>Select your source</label></h4>
<select form="SrcForm" id="source" onchange="SelectSource()" name="source" style="width:80%">
	<option value="" class="label"></option> 
	<option id="NS_option" value="New">New source</option>
	<optgroup label="Normal Star-forming Galaxy">
		<option id="MW_option" value="MW">Milky Way</option>
	</optgroup>		
	<optgroup label="Starburst Galaxy">
		<option id="M82_option" value="M82">M82</option>
		<option id="M51_option" value="M51">M51</option>
		<option id="Arp220_option" value="Arp220">Arp 220</option>
	</optgroup>
</select>

<br><br>

<div style="display:none;" id="second_curtain"> <!-- Show next div only if source selected -->
<h4><label>Individual parameters</label></h4>
<table id="MainTable" class="datatable">
<tbody>
<tr style="display:none;" id="SFR_tr">
<td> Star formation rate </td> <td> <input type="text" name="SFR0" id="SFR0" placeholder="SFR"/> [M<sub>&odot;</sub>/yr]</td>
<tr/>

<tr style="display:none;" id="flux_tr">
<td> Radio flux </td> <td> <input type="text" name="flux" id="flux" placeholder="flux" /> [Jy]</td>
<tr/>

<tr>
<td> Gas density </td> <td> <input type="text" name="n0" id="n0" placeholder="n" required="true" /> [cm<sup>-3</sup>]</td>
<tr/><tr>
<td> Electron temperature </td><td><input type="text" name="Te0" id="Te0"  placeholder="Te" required="true"/> [K]	</td>	
<tr/><tr>
<td> Galactic scale height and ion depth </td><td><input type="text" name="Lion0" id="Lion0"  placeholder="Lion" required="true"/> [pc]</td>
<tr/><tr>
<td> Ionization degree </td> <td> <input type="text" name="fion0" id="fion0"  placeholder="fion" required="true"/> </td>
<tr/><tr>
<td> Filling factor </td> <td> <input type="text" name="ffill0" id="ffill0" placeholder="ffill" required="true"/> </td>
<tr/><tr>
<td> Magnetic field strength </td> <td> <input type="text" name="B0" id="B0" placeholder="B" required="true"/> [&mu;G]</td>	
<tr/><tr>
<td> Stellar radiation field </td> <td> <input type="text" name="uint0" id="uint0" placeholder="uint" required="true"/> [erg/cm<sup>3</sup>]</td>
<tr/><tr>
<td> Velocity of galactic winds </td> <td> <input type="text" name="vwind0" id="vwind0" placeholder="vwind" required="true"/> [km/s]</td>
<tr/><tr>
<td> Surface area </td><td><input type="text" name="Sarea0" id="Sarea0" placeholder="Area" required="true"/> [kpc<sup>2</sup>]</td>
<tr/><tr>
<td> Redshift </td> <td> <input type="text" name="z0" id="z0" placeholder="z" required="true"/>				</td>	
<tr/><tr>
<td> Distance </td> <td> <input type="text" name="d0" id="d0" placeholder="D" required="true"/> [Mpc]			</td>
<tr/><tr>
<td> Slope of cosmic ray spectrum (&chi;) </td> <td> <input type="text" name="Chi" id="Chi" value="2.2"  placeholder="&chi;" required="true"/> </td>
<tr/>
<tr style=display:none;" id="SFRparams_tr" >
<td colspan="2">
	<input style="margin-left: 20%" type="checkbox" onchange="$('#extratable').fadeToggle(300,'swing');" id="sfropt" name="sfroptions">
	<span title="Set parameters for SFR estimation such as lower and upper limit for finding expected SFR (it will take longer if very broad), and also the percentage of accuracy of the estimation">
	Further parameters for SFR estimation
	</span>
</td>
</tr>
</tbody>
</table>

<table style="display:none;" class="datatable" id="extratable">
<tbody>
<tr>
<td> Lower limit for SFR estimation </td> <td> <input class="extrainputs" type="text"name="sfrmin" id="sfrmin" placeholder="SFRmin" value="1e-3" required="true"/>  [M<sub>&odot;</sub>/yr]</td>
</tr>
<tr>
<td> Upper limit for SFR estimation </td> <td> <input class="extrainputs" type="text" name="sfrmax" id="sfrmax" placeholder="SFRmax" value="1e5" required="true"/>  [M<sub>&odot;</sub>/yr]</td>
</tr>
<tr>
<td> Accuracy of the SFR estimation (&epsilon;) </td> <td> <input class="extrainputs" type="text" id="epsilon" name="epsilon" placeholder="&epsilon;" value="1" required="true"/> %</td>
</tr>
</tbody>
</table>

<h3><label>Computation</label></h3>
<h4><label>Compute critical frequency</label></h4>
<button type="button" style="background-color: #1d9e74;" onclick="ShowNuCritical();"><span>Compute critical frequency </span></button>

<br><br>
<h4><label>Compute <span id="ComputeButton"></span></label></h4>

<span>Frequency</span>
<input style="margin-left:25%;width:27%;" class="extrainputs" type="text" name="freq" id="freq" required="true"/> [MHz]
<br/>
<input style="margin-left:20%;" type="checkbox" name="additional_freqs" value="additional_freqs">
<span title="It will take longer">Compute also for other frequencies </span>

<button type="submit" name="submit">Go!</button>

<span style="display:inline-block; height:40px"></span>							

</div> <!-- close first curtain -->
</div> <!-- close second curtain -->

</div> <!-- close form-container -->
</div> <!-- close main-content -->
</div> <!-- close main-container -->

<?php	include_once("footer.php");	?>


















<!-- ALL JAVASCRIPT CODE GOES TO THE BOTTOM TO INCREASE DISPLAY SPEED AS JS COMPILATION SLOWN DOWN THE HTML LOAD -->

<!-- JS SCRIPT TO HANDLE INPUT -->
<script type="text/javascript">
	//for calculator mode selector
	function ShowTr(calcmode,){
		//document.getElementById('first_curtain').style.visibility="visible";
		$("#first_curtain").show(300);
		if (calcmode == "Flux"){
			//Hide sfr elements and add class datatable to flux elements to appear
			document.getElementById("ComputeButton").innerHTML="radio luminosity";
			$("#flux_tr").hide();
			$("#SFR_tr").show();
			$("#SFRparams_tr").hide(500);
		}
		else if (calcmode == "SFR"){
			//Hide flux elements and add class datatable to sfr elements to appear
			document.getElementById("MW_option").disabled=true;
			document.getElementById("MW_option").title="Unable to compute SFR for Milky way as it is based on its radio flux.";
			document.getElementById("ComputeButton").innerHTML="star formation rate";
			$("#flux_tr").show();
			$("#SFR_tr").hide();
			$("#SFRparams_tr").show(500);
		}
	}

	function SelectSource(){
		$("#second_curtain").show(300);

		var s = document.getElementById('source');
		var opt = s.options[s.selectedIndex].value;

		//Milky Way parameters
		var MW = {
			n		:2,						//[cm^-3] Galactic Denisty
			Te		:10000,					//[K] electron temperature
			Lion	:500,					//[pc] galactic scale height and ion depth. (RixBovy2013)
			fion	:0.1,					//ionization degree
			ffill	:0.2,					//filling factor
			B		:10,					//[µG] magnetic field strenght
			uint	:1e-12,					//[erg/cm^3] internal component of the interstellar radiation field
			vwind	:50,					//[km/s] velocity of galactic winds	
			Sarea	:1.05026323,			//[kpc^2] Surface area
			SFR		:2,						//[M_0/yr] Star Formation Rate
			z		:0,						//Redshift
			d		:0						//[Mpc] Distance
		};
		//M82 parameters
		var M82 = {
			n		:300,					//[cm^-3] Galactic Denisty
			Te		:5000,					//[K] electron temperature
			Lion	:200,					//[pc] galactic scale height and ion depth. (RixBovy2013)
			fion	:0.1,					//ionization degree
			ffill	:0.2,					//filling factor
			B		:50,					//[µG] magnetic field strenght
			uint	:1e-9,					//[erg/cm^3] internal component of the interstellar radiation field
			vwind	:230,					//[km/s] velocity of galactic winds	
			Sarea	:0.105026323,			//[kpc^2] Surface area
			SFR		:10,					//[M_0/yr] Star Formation Rate
			z		:0.000677,				//Redshift
			d		:1.0710					//[Mpc] Distance
		};
		//M51 parameters
		var M51 = {
			n		:5,						//[cm^-3] Galactic Denisty
			Te		:10000,					//[K] electron temperature
			Lion	:150,					//[pc] galactic scale height and ion depth. (RixBovy2013)
			fion	:0.1,					//ionization degree
			ffill	:0.2,					//filling factor
			B		:20,					//[µG] magnetic field strenght
			uint	:1e-12,					//[erg/cm^3] internal component of the interstellar radiation field
			vwind	:50,					//[km/s] velocity of galactic winds	
			Sarea	:1.05026323,			//[kpc^2] Surface area
			SFR		:2,						//[M_0/yr] Star Formation Rate
			z		:0.002,					//Redshift
			d		:7.974					//[Mpc] Distance
		};
		//Arp220 parameters
		var Arp220 = {
			n		:10000,					//[cm^-3] Galactic Denisty
			Te		:7500,					//[K] electron temperature
			Lion	:500,					//[pc] galactic scale height and ion depth. (RixBovy2013)
			fion	:0.1,					//ionization degree
			ffill	:0.2,					//filling factor
			B		:50,					//[µG] magnetic field strenght
			uint	:1e-9,					//[erg/cm^3] internal component of the interstellar radiation field
			vwind	:230,					//[km/s] velocity of galactic winds	
			Sarea	:0.105026323,			//[kpc^2] Surface area
			SFR		:200,					//[M0/yr] Star Formation Rate
			z		:0.018126,				//Redshift
			d		:75.5					//[Mpc] Distance
		};
		//New Source parameters
		var NewSource = {
			n		:"",					//[cm^-3] Galactic Denisty
			Te		:"",					//[K] electron temperature
			Lion	:"",					//[pc] galactic scale height and ion depth. (RixBovy2013)
			fion	:"",					//ionization degree
			ffill	:"",					//filling factor
			B		:"",					//[µG] magnetic field strenght
			uint	:"",					//[erg/cm^3] internal component of the interstellar radiation field
			vwind	:"",					//[km/s] velocity of galactic winds	
			Sarea	:"",					//[kpc^2] Surface area
			SFR		:"",					//[M0/yr] Star Formation Rate
			z		:"",					//Redshift
			d		:""						//[Mpc] Distance
		};
		

		if (opt == "MW"){
			var gal = MW;
		}else if (opt == "M82"){
			var gal = M82;
		}else if (opt == "M51"){
			var gal = M51;
		}else if (opt == "Arp220"){
			var gal = Arp220;
		}else{
			var gal = NewSource;
		}
		//Apply source values to html inputs
		document.getElementById('n0').value=gal.n;				
		document.getElementById('Te0').value=gal.Te;				
		document.getElementById('Lion0').value=gal.Lion;			
		document.getElementById('fion0').value=gal.fion;			
		document.getElementById('ffill0').value=gal.ffill;		
		document.getElementById('B0').value=gal.B;				
		document.getElementById('uint0').value=gal.uint;			
		document.getElementById('vwind0').value=gal.vwind;			
		document.getElementById('Sarea0').value=gal.Sarea;		
		document.getElementById('SFR0').value=gal.SFR;			
		document.getElementById('z0').value=gal.z;									
		document.getElementById('d0').value=gal.d;				

	}

//	INPUT JS FORM TO COMPUTE CRITICAL FREQUENCY 
	function nucritical(){
		//calculate critical frequency
		var ffill = parseFloat(document.getElementById('ffill0').value);
		var fion = parseFloat(document.getElementById("fion0").value);
		var Lion = parseFloat(document.getElementById("Lion0").value);
		var n = parseFloat(document.getElementById("n0").value);
		var Te = parseFloat(document.getElementById("Te0").value);
		var nu = parseFloat(document.getElementById("freq").value);
		var pc = 3.086*(1e18); //[cm] parsec
		var EM = ((fion*n)**2)*((Lion*pc)/ffill);
		var result;

		result=((1e9)*Math.pow(3.28e-7*Math.pow(Te/(1e4),-1.35)*(EM/pc),1/2.1))/(1e6); //[MHz]
		return result;
	}
	function ShowNuCritical(){	
		if ( isNaN(nucritical()) ){
			swal({
				title: "You did not entered the required values",
				type: "error",
				animation: "slide-from-top",
				closeOnConfirm: false,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "Done"
			});
		}else{ 
			var nu = nucritical().toString().concat(" [MHz]");
			swal({
				title: "Critial Frequency",
				text: nu,
				imageUrl: "img/RESoRt.png",
				imageSize: "300x135",
				animation: "slide-from-top",
				closeOnConfirm: false,
				confirmButtonColor: "#DD6B55",
				confirmButtonText: "Done"
			});
		}
	}
	</script>

<!-----------------------  JAVASCRIPT VERSION OF THE CALCULATOR ----------------------->

<!--
	<script src="bessel.js"></script>
	<script type="text/javascript">
		/*	CALC.JS	*/
		function FinalOut(){
		/* BEGIN MATH DEFINITION */
			//ALL CONSTANTS IN CGS SYSTEM
			//PHYSICAL CONSTANTS
			var M=1e6;
			var mu=1e-6;							
			var k=1.380658e-16;					//[erg/K] Boltzmann constant
			var h=6.6260755e-27;				//[erg s] Plank constant
			var c=2.998e10;						//[cm/s] Speed of light
			var me=9.1093897e-28;				//[g] electron mass
			var mp=1.675e-24;					//[g] proton mass
			var b=5.878933e10; 					//[Hz/K] Wiens displacement law constant
			var e=4.8032068e-10;				//[esu] electric charge
			var r0=(e*e)/(me*(c*c));			//classical electron radius
			var Lsun=3.839e33;					//[erg/s] Sun Luminosity
			var pc=3.086e18;					//[cm] parsec
			var H0=(67.11e5)/(M*pc);			//[1/s] Hubble constant
			var sigmaM=0.3175;					//Matter parameter, Plank best fit
			var eV=1.60217657e-12;	 			//[erg] electron volt
			var Msun=1.989e33;					//[g] Sun mass
			var yr=365.25*24*60*60;				//[s] year
			var G=6.67259e-8;					//[cm^3 g^-1 s^-2] Gravitational constant
			var sigmaT=(8/3)*Math.PI*(r0*r0);	//[cm^2] Thomson cross section
			var Jy=1e-23; 						//[erg s^-1 cm^-2 Hz^-1] Jansky
				//COSMIC RAYS PARAMETERS
			var Fcal=0.4;						//Fraction of CR protons that goes into pion production
			var Fsec=0.7;						//Fraction of secondary CR electrons from all CR electrons
			var Eps= 0.1;						//Normalization of CR spectrum
			var Esn=1e51;						//[erg] energy released in one SN
			var gammae0=(1e7)*(eV/(me*(c*c)));	//Begin of electron CR spectrum
			var gammap0=(1e9)*(eV/(mp*(c*c)));	//Begin of proton CR spectrum
			var Chi = parseFloat(document.getElementById('Chi').value);			

			/* GALAXY MODEL */
			function NSNrate(sfr){
				return 0.156*(sfr/(12.26*Msun));
			}
			function  uISRF(uint,z){
				return uint+(((8*(k*k*k*k)*Math.pow(Math.PI,5))/(15*(c*c*c)*(h*h*h)))*(2.73*2.73*2.73*2.73)*((1+z)*(1+z)*(1+z)*(1+z)));
			}
			//Energy loses timescales
			function  tsynch(B,gamma){
				return  (3*me*c)/(4*sigmaT*((B*B)/(8*Math.PI))*gamma);
			}
			function  tIC(gamma,uint,z){
				return  (3*me*c)/(4*sigmaT*uISRF(uint,z)*gamma);
			}
			function  tion(gamma,n){
				return gamma/(2.7*c*sigmaT*(6.85+0.5*Math.log(gamma))*n);
			}
			function  tbrems(n){
				return 104e3*yr*(1/(n/300));
			}
			function  twind(Lion,vwind){
				return (Lion/vwind);
			}
			function  t(B,gamma,Lion,n,uint,vwind,z){
				return 1/( (1/tsynch(B,gamma))+(1/tIC(gamma,uint,z))+(1/tion(gamma,n))+(1/tbrems(n))+(1/twind(Lion,vwind)) );
			}

			//Injection spectrum of cosmic ray protons
			function  k0(sfr){
				return (Eps*Esn*NSNrate(sfr)*(Chi-2))/(Math.pow(gammap0,2-Chi)*(mp*(c*c)*mp*(c*c)));
			}
			function  Qp(gammap,sfr){
				return k0(sfr)*Math.pow(gammap,(-1)*Chi);
			}

			//Spectra in steady state
			function  Ne(B,gamma,Lion,n,sfr,uint,vwind,z){
				return ((20*20*Fcal)/(6*(Chi-1)*Fsec))*k0(sfr)*Math.pow((20*gamma*me)/mp,(-1)*Chi)*t(B,gamma,Lion,n,uint,vwind,z)*me*(c*c);
			}
			function  tpion(n){
				return (50e6*yr)/n;
			}
			function  Np(gamma,n,sfr){
				return Fcal*tpion(n)*k0(sfr)*Math.pow(gamma,(-1)*Chi)*mp*(c*c);
			}


			/* RADIO EMISSION */
			//synchrotron spectrum (for power-law distributed CR electrons)
			function  nuc(B,gamma){
				return (3*e*B*(gamma*gamma))/(4*Math.PI*me*c);
			}
			function  Pitch(){
				return (2*Math.pow(Math.PI,3/2)*math.gamma((5+Chi)/4))/(math.gamma((7+Chi)/4));	
			}
		
			var N=6e18;			//outer loop maxit	| ideal value, always even
			var xend=1.075e2;	//inner loop maxit	| ideal value
			var nsteps=1e3;

			function integrand1(x){
				var n=5/3;
				//console.log(x);
				return bessik(x,n);
			}
	
			//Simpson's rule for numerical integral
			function simpson1(x0,xf){
				var i;
				var hh, S, xi;
				if (nsteps % 2 == 0 ){
					hh=(xf-x0)/nsteps;
					S=integrand1(x0) + integrand1(xf);
					i=1;	
					while (i <= (nsteps-1)){
						xi=x0+hh*i;
						if(i%2 == 0){
							S=S+2*integrand1(xi);
						}else{
							S=S+4*integrand1(xi);
						}
						i++;
					}
				return hh/3*S;
				}	
			}
			function integrand2(gamma,B,Lion,n,sfr,uint,vwind,z,nu){
				var Lnu,cst;
				cst=(Math.sqrt(3)*(e*e*e)*B)/(me*(c*c));
				Lnu=simpson1(nu/nuc(B,gamma),xend);
				return cst*(nu/nuc(B,gamma))*Lnu*Ne(B,gamma,Lion,n,sfr,uint,vwind,z);
			}
			function simpson2(x0,xf,B,Lion,n,sfr,uint,vwind,z,nu){
				var i;
				var hh, S, xi;
				if (nsteps % 2 == 0 ){
					hh=(xf-x0)/nsteps;
					S=integrand2(x0,B,Lion,n,sfr,uint,vwind,z,nu) + integrand2(xf,B,Lion,n,sfr,uint,vwind,z,nu);
					i=1;	
					while (i <= (nsteps-1)){
						xi=x0+hh*i;
						if(i%2 == 0){
							S=S+2*integrand2(xi,B,Lion,n,sfr,uint,vwind,z,nu);
						}else{
							S=S+4*integrand2(xi,B,Lion,n,sfr,uint,vwind,z,nu);
						}
						i++;
					}
				return hh/3*S;
				}	
			}

			function Lsynchnu(B,Lion,n,sfr,uint,vwind,z,nu) {
				var Lsynchnu;
				Lsynchnu = simpson2(gammae0,N,B,Lion,n,sfr,uint,vwind,z,nu);	
				Lsynchnu*=Pitch();	
				return Lsynchnu;
			}


			//Free-free contribution
			function em(ffill,fion,Lion,n){
				return (fion*n*fion*n)*(Lion/ffill);
			}
			function tff(ffill,fion,Lion,n,Te,nu){
				var opdepth, upperlim;
				opdepth = (3.28e-7)*Math.pow(Te*(1e-4),-1.35)*Math.pow(nu*(1e-9),-2.1)*(em(ffill,fion,Lion,n)/pc);
				upperlim = 50;
				return (opdepth > upperlim ? upperlim : opdepth);
			}
			function nucritical(ffill,fion,Lion,n,Te){
				return (1e9)*Math.pow((3.28e-7)*Math.pow(Te/(1e4),-1.35)*(em(ffill,fion,Lion,n)/pc),1/2.1);
			}
			function Lffnu(Sarea,ffill,fion,Lion,n,Te,nu){
				return 2*k*Te*(1-Math.exp(-tff(ffill,fion,Lion,n,Te,nu)))*(Sarea/(c*c))*(nu*nu);
			}
			function Lsynchffnu(B,ffill,fion,Lion,sfr,n,Te,uint,vwind,z,nu){
				return Lsynchnu(B,Lion,n,sfr,uint,vwind,z,nu)*Math.exp(-tff(ffill,fion,Lion,n,Te,nu));
			}


			/* COMPUTING LUMINOSITIES */
			function Lnuobs(B,Sarea,ffill,fion,Lion,sfr,n,Te,uint,vwind,z,nu){
				return nu*( Lsynchffnu(B,ffill,fion,Lion,sfr,n,Te,uint,vwind,z,(1+z)*nu) + Lffnu(Sarea,ffill,fion,Lion,n,Te,(1+z)*nu) );
			}


			/* COMPUTING STAR FORMATION RATES */
			function ComputeSFR(B,Sarea,ffill,fion,Lion,n,Te,uint,vwind,z,d,nu,flux,SFRmin,SFRmax,epsilon){
				var SFRlow,SFRhigh,SFRmid,SFRin,Lin,Lmin,Lmax,Lmid;
	
				if (flux==0){
					return 0;
				}

				Lin=(nu*flux)*(4*Math.PI*(d*d));

				Lmin = Lnuobs(B,Sarea,ffill,fion,Lion,SFRmin,n,Te,uint,vwind,z,nu);
				Lmax = Lnuobs(B,Sarea,ffill,fion,Lion,SFRmax,n,Te,uint,vwind,z,nu);

				epsilon*=Lin;	//% of Lin (accuracy)
				SFRlow=SFRmin;			
				SFRhigh=SFRmax;
				while(true){
					SFRmid=Math.exp(Math.log(SFRlow)+(Math.log(SFRhigh)-Math.log(SFRlow))/2);
					Lmid=Lnuobs(B,Sarea,ffill,fion,Lion,SFRmid,n,Te,uint,vwind,z,nu);

					if ( Math.abs(Lmid-Lin) <= epsilon ){
						return SFRmid/(Msun/yr);
					}else{
						if (Lmid < Lin){
							SFRlow=SFRmid;
							SFRhigh=SFRhigh;
						}else if(Lmid > Lin){
							SFRlow=SFRlow;
							SFRhigh=SFRmid;
						}
					}
				}

				return SFRmid/(Msun/yr);
			}
		/* END MATH DEFINITION*/

			var n = parseFloat(document.getElementById('n0').value);				
			var Te = parseFloat(document.getElementById('Te0').value);				
			var Lion = parseFloat(document.getElementById('Lion0').value);			
			var fion = parseFloat(document.getElementById('fion0').value);			
			var ffill = parseFloat(document.getElementById('ffill0').value);		
			var B = parseFloat(document.getElementById('B0').value);				
			var uint = parseFloat(document.getElementById('uint0').value);			
			var vwind = parseFloat(document.getElementById('vwind0').value);			
			var Sarea = parseFloat(document.getElementById('Sarea0').value);		
			var z = parseFloat(document.getElementById('z0').value);									
			var d = parseFloat(document.getElementById('d0').value);				
			var sfrmin = parseFloat(document.getElementById('sfrmin').value);
			var sfrmax = parseFloat(document.getElementById('sfrmax').value);		
			var epsilon = parseFloat(document.getElementById('epsilon').value);		
			var sfr = parseFloat(document.getElementById('SFR0').value);			
			var nu = parseFloat(document.getElementById('freq').value);	
	
			//conversions to cgs
			Lion*=pc;
			B*=mu;
			vwind*=1e5;		
			Sarea*=M;
			Sarea*=9.52142254852e36;
			sfr*=(Msun/yr);
			d*=M;
			d*=pc;
			nu*=M;
			flux*=Jy;
			sfrmin*=(Msun/yr);
			sfrmax*=(Msun/yr);
			epsilon/=100;
/*
			document.write('sfr: '+sfr+'<br>');
			document.write('n: '+n+'<br>');
			document.write('Te: '+Te+'<br>');
			document.write('Lion: '+Lion+'<br>');
			document.write('fion: '+fion+'<br>');
			document.write('ffill: '+ffill+'<br>');
			document.write('B: '+B+'<br>');
			document.write('uint: '+uint+'<br>');
			document.write('vwind: '+vwind+'<br>');
			document.write('Sarea: '+Sarea+'<br>');
			document.write('z: '+z+'<br>');
			document.write('d: '+d+'<br>');
			document.write('Chi: '+Chi+'<br>');
			document.write('nu: '+nu+'<br>');
*/
			if (document.getElementById('CalcModeFlux').checked){
				var result = Lnuobs(B,Sarea,ffill,fion,Lion,sfr,n,Te,uint,vwind,z,nu)/Lsun;		
				swal({
					title: "Luminosity: "+result,
					text: result,
					animation: "slide-from-top",
					closeOnConfirm: false,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: "Done"
				});
			}
			else if (document.getElementById('CalcModeSFR').checked){
				var result = ComputeSFR(B,Sarea,ffill,fion,Lion,n,Te,uint,vwind,z,d,nu,flux,sfrmin,sfrmax,epsilon);
				swal({
					title: "SFR: "+result,
					animation: "slide-from-top",
					closeOnConfirm: false,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: "Done"
				});
			}

		}
	</script>
-->
</body>
</html>
