<?php
/*
 * Copyright (c) 2013, PGPSender.org
 */

class MailQueue {
	
	const TABLE = "mail";
	
	private $db;
	
	public function __construct ($file)
	{
		$sql = "CREATE TABLE IF NOT EXISTS ".$this::TABLE." (
						recipient varchar (80),
						sender varchar (80),
						subject varchar (255),
						message text);";
		$this->db = new SQLite3 ($file);
		$this->db->exec ($sql);
	}
	
	public function __destruct ()
	{
		$this->db->close ();
	}
	
	public function push ($recipient, $sender, $subject, $message)
	{
		$sql = "INSERT INTO ".$this::TABLE." VALUES (
						'".$this->db->escapeString ($recipient)."',
						'".$this->db->escapeString ($sender)."',
						'".$this->db->escapeString ($subject)."',
						'".$this->db->escapeString ($message)."'
		);";
		
		return $this->db->exec ($sql);
	}
	
	public function pull ($limit)
	{
		$sql = "SELECT rowid, recipient, sender, subject, message FROM ".$this::TABLE." LIMIT ".intval ($limit).";";
		$res = $this->db->query ($sql);
		
		if ( $res === false )
			return false;
		
		$rows = array ();
		while ( ($row = $res->fetchArray (SQLITE3_ASSOC) ) )
			$rows[] = $row;
		
		return $rows;
	}
	
	public function delete ($id)
	{
		$sql = "DELETE FROM ".$this::TABLE." WHERE rowid = ".intval ($id).";";
		return $this->db->exec ($sql);
	}
};

?>
