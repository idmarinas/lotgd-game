<?php
// translator ready
// addnews ready
// mail ready


class settings {

	private $tablename,$settings;

	function __construct($tablename=false) {
		if ($tablename===false) $tablename=db_prefix('settings');
			else $tablename=db_prefix($tablename);
		$this->tablename=$tablename;
		$this->settings="";
		$this->loadSettings();
	}

	function saveSetting($settingname,$value){
		$this->loadSettings();
		if (!isset($this->settings[$settingname]) && $value){ //value needs to be elimintated - once we have our defaults in lib/data/settings.php ... this can GO
				$sql = "INSERT INTO " . $this->tablename . " (setting,value) VALUES (\"".addslashes($settingname)."\",\"".addslashes($value)."\")";
		}elseif (isset($this->settings[$settingname])) {
				$sql = "UPDATE " . $this->tablename . " SET value=\"".addslashes($value)."\" WHERE setting=\"".addslashes($settingname)."\"";
		} else {
			return false;
		}
		db_query($sql);
		$this->settings[$settingname]=$value;
		invalidatedatacache("game".$this->tablename);
		if (db_affected_rows()>0) {
			return true;
		}else{
			return false;
		}
	}

	function loadSettings(){
		if (!is_array($this->settings)){
			$this->settings=datacache("game".$this->tablename);
			if (!is_array($this->settings)){
				$this->settings=array();
				$sql = "SELECT * FROM " . $this->tablename;
				$result = db_query($sql);
				while ($row = db_fetch_assoc($result)) {
					$this->settings[$row['setting']] = $row['value'];
				}
				db_free_result($result);
				updatedatacache("game".$this->tablename,$this->settings);
			}
		}
	}

	function clearSettings(){
		//scraps the $this->loadSettings() data to force it to reload.
		invalidatedatacache("game".$this->tablename);
		$this->settings="";
	}

	function getSetting($settingname,$default=false){
		global $DB_USEDATACACHE,$DB_DATACACHEPATH;
		if ($settingname=="usedatacache") return $DB_USEDATACACHE;
			elseif ($settingname=="datacachepath") return $DB_DATACACHEPATH;
		if ( 'object' == gettype($this) && !isset($this->settings[$settingname])) {
			$this->loadSettings();
		}else {
			return $this->settings[$settingname];
		}
		if (!isset($this->settings[$settingname])){
			//nothing set, we have to use the default value
			if (file_exists("lib/data/".$this->tablename.".php")) require("lib/data/".$this->tablename.".php");
			if ($default===false) {
				if (isset($defaults[$settingname])) $setDefault=$defaults[$settingname];
					else $setDefault='';
			} else $setDefault=$default;
			$this->saveSetting($settingname,$setDefault);
			return $setDefault;
		}else{
			return $this->settings[$settingname];
		}
	}

	function getArray() {
		return $this->settings;
	}
}
?>
