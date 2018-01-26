<?php

/*	DEFINITION OF FACTORIAL FUNCTION	*/
	function factorial($nr)
	{
		$product = array_product(range(1,++$nr));
		return $product / $nr;
	}


/*	DEFINITION OF GAMMA FUNCTION
	SOURCE:	http://picomath.org/php/gamma.php.html  */
	
function Gamma($x) {

	switch ($x){
		case (-(5./3.)+1): return -4.0184078020616433111;	
	}
	
    if ($x <= 0.0 && $x != (5./3.)-1)
    {
        die("Invalid input argument $x. Argument must be positivex\n");
    }

    # Split the function domain into three intervals:
    # (0, 0.001), [0.001, 12), and (12, infinity)

    ###########################################################################
    # First interval: (0, 0.001)
    #
    # For small x, 1/Gamma(x) has power series x + gamma x^2  - ...
    # So in this range, 1/Gamma(x) = x + gamma x^2 with error on the order of x^3.
    # The relative error over this interval is less than 6e-7.

    $gamma = 0.577215664901532860606512090; # Euler's gamma constant

    if ($x < 0.001) {
        return 1.0/($x*(1.0 + $gamma*$x));
    }

    ###########################################################################
    # Second interval: [0.001, 12)

    if ($x < 12.0)
    {
        # The algorithm directly approximates gamma over (1,2) and uses
        # reduction identities to reduce other arguments to this interval.
        
        $y = $x;
        $n = 0;
        $arg_was_less_than_one = ($y < 1.0);

        # Add or subtract integers as necessary to bring y into (1,2)
        # Will correct for this below
        if ($arg_was_less_than_one)
        {
            $y += 1.0;
        }
        else
        {
            $n = floor($y) - 1;  # will use n later
            $y -= $n;
        }

        # numerator coefficients for approximation over the interval (1,2)
        $p =
        array(
            -1.71618513886549492533811E+0,
             2.47656508055759199108314E+1,
            -3.79804256470945635097577E+2,
             6.29331155312818442661052E+2,
             8.66966202790413211295064E+2,
            -3.14512729688483675254357E+4,
            -3.61444134186911729807069E+4,
             6.64561438202405440627855E+4
        );

        # denominator coefficients for approximation over the interval (1,2)
        $q =
        array(
            -3.08402300119738975254353E+1,
             3.15350626979604161529144E+2,
            -1.01515636749021914166146E+3,
            -3.10777167157231109440444E+3,
             2.25381184209801510330112E+4,
             4.75584627752788110767815E+3,
            -1.34659959864969306392456E+5,
            -1.15132259675553483497211E+5
        );

        $num = 0.0;
        $den = 1.0;

        $z = $y - 1;
        for ($i = 0; $i < 8; $i++)
        {
            $num = ($num + $p[$i])*$z;
            $den = $den*$z + $q[$i];
        }
        $result = $num/$den + 1.0;

        # Apply correction if argument was not initially in (1,2)
        if ($arg_was_less_than_one)
        {
            # Use identity gamma(z) = gamma(z+1)/z
            # The variable "result" now holds gamma of the original y + 1
            # Thus we use y-1 to get back the orginal y.
            $result /= ($y-1.0);
        }
        else
        {
            # Use the identity gamma(z+n) = z*(z+1)* ... *(z+n-1)*gamma(z)
            for ($i = 0; $i < $n; $i++) {
                $result *= $y++;
            }
        }

        return $result;
    }

    ###########################################################################
    # Third interval: [12, infinity)

    if ($x > 171.624)
    {
        # Correct answer too large to display. 
        return Double.POSITIVE_INFINITY;
    }

    return exp(log_gamma($x));
}

function log_gamma($x) {
	if ($x == -0.66666666666667){
		return -4.0184078020616433111;
	}
    if ($x <= 0.0)
    {
        die("Invalid input argument $x. Argument must be positive\n");
    }

    if ($x < 12.0)
    {
        return log(abs(Gamma($x)));
    }

    # Abramowitz and Stegun 6.1.41
    # Asymptotic series should be good to at least 11 or 12 figures
    # For error analysis, see Whittiker and Watson
    # A Course in Modern Analysis (1927), page 252

    $c =
    array(
         1.0/12.0,
        -1.0/360.0,
         1.0/1260.0,
        -1.0/1680.0,
         1.0/1188.0,
        -691.0/360360.0,
         1.0/156.0,
        -3617.0/122400.0
    );
    $z = 1.0/($x*$x);
    $sum = $c[7];
    for ($i=6; $i >= 0; $i--)
    {
        $sum *= $z;
        $sum += $c[$i];
    }
    $series = $sum/$x;

    $halfLogTwoPi = 0.91893853320467274178032973640562;
    $logGamma = ($x - 0.5)*log($x) - $x + $halfLogTwoPi + $series;    
    return $logGamma;
}


