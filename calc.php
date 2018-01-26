<!--
	PHP web module to compute star formation rates from radio (synchrotron and free-free) luminosities for galaxies at different reshifts bidirectionally.
	Source: http://adsabs.harvard.edu/abs/2016arXiv161108311S
	Written by Joaquin Zamponi F. Feb 2017.	
	Last updated 18 Oct 2017
-->

<!-- HTML CODE FOR SHOWING RESULTS -->
<!DOCTYPE html>	
<html>
<head>
	<?php 
	
	if ($_REQUEST['source'] == 'MW'){
		$source='Milky Way'; $src='MW'; $srctype='diskgalaxy'; $srctitle='Milky Way (ESO - www.goodfon.ru)';	
	}elseif ($_REQUEST['source'] == 'M82'){
		$source='M82'; $src='M82'; $srctype='starburst'; $srctitle='M82 (NASA, ESA and the Hubble Heritage Team (STScl/AURA) - www.seasky.org)';
	}elseif ($_REQUEST['source'] == 'M51'){
		$source='M51'; $src='M51'; $srctype='starburst'; $srctitle='M51 (NASA/ESA - www.wikipedia.org)';
	}elseif ($_REQUEST['source'] == 'Arp220'){
		$source='Arp220'; $src='Arp220'; $srctype='starburst'; $srctitle='Arp 220 (NASA/ESA - www.annesastronomynews.com)';
	}else{
		$source='New Source'; $src='NS'; $srctype=''; $srctitle='Interstellar clouds (Artist creation - www.wallpapers-web.com)';
	}	
	?>
	<title>	Your Results for <?php echo $source; ?> </title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="style.css" type="text/css">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<meta name="viewport" content="width=device-width, initial-scale=1"> 
	<link rel="shortcut icon" href="img/RESoRt.ico" />
	<!--For nice JS sweetAlert messages -->
	<script src="sweetalert.min.js"></script>
	<link rel="stylesheet" type="text/css" href="sweetalert.css">
	

