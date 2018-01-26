<!DOCTYPE html>
<html>
<head>
	<title>	Radio Emission Star fOrmation Rate </title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="style.css" type="text/css">
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
</head>
<body>

<!--FOOTER-->
<span style="display:block; height: 50px;"></span>

<?php
$absolute_footer_files = array("contact.php"); //Files which need the footer position:absolute

if (in_array(basename($_SERVER['PHP_SELF']),$absolute_footer_files)){
	echo '<footer style="position:absolute; bottom: 0;">';
}else{
	echo '<footer>';
}
?>
<adress>
<p style="center-page"> Written by Joaquin Zamponi F. <img class="icon icons8-English-Mustache-Filled" width="35" height="35" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAABKElEQVRoge2W23GDMBRETwmUoBJcAp1EHcSdmA7iEujAdBB3IDrAHSQfuRrLxEYmCNsz2TNzf/RY7cLVAAghhBBCCCGEECVxwAcwAF9AAHZAlaypbCzYmsH2uAf6nGTDOcC4gs1PrRls/qnU3DaYhgmZNYNpFTE0F58x95fyS70HLvt5itjrpUPEGt+tnJeQDvTA5x0CNfk2KVGBfJdU5rlPB1vOvfrO5eVzwBtweECAcR3sbJf42ZjHeDfbNIh/gslS5dMgFXB6AVNz68SV67Bd6bDeag3t7ThEZL/CE4sfxNJvfH8rRKQpdFDH71+UrpB2kwsRqRcc2jH9YfMLtet7Q6Q4fvqwBY5XhI8m3phBN1Pb295uQr81D3O0hRBCCCGEEEL8W74BuF6f2AG9U9MAAAAASUVORK5CYII="> </p>
</adress>
</footer>

</body>
</html>