/* DEFINITION OF MODIFIED BESSEL FUNCTIONS OF THE FIRST AND SECOND KIND */

function BesselI($n,$x){
	$I=0;
	$maxit=40;
	for ($j=0; $j<$maxit; $j++){
		$I+=($x/2.)**($n+2.*$j)/(factorial($j)*Gamma($j+$n+1.));
	}
	return $I;
}
function BesselK($n,$x){
	return M_PI*(BesselI(-$n,$x)-BesselI($n,$x))/(2.*sin($n*M_PI));
}


# Attempt to translate bessik() and their nested functions from "Numerical Recipes
# in C" to PHP functions. Problems may be encountered because many C implementations
# make use of pointer which in here are treated only as variables.
if (!function_exists('bessik')) {
function bessik($x,$xnu,$ri,$rk,$rip,$rkp){
	if (!defined('EPS')) define("EPS",1.0e-16);
	if (!defined('FPMIN')) define("FPMIN",1.0e-30);
	if (!defined('MAXIT')) define("MAXIT",10000);
	if (!defined('XMIN')) define("XMIN",2.0);

	if (!function_exists('chebev')) {
	function chebev($a,$b,$c,$m,$x){
		$d=0.; $dd=0.; $sv=0.; $y=0.; $y2=0.;
		$j=0;
		if (($x-$a)*($x-$b) > 0.0){ return 1; }//"x not in range in routine chebev"; }
		$y2=2.*($y=(2.*$x-$a-$b)/($b-$a));
		for ($j=$m-1; $j>=1; $j--){
			$sv=$d;
			$d=$y2*$d-$dd+$c[$j];
			$dd=$sv;
		}
		return $y*$d-$dd+0.5*$c[0];
	}
	}

	#int	
	$i=0;$l=0;$nl=0;
	#doubles
	$a1=0.;$b=0.;$c=0.;$d=0.;$del=0.;$del1=0.;$delh=0.;$dels=0.;$e=0.;$f=0.;$fact=0.;$fact2=0.;$ff=0.;$gam1=0.;$gam2=0.;
	$gammi=0.;$gampl=0.;$h=0.;$p=0.;$pimu=0.;$q=0.;$q1=0.;$q2=0.;$qnew=0.;$ril=0.;$ril1=0.;$rimu=0.;$rip1=0.;$ripl=0.;
	$ritemp=0.;$rk1=0.;$rkmu=0.;$rkmup=0.;$rktemp=0.;$s=0.;$sum=0.;$sum1=0.;$x2=0.;$xi=0.;$xi2=0.;$xmu=0.;$xmu2=0;

	if ($x <= 0.0 || $xnu < 0.0){ return 1; }//"bad arguments in bessik"; }
	$nl=($xnu+0.5);
	$xmu=$xnu-$nl;
	$xmu2=$xmu*$xmu;
	$xi=1.0/$x;
	$xi2=2.0*$xi;
	$h=$xnu*$xi;
	if ($h < FPMIN){ $h=FPMIN; }
	$b=$xi2*$xnu;
	$d=0.0;
	$c=$h;
	for ($i=1;$i<=MAXIT;$i++) {
		$b += $xi2;
		$d=1.0/($b+$d);
		$c=$b+1.0/$c;
		$del=$c*$d;
		$h=$del*$h;
		if (abs($del-1.0) < EPS){ break; }
	}
	if ($i > MAXIT) return 1; "x too large in bessik; try asymptotic expansion";
	$ril=FPMIN;
	$ripl=$h*$ril;
	$ril1=$ril;
	$rip1=$ripl;
	$fact=$xnu*$xi;
	for ($l=$nl;$l>=1;$l--) {
		$ritemp=$fact*$ril+$ripl;
		$fact -= $xi;
		$ripl=$fact*$ritemp+$ril;
		$ril=$ritemp;
	}
	$f=$ripl/$ril;
	if ($x < XMIN) {
		$x2=0.5*$x;
		$pimu=M_PI*$xmu;
		$fact = (abs($pimu) < EPS ? 1.0 : $pimu/sin($pimu));
		$d = -log($x2);
		$e=$xmu*$d;
		$fact2 = (abs($e) < EPS ? 1.0 : sinh($e)/$e);
//		beschb(xmu,&gam1,&gam2,&gampl,&gammi);
		# function beschb(){	
			# content of beschb() void function, used in main program because beschb() doesnt
			# return anything, making complicated for php to pass variables outwards	
			$xx=0;
			$c1 = array(1.142022680371172e0,6.516511267076e-3,
			3.08709017308e-4,-3.470626964e-6,6.943764e-9,
			3.6780e-11,-1.36e-13);
			$c2 = array(1.843740587300906e0,-0.076852840844786e0,
			1.271927136655e-3,-4.971736704e-6,-3.3126120e-8,
			2.42310e-10,-1.70e-13,-1.0e-15);
	
			if (!defined('NUSE')) define("NUSE",5);
	
			$xx=8.0*$x*$x-1.0;
			$gam1=chebev(-1.0,1.0,$c1,NUSE,$xx);
			$gam2=chebev(-1.0,1.0,$c2,NUSE,$xx);
			$gampl=$gam2-$x*($gam1);
			$gammi=$gam2+$x*($gam1);
//			echo 'gam1: '.$gam1.'<br>';
//2			echo 'gam2: '.$gam2.'<br>';
		#}
		$ff=$fact*($gam1*cosh($e)+$gam2*$fact2*$d);
		$sum=$ff;
		$e=exp($e);
		$p=0.5*$e/$gampl;
		$q=0.5/($e*$gammi);
		$c=1.0;
		$d=$x2*$x2;
		$sum1=$p;
		for ($i=1;$i<=MAXIT;$i++) {
			$ff=($i*$ff+$p+$q)/($i*$i-$xmu2);
			$c *= ($d/$i);
			$p /= ($i-$xmu);
			$q /= ($i+$xmu);
			$del=$c*$ff;
			$sum += $del;
			$del1=$c*($p-$i*$ff);
			$sum1 += $del1;
			if (abs($del) < abs($sum)*EPS){ break; }
		}
		if ($i > MAXIT)  return 1; "bessk series failed to converge";
		$rkmu=$sum;
		$rk1=$sum1*$xi2;
	} else {
		$b=2.0*(1.0+$x);
		$d=1.0/$b;
		$h=$delh;	//$h=$delh=d;
		$h=$d;
		$q1=0.0;
		$q2=1.0;
		$a1=0.25-$xmu2;
		$q=$c;	//$q=$c=$a1;
		$q=$a1;
		$a = -$a1;
		$s=1.0+$q*$delh;
		for ($i=2;$i<=MAXIT;$i++) {
			$a -= 2*($i-1);
			$c = -$a*$c/$i;
			$qnew=($q1-$b*$q2)/$a;
			$q1=$q2;
			$q2=$qnew;
			$q += $c*$qnew;
			$b += 2.0;
			$d=1.0/($b+$a*$d);
			$delh=($b*$d-1.0)*$delh;
			$h += $delh;
			$dels=$q*$delh;
			$s += $dels;
			if (abs($dels/$s) < EPS){ break; }
		}
		if ($i > MAXIT){ return 1; "bessik: failure to converge in cf2"; }
		$h=$a1*$h;
		$rkmu=sqrt(M_PI/(2.0*$x))*exp(-$x)/$s;
		$rk1=$rkmu*($xmu+$x+0.5-$h)*$xi;
	}
	$rkmup=$xmu*$xi*$rkmu-$rk1;
	//$rimu=$xi/($f*$rkmu-$rkmup);
	$ri=($rimu*$ril1)/$ril;
	$rip=($rimu*$rip1)/$ril;
	for ($i=1;$i<=$nl;$i++) {
		$rktemp=($xmu+$i)*$xi2*$rk1+$rkmu;
		$rkmu=$rk1;
		$rk1=$rktemp;
	}
	$rkp=$xnu*$xi*$rkmu-$rk1;
	$rk=$rkmu;
	
	// returns only besselK unlike the original void bessik() which edited the set of passed pointer
	return $rk;
}
}















































?>