</head>
<body>
<?php
	echo '<div style="z-index: -1; cursor: wait;" title="'.$srctitle.'" class="background-'.$src.'"></div>';	

	ob_flush();flush();	//Flush the output buffer to show the loader before the results page

	//TO GET TOTAL TIME USED BY PAGE
	$pagetime = microtime();
	$pagetime = explode(' ', $pagetime);
	$pagetime = $pagetime[1] + $pagetime[0];
	$pagestart = $pagetime;

	ini_set('display_errors', 'off');
	ini_set('max_execution_time', 2500); //seconds. 
	include_once("math.php");

	//ALL CONSTANTS IN CGS SYSTEM
	//PHYSICAL CONSTANTS
	define("M",	1.e6							);
	define("k",	1.380658*(1e-16)				);//[erg/K] Boltzmann constant
	define("h",	6.6260755*(1e-27)				);//[erg s] Plank constant
	define("c", 2.998*(1e10)					);//[cm/s] Speed of light
	define("me", 9.1093897*(1e-28)				);//[g] electron mass
	define("mp", 1.675*(1e-24)					);//[g] proton mass
	define("b",	5.878933*(1e10) 				);//[Hz/K] Wiens displacement law constant
	define("e",	4.8032068*(1e-10)				);//[esu] electric charge
	define("r0", (e**2)/(me*(c**2))				);//classical electron radius
	define("Lsun", 3.839*(1e33)					);//[erg/s] Sun Luminosity
	define("pc", 3.086*(1e18)					);//[cm] parsec
	define("H0", (67.11*(1e5))/(M*pc)			);//[1/s] Hubble constant
	define("sigmaM", 0.3175						);//Matter parameter, Plank best fit
	define("eV", 1.60217657*(1e-12) 			);//[erg] electron volt
	define("Msun", 1.989*(1e33)					);//[g] Sun mass
	define("yr", 365.25*24*60*60				);//[s] year
	define("G",	6.67259*(1e-8)					);//[cm^3 g^-1 s^-2] Gravitational constant
	define("sigmaT", (8./3)*M_PI*(r0**2)		);//[cm^2] Thomson cross section
	define("Jy", 1e-23 							);//[erg s^-1 cm^-2 Hz^-1] Jansky
	//COSMIC RAYS PARAMETERS
	define("Fcal", 0.4						);//Fraction of CR protons that goes into pion production
	define("Fsec", 0.7						);//Fraction of secondary CR electrons from all CR electrons
	define("Eps",  0.1						);//Normalization of CR spectrum
	define("ESN", 1e51						);//[erg] energy released in one SN
	define("gammae0", (1e7)*(eV/(me*(c**2)))			);//Begin of electron CR spectrum
	define("gammap0", (1e9)*(eV/(mp*(c**2)))			);//Begin of proton CR spectrum
	if (!empty($_REQUEST['Chi'])){	define("Chi", $_REQUEST['Chi']	);}	//Slope of primary CR spectrum
	else{				define("Chi", 2.2		);}	//Slope of primary CR spectrum


	//New user's galaxy parameters
	//First three depend on the calculator mode, others always come
	if (isset($_REQUEST['SFR0']) && $_REQUEST['SFR0']!=0 ){
		$NEW['SFR0']=$_REQUEST['SFR0'];
	}
	if (isset($_REQUEST['flux']) && $_REQUEST['flux']!=0 ){
		$NEW['flux']=$_REQUEST['flux'];			//[erg/s/Hz] Observed Radio Flux
	}
	if (isset($_REQUEST['sfrmin']) && isset($_REQUEST['sfrmax']) && isset($_REQUEST['epsilon'])){
		$NEW['sfrmin']=$_REQUEST['sfrmin'];
		$NEW['sfrmax']=$_REQUEST['sfrmax'];
		$NEW['epsilon']=$_REQUEST['epsilon'];	//[M_0/yr]   Star Formation Rate	
	}
	$NEW['freq']=$_REQUEST['freq'];
	$NEW['n0']=$_REQUEST['n0'];					//[cm^-3] Gas Denisty. (Koda+09)
	$NEW['Te0']=$_REQUEST['Te0'];				//[K] electron temperature
	$NEW['Lion0']=$_REQUEST['Lion0'];			//[cm] galactic scale height and ion depth. (Hu+13)
	$NEW['fion0']=$_REQUEST['fion0'];			//ionization degree
	$NEW['ffill0']=$_REQUEST['ffill0'];			//filling factor
	$NEW['B0']=$_REQUEST['B0'];					//magnetic field strenght. (Fletcher+2011)
	$NEW['uint0']=$_REQUEST['uint0'];			//[erg/cm^3] internal component of the interstellar radiation field
	$NEW['vwind0']=$_REQUEST['vwind0'];			//[cm/s] velocity of galactic winds	
	$NEW['Sarea0']=$_REQUEST['Sarea0'];			//[cm^2] Surface area
	$NEW['z0']=$_REQUEST['z0'];					//Redshift
	$NEW['d0']=$_REQUEST['d0'];					//[cm] Distance


	/* GALAXY MODEL */
	function NSNrate($SFR){ return 0.156*($SFR/(12.26*Msun)); }										//[1/s] Supernova rate for Kroupa IMF
	function uISRF($uint,$z){ return $uint+(((8*(k**4)*(M_PI**5))/(15*(c**3)*(h**3)))*(2.73*(1+$z))**4); }	//Interstellar radiation field

	//Energy loses timescales
	function tsynch($B,$gamma){ return  (3.*me*c)/(4.*sigmaT*(($B**2.)/(8.*M_PI))*$gamma); }	//[s] Synchrotron
	function tIC($gamma,$uint,$z){ return  (3.*me*c)/(4.*sigmaT*uISRF($uint,$z)*$gamma);}	//[s] Inverse Compton		
	function tion($gamma,$n){ return $gamma/(2.7*c*sigmaT*(6.85+0.5*log($gamma))*$n); }	//[s] Ionization
	function tbrems($n){ return 104000.*yr*(1./($n/300.));	 }				//[s] Bremsstrahlung
	function twind($Lion,$vwind){ return $Lion/$vwind; }					//[s] Wind
	function t($B,$gamma,$Lion,$n,$uint,$vwind,$z){ return 1./( (1./tsynch($B,$gamma))+(1./tIC($gamma,$uint,$z))+(1./tion($gamma,$n))+(1./tbrems($n))+(1./twind($Lion,$vwind)) ); }	//Total cooling timescale

	//Injection spectrum of cosmic ray protons 
	function K0($SFR){ return (Eps*ESN*NSNrate($SFR)*(Chi-2))/((gammap0**(2.-Chi))*((mp*(c**2)**2))); }
	function Qp($gammap,$SFR){ return K0($SFR)*($gammap**(-Chi)); }

	//Spectra in steady state
	function Ne($B,$gamma,$Lion,$n,$SFR,$uint,$vwind,$z){
		return (((20.**2)*Fcal)/(6.*(Chi-1)*Fsec))*K0($SFR)*(((20.*$gamma*me)/mp)**(-Chi))*t($B,$gamma,$Lion,$n,$uint,$vwind,$z)*me*(c**2);
	}//Electrons
	function tpion($n){ return (50.*(1.e6)*yr)/$n;	}											//[s] pion production time (from CR protons)
	function Np($gamma,$n,$SFR){ return Fcal*tpion($n)*K0($SFR)*($gamma**-Chi)*mp*(c**2);	}		// Protons


	/* RADIO EMISSION */
	//Synchrotron spectrum (for power-low distributed CR electrons)
	function nuc($B,$gamma){ return (3.*e*$B*($gamma**2.))/(4.*M_PI*me*c);}					//Characteristic synchrotron frequency	
	function Pitch(){ return (2.*(M_PI**(3./2))*Gamma((5.+Chi)/4.)) / (Gamma((7.+Chi)/4.));}	//Contribution of pitch angle in case of local isotropy


	/* INTEGRAL NEEDED FUNCTIONS*/
	
	define("N",6.e18); 			//outer integral upper limit
	define("xend",1.075e2);			//BesselK (inner) integral upper limit 
	define("nsteps",1e2);			//accuracy

	function integrand1($x){
		$ri=0.; $rk=0.; $rip=0.; $rkp=0.;
		//return BesselK(5./3,$x);	
		return bessik($x,5/3.,$ri,$rk,$rip,$rkp);
	}
	//Simpson's rule for numerical integral
	function simpson1($x0,$xf){
		$i=0;
		$hh=0; $S=0; $xi=0;
		if (nsteps % 2 == 0 ){
			$hh=($xf-$x0)/nsteps;
			$S=integrand1($x0) + integrand1($xf);
			$i=1;	
			while ($i <= (nsteps-1)){
				$xi=$x0+$hh*$i;
				if($i%2 == 0){
					$S=$S+2*integrand1($xi);
				}else{
					$S=$S+4*integrand1($xi);
				}
				$i++;
			}
		return $hh/3*$S;
		}	
	}
	function integrand2($gamma,$B,$Lion,$n,$sfr,$uint,$vwind,$z,$nu){
		$Lnu=0;
		$cst=(sqrt(3.)*(e*e*e)*$B)/(me*(c*c));
		$Lnu=simpson1($nu/nuc($B,$gamma),xend);
		return $cst*($nu/nuc($B,$gamma))*$Lnu*Ne($B,$gamma,$Lion,$n,$sfr,$uint,$vwind,$z);
	}
	function simpson2($x0,$xf,$B,$Lion,$n,$sfr,$uint,$vwind,$z,$nu){
		$i=0;
		$hh=0; $S=0; $xi=0;
		if (nsteps % 2 == 0 ){
			$hh=($xf-$x0)/nsteps;
			$S=integrand2($x0,$B,$Lion,$n,$sfr,$uint,$vwind,$z,$nu) + integrand2($xf,$B,$Lion,$n,$sfr,$uint,$vwind,$z,$nu);
			$i=1;	
			while ($i <= (nsteps-1)){
				$xi=$x0+$hh*$i;
				if($i%2 == 0){
					$S=$S+2*integrand2($xi,$B,$Lion,$n,$sfr,$uint,$vwind,$z,$nu);
				}else{
					$S=$S+4*integrand2($xi,$B,$Lion,$n,$sfr,$uint,$vwind,$z,$nu);
				}
				$i++;
			}
		return $hh/3*$S;
		}	
	}

	function Lsynchnu($B,$Lion,$n,$sfr,$uint,$vwind,$z,$nu) {
		$Lsynchnu=0;
		$Lsynchnu = simpson2(gammae0,N, $B,$Lion,$n,$sfr,$uint,$vwind,$z,$nu);	
		$Lsynchnu*=Pitch();	
		return $Lsynchnu;
	}

	//Free-free contribution
	function EM($ffill,$fion,$Lion,$n){ return (($fion*$n)**2)*($Lion/$ffill);}				//Emission measure
	function tff($ffill,$fion,$Lion,$n,$Te,$nu)
	{ 
		$opticaldepth=3.28*(1.e-7)*pow($Te/(1.e4),-1.35)*pow($nu/(1.e9),-2.1)*(EM($ffill,$fion,$Lion,$n)/pc);
		$upperlim=50.;
		return ( $opticaldepth < $upperlim ? $upperlim : $opticaldepth );	//Optical Depth
	}
	function nucritical($ffill,$fion,$Lion,$n,$Te){ return (1.e9)*pow(3.28*(1.e-7)*pow($Te/(1.e4),-1.35)*(EM($ffill,$fion,$Lion,$n)/pc),1/2.1);} 	//Critical Frequency	
	function Lffnu($Sarea,$ffill,$fion,$Lion,$n,$Te,$nu){ return 2.*k*$Te*(1.-exp(-tff($ffill,$fion,$Lion,$n,$Te,$nu)))*$Sarea*(c**-2.)*$nu**2.;}	//Spectral free-free luminosity	
	function Lsynchffnu($B,$ffill,$fion,$Lion,$SFR,$n,$Te,$uint,$vwind,$z,$nu){ return Lsynchnu($B,$Lion,$n,$SFR,$uint,$vwind,$z,$nu)*exp(-tff($ffill,$fion,$Lion,$n,$Te,$nu));}		//Absorption of spectral synchrotron flux
	

	/* COMPUTING LUMINOSITIES */	
	function Lnuobs($B,$Sarea,$ffill,$fion,$Lion,$SFR,$n,$Te,$uint,$vwind,$z,$nuobs){
		return $nuobs*(Lsynchffnu($B,$ffill,$fion,$Lion,$SFR,$n,$Te,$uint,$vwind,$z,(1+$z)*$nuobs) + Lffnu($Sarea,$ffill,$fion,$Lion,$n,$Te,(1+$z)*$nuobs)); 	
	}

	/* COMPUTING STAR FORMATION RATES */
	function EstimateSFR($B,$Sarea,$ffill,$fion,$Lion,$n,$Te,$uint,$vwind,$z,$d,$nu,$flux, $SFRmin, $SFRmax,$epsilon){
		$SFRlow=0; $SFRhigh=0; $SFRmid=0; $SFRin=0; $Lin=0; $Lmin=0; $Lmax=0; $Lmid=0;
		$Lin=($nu*($flux))*(4*M_PI*($d*$d));

		$Lmin = Lnuobs($B,$Sarea,$ffill,$fion,$Lion,$SFRmin,$n,$Te,$uint,$vwind,$z,$nu);
		$Lmax = Lnuobs($B,$Sarea,$ffill,$fion,$Lion,$SFRmax,$n,$Te,$uint,$vwind,$z,$nu);

		$epsilon*=$Lin;	//e% of Lin (accuracy)
		$SFRlow=$SFRmin;			
		$SFRhigh=$SFRmax;
		while(1){
			$SFRmid=exp(log($SFRlow)+(log($SFRhigh)-log($SFRlow))/2);
			$Lmid=Lnuobs($B,$Sarea,$ffill,$fion,$Lion,$SFRmid,$n,$Te,$uint,$vwind,$z,$nu);
			if ( abs($Lmid-$Lin) <= $epsilon ){
				break;
			}else{
				if ($Lmid < $Lin){
					$SFRlow=$SFRmid;
					$SFRhigh=$SFRhigh;
				}else if($Lmid > $Lin){
					$SFRlow=$SFRlow;
					$SFRhigh=$SFRmid;
				}
			}
		}

		return $SFRmid/(Msun/yr);
	}
	//END OF FUNCTION DEFINITIONS

?>


<!-- PRINT RESULTS -->
<?php
	/*	FINAL CALLS	*/

	function FinalOut($NEW,$calcmode){
		//TO GET COMPUTATION TIME
		$time = microtime();
		$time = explode(' ', $time);
		$time = $time[1] + $time[0];
		$start = $time;

		$lang="C";//C or PHP
	
		if ($lang == "C"){
		//TO EXECUTE C CODE
		$cfolder = "c";
		$cfile = $cfolder."/"."calc.c";
		$binfile = $cfolder."/"."calc";
		$outfile = $cfolder."/"."OutForPHP.txt";

		$compile = "gcc ".$cfile." -lm -o ".$binfile." 2>&1";	
		$compileerr = system($compile,$retval);

		$execute = 	"./".$binfile.
					" -m ".$calcmode.
					" -s ".$_REQUEST['source'].
					" -n ".$NEW['n0'].
					" -T ".$NEW['Te0'].
					" -L ".$NEW['Lion0'].
					" -f ".$NEW['fion0'].
					" -F ".$NEW['ffill0'].
					" -B ".$NEW['B0'].
					" -u ".$NEW['uint0'].
					" -V ".$NEW['vwind0'].
					" -a ".$NEW['Sarea0'].
					" -S ".$NEW['SFR0'].
					" -z ".$NEW['z0'].
					" -d ".$NEW['d0'].
					" -v ".$NEW['freq'].
					" -C ".Chi;

		if ($calcmode == "SFR"){
			$execute.=" -x ".$NEW['flux']." -j ".$NEW['sfrmin']." -k ".$NEW['sfrmax']." -e ".$NEW['epsilon'];
		}
		//echo 'execute: '.$execute.'<br>';

		//system("pkill calc ");

		unset($ret);
		exec('timeout --preserve-status 600s '.$execute.' 2>/dev/null & echo $!',$ret);	//run the c binary, i.e., performs the hard calculation (Max exec time 10min).
	    $pid = (int)$ret[0]; 



		$final_lum = $ret[1];

		}else if ($lang == "PHP"){
		//TO EXECUTE PHP CODE
		if ($calcmode == "Flux"){
			$NEW['SFR0']*=(Msun/yr);
			$NEW['Lion0']*=pc;
			$NEW['freq']*=M;
			return 	Lnuobs($B=$NEW['B0'],
				$Sarea=$NEW['Sarea0'],
				$ffill=$NEW['ffill0'],
				$fion=$NEW['fion0'],
				$Lion=$NEW['Lion0'],
				$SFR=$NEW['SFR0'],
				$n=$NEW['n0'],
				$Te=$NEW['Te0'],
				$uint=$NEW['uint0'],
				$vwind=$NEW['vwind0'],
				$z=$NEW['z0'],
				$nuobs=$NEW['freq'])/Lsun;	 
		}else if($calcmode == "SFR"){	
			$NEW['d0']*=pc;
			$NEW['Lion0']*=pc;
			$NEW['flux']*=Jy;
			$NEW['sfrmin']*=(Msun/yr);
			$NEW['sfrmax']*=(Msun/yr);
			$NEW['epsilon']/=100;
			$NEW['freq']*=M;
			return 	EstimateSFR($B=$NEW['B0'],
				$Sarea=$NEW['Sarea0'],
				$ffill=$NEW['ffill0'],
				$fion=$NEW['fion0'],
				$Lion=$NEW['Lion0'],
				$n=$NEW['n0'],
				$Te=$NEW['Te0'],
				$uint=$NEW['uint0'],
				$vwind=$NEW['vwind0'],
				$z=$NEW['z0'],
				$d=$NEW['d0'],
				$nu=$NEW['freq'],
				$flux=$NEW['flux'],
				$SFRmin=$NEW['sfrmin'],
				$SFRmax=$NEW['sfrmax'],
				$epsilon=$NEW['epsilon'])/Lsun;	//$nuobs[Hz] 
		}
		}

		//FINAL EXECUTION TIME
		$time = microtime();
		$time = explode(' ', $time);
		$time = $time[1] + $time[0];
		$finish = $time;
		global $exec_time;
		$exec_time += ($finish - $start);

		return $final_lum;
	}


	if ($_REQUEST['CalcMode'] == 'Flux'){
			//IF NO FREQUENCY ENTERED
			if (empty($NEW['freq'])){
				echo '<script type="text/javascript">';
				echo '	swal({
							title: "Oops!",
							text: "Seems like you did not entered a frequency value for the Luminosity to be computed.",
							type: "error",
							inputPlaceholder: false,
							html: true,
							closeOnConfirm: false,
							confirmButtonColor: "#DD6B55",
							confirmButtonText:	"Home"
						},
						function(){
							window.history.go(-1)="index.php"	
						}); ';
				echo '</script>';
			}else if (empty($NEW['SFR0'])){
				echo '<script type="text/javascript">';
				echo '	swal({
							title: "Oops!",
							text: "Seems like you did not entered a star formation rate. I can not obtain a radio flux for you.",
							type: "error",
							inputPlaceholder: false,
							html: true,
							closeOnConfirm: false,
							confirmButtonColor: "#DD6B55",
							confirmButtonText:	"Home"
						},
						function(){
							window.history.go(-1)="index.php"	
						}); ';
				echo '</script>';
			}

			$mode = "Flux";
			echo '<div id="2nd-background" style="z-index: 1; cursor: auto;" title="'.$srctitle.'" class="background-'.$src.'"></div>';	//Conditional for selecting background image and its title for each source at showing results
			echo '<div class="content">';
			echo '<div class="center-results">';
			echo '<h2 style="margin-left:10%"> <b>Luminosities calculated for <span style="color:#337ab7">'.$source.'</span></b> </h2>';
			echo '<span style="display:inline-block; height:30px;"></span>';		
			echo '<h3 style="margin-left:8%;  background: rgba(255, 255, 255, 0.3); border-radius: 8px; box-shadow: 0 1px 5px rgba(0, 0, 0, 0.25); width: 70%; overflow: auto;">
					  <b>&nu;<sub>0</sub>L<sub>&nu;</sub>(&nu;<sub>0</sub>) at '.$NEW['freq'].' MHz: </b><span style="color:#337ab7; overflow: auto;">'.FinalOut($NEW, $mode).' L<sub>&odot;</sub></span></h3><br>';


			//If "Add extra frequencies" option checked. It also test if new freq match recent calculated to avoid recalling			
			if ( in_array("additional_freqs", $_REQUEST) ){
				if ($NEW['freq'] == 1400){
					$NEW['freq']=500;
					echo '<h4 style="margin-left:12%">&nu;<sub>0</sub>L<sub>&nu;</sub>(&nu;<sub>0</sub>) at 500MHz: '.FinalOut($NEW, $mode)." L<sub>&odot;</sub><br></h4>";
					ob_flush();flush();	//Flush the output buffer to show the loader before the results page
					$NEW['freq']=200;
					echo '<h4 style="margin-left:12%">&nu;<sub>0</sub>L<sub>&nu;</sub>(&nu;<sub>0</sub>) at 200MHz: '.FinalOut($NEW, $mode)." L<sub>&odot;</sub><br></h4>";
					ob_flush();flush();	//Flush the output buffer to show the loader before the results page
					$NEW['freq']=60;
					echo '<h4 style="margin-left:12%">&nu;<sub>0</sub>L<sub>&nu;</sub>(&nu;<sub>0</sub>) at 60MHz: ' .FinalOut($NEW, $mode)." L<sub>&odot;</sub><br></h4>";	
				}else if($NEW['freq'] == 500){
					$NEW['freq']=1400;
					echo '<h4 style="margin-left:12%">&nu;<sub>0</sub>L<sub>&nu;</sub>(&nu;<sub>0</sub>) at 1.4GHz: '.FinalOut($NEW, $mode)." L<sub>&odot;</sub><br></h4>";
					ob_flush();flush();	//Flush the output buffer to show the loader before the results page
					$NEW['freq']=200;
					echo '<h4 style="margin-left:12%">&nu;<sub>0</sub>L<sub>&nu;</sub>(&nu;<sub>0</sub>) at 200MHz: '.FinalOut($NEW, $mode)." L<sub>&odot;</sub><br></h4>";
					ob_flush();flush();	//Flush the output buffer to show the loader before the results page
					$NEW['freq']=60;
					echo '<h4 style="margin-left:12%">&nu;<sub>0</sub>L<sub>&nu;</sub>(&nu;<sub>0</sub>) at 60MHz: ' .FinalOut($NEW, $mode)." L<sub>&odot;</sub><br></h4>";	
				}else if($NEW['freq'] == 200){
					$NEW['freq']=1400;
					echo '<h4 style="margin-left:12%">&nu;<sub>0</sub>L<sub>&nu;</sub>(&nu;<sub>0</sub>) at 1.4GHz: '.FinalOut($NEW, $mode)." L<sub>&odot;</sub><br></h4>";
					ob_flush();flush();	//Flush the output buffer to show the loader before the results page
					$NEW['freq']=500;
					echo '<h4 style="margin-left:12%">&nu;<sub>0</sub>L<sub>&nu;</sub>(&nu;<sub>0</sub>) at 500MHz: '.FinalOut($NEW, $mode)." L<sub>&odot;</sub><br></h4>";
					ob_flush();flush();	//Flush the output buffer to show the loader before the results page
					$NEW['freq']=60;
					echo '<h4 style="margin-left:12%">&nu;<sub>0</sub>L<sub>&nu;</sub>(&nu;<sub>0</sub>) at 60MHz: ' .FinalOut($NEW, $mode)." L<sub>&odot;</sub><br></h4>";	
				}else if($NEW['freq'] == 60){
					$NEW['freq']=1400;
					echo '<h4 style="margin-left:12%">&nu;<sub>0</sub>L<sub>&nu;</sub>(&nu;<sub>0</sub>) at 1.4GHz: '.FinalOut($NEW, $mode)." L<sub>&odot;</sub><br></h4>";
					ob_flush();flush();	//Flush the output buffer to show the loader before the results page
					$NEW['freq']=500;
					echo '<h4 style="margin-left:12%">&nu;<sub>0</sub>L<sub>&nu;</sub>(&nu;<sub>0</sub>) at 500MHz: '.FinalOut($NEW, $mode)." L<sub>&odot;</sub><br></h4>";
					ob_flush();flush();	//Flush the output buffer to show the loader before the results page
					$NEW['freq']=200;
					echo '<h4 style="margin-left:12%">&nu;<sub>0</sub>L<sub>&nu;</sub>(&nu;<sub>0</sub>) at 200MHz: '.FinalOut($NEW, $mode)." L<sub>&odot;</sub><br></h4>";
				}else{
					$NEW['freq']=1400;
					echo '<h4 style="margin-left:12%">&nu;<sub>0</sub>L<sub>&nu;</sub>(&nu;<sub>0</sub>) at 1.4GHz: '.FinalOut($NEW, $mode)." L<sub>&odot;</sub><br></h4>";
					ob_flush();flush();	//Flush the output buffer to show the loader before the results page
					$NEW['freq']=500;
					echo '<h4 style="margin-left:12%">&nu;<sub>0</sub>L<sub>&nu;</sub>(&nu;<sub>0</sub>) at 500MHz: '.FinalOut($NEW, $mode)." L<sub>&odot;</sub><br></h4>";
					ob_flush();flush();	//Flush the output buffer to show the loader before the results page
					$NEW['freq']=200;
					echo '<h4 style="margin-left:12%">&nu;<sub>0</sub>L<sub>&nu;</sub>(&nu;<sub>0</sub>) at 200MHz: '.FinalOut($NEW, $mode)." L<sub>&odot;</sub><br></h4>";
					ob_flush();flush();	//Flush the output buffer to show the loader before the results page
					$NEW['freq']=60;
					echo '<h4 style="margin-left:12%">&nu;<sub>0</sub>L<sub>&nu;</sub>(&nu;<sub>0</sub>) at 60MHz: ' .FinalOut($NEW, $mode)." L<sub>&odot;</sub><br></h4>";	
				}
			}

	}
	elseif($_REQUEST['CalcMode'] == 'SFR'){
			//IF NO FREQUENCY ENTERED
			if (empty($NEW['freq'])){
				echo '<script type="text/javascript">';
				echo '	swal({
							title: "Oops!",
							text: "Seems like you did not entered a frequency value for the Star Formation Rate to be computed.",
							type: "error",
							inputPlaceholder: false,
							html: true,
							closeOnConfirm: false,
							confirmButtonColor: "#DD6B55",
							confirmButtonText:	"Home"
						},
						function(){
							window.history.go(-1)="index.php"	
						}); ';
				echo '</script>';
			}else if (empty($NEW['flux'])){
				echo '<script type="text/javascript">';
				echo '	swal({
							title: "Oops!",
							text: "Seems like you did not entered a flux value. I can not obtain a star formation rate for you.",
							type: "error",
							inputPlaceholder: false,
							html: true,
							closeOnConfirm: false,
							confirmButtonColor: "#DD6B55",
							confirmButtonText:	"Home"
						},
						function(){
							window.history.go(-1)="index.php"	
						}); ';
				echo '</script>';
			}
			
			$mode = "SFR";
			//IF FREQUENCY ENTERED
			echo '<div id="2nd-background" style="z-index: 1; cursor: auto;"  title="'.$srctitle.'" class="background-'.$src.'"></div>';	//Conditional for selecting background image and its title for each source at showing results
			echo '<div class="content">';	
			echo '<div class="center-results">';
			echo '<h2 style="margin-left:5%"> <b>Star Formation Rate calculated for <span style="color:#337ab7">'.$source.'</span></b> </h2>';
			echo '<span style="display:inline-block; height:30px;"></span>';		
			echo '<h3 style="margin-left:8%;  background: rgba(255, 255, 255, 0.3); border-radius: 8px; box-shadow: 0 1px 5px rgba(0, 0, 0, 0.25);width: 70%;">
					  <b>Star Formation Rate at '.$NEW['freq'].' MHz: </b><span style="color:#337ab7">'.FinalOut($NEW, $mode)." M<sub>&odot;</sub>yr<sup>-1</sup></span></h3><br>";

			//If "Add extra frequencies" option checked. It also test if new freq match recent calculated to avoid recalling			
			if ( in_array("additional_freqs", $_REQUEST) ){
				if ($NEW['freq'] == 1400){
					$NEW['freq']=500;
					echo '<h4 style="margin-left:12%">Star Formation Rate at 500MHz: '.FinalOut($NEW, $mode)." M<sub>&odot;</sub>yr<sup>-1</sup><br></h4>";
					ob_flush();flush();	//Flush the output buffer to show the loader before the results page
					$NEW['freq']=200;
					echo '<h4 style="margin-left:12%">Star Formation Rate at 200MHz: '.FinalOut($NEW, $mode)." M<sub>&odot;</sub>yr<sup>-1</sup><br></h4>";
					ob_flush();flush();	//Flush the output buffer to show the loader before the results page
					$NEW['freq']=60;	
					echo '<h4 style="margin-left:12%">Star Formation Rate at 60MHz: ' .FinalOut($NEW, $mode)." M<sub>&odot;</sub>yr<sup>-1</sup><br></h4>";	
				}else if($NEW['freq'] == 500){
					$NEW['freq']=1400;
					echo '<h4 style="margin-left:12%">Star Formation Rate at 1.4GHz: '.FinalOut($NEW, $mode)." M<sub>&odot;</sub>yr<sup>-1</sup><br></h4>";
					ob_flush();flush();	//Flush the output buffer to show the loader before the results page
					$NEW['freq']=200;
					echo '<h4 style="margin-left:12%">Star Formation Rate at 200MHz: '.FinalOut($NEW, $mode)." M<sub>&odot;</sub>yr<sup>-1</sup><br></h4>";
					ob_flush();flush();	//Flush the output buffer to show the loader before the results page
					$NEW['freq']=60;	
					echo '<h4 style="margin-left:12%">Star Formation Rate at 60MHz: ' .FinalOut($NEW, $mode)." M<sub>&odot;</sub>yr<sup>-1</sup><br></h4>";	
				}else if($NEW['freq'] == 200){
					$NEW['freq']=1400;
					echo '<h4 style="margin-left:12%">Star Formation Rate at 1.4GHz: '.FinalOut($NEW, $mode)." M<sub>&odot;</sub>yr<sup>-1</sup><br></h4>";
					ob_flush();flush();	//Flush the output buffer to show the loader before the results page
					$NEW['freq']=500;
					echo '<h4 style="margin-left:12%">Star Formation Rate at 500MHz: '.FinalOut($NEW, $mode)." M<sub>&odot;</sub>yr<sup>-1</sup><br></h4>";
					ob_flush();flush();	//Flush the output buffer to show the loader before the results page
					$NEW['freq']=60;	
					echo '<h4 style="margin-left:12%">Star Formation Rate at 60MHz: ' .FinalOut($NEW, $mode)." M<sub>&odot;</sub>yr<sup>-1</sup><br></h4>";	
				}else if($NEW['freq'] == 60){
					$NEW['freq']=1400;
					echo '<h4 style="margin-left:12%">Star Formation Rate at 1.4GHz: '.FinalOut($NEW, $mode)." M<sub>&odot;</sub>yr<sup>-1</sup><br></h4>";
					ob_flush();flush();	//Flush the output buffer to show the loader before the results page
					$NEW['freq']=500;
					echo '<h4 style="margin-left:12%">Star Formation Rate at 500MHz: '.FinalOut($NEW, $mode)." M<sub>&odot;</sub>yr<sup>-1</sup><br></h4>";
					ob_flush();flush();	//Flush the output buffer to show the loader before the results page
					$NEW['freq']=200;
					echo '<h4 style="margin-left:12%">Star Formation Rate at 200MHz: '.FinalOut($NEW, $mode)." M<sub>&odot;</sub>yr<sup>-1</sup><br></h4>";
				}else{								
					$NEW['freq']=1400;
					echo '<h4 style="margin-left:12%">Star Formation Rate at 1.4GHz: '.FinalOut($NEW, $mode)." M<sub>&odot;</sub>yr<sup>-1</sup><br></h4>";
					$NEW['freq']=500;
					echo '<h4 style="margin-left:12%">Star Formation Rate at 500MHz: '.FinalOut($NEW, $mode)." M<sub>&odot;</sub>yr<sup>-1</sup><br></h4>";
					$NEW['freq']=200;
					echo '<h4 style="margin-left:12%">Star Formation Rate at 200MHz: '.FinalOut($NEW, $mode)." M<sub>&odot;</sub>yr<sup>-1</sup><br></h4>";
					$NEW['freq']=60;	
					echo '<h4 style="margin-left:12%">Star Formation Rate at 60MHz: ' .FinalOut($NEW, $mode)." M<sub>&odot;</sub>yr<sup>-1</sup><br></h4>";	
				}
			}
	}	
	else{
		echo '<script type="text/javascript">';
		echo 'window.alert("You must choose one calculator mode.';
		echo 'window.history.go(-1)="index.php";';
		echo '</script>';
	}
	
?>

	<button type="submit"  onclick="window.history.go(-1);">
		<span>New calculation</span>
	</button>

<?php 
	//FINAL TIME
	$pagetime = microtime();
	$pagetime = explode(' ', $pagetime);
	$pagetime = $pagetime[1] + $pagetime[0];
	$pagefinish = $pagetime;
	$page_time = ($pagefinish - $pagestart);
?>
	<h6>
		<span style="cursor:help;" title="Time taken by the C module to perform all calculations.">
			Execution time: <?php ($exec_time > 60) ? printf("%.2lf minutes",$exec_time/60) : printf("%lf seconds",$exec_time);?>
		</span>
		<br />
		<span style="cursor:help;" title="Time taken by the PHP code to load the whole webpage along with the C returned results.">
			Total time: <?php ($page_time > 60) ? printf("%.2lf minutes",$page_time/60) : printf("%.lf seconds",$page_time);?>
		</span>
	<h6>	
	</div>	
	</div><!-- close <div> for showing results in blurred box -->
</div>

<script>
document.getElementById( '2nd-background' ).scrollIntoView();
</script>


</body>
</html>
