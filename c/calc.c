#include <stdio.h>
#include <string.h>
#include <stdlib.h>
#include <math.h>
#include <unistd.h>
#include "nrutil.h"
#include "nrutil.c"
#include "bessik.c"
#include "beschb.c"
#include "chebev.c"

#define CYN   "\x1B[36m"
#define RESET "\x1B[0m"

//PHYSICAL CONSTANTS
#define	PI	3.1415926535897932384626433832795028841971693993
#define	M	1e6
#define	mu	1e-6
#define	k	1.380658e-16
#define h	6.6260755e-27
#define	c	2.998e10
#define	me	9.1093897e-28
#define	mp	1.675e-24
#define	b	5.878933e10
#define	e	4.8032068e-10
#define	r0	(e*e)/(me*(c*c))
#define	Lsun	3.839e33
#define	pc	3.086e18
#define	H0	(67.11e5)/(M*pc)
#define	sigmaM	0.3175
#define	eV	1.60217657e-12
#define	Msun	1.989e33
#define	yr	365.25*24*60*60
#define	G	6.67259e-8
#define	sigmaT	(8./3.)*PI*(r0*r0)
#define	Jy	1e-23
//COSMIC RAY PARAMETERS
#define	Fcal	0.4
#define	Fsec	0.7
#define	Eps	0.1
#define	Esn	1e51
#define	gammae0	1e7*(eV/(me*(c*c)))
#define	gammap0	1e9*(eV/(me*(c*c)))

int main(int argc, char **argv){

	double Chi;	//not defined as constant so it can be given by PHP input 


	/* GALAXY MODEL */
	double NSNrate(double sfr){
		return 0.156*(sfr/(12.26*Msun));
	}
	double uISRF(double uint, double z){
		return uint+(((8.0*(k*k*k*k)*(PI*PI*PI*PI*PI))/(15.*(c*c*c)*(h*h*h)))*(2.73*2.73*2.73*2.73)*((1+z)*(1+z)*(1+z)*(1+z)));
	}
	//Energy loses timescales
	double tsynch(double B,double gamma){
		return  (3*me*c)/(4*sigmaT*((B*B)/(8*PI))*gamma);
	}
	double tIC(double gamma,double uint,double z){
		return  (3*me*c)/(4*sigmaT*uISRF(uint,z)*gamma);
	}
	double tion(double gamma,double n){
		return gamma/(2.7*c*sigmaT*(6.85+0.5*log(gamma))*n);
	}
	double tbrems(double n){
		return 104e3*yr*(1/(n/300));
	}
	double twind(double Lion,double vwind){
		return (Lion/vwind);
	}
	double t(double B,double gamma,double Lion,double n,double uint,double vwind,double z){
		return 1/( (1/tsynch(B,gamma))+(1/tIC(gamma,uint,z))+(1/tion(gamma,n))+(1/tbrems(n))+(1/twind(Lion,vwind)) );
	}

	//Injection spectrum of cosmic ray protons
	double k0(double sfr){
		return (Eps*Esn*NSNrate(sfr)*(Chi-2))/(pow(gammap0,2.0-Chi)*(mp*(c*c)*mp*(c*c)));
	}
	double Qp(double gammap, double sfr){
		return k0(sfr)*pow(gammap,-1.0*Chi);
	}

	//Spectra in steady state
	double Ne(double B,double gamma,double Lion, double n, double sfr, double uint, double vwind, double z){
		return (((20*20)*Fcal)/(6*(Chi-1)*Fsec))*k0(sfr)*pow((20*gamma*me)/mp,-1*Chi)*t(B,gamma,Lion,n,uint,vwind,z)*me*(c*c);
	}
	double tpion(double n){
		return (50e6*yr)/n;
	}
	double Np(double gamma, double n, double sfr){
		return Fcal*tpion(n)*k0(sfr)*pow(gamma,-1.0*Chi)*mp*(c*c);
	}


	/* RADIO EMISSION */
	//synchrotron spectrum (for power-law distributed CR electrons)
	double nuc(double B, double gamma){
		return (3*e*B*(gamma*gamma))/(4*PI*me*c);
	}
	double Pitch(){
		return (2*pow(PI,3./2.)*tgamma((5.+Chi)/4.0))/(tgamma((7.+Chi)/4.0));	//tgamma(x) = Gamma(x)
	}



	/*INTEGRAL NEEDED FUNCTIONS*/
	long long int N;    //outer loop maxit
	long long int xend; //inner loop maxit
	long int nsteps;
	N = 6e18;	//ideal value	|	always even
	xend = 1.075e2;	//ideal value
	nsteps = 1e3;	//accuracy

	double integrand1(double x){
		double ri,besselk,rip,rkp; //for bessik call
		bessik(x,5/3.,&ri,&besselk,&rip,&rkp);
		//printf("x\t%lf\n",x);
		return	besselk;
	}
	//Simpson's rule for numerical integral
	double simpson1(double x0, double xf){
		int i;
		double hh, S, xi;
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
	double integrand2(double gamma, double B, double Lion, double n, double sfr, double uint, double vwind, double z, double nu){
		double Lnu,cst;
		cst=(sqrt(3.)*(e*e*e)*B)/(me*(c*c));
		Lnu=simpson1(nu/nuc(B,gamma),xend);
		return cst*(nu/nuc(B,gamma))*Lnu*Ne(B,gamma,Lion,n,sfr,uint,vwind,z);
	}
	double simpson2(double x0, double xf, double B, double Lion, double n, double sfr, double uint, double vwind, double z, double nu){
		int i;
		double hh, S, xi;
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

	double Lsynchnu(double B,double Lion,double n,double sfr,double uint,double vwind,double z,double nu) {
		double Lsynchnu;
		Lsynchnu = simpson2(gammae0,N, B,Lion,n,sfr,uint,vwind,z,nu);	
		Lsynchnu*=Pitch();	
		return Lsynchnu;
	}


	//Free-free contribution
	double em(double ffill, double fion, double Lion, double n){
		return (fion*n*fion*n)*(Lion/ffill);
	}
	double tff(double ffill, double fion, double Lion, double n, double Te, double nu){
		double opdepth, upperlim;
		opdepth = (3.28e-7)*pow(Te*(1e-4),-1.35)*pow(nu*(1e-9),-2.1)*(em(ffill,fion,Lion,n)/pc);
		upperlim = 50;
		return (opdepth > upperlim ? upperlim : opdepth);
	}
	double nucritical(double ffill, double fion, double Lion, double n, double Te){
		return (1e9)*pow((3.28e-7)*pow(Te/(1e4),-1.35)*(em(ffill,fion,Lion,n)/pc),1/2.1);
	}
	double Lffnu(double Sarea, double ffill, double fion, double Lion, double n, double Te, double nu){
		return 2*k*Te*(1-exp(-tff(ffill,fion,Lion,n,Te,nu)))*(Sarea/(c*c))*(nu*nu);
	}
	double Lsynchffnu(double B,double ffill,double fion,double Lion,double sfr,double n,double Te,double uint,double vwind, double z, double nu){
		return Lsynchnu(B,Lion,n,sfr,uint,vwind,z,nu)*exp(-tff(ffill,fion,Lion,n,Te,nu));
	}


	/* COMPUTING LUMINOSITIES */
	double Lnuobs(double B, double Sarea, double ffill, double fion, double Lion, double sfr, double n, double Te, double uint, double vwind, double z, double nu){
		return nu*( Lsynchffnu(B,ffill,fion,Lion,sfr,n,Te,uint,vwind,z,(1.+z)*nu) + Lffnu(Sarea,ffill,fion,Lion,n,Te,(1.+z)*nu) );
	}


	/* COMPUTING STAR FORMATION RATES */
	double ComputeSFR(double B,double Sarea,double ffill,double fion,double Lion,double n,double Te,double uint,double vwind,double z,double d,double nu,double flux, double SFRmin, double SFRmax,double epsilon){
		double SFRlow,SFRhigh,SFRmid,SFRin,Lin,Lmin,Lmax,Lmid;
	
		if (flux==0){
			return 0;
		}

		Lin=(nu*flux)*(4*PI*(d*d));

		Lmin = Lnuobs(B,Sarea,ffill,fion,Lion,SFRmin,n,Te,uint,vwind,z,nu);
		Lmax = Lnuobs(B,Sarea,ffill,fion,Lion,SFRmax,n,Te,uint,vwind,z,nu);

		epsilon*=Lin;	//% of Lin (accuracy)
		SFRlow=SFRmin;			
		SFRhigh=SFRmax;
		while(1){
			SFRmid=exp(log(SFRlow)+(log(SFRhigh)-log(SFRlow))/2);
			Lmid=Lnuobs(B,Sarea,ffill,fion,Lion,SFRmid,n,Te,uint,vwind,z,nu);
/*
			printf(CYN "epsilon\t%e\n" RESET,epsilon);
			printf("SFRlow\t%e\t",SFRlow);
			printf("Lin\t%e\n",Lin);
			printf("SFRhigh\t%e\t", SFRhigh);
			printf("Lmid\t%e\n",Lmid);
			printf("SFRmid\t%e\n",SFRmid);
			printf(CYN "|Lmid-Lin|\t%e\n\n" RESET,fabs(Lmid-Lin));
*/
			if ( fabs(Lmid-Lin) <= epsilon ){
				break;
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


/*********************************************************** MAIN **********************************************************/


	char *gal=NULL,*CalcMode=NULL;
	double n,Te,Lion,fion,ffill,B,uint,vwind,Sarea,sfr,z,d,nu,flux,sfrmin,sfrmax,epsilon;

	//RETRIEVING GALAXY PROPERTIES
	/* ARGPARSE */
	// ALL UNITS MUST BE IN CGS SYSTEM
	int opt;
	while ((opt = getopt(argc,argv,"m:s:n:T:L:f:F:B:u:V:a:S:z:d:v:C:x:e:j:k:")) != -1){
		switch(opt){
		case 'm':
			CalcMode = optarg;
			break;
		case 's':
			gal = optarg;
			break;
		case 'n':
			n = (double)atof(optarg);
			break;
		case 'T':
			Te = (double)atof(optarg);
			break;
		case 'L':
			Lion = (double)atof(optarg);
			Lion*=pc;	//convert to cm because it is given in pc
			break;
		case 'f':
			fion = (double)atof(optarg);
			break;
		case 'F':
			ffill = (double)atof(optarg);
			break;
		case 'B':
			B = (double)atof(optarg);
			B*=mu;	//convert to G because it is received in µG
			break;
		case 'u':
			uint = (double)atof(optarg);
			break;
		case 'V':
			vwind = (double)atof(optarg);
			vwind*=1e5;	//convert to cm/s because it is received in km/s
			break;
		case 'a':
			Sarea = (double)atof(optarg);
			Sarea*=M;					//convert to pc² because it is received in kpc²
			Sarea*=9.52142254852e36;	//convert to cm² because it is received in pc²
			break;
		case 'S':
			sfr = (double)atof(optarg);
			sfr*=(Msun/yr);	//convert to g/s because it is received in (Msun/yr)
			break;
		case 'z':
			z = (double)atof(optarg);
			break;
		case 'd':	
			d = (double)atof(optarg);
			d*=M;	//convert to pc because it is given in Mpc
			d*=pc;	//convert to cm because it is given in pc
			break;
		case 'v':
			nu = (double)atof(optarg);
			nu*=M; // convert to Hz because it is received in MHz
			break;
		case 'C':
			Chi = (double)atof(optarg);
			break;
		case 'x':
			flux = (double)atof(optarg);
			flux*=Jy; // convert to [erg s^-1 cm^-2 Hz^-1] because it is received in Jy
			break;
		case 'j':
			sfrmin = (double)atof(optarg);
			sfrmin*=(Msun/yr);	// convert to g/s because it is received in (Msun/yr)
			break;
		case 'k':
			sfrmax = (double)atof(optarg);
			sfrmax*=(Msun/yr);	// convert to g/s because it is received in (Msun/yr)
			break;
		case 'e':
			epsilon = (double)atof(optarg);
			epsilon/=100;	//convert to decimal
			break;
		default:
			fprintf(stderr, "Usage %s [-m mode] [-s source] [-n density] [-T temp] [-L Lion] [-f fion] [-F ffill] [-B Bfield] [-u radfield] [-V galwinds] [-a Sarea] [-S sfr] ] [-z redshift] [-d distance] [-v nu] [-C Chi] [-x flux] [-j lowersfr] [-k uppersfr] [-e accuracy]\n", argv[0]);
			exit(EXIT_FAILURE);
		}
	}

	//Calculator Mode parser
	if ( strcmp(CalcMode, "Flux") == 0 )
	{
		printf("%10.7lf\n",Lnuobs(B,Sarea,ffill,fion,Lion,sfr,n,Te,uint,vwind,z,nu)/Lsun);
	}

	else if( strcmp(CalcMode, "SFR") == 0 )
	{
		printf("%6.2lf\n",ComputeSFR(B,Sarea,ffill,fion,Lion,n,Te,uint,vwind,z,d,nu,flux,sfrmin,sfrmax,epsilon));
	}	


	return 0;
}

