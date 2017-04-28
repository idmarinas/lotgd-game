<?php
// translator ready
// addnews ready
// mail ready


class settings
{

	protected $tablename;
    protected $settings = [];

	function __construct($tablename=false)
	{
		if ($tablename === false) $tablename = DB::prefix('settings');
		else $tablename = DB::prefix($tablename);

		$this->tablename = $tablename;
		$this->settings = '';
		$this->loadSettings();
	}

	function saveSetting($settingname,$value)
    {
		$this->loadSettings();
		if (!isset($this->settings[$settingname]) && $value)
		{ //value needs to be elimintated - once we have our defaults in lib/data/settings.php ... this can GO
			$sql = "INSERT INTO " . $this->tablename . " (setting,value) VALUES (\"".addslashes($settingname)."\",\"".addslashes($value)."\")";
		}
		elseif (isset($this->settings[$settingname]))
		{
			$sql = "UPDATE " . $this->tablename . " SET value=\"".addslashes($value)."\" WHERE setting=\"".addslashes($settingname)."\"";
		}
		else
		{
			return false;
		}
		DB::query($sql);
		$this->settings[$settingname] = $value;
		invalidatedatacache("game".$this->tablename);

		if (DB::affected_rows()>0) return true;
		else return false;
	}

	function loadSettings()
	{
		if (!is_array($this->settings))
		{
			$this->settings = datacache('game' . $this->tablename);
			if (! is_array($this->settings))
			{
				try
				{
					$this->settings = [];
					$sql = "SELECT * FROM " . $this->tablename;
					$result = DB::query($sql);

					if (! $result) return;

					foreach($result as $row)
					{
						$this->settings[$row['setting']] = $row['value'];
					}
					updatedatacache('game'.$this->tablename, $this->settings);
				}
				catch( \Exception $ex)
				{
					debug('Cant get Settings.');
				}
			}
		}
	}

	function clearSettings(){
		//scraps the $this->loadSettings() data to force it to reload.
		invalidatedatacache("game".$this->tablename);
		$this->settings="";
	}

	function getSetting($settingname,$default=false)
    {
		global $DB_USEDATACACHE,$DB_DATACACHEPATH;

		if ($settingname == 'usedatacache') return $DB_USEDATACACHE;
		elseif ($settingname == 'datacachepath') return $DB_DATACACHEPATH;

        if ( 'object' == gettype($this) && !isset($this->settings[$settingname]))
        {
			$this->loadSettings();
		}
        else
        {
			return $this->settings[$settingname];
		}

		if (!isset($this->settings[$settingname]))
        {
			//nothing set, we have to use the default value
			if (file_exists("lib/data/".$this->tablename.".php")) require("lib/data/".$this->tablename.".php");
			if ($default===false)
            {
				if (isset($defaults[$settingname])) $setDefault=$defaults[$settingname];
					else $setDefault='';
			}
            else $setDefault=$default;
			$this->saveSetting($settingname,$setDefault);
			return $setDefault;
		}
        else
        {
			return $this->settings[$settingname];
		}
	}

	function getArray() {
		return $this->settings;
	}
}
?>
