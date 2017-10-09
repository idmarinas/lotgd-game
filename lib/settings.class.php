<?php

class settings
{
	protected $tablename;
    protected $settings = [];
    protected $settingsKey = 'game-settings-';

	public function __construct($tablename = false)
	{
		if ($tablename === false) $tablename = DB::prefix('settings');
		else $tablename = DB::prefix($tablename);

		$this->tablename = $tablename;
		$this->settings = [];
		$this->loadSettings();
	}

    /**
     * Save setting in to Data Base
     *
     * @param string $settingname
     * @param mixed $value
     *
     * @return bool
     */
	public function saveSetting($settingname, $value)
    {
        $this->loadSettings();
        $key = $this->settingsKey . $this->tablename;
        $settingname = (string) $settingname;

		if (! isset($this->settings[$settingname]) && $value)
		{ //value needs to be elimintated - once we have our defaults in lib/data/settings.php ... this can GO
            $sql = DB::insert($this->tablename);
            $sql->values([
                'setting' => $settingname,
                'value' => $value
            ]);
		}
		elseif (isset($this->settings[$settingname]))
		{
            $sql = DB::update($this->tablename);
            $sql->set(['value' => $value])
                ->where->equalTo('setting', $settingname)
            ;
		}
		else
		{
			return false;
        }

        DB::query($sql);

        $this->settings[$settingname] = $value;

		updatedatacache($key, $this->settings, true);

		if (DB::affected_rows() > 0) return true;
		else return false;
	}

    /**
     * Load all settings in table
     *
     * @return void
     */
	public function loadSettings()
	{
        $key = $this->settingsKey . $this->tablename;
        $this->settings = datacache($key, 86400, true);

		if (! is_array($this->settings) || empty($this->settings))
		{
			try
            {
                $this->settings = [];
                $select = DB::select($this->tablename);
                $result = DB::execute($select);

                if (! $result->count()) return;

                foreach($result as $row)
                {
                    $this->settings[$row['setting']] = $row['value'];
                }
                updatedatacache($key, $this->settings, true);
            }
            catch( \Exception $ex)
            {
                debug('Cant get Settings.');
            }
		}
	}

    public function clearSettings()
    {
		//scraps the $this->loadSettings() data to force it to reload.
		invalidatedatacache($this->settingsKey . $this->tablename, true);
		$this->settings = [];
	}

	public function getSetting($settingname, $default = false)
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
			if (file_exists("lib/data/{$this->tablename}.php")) { require "lib/data/{$this->tablename}.php"; }
			if ($default === false)
            {
				if (isset($defaults[$settingname])) $setDefault=$defaults[$settingname];
				else $setDefault = '';
			}
            else $setDefault = $default;

            $this->saveSetting($settingname, $setDefault);

			return $setDefault;
		}
        else
        {
			return $this->settings[$settingname];
		}
	}

	public function getArray() { return $this->settings; }
}
