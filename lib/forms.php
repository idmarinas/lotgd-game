<?php

function previewfield($name, $startdiv = false, $talkline = 'says', $showcharsleft = true, $info = false, $script_output = true)
{
    global $schema, $session, $output;

    $talkline = translate_inline($talkline, $schema);
    $youhave = translate_inline('You have %s characters left.');

    $script = '';

    if (false === $startdiv)
    {
        $startdiv = '';
    }

    $switchscript = datacache('switchscript_comm'.rawurlencode($name));

    if (! is_array($switchscript))
    {
        $switchscript = '<script>Lotgd.set("colors", "'.addslashes(json_encode($output->getColors())).'");</script>';

        updatedatacache('switchscript_comm'.rawurlencode($name), $switchscript);
    }

    $script .= $switchscript;

    if (! is_array($info))
    {
        $maxchars = getsetting('maxchars', 200);
        //adding maxchars + a misc overflow which we don't need when javascript is enabled ^^ 100 as failsafe should be enough for a name
        $input = "<input autocomplete='off' name='$name' id='input$name' maxsize='".($maxchars + 100)."' onkeyup='Lotgd.previewfield(this, $maxchars, \"$name\", \"$startdiv\", \"$talkline\", $showcharsleft, \"$youhave\");'>";
    }
    else
    {
        if (isset($info['maxlength']))
        {
            $l = $info['maxlength'];
        }
        else
        {
            $l = getsetting('maxchars', 200);
        }

        $attributes = '';

        foreach ($info as $key => $val)
        {
            $attributes .= "$key='$val'";
        }

        if (isset($info['type']) && 'textarea' == $info['type'])
        {
            $input = "<textarea name='$name' id='input$name' onkeyup='Lotgd.previewfield(this, $l, \"$name\", \"$startdiv\", \"$talkline\", $showcharsleft, \"$youhave\");' $attributes></textarea>";
        }
        else
        {
            $input = "<input autocomplete='off' type='text' name='$name' id='input$name' onkeyup='Lotgd.previewfield(this, $l, \"$name\", \"$startdiv\", \"$talkline\", $showcharsleft, \"$youhave\");' $attributes>";
        }
    }
    $add = translate_inline('Add');

    rawoutput('<div class="ui fluid left action input">');
    rawoutput('<button class="ui button" type="submit">'.$add.'</button>');
    rawoutput($input);
    rawoutput('<div class="ui left pointing hidden label" id="charsleft'.$name.'"></div></div>');
    rawoutput('<div class="ui fluid pointing hidden label" id="previewtext'.$name.'"></div>');

    if ($script_output)
    {
        rawoutput($script);
    }

    return;
}
