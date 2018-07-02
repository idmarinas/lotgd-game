<?php

return [
    //taken out in 1.1.1 as the game settings were not cacheable if there was no directory known for the cache without database access
    //here to display what has *been* in there.
    'High Load Optimization,title',
    'usedatacache' => 'Use Data Caching (D),viewonly',
    '`iNote`i when using in an environment where Safe Mode is enabled; this needs to be a path that has the same UID as the web server runs,note',
    'datacachepath' => 'Path to store data cache information (D),viewonly',
    'gziphandler' => 'Is the GzHandler turned on (S),viewonly',
    'databasetype' => 'Type of database (S),viewonly'
];
