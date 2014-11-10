<?php
require("libs/phpmailer/PHPMailerAutoload.php");

require("classes/ValidationResult.class.php");
require("classes/FieldType.class.php");
require("classes/Field.class.php");
require("classes/BaseFormModel.class.php");
require("classes/CvsExporter.class.php");
require("classes/Mailer.class.php");

require('libs/recaptchalib.php');
require('register.config.php');

//Recaptcha
$recaptchaAnswer = recaptcha_check_answer (ReCaptchaKeyring::PRIVATE_KEY,
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);

//variable initialization
$errorMessages = array();
$registrationSuccess = false;
$formValid = (boolean) false;
$formFilled = (boolean) false;

if( $recaptchaAnswer->is_valid )
//if(true)
{
	$formModel = new FormModel();
	$formModel->loadDataFromPost();
	$formValid = (boolean) $formModel->isValid();
	$formFilled = (boolean) $formModel->isFilled();
}else{
	if(!empty($_POST)){
		$errorMessages['recaptcha'] = 'ReCAPTCHA nebola zadaná správne. Skúste znova.';
	}
}

if($formFilled)
{
	if( !$formValid )
	{
		$errorMessages = $formModel->getErrorList();
	}
	else
	{
		try
		{
			$dataModel = $formModel->getFilledModel();
			$dataModel->Lunch = ( ($dataModel->Lunch == 'true') ? "áno" : "nie" );  
			
			$exporter = new CvsExporter( $dataModel );
			$exporter->export(dirname( __FILE__ ).'/export/participants.csv');
			$mailer = new Mailer( $dataModel );
			$mailer->loadTemplate('emails/registration-confirm.html');
			$mailer->SendRegistrationMail('Snowfur 2015: Registrácia.','info@snowfur.info');
			$registrationSuccess = true;
		}
		catch(Exception $ex)
		{
			$errorMessages["exporter"] = $ex->getMessage();
		}
	}
}
?>

<!DOCTYPE HTML>
<html lang="sk">
	<head>
		<title>Snowfur</title>
		<link href="template/style.css" type="text/css" rel="stylesheet"/>
		<link href="template/register.css" type="text/css" rel="stylesheet"/>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	</head>
	<body>
		<div class="MainPageWrapper">
			<header>
				<div class="Logo" id="logo">
					<div class="InnerLogo">
					</div>
					<div class="LogoText">
						Snowfur 2015
					</div>
				</div>
				<nav class ="MainMenu">
					<ul class="MenuList">
						<li class="MenuItem"><a class="MenuLink" href="index.html">Info</a></li>	
						<li class="MenuItem Selected"><a class="MenuLink">Regitrácia</a></li>
						<li class="MenuItem"><a class="MenuLink" href="program.html">Program</a></li>
						<li class="MenuItem"><a class="MenuLink" href="price-list.html">Cenník</a></li>
						<!-- <li class="MenuItem"><a class="MenuLink" href="archive.html">Minulé roky</a></li> -->
					</ul>
				</nav>
			</header>
			<section class="PageContent">
				<div class="PageTitle">
					<h1>Registrácia</h1>
				</div>
				<div class="ContentWrapper">
					<p><b><u>Registračné pravidlá a podmienky:</u></b></p>
					<ol class="RegistrationRules">
						<li>Registrácia prebieha v dvoch fázach. V prvej účastník vyplní registračný formulár, registrácia je ukončená zaslaním platby na účet Snowfuru.</li>
						<li>V prípade nedostatku miesta dostane prednosť ten, kto pošle platbu na náš účet skôr.</li>
						<li>Registrácia prebieha do 16.1.2015, po tomto termíne bude registrácia uzavretá.</li>
						<li>V prípade, že sa zaregistrujete a nembudete sa môcť zúčastniť, čo najskôr informujte organizačný tím. Platby za registrácie je možné vrátiť do 16.1.2015 v plnej výške, po tomto termíne si poskytovateľ ubytovania môže účtovať časť sumy ako penále za zrušenie.</li>
						<li>Ak registráciu predate niekomu inému, dajte nám vedieť.</li>
						<li>V prípade ak chcete vziať so sebou vášho psa, kontaktujte orgatím.</li>
						<li>Organizačný tím Snowfuru si vyhradzuje právo odmietnuť registráciu.</li>
					</ol>
					<script type="text/javascript">
					//<![CDATA[
						function OnLunchCheck(s, e)
						{
				  			document.getElementById('hiLunch').disabled = document.getElementById("chLunch").checked;
						}                        
					//]]>
					</script> 

					<?php if($formFilled) if($registrationSuccess) echo "<h3 class=\"Success\">Registracia úspešná. Skontrolujte svoj e-mail.</h3>"; else echo "<h3 class=\"Error\">Registrácia neúspešná.</h3>"; ?>
					<?php if( count($errorMessages) ) echo "<ul class=\"RegistrationMessageList\">"?>
					<?php 
					foreach ( $errorMessages as $key => $message) 
					{
						echo "<li>".$message."</li>";
					}
					?>
					<?php if( count($errorMessages) ) echo "</ul>"?>
					<form action="register.php" method="post">
						<ul class="RegistrationForm">
							<li><label>Prezyvka:</label>	<input name="Nickname" type="text" maxlength="50" /></li>
							<li><label>Meno:</label>		<input name="FirstName" type="text" maxlength="50" /></li>
							<li><label>Priezvysko:</label>	<input name="LastName" type="text" maxlength="50" /></li>
							<li><label>E-mail:</label>		<input name="Email" type="text" maxlength="50" /></li>
							<li><label>Adresa:</label>		<input name="Address" type="text" maxlength="100" /></li>
							<li><label>Mesto:</label>		<input name="City" type="text" maxlength="50" /></li>
							<li><label>PSČ:</label>			<input name="PostalCode" type="text" maxlength="10" /></li>
							<li><label>Vek:</label>			<input name="Age" type="number" step="1" min="0" max="100"/></li>				
							<li><label>Chcem plnú penziu:</label>	<input id="chLunch" onclick="OnLunchCheck()" name="Lunch" value="true" type="checkbox" /><input id="hiLunch" name="Lunch" value="false" type="hidden" /></li>
							<li class="Recaptcha"><?php echo recaptcha_get_html(ReCaptchaKeyring::PUBLIC_KEY); ?></li>
							<li>&nbsp;</li>
							<li><input type="submit" value="Registruj"></li>
						</ul>
					</form>
				</div>
			</section>
		</div>
		<footer>
			♥2014 - dielo je uverejnené pod licenciou <a href="http://creativecommons.org/licenses/by/3.0/">CC BY 3.0</a>.<br />
			Grafika: Blue Horsewolf, Azshara Kletete.
			Web: Mlpard, Kiraa Corsac.
			Obsah: Greyfur.
		</footer>
	</body>
</html>
