<?php

page_header('Clan Halls');

addnav('Clan Options');
if ($session['user']['clanrank'] == CLAN_APPLICANT) { addnav('Return to the Lobby', 'clan.php'); }
else { addnav('Return to your Clan Rooms', 'clan.php'); }

rawoutput($lotgdTpl->renderThemeTemplate('pages/clan/waiting.twig', []));

commentdisplay('', 'waiting', 'Speak', 25);
