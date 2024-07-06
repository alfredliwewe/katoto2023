<?php

/**
 * 
 */
class System
{
	
	function __construct($db)
	{
		$this->db = $db;
		$this->data = [];
		$r = $db->query("SELECT * FROM systemctl");
		while ($row = $r->fetchArray()) {
			$this->data[$row['name']] = $row['value'];
		}
		$this->name = $this->data['system_name'];
		$this->logo = $this->data['logo'];
	}
}

?>