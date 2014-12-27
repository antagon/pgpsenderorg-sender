<?php
/*
 * Copyright (c) 2013, PGPSender.org
 */

class Random
{
	public static function read ($length_b)
	{
		$fd = fopen ("/dev/urandom", "rb");

		if ( $fd === false )
			return false;

		$data = fread ($fd, $length_b);

		fclose ($fd);

		return bin2hex ($data);
	}
};

?>

