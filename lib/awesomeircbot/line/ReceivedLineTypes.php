<?php
/**
 * Received Line Types Class
 * Contains constants for each different
 * type of IRC line which can be received
 *
 * Copyright (c) 2011, Jack Harley
 * All Rights Reserved
 */

namespace awesomeircbot\line;

class ReceivedLineTypes {
	
	/**
	 * The following are all constants corresponding
	 * to the IRC RFC 1459 decimal reference, without
	 * the decimals
	 * For example, in RFC 1459, Section 4.6.2 is PING,
	 * so PING = 462
	 */
	const NICK = 412;
	const QUIT = 416;
	const JOIN = 421;
	const PART = 422;
	const MODE = 423;
	const KICK = 428;
	const PRIVMSG = 4411;
	const CHANMSG = 4412;
	const PING = 462;
	
	
	/**
	 * These are the server reply codes, which all begin with either
	 * 61 - Server error replies
	 * 62 - Server replies to commands
	 * 63 - Reserved numerics
	 * Those 2 numbers are then followed by the reply numeric
	 */
	const SERVERREPLYTHREEONEONE = 62311;
	const SERVERREPLYTHREETHREEZERO = 62330;
	const SERVERREPLYTHREETHREETWO = 62332;
	const SERVERREPLYTHREEZEROSEVEN = 63307;
	const SERVERREPLYTHREEFIVETHREE = 62353;
}
?>
	