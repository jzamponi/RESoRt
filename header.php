<header>
    <h2>
		<div class="HeaderLogo-resort">
		    <a href="index.php" title="Radio Emission Star fOrmation Rate">
		            <img src="img/RESoRt.png" alt="Logo unavailable" width="200px" height="90px">
		    </a> 
		</div>
		<div class="HeaderLogo-institutions">
		    <a href="http://www.astro.udec.cl" target="_blank" title="Astronomy Department at Universidad de Concepci&oacute;n">
		            <img src="img/astroudec.jpg" alt="Astroudec logo unavailable" width="200px" heigth="90px">
		    </a>
		    <a href="http://www.nordita.org" target="_blank" title="Nordic Institute for Theoretical Physics">
		            <img src="img/nordita_logo.png" alt="Nordita logo unavailable" width="200px" height="65px">
		    </a>
		</div>
	</h2>

</header>

<?php
switch (basename($_SERVER['PHP_SELF'])) {
	case "index.php":
		$active_stat_in='class="active"';
		$active_stat_th='';
		$active_stat_gl='';
		$active_stat_co='';
		break;
	case "theory.php":
		$active_stat_in='';
		$active_stat_th='class="active"';
		$active_stat_gl='';
		$active_stat_co='';
		break;
	case "glossary.php":
		$active_stat_in='';
		$active_stat_th='';
		$active_stat_gl='class="active"';
		$active_stat_co='';
		break;
	case "contact.php":
		$active_stat_in='';
		$active_stat_th='';
		$active_stat_gl='';
		$active_stat_co='class="active"';
		break;
}

?>

<ul class="topnav">
	<li>
		<a <?php echo $active_stat_in;?> href="index.php" title="Calculator"><i class="fa fa-home"></i> Web module</a>
	</li>
	<li style="float:left">
		<a <?php echo $active_stat_th;?> href="theory.php" title="Description of the theoretical framweork"><i class="fa fa-desktop"></i> Theory</a>
	</li>
	<li style="float:left">
		<a <?php echo $active_stat_gl;?>  href="glossary.php" title="Definition of all concepts involved"><i class="fa fa-book"></i> Glossary</a>
	</li>
	<li style="float:left">
		<span style="display:inline-block;width:200px;">
	<li class="question"><a onclick="document.getElementById('HowTo').style.display='block'"> How to use this <i class="fa fa-question-circle" aria-hidden="true"></i></a>
			<div id="HowTo" class="modal"><form class="modal-content animate">
				<div class="imgcontainer">
					<span onclick="document.getElementById('HowTo').style.display='none'" class="close" title="Close">&times;</span>
				</div>
				<div class="container">
				<?php	include_once("howto.html");	//Include the readme file for how to use the calculator	?>	
				</div></form></div>
				<script>
				window.onclick = function(event) {
					if (event.target == document.getElementById('HowTo')) {
						document.getElementById('HowTo').style.display = 'none';
					}
				}
				</script>
	</li>
	<li style="float:right">
		<a <?php echo $active_stat_co;?> href="contact.php" title="Contact authors and web developer">Contact <i class="fa fa-envelope"></i></a>
	</li>
</ul>
