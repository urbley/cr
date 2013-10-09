<?php

include( "Battle.class.php" );
include( "Attacker.class.php" );
include( "Defender.class.php" );

class Report {

	private $attacker;
	private $defender;
	private $battle;

	public function __construct( $cr )
	{
		$this->cr = $cr;
	}

	public function getAttacker()
	{
		// This ensures that there is only 1 attacker object created per report
		if( !$this->attacker )
		{
			$attackerDetails = $this->parseAttacker();
			$this->attacker = new Attacker();

			$this->attacker->Name = $attackerDetails[ "Name" ];
			$this->attacker->Coords = $attackerDetails[ "Coords" ];
			$this->attacker->Weapons = $attackerDetails[ "Weapons" ];
			$this->attacker->Shields = $attackerDetails[ "Shields" ];
			$this->attacker->Armour = $attackerDetails[ "Armour" ];
            $this->attacker->StartingFleet = $attackerDetails[ "StartingFleet" ];
            $this->attacker->EndingFleet = $attackerDetails[ "EndingFleet" ];
		}
		return $this->attacker;
	}

    public function parseAttacker()
	{
		// All of the extracted details for the attacker will be added to the details array
		$details = array();

		// Name
        preg_match( "/Attacker ([[:alnum:][:space:]]+) \(/", $this->cr, $attName );
		$details[ "Name" ] = $attName[1];

		// Coords
		preg_match( "/Attacker [[:alnum:][:space:]]+ \(\[(\d+:\d+:\d+)\]\)/m", $this->cr, $attCoords );
		$details[ "Coords" ] = $attCoords[1];

		// Attacker Techs
		preg_match( "/Attacker[.\s\S]+?Type/", $this->cr, $attTechs );

		preg_match( "/Weapons: (\d{1,3}%+?) /", $attTechs[0], $attWeapons);
		$details[ "Weapons" ] = $attWeapons[1];
		preg_match( "/Shields: (\d{1,3}%+?) /", $attTechs[0], $attShields);
		$details[ "Shields" ] = $attShields[1];
		preg_match( "/Armour: (\d{1,3}%+?)/", $attTechs[0], $attArmour);
		$details[ "Armour" ] = $attArmour[1];

		// Attacking fleet starting info
		preg_match( "/Attacker[.\s\S]+?Type([.\s\S])+?Total/m", $this->cr, $attFleetString );

        // Lets get the fleet types
		preg_match( "/\t([[:word:]\.\s]+)?Total/m", $attFleetString[0], $attFleetTypes );
		$attFleetTypesArray = explode( "\t", $attFleetTypes[1] );

        // Now lets get the total of each type of ship
        preg_match( "/Attacker[.\s\S]+?Total\t([.\s\S\t]+?)Weapons/m", $this->cr, $attFleetTotals );
        $attFleetTotalsArray = explode( "\t", $attFleetTotals[1] );

        // Now lets build up a key pair array of the fleet types and totals
        $x = 0;
        foreach( $attFleetTypesArray as $type )
        {
            $attFleet[$type] = $attFleetTotalsArray[$x];
            $x++;
        }

        $details[ "StartingFleet" ] = $attFleet;

        // Attackers fleet after the battle checking to see if the attacker actually won first.
        if( $this->getBattle()->Winner == "attacker" )
        {
            preg_match( "/Attacker [[:alnum:]\s]+? [[:digit:]:\[\]\(\)]+\x0D\x0AType\t([[:word:]+\t+\.*\s*]+?)\x0D\x0A/m", $this->cr, $attFleetAfterString );
            print_r( $attFleetAfterString );
        }
        else
        {
            $attFleetAfter = $this->getAttacker()->Name . "'s fleet was completely destroyed in the battle.";
            $details[ "EndingFleet" ] = $attFleetAfter;
        }

		return $details;
	}

	public function getDefender()
	{
		if( !$this->defender )
		{
			$defenderDetails = $this->parseDefender();
			$this->defender = new Defender();

			$this->defender->Name = $defenderDetails[ "Name" ];
			$this->defender->Coords = $defenderDetails[ "Coords" ];
			$this->defender->Weapons = $defenderDetails[ "Weapons" ];
			$this->defender->Shields = $defenderDetails[ "Shields" ];
			$this->defender->Armour = $defenderDetails[ "Armour" ];
            $this->defender->StartingFleet = $defenderDetails[ "StartingFleet" ];
		}
		return $this->defender;
	}

	private function parseDefender()
	{
		// All of the extracted details for the defender will be added to the details array
		$details = array();

		// Name
		preg_match( "/Defender ([a-zA-Z0-9 ]+) \(/", $this->cr, $defender );
		$details["Name"] = $defender[1];

		// Defender location
		preg_match( "/Defender [[:alnum:][:space:]]+ \(\[(\d+:\d+:\d+)\]\)/m", $this->cr, $defCoords );
		$details["Coords"] = $defCoords[1];

		// Defender techs
		preg_match( "/Defender[.\s\S]+?Type/", $this->cr, $defTechs );

		preg_match( "/Weapons: (\d{1,3}%+?) /", $defTechs[0], $defWeapons);
		$details["Weapons"] = $defWeapons[1];
		preg_match( "/Shields: (\d{1,3}%+?) /", $defTechs[0], $defShields);
		$details["Shields"] = $defShields[1];
		preg_match( "/Armour: (\d{1,3}%+?)/", $defTechs[0], $defArmour);
		$details["Armour"] = $defArmour[1];

        // Defending fleet starting info
		preg_match( "/Defender[.\s\S]+?Type([.\s\S])+?Total/m", $this->cr, $defFleetString );

        // Lets get the fleet types
		preg_match( "/\t([[:word:]\.\s]+)?Total/m", $defFleetString[0], $defFleetTypes );
		$defFleetTypesArray = explode( "\t", $defFleetTypes[1] );

        // Now lets get the total of each type of ship
        preg_match( "/Defender[.\s\S]+?Total\t([.\s\S\t]+?)Weapons/m", $this->cr, $defFleetTotals );
        $defFleetTotalsArray = explode( "\t", $defFleetTotals[1] );

        // Now lets build up a key pair array of the fleet types and totals
        $x = 0;
        foreach( $defFleetTypesArray as $type )
        {
            $defFleet[$type] = $defFleetTotalsArray[$x];
            $x++;
        }

        $details[ "StartingFleet" ] = $defFleet;

		return $details;
	}

    public function getBattle()
	{
		if( !$this->battle )
		{
			$battleDetails = $this->parseBattle();
			$this->battle = new Battle();

			$this->battle->When = $battleDetails[ "When" ];
            $this->battle->NumRounds = $battleDetails[ "NumRounds" ];
            $this->battle->Winner = $battleDetails[ "Winner" ];
		}
		return $this->battle;
	}

	private function parseBattle()
	{
		// All of the extracted details for the defender will be added to the details array
		$details = array();

        // When did it all happen?
        preg_match( "/^(At.+)::/", $this->cr, $opening );
        $details[ "When" ] = $opening[1];

        // How many rounds were there?
        preg_match_all( "/ fires /", $this->cr, $rounds );
        $details[ "NumRounds" ] = sizeof( $rounds[0] ) / 2;

        // Who won
        preg_match( "/The ([[:word:]]+)? has won the/m", $this->cr, $winner );
        $details[ "Winner" ] = $winner[1];

		return $details;
	}
}