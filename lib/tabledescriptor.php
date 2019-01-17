<?php

// translator ready
// addnews ready
// mail ready
//
//functions to pay attention to in this script:
// synctable() ensures that a table in the database matches the
// descriptor it's passed.
// table_create_descriptor() creates a descriptor from an existing table
// in the database.
// table_create_from_descriptor() writes SQL to create the table described
// by the descriptor.
//
// There's no support for foreign keys that INNODB offers.  Sorry.

/**
 * @deprecated 4.0.0 Delete in version 4.1.0
 */
function synctable($tablename, $descriptor, $nodrop = false)
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in version 4.1.0, use Doctrine Entities to sync table schema.',
        __METHOD__
    ), E_USER_DEPRECATED);

    //table names should be DB::prefix'd before they get in to
    //this function.
    if (! DB::table_exists($tablename))
    {
        //the table doesn't exist, so we create it and are done.
        reset($descriptor);
        $sql = table_create_from_descriptor($tablename, $descriptor);
        debug($sql);

        if (! DB::query($sql))
        {
            output('`$Error:`^ %s`n', DB::error());
            rawoutput('<pre>'.htmlentities($sql, ENT_COMPAT, getsetting('charset', 'UTF-8')).'</pre>');
        }
        else
        {
            output('`^Table `#%s`^ created.`n', $tablename);
        }
    }
    else
    {
        //the table exists, so we need to compare it against the descriptor.
        $existing = table_create_descriptor($tablename);
        reset($descriptor);
        $changes = [];

        foreach ($descriptor as $key => $val)
        {
            if ('RequireMyISAM' == $key)
            {
                continue;
            }
            $val['type'] = descriptor_sanitize_type($val['type']);

            if (! isset($val['name']))
            {
                if (('key' == $val['type'] ||
                            'unique key' == $val['type'] ||
                            'primary key' == $val['type']))
                {
                    if ('key-' == substr($key, 0, 4))
                    {
                        $val['name'] = substr($key, 4);
                    }
                    else
                    {
                        debug("<b>Warning</b>: the descriptor for <b>$tablename</b> includes a {$val['type']} which isn't named correctly.  It should be named key-$key. In your code, it should look something like this (the important change is bolded):<br> \"<b>key-$key</b>\"=>array(\"type\"=>\"{$val['type']}\",\"columns\"=>\"{$val['columns']}\")<br> The consequence of this is that your keys will be destroyed and recreated each time the table is synchronized until this is addressed.");
                        $val['name'] = $key;
                    }
                }
                else
                {
                    $val['name'] = $key;
                }
            }
            else
            {
                if ('key' == $val['type'] ||
                        'unique key' == $val['type'] ||
                        'primary key' == $val['type'])
                {
                    $key = 'key-'.$val['name'];
                }
                else
                {
                    $key = $val['name'];
                }
            }
            $newsql = descriptor_createsql($val);

            if (! isset($existing[$key]))
            {
                //this is a new column.
                array_push($changes, "ADD $newsql");
            }
            else
            {
                //this is an existing column, let's make sure the
                //descriptors match.
                $oldsql = descriptor_createsql($existing[$key]);

                if ($oldsql != $newsql)
                {
                    //this descriptor line has changed.  Change the
                    //table to suit.
                    debug("Old: $oldsql<br>New:$newsql");

                    if ('key' == $existing[$key]['type'] ||
                            'unique key' == $existing[$key]['type'])
                    {
                        array_push($changes,
                                "DROP KEY {$existing[$key]['name']}");
                        array_push($changes, "ADD $newsql");
                    }
                    elseif ('primary key' == $existing[$key]['type'])
                    {
                        array_push($changes, 'DROP PRIMARY KEY');
                        array_push($changes, "ADD $newsql");
                    }
                    else
                    {
                        array_push($changes,
                                "CHANGE {$existing[$key]['name']} $newsql");
                    }
                }//end if
            }//end if
            unset($existing[$key]);
        }//end foreach
        //drop no longer needed columns
        if (! $nodrop)
        {
            reset($existing);

            foreach ($existing as $val)
            {
                //This column no longer exists.
                if ('key' == $val['type'] || 'unique key' == $val['type'])
                {
                    $sql = "DROP KEY {$val['name']}";
                }
                elseif ('primary key' == $val['type'])
                {
                    $sql = 'DROP PRIMARY KEY';
                }
                else
                {
                    $sql = "DROP {$val['name']}";
                }
                array_push($changes, $sql);
            }//end foreach
        }

        if (count($changes) > 0)
        {
            //we have changes to do!  Woohoo!
            $sql = "ALTER TABLE $tablename \n".join(",\n", $changes);
            debug(nl2br($sql));
            DB::query($sql);

            return count($changes);
        }
    }//end if
}//end function

/**
 * @deprecated 4.0.0 Delete in version 4.1.0
 */
function table_create_from_descriptor($tablename, $descriptor)
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in version 4.1.0, use Doctrine Entities to sync table schema.',
        __METHOD__
    ), E_USER_DEPRECATED);

    $sql = "CREATE TABLE $tablename (\n";
    $type = 'INNODB';
    reset($descriptor);
    $i = 0;

    foreach ($descriptor as $key => $val)
    {
        if ('RequireMyISAM' === $key && 1 == $val)
        {
            // Let's hope that we don't run into badly formatted strings
            // but you know what, if we do, tough
            if (DB::get_server_version() < '4.0.14')
            {
                $type = 'MyISAM';
            }
            continue;
        }
        elseif ('RequireMyISAM' === $key)
        {
            continue;
        }

        if (! isset($val['name']))
        {
            if (('key' == $val['type'] ||
                        'unique key' == $val['type'] ||
                        'primary key' == $val['type']))
            {
                if ('key-' == substr($key, 0, 4))
                {
                    $val['name'] = substr($key, 4);
                }
                else
                {
                    debug("<b>Warning</b>: the descriptor for <b>$tablename</b> includes a {$val['type']} which isn't named correctly.  It should be named key-$key.  In your code, it should look something like this (the important change is bolded):<br> \"<b>key-$key</b>\"=>array(\"type\"=>\"{$val['type']}\",\"columns\"=>\"{$val['columns']}\")<br> The consequence of this is that your keys will be destroyed and recreated each time the table is synchronized until this is addressed.");
                    $val['name'] = $key;
                }
            }
            else
            {
                $val['name'] = $key;
            }
        }
        else
        {
            if ('key' == $val['type'] ||
                    'unique key' == $val['type'] ||
                    'primary key' == $val['type'])
            {
                $key = 'key-'.$val['name'];
            }
            else
            {
                $key = $val['name'];
            }
        }

        if ($i > 0)
        {
            $sql .= ",\n";
        }
        $sql .= descriptor_createsql($val);
        $i++;
    }
    $sql .= ") Engine=$type";

    return $sql;
}

/**
 * @deprecated 4.0.0 Delete in version 4.1.0
 */
function table_create_descriptor($tablename)
{

    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in version 4.1.0, use Doctrine Entities to sync table schema.',
        __METHOD__
    ), E_USER_DEPRECATED);

    //this function assumes that $tablename is already passed
    //through DB::prefix.
    $descriptor = [];

    //fetch column desc's
    $sql = "DESCRIBE $tablename";
    $result = DB::query($sql);

    while ($row = DB::fetch_assoc($result))
    {
        $item = [];
        $item['name'] = $row['Field'];
        $item['type'] = $row['Type'];

        if ('Yes' == $row['Null'])
        {
            $item['null'] = true;
        }

        if ('' != trim($row['Default']))
        {
            $item['default'] = $row['Default'];
        }

        if ('' !== trim($row['Extra']))
        {
            $item['extra'] = $row['Extra'];
        }
        $descriptor[$item['name']] = $item;
    }

    $sql = "SHOW KEYS FROM $tablename";
    $result = DB::query($sql);

    while ($row = DB::fetch_assoc($result))
    {
        if ($row['Seq_in_index'] > 1)
        {
            //this is a secondary+ column on some previous key;
            //add this to that column's keys.
            $str = $row['Column_name'];

            if ($row['Sub_part'])
            {
                $str .= '('.$row['Sub_part'].')';
            }
            $descriptor['key-'.$row['Key_name']]['columns'] .=
                ','.$str;
        }
        else
        {
            $item = [];
            $item['name'] = $row['Key_name'];

            if ('PRIMARY' == $row['Key_name'])
            {
                $item['type'] = 'primary key';
            }
            else
            {
                $item['type'] = 'key';
            }

            if (0 == $row['Non_unique'])
            {
                $item['unique'] = true;
            }
            $str = $row['Column_name'];

            if ($row['Sub_part'])
            {
                $str .= '('.$row['Sub_part'].')';
            }
            $item['columns'] = $str;
            $descriptor['key-'.$item['name']] = $item;
        }//end if
    }//end while

    return $descriptor;
}

/**
 * @deprecated 4.0.0 Delete in version 4.1.0
 */
function descriptor_createsql($input)
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in version 4.1.0, use Doctrine Entities to sync table schema.',
        __METHOD__
    ), E_USER_DEPRECATED);

    $input['type'] = descriptor_sanitize_type($input['type']);

    if ('key' == $input['type'] || 'unique key' == $input['type'])
    {
        //this is a standard index
        if (is_array($input['columns']))
        {
            $input['columns'] = join(',', $input['columns']);
        }

        if (! isset($input['name']))
        {
            //if the user didn't define a name we should give it one
            if (false !== strpos($input['columns'], ','))
            {
                //if there are multiple columns, the name is just the
                //first column
                $input['name'] =
                    substr($input['columns'], strpos($input['columns'], ','));
            }
            else
            {
                //if there is only one column, the key name is the same
                //as the column name.
                $input['name'] = $input['columns'];
            }
        }

        if ('unique ' == substr($input['type'], 0, 7))
        {
            $input['unique'] = true;
        }
        $return = (isset($input['unique']) && $input['unique'] ? 'UNIQUE ' : '')
            ."KEY {$input['name']} "
            ."({$input['columns']})";
    }
    elseif ('primary key' == $input['type'])
    {
        //this is a primary key
        if (is_array($input['columns']))
        {
            $input['columns'] = join(',', $input['columns']);
        }
        $return = "PRIMARY KEY ({$input['columns']})";
    }
    else
    {
        //this is a standard column
        if (! array_key_exists('extra', $input))
        {
            $input['extra'] = '';
        }
        $return = $input['name'].' '
            .$input['type']
            .(isset($input['null']) && $input['null'] ? '' : ' NOT NULL')
            .(isset($input['default']) &&
                    $input['default'] > '' ? " default '{$input['default']}'" : '')
            .' '.$input['extra'];
    }

    return $return;
}

/**
 * @deprecated 4.0.0 Delete in version 4.1.0
 */
function descriptor_sanitize_type($type)
{
    trigger_error(sprintf(
        'Usage of %s is obsolete since 4.0.0; and delete in version 4.1.0, use Doctrine Entities to sync table schema.',
        __METHOD__
    ), E_USER_DEPRECATED);

    $type = strtolower($type);
    $changes = [
        'primary index' => 'primary key',
        'primary' => 'primary key',
        'index' => 'key',
        'unique index' => 'unique key',
    ];

    if (isset($changes[$type]))
    {
        return $changes[$type];
    }
    else
    {
        return $type;
    }
}
