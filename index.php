<?php

/***********************************************************************
 *
 *	OGame CR Converter v1.0
 *
 * 	Author:  Stephen Eardley
 *  Date:  22/11/2011
 *
 ****/

include( "Report.class.php" );

if( $_GET[ "cr" ] )
{
	$crReport = $_GET[ "cr" ];
}
else
{
    $crReport = file_get_contents( "battle.txt" );
}

// Ok so lets initialise the report by creating a new instance of the Report class
// This will be the reference point for all of the attacker, defender and battle details
$cr = new Report( $crReport );

?>

<html>
<head>
    <title>CR Converter 1.0b</title>
</head>
<body>

<?php

/******************************************
*  Get some basic details
*****/

// When it happened
print $cr->getBattle()->When;
print "<br />";

print "<br />";


/******************************************
*  Get the Attackers starting details
*****/

// Name
print $cr->getAttacker()->Name;
print "<br />";

// Coords
print $cr->getAttacker()->Coords;
print "<br />";

// Attacker Techs
print "Weapons: " . $cr->getAttacker()->Weapons . " Shields: " . $cr->getAttacker()->Shields . " Armour: " . $cr->getAttacker()->Armour;
print "<br />";

// Attacking fleet info
$attFleetStart = $cr->getAttacker()->StartingFleet;

print "<table border=1><tr><th>Type</th><th>Number</th></tr>";
foreach( $attFleetStart as $key => $val )
{
    print "<tr><td>".$key."</td><td>".$val."</td></tr>";
}
print "</table>";

print "<br />";

print "<br />";


/******************************************
*  Get the defenders starting details
*****/

// Name
print $cr->getDefender()->Name;
print "<br />";

// Coords
print $cr->getDefender()->Coords;
print "<br />";

// Defender Techs
print "Weapons: " . $cr->getDefender()->Weapons . " Shields: " . $cr->getDefender()->Shields . " Armour: " . $cr->getDefender()->Armour;
print "<br />";

// Defending fleet info
$defFleetStart = $cr->getDefender()->StartingFleet;

print "<table border=1><tr><th>Type</th><th>Number</th></tr>";
foreach( $defFleetStart as $key => $val )
{
    print "<tr><td>".$key."</td><td>".$val."</td></tr>";
}
print "</table>";

print "<br />";

print "<br />";

////////////////////////////////////////////////

/******************************************
*  The outcome
*****/

// Num rounds
print "The battle lasted for " . $cr->getBattle()->NumRounds . " rounds";
print "<br />";

////////////////////////////////////////////////

/******************************************
*  Attackers fleet after the battle
*****/



$winner = $cr->getBattle()->Winner;
if( $winner == "attacker" )
{
    $winner = $cr->getAttacker()->Name;
}
else
{
    $winner = $cr->getDefender()->Name;
}

print $winner . " has won the battle!";
print "<br />";

print "<br />";
?>
</body>
</html>