<?php
/*
 * Copyright (c) 2013, PGPSender.org
 */

class Keychain {
	
	const TABLE = "keychain";
	
	private $db;
	
	public function __construct ($file)
	{
		$sql = "CREATE TABLE IF NOT EXISTS ".$this::TABLE." (
						recipient varchar (32),
						sender varchar (32),
						key text);";
		$this->db = new SQLite3 ($file);
		$this->db->exec ($sql);
	}
	
	public function __destruct ()
	{
		$this->db->close ();
	}
	
	public function insert ($recipient, $sender, $key)
	{
		if ( $this->exists ($recipient, $sender) ){
			$sql = "UPDATE ".$this::TABLE." SET key = '".$this->db->escapeString (trim ($key))."'
						WHERE '".$this->db->escapeString ($recipient)."' AND '".$this->db->escapeString ($sender)."';";
		} else {		
			$sql = "INSERT INTO ".$this::TABLE." VALUES (
							'".$this->db->escapeString ($recipient)."',
							'".$this->db->escapeString ($sender)."',
							'".$this->db->escapeString (trim ($key))."'
			);";
		}
		
		return $this->db->exec ($sql);
	}
	
	public function get ($recipient, $sender)
	{
		$sql = "SELECT key FROM ".$this::TABLE." WHERE recipient = '".$this->db->escapeString ($recipient)."' AND sender = '".$this->db->escapeString ($sender)."' LIMIT 1;";
		$res = $this->db->query ($sql);
		
		if ( $res === false )
			return false;
		
		return $res->fetchArray (SQLITE3_ASSOC);
	}
	
	private function exists ($recipient, $sender)
	{
		$sql = "SELECT rowid FROM ".$this::TABLE."
					WHERE '".$this->db->escapeString ($recipient)."' AND '".$this->db->escapeString ($sender)."';";

		$res = $this->db->query ($sql);
		
		if ( $res === false )
			return false;
		
		return ($res->fetchArray (SQLITE3_ASSOC) !== false);
	}
};

?>
