/* global Lotgd */

import jQuery from 'jquery'
import './jqueryCookieGuard.1.1.0'

jQuery(document).ready(function ()
{
    jQuery.cookieguard()
    jQuery.cookieguard.cookies.add('PHP Session', Lotgd.get('cookie_name'), 'This cookie is used to track important logical information for the smooth operation of the site', true)
    jQuery.cookieguard.run()
})
