<?php

namespace UKMNorge\DesignBundle\Services;

/**
 * Leverer en random streng til nyhetsbrevet.
 *
 */
class RandomTextService {	
	
 	public static $navn = array(
		"Magnus",
		"Jonas",
		"Thomas",
		"Emma",
		"Julie",
		"Marte",
		"Martin",
		"Anna",
		"Mari",
		"Stine",
		"Sara",
		"Markus",
		"Morten",
		"Inger",
		"Linda",
		"Ingeborg",
		"Oskar"
	);

	public static $action = array(
		"juletreklatring (førstemann til stjerna)",
		"pepperkakebaking",
		"kurs i brillepussing",
		"snømåke-workshop",
		"kaffetrakte-kurs",
		"birøkter-kurs",
		"ballongblåse-workshop",
		"skitrehopping",
		"kurs i sparkesykling",
		"boksstabling",
		"hekkeløp",
		"epleslang",
		"granplanting",
		"plenklipping med saks",
		"tapekunst-workshop",
		"introduksjon i rørleggerfaget",
		"VM i hutring",
		"rubiks Kube-battle"
	);

	public static $where = array(
		"i bakgården",
		"på Kimen",
		"i 1001 natt",
		"i bestemorkroken",
		"på soverommet",
		"i kantina",
		"hos sjefen"
	);

	public static function getText() {
		$navnRand = rand(0,sizeof(self::$navn)-1);
		$actionRand = rand(0, sizeof(self::$action)-1);
		$whereRand = rand(0, sizeof(self::$where)-1);
		return self::$navn[$navnRand] . ' inviterer til ' . self::$action[$actionRand] . ' ' . self::$where[$whereRand];
	}
	
	
}
