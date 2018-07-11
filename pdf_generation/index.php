<?php

$companyName = 'Etna School';
$companyAdress = '7 rue Grandcoing<br />94200 IVRY-SUR-SEINE';
$companySite = 'www.etna-alternance.net';
$companyTel = '01 44 08 00 22';

$clientName = 'Group IONIS';
$clientAdress = '87BIS rue de Charenton<br />75012 PARIS';
$clientSite = 'www.ionis-group.com';
$clientTel = '01 60 66 60 66';

$factureNumber = '0001';

$projectName = 'Création de votre site Internet eCommerce';

$htmlpageheader = '<htmlpageheader name="myheader">
<table width="100%"><tr>
<td width="50%" style="color:#0000BB; "><span style="font-weight: bold; font-size: 14pt;">'.$companyName.'</span><br />'.$companyAdress
.'<br />'. $companySite .'<br /><span style="font-family:dejavusanscondensed;">&#9742;</span>'. $companyTel .'</td>
<td width="50%" style="text-align: right;">Facture No.<br /><span style="font-weight: bold; font-size: 12pt;">'. $factureNumber .'</span></td>
</tr></table>
</htmlpageheader>';

$htmlpagefooter = '<htmlpagefooter name="myfooter">
<div style="border-top: 1px solid #000000; font-size: 9pt; text-align: center; padding-top: 3mm; ">
Page {PAGENO} of {nb}
</div>
</htmlpagefooter>';

$date_time = new DateTime();
$intl_date_formatter = new IntlDateFormatter('fr_FR',IntlDateFormatter::FULL,IntlDateFormatter::NONE);
$date =  $intl_date_formatter->format($date_time);

$money = ' &euro;';
$workUnit = ' j/h';

$element1 = 'Création de la charte graphique pour le web<br />Traitement des images fournies et adaptation.';
$element1Quantity = 1;
$element1UnitPrice = 1200;
$element1FinalPrice = $element1Quantity * $element1UnitPrice;

$element2 = 'Déclinaison de la charte dans les 2 versions de la langue (anglais / allemand).';
$element2Quantity = 2;
$element2UnitPrice = 250;
$element2FinalPrice = $element2Quantity * $element2UnitPrice;

$element3 = 'Intégration dans le site (3 versions de langue)';
$element3Quantity = 4;
$element3UnitPrice = 250;
$element3FinalPrice = $element3Quantity * $element3UnitPrice;

$element4 = 'Développement des pages, intégration des contenus, mis en page des rubriques statiques';
$element4Quantity = 37;
$element4UnitPrice = 100;
$element4FinalPrice = $element4Quantity * $element4UnitPrice;

$element5 = 'Forum de type PHPBB';
$element5Quantity = 1;
$element5UnitPrice = 250;
$element5FinalPrice = $element5Quantity * $element5UnitPrice;

$element6 = 'Espaces membres';
$element6Quantity = 2;
$element6UnitPrice = 250;
$element6FinalPrice = $element6Quantity * $element6UnitPrice;

$element7 = 'Module actualités';
$element7Quantity = 1;
$element7UnitPrice = 300;
$element7FinalPrice = $element7Quantity * $element7UnitPrice;

$element8 = 'Gestion des abonnements et désabonnements automatiques<br /> Interface pour la création des messages.';
$element8Quantity = 1;
$element8UnitPrice = 300;
$element8FinalPrice = $element8Quantity * $element8UnitPrice;

$element9 = 'Intégration des contenus en version anglaise et allemande';
$element9Quantity = 6;
$element9UnitPrice = 250;
$element9FinalPrice = $element9Quantity * $element9UnitPrice;

$element10 = '720 Plan';
$element10Quantity = 1;
$element10UnitPrice = 220;
$element10FinalPrice = $element10Quantity * $element10UnitPrice;

$element11 = 'Optimisation des pages pour une bonne visibilité des moteurs de recherche <br />
Inscription manuelle du site dans les principaux moteurs de recherche <br />
Inscription dans les annuaires thématiques gratuit ou Allo Pass.';
$element11Quantity = 3;
$element11UnitPrice = 200;
$element11FinalPrice = $element11Quantity * $element11UnitPrice;

$element = $element1FinalPrice + $element2FinalPrice + $element3FinalPrice + $element4FinalPrice + $element5FinalPrice + $element6FinalPrice + 
$element7FinalPrice + $element8FinalPrice + $element9FinalPrice + $element10FinalPrice + $element11FinalPrice;

$elementTva = $element * 0.2;

$elementFinal = $element - $elementTva;

$html = '
<html>
<head>
</head>
<body>'.
'<!--mpdf'.$htmlpageheader.$htmlpagefooter.'

<sethtmlpageheader name="myheader" value="on" show-this-page="1" />
<sethtmlpagefooter name="myfooter" value="on" />
mpdf-->

<div style="text-align: right">Date: '.$date.'</div><br />

<table class="client" cellpadding="10"><tr>
<td width="100%" style="border: 0.1mm solid #888888; "><span style="font-size: 10pt; color: #555555; font-family: sans;">CLIENT :
</span><br />'.$clientName.'<br />'.$clientAdress.'<br />'.$clientSite.'<br />'.$clientTel.'</td>
</tr></table>
<br />

<p class="projectName">Projet : '.$projectName.' </p><br />

<table class="items" cellpadding="10">
<thead>
<tr>
<td width="15%">Quantite</td>
<td width="50%">Description</td>
<td width="15%">Prix Unit HT</td>
<td width="20%">Prix Total HT</td>
</tr>
</thead>
<tbody>
<!-- ITEMS HERE -->
<tr>
<td align="center"></td>
<td class="info">Charte graphique</td>
<td class="cost"></td>
<td class="cost"></td>
</tr>
<tr>
<td align="center">'.$element1Quantity.'</td>
<td>'.$element1.'</td>
<td class="cost">'.$element1UnitPrice.$money.'</td>
<td class="cost">'.$element1FinalPrice.$money.'</td>
</tr>
<tr>
<td align="center">'.$element2Quantity.$workUnit.'</td>
<td>'.$element2.'</td>
<td class="cost">'.$element2UnitPrice.$money.'</td>
<td class="cost">'.$element2FinalPrice.$money.'</td>
</tr>
<tr>
<td align="center">'.$element3Quantity.$workUnit.'</td>
<td>'.$element3.'</td>
<td class="cost">'.$element3UnitPrice.$money.'</td>
<td class="cost">'.$element3FinalPrice.$money.'</td>
</tr>
<tr>
<td align="center"></td>
<td class="info">Les rubriques statiques</td>
<td class="cost"></td>
<td class="cost"></td>
</tr>
<tr>
<td align="center">'.$element4Quantity.'</td>
<td>'.$element4.'</td>
<td class="cost">'.$element4UnitPrice.$money.'</td>
<td class="cost">'.$element4FinalPrice.$money.'</td>
</tr>
<tr>
<td align="center"></td>
<td class="info">Modules spécifiques</td>
<td class="cost"></td>
<td class="cost"></td>
</tr>
<tr>
<td align="center">'.$element5Quantity.$workUnit.'</td>
<td>'.$element5.'</td>
<td class="cost">'.$element5UnitPrice.$money.'</td>
<td class="cost">'.$element5FinalPrice.$money.'</td>
</tr>
<tr>
<td align="center">'.$element6Quantity.$workUnit.'</td>
<td>'.$element6.'</td>
<td class="cost">'.$element6UnitPrice.$money.'</td>
<td class="cost">'.$element6FinalPrice.$money.'</td>
</tr>
<tr>
<td align="center"></td>
<td class="info">Autres modules en gestion autonome</td>
<td class="cost"></td>
<td class="cost"></td>
</tr>
<tr>
<td align="center">'.$element7Quantity.'</td>
<td>'.$element7.'</td>
<td class="cost">'.$element7UnitPrice.$money.'</td>
<td class="cost">'.$element7FinalPrice.$money.'</td>
</tr>
<tr>
<td align="center"></td>
<td class="info">Module Newsletter</td>
<td class="cost"></td>
<td class="cost"></td>
</tr>
<tr>
<td align="center">'.$element8Quantity.$workUnit.'</td>
<td>'.$element8.'</td>
<td class="cost">'.$element8UnitPrice.$money.'</td>
<td class="cost">'.$element8FinalPrice.$money.'</td>
</tr>
<tr>
<td align="center"></td>
<td class="info">Version anglaise / allemande</td>
<td class="cost"></td>
<td class="cost"></td>
</tr>
<tr>
<td align="center">'.$element9Quantity.$workUnit.'</td>
<td>'.$element9.'</td>
<td class="cost">'.$element9UnitPrice.$money.'</td>
<td class="cost">'.$element9FinalPrice.$money.'</td>
</tr>
<tr>
<td align="center"></td>
<td class="info">Hébergement du site</td>
<td class="cost"></td>
<td class="cost"></td>
</tr>
<tr>
<td align="center">'.$element10Quantity.'</td>
<td>'.$element10.'</td>
<td class="cost">'.$element10UnitPrice.$money.'</td>
<td class="cost">'.$element10FinalPrice.$money.'</td>
</tr>
<tr>
<td align="center"></td>
<td class="info">Optimisation pour le référencement</td>
<td class="cost"></td>
<td class="cost"></td>
</tr>
<tr>
<td align="center">'.$element11Quantity.'</td>
<td>'.$element11.'</td>
<td class="cost">'.$element11UnitPrice.$money.'</td>
<td class="cost">'.$element11FinalPrice.$money.'</td>
</tr>
<!-- END ITEMS HERE -->
<tr>
<td class="blanktotal" colspan="3" rowspan="3"></td>
<td class="totals">Sous total:</td>
<td class="totals cost">'.$element.$money.'</td>
</tr>
<tr>
<td class="totals">Tax 20% :</td>
<td class="totals cost">'.$elementTva.$money.'</td>
</tr>
<tr>
<td class="totals"><b>TOTAL:</b></td>
<td class="totals cost"><b>'.$elementFinal.$money.'</b></td>
</tr>
</tbody>
</table>

</body>
</html>
';
$path = (getenv('MPDF_ROOT')) ? getenv('MPDF_ROOT') : __DIR__;
require_once $path . '/vendor/autoload.php';
$mpdf = new \Mpdf\Mpdf([
	'margin_left' => 20,
	'margin_right' => 15,
	'margin_top' => 40,
	'margin_bottom' => 25,
	'margin_header' => 10,
	'margin_footer' => 10
]);
$stylesheet = file_get_contents('styles.css');

$mpdf->SetProtection(array('print'));
$mpdf->SetTitle("Facture BillandGo");
$mpdf->SetAuthor("BillandGo.");
$mpdf->SetWatermarkText("Paid");
$mpdf->showWatermarkText = true;
$mpdf->watermark_font = 'DejaVuSansCondensed';
$mpdf->watermarkTextAlpha = 0.1;
$mpdf->SetDisplayMode('fullpage');

$mpdf->WriteHTML($stylesheet,1);
$mpdf->WriteHTML($html,0);
$mpdf->Output();