// Attempt to translate bessik() and their nested functions from "Numerical Recipes
// in C" to JavaScript functions. Problems may be encountered because many C implementations
// make use of pointer which in here are treated only as variables.
function bessik(x,xnu){
	var lEPS=1e-16;
	var FPMIN=1e-30;
	var MAXIT=10000;
	var XMIN=2;	

	var ri=rk=rip=rkp=0;

	function chebev(a,b,c,m,x){
		var j=d=dd=sv=y=y2=0;
		if ((x-a)*(x-b) > 0.0){ return 0;}//"x not in range in routine chebev"; }
		y2=2.*(y=(2.*x-a-b)/(b-a));
		for (j=m-1; j>=1; j--){
			sv=d;
			d=y2*d-dd+c[j];
			dd=sv;
		}
		return y*d-dd+0.5*c[0];
	}

	//int	
	var i=l=nl=0;
	//doubles
	var a1=b=c=d=del=del1=delh=dels=e=f=fact=fact2=ff=gam1=gam2=0;
	var gammi=gampl=h=p=pimu=q=q1=q2=qnew=ril=ril1=rimu=rip1=ripl=0;
	var ritemp=rk1=rkmu=rkmup=rktemp=s=sum=sum1=x2=xi=xi2=xmu=xmu2=0;

	if (x <= 0.0 || xnu < 0.0){ return 0;}//"bad arguments in bessik"; }
	nl=(xnu+0.5);
	xmu=xnu-nl;
	xmu2=xmu*xmu;
	xi=1.0/x;
	xi2=2.0*xi;
	h=xnu*xi;
	if (h < FPMIN){ h=FPMIN; }
	b=xi2*xnu;
	d=0.0;
	c=h;
	for (i=1;i<=MAXIT;i++) {
		b += xi2;
		d=1.0/(b+d);
		c=b+1.0/c;
		del=c*d;
		h=del*h;
		if (Math.abs(del-1.0) < lEPS){ break; }
	}
	if (i > MAXIT) return 0;//"x too large in bessik; try asymptotic expansion";
	ril=FPMIN;
	ripl=h*ril;
	ril1=ril;
	rip1=ripl;
	fact=xnu*xi;
	for (l=nl;l>=1;l--) {
		ritemp=fact*ril+ripl;
		fact -= xi;
		ripl=fact*ritemp+ril;
		ril=ritemp;
	}
	f=ripl/ril;
	if (x < XMIN) {
		x2=0.5*x;
		pimu=Math.PI*xmu;
		fact = (Math.abs(pimu) < lEPS ? 1.0 : pimu/Math.sin(pimu));
		d = -Math.log(x2);
		e=xmu*d;
		fact2 = (Math.abs(e) < lEPS ? 1.0 : math.sinh(e)/e);
//		beschb(xmu,gam1,gam2,gampl,gammi);
		// function beschb(){	
			// content of beschb() void function, used in main program because beschb() doesnt
			// return anything, making complicated for js to pass variables outwards	
			xx=0;
			c1 = [1.142022680371172e0,6.516511267076e-3,
			3.08709017308e-4,-3.470626964e-6,6.943764e-9,
			3.6780e-11,-1.36e-13];
			c2 = [1.843740587300906e0,-0.076852840844786e0,
			1.271927136655e-3,-4.971736704e-6,-3.3126120e-8,
			2.42310e-10,-1.70e-13,-1.0e-15];

			var NUSE=5;	
			xx=8.0*x*x-1.0;
			gam1=chebev(-1.0,1.0,c1,NUSE,xx);
			gam2=chebev(-1.0,1.0,c2,NUSE,xx);
			gampl=gam2-x*(gam1);
			gammi=gam2+x*(gam1);
//			document.write('gam1: '+gam1+'<br>');
//			document.write('gam2: '+gam2+'<br>');
		//}
		ff=fact*(gam1*math.cosh(e)+gam2*fact2*d);
		sum=ff;
		e=Math.exp(e);
		p=0.5*e/gampl;
		q=0.5/(e*gammi);
		c=1.0;
		d=x2*x2;
		sum1=p;
		for (i=1;i<=MAXIT;i++) {
			ff=(i*ff+p+q)/(i*i-xmu2);
			c *= (d/i);
			p /= (i-xmu);
			q /= (i+xmu);
			del=c*ff;
			sum += del;
			del1=c*(p-i*ff);
			sum1 += del1;
			if (Math.abs(del) < Math.abs(sum)*lEPS){ break; }
		}
		if (i > MAXIT)  return 0;//"bessk series failed to converge";
		rkmu=sum;
		rk1=sum1*xi2;
	} else {
		b=2.0*(1.0+x);
		d=1.0/b;
		h=delh=d;
		q1=0.0;
		q2=1.0;
		a1=0.25-xmu2;
		q=c=a1;
		a = -a1;
		s=1.0+q*delh;
		for (i=2;i<=MAXIT;i++) {
			a -= 2*(i-1);
			c = -a*c/i;
			qnew=(q1-b*q2)/a;
			q1=q2;
			q2=qnew;
			q += c*qnew;
			b += 2.0;
			d=1.0/(b+a*d);
			delh=(b*d-1.0)*delh;
			h += delh;
			dels=q*delh;
			s += dels;
			if (Math.abs(dels/s) < lEPS){ break; }
		}
		if (i > MAXIT){ return 0;}//"bessik: failure to converge in cf2"; }
		h=a1*h;
		rkmu=Math.sqrt(Math.PI/(2.0*x))*Math.exp(-x)/s;
		rk1=rkmu*(xmu+x+0.5-h)*xi;
	}
	rkmup=xmu*xi*rkmu-rk1;
	//rimu=xi/(f*rkmu-rkmup);
	ri=(rimu*ril1)/ril;
	rip=(rimu*rip1)/ril;
	for (i=1;i<=nl;i++) {
		rktemp=(xmu+i)*xi2*rk1+rkmu;
		rkmu=rk1;
		rk1=rktemp;
	}
	rkp=xnu*xi*rkmu-rk1;
	rk=rkmu;
	
	// returns only besselK unlike the original void bessik() which edited the set of passed pointer
	return rk;
}
