<?php
//
// +----------------------------------------------------------------------+
// | Copyright (c) 2006-2007 Paul Mathot                                  |
// | Haarlem, the Netherlands                                             |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the GPL license,       |
// | that is bundled with this package in the file LICENSE.TXT            |
// +----------------------------------------------------------------------+
// $Id: superglobals.php v1.4.4
//
// Change History:
// 2007/10/15 ... v1.24 ... Paul Mathot ... Initial Zen Cart release
// 2010/08/07 ... v1.3 .... lat9 .......... Correct validation errors.
// 2012/11/23 ... v1.4.2 .. lat9 .......... Added flag to enable/disable output of the queryCache object in v1.5.1 and later.
// 2013/01/23 ... v1.4.3 .. lat9 .......... Needed to use identical (===) operator, see https://bugs.php.net/bug.php?id=39579 for details; the first index (0) of
//                                            an indexed array was not being output.
// 2013/01/24 ... v1.4.4 .. lat9/torvista . - Correct validation errors (empty <strong></strong> tags and javascript popup) - torvista
//                                          - Added "future proofing" around call to mysql_fetch_array call (deprecated in PHP 5.5) - lat9

//define ('SHOW_SUPERGLOBALS', 'true');
//define ('SHOW_SUPERGLOBALS_TO_ALL', 'true');
//define ('SHOW_SUPERGLOBALS_IP', '127.0.0.1'); // comma separated list, replace by your own ip-address
//define ('SHOW_SUPERGLOBALS_TOP_BOTTOM', '1'); // '1' = top, '2' = bottom
//define ('SHOW_SUPERGLOBALS_GET', 'false');
//define ('SHOW_SUPERGLOBALS_POST', 'true');
//define ('SHOW_SUPERGLOBALS_COOKIE', 'true');
//define ('SHOW_SUPERGLOBALS_SESSION', 'true');
//define ('SHOW_SUPERGLOBALS_SERVER', 'false');
//define ('SHOW_SUPERGLOBALS_ENV', 'false');
//define ('SHOW_SUPERGLOBALS_FILES', 'false');
//define ('SHOW_SUPERGLOBALS_ALL', 'true'); // shows all globals, overriding the settings for the individual globals
//define ('SHOW_SUPERGLOBALS_GET_DEFINED_CONSTANTS', 'true');
//define ('SHOW_SUPERGLOBALS_MAX_LEVEL',12); // prevents infinite recursion, 0 disables the max recursion level ($GLOBALS recursion still detected/prevented)
//define('SHOW_SUPERGLOBALS_FILTER_HTTP', 'true');
//define('SHOW_SUPERGLOBALS_GET_INCLUDED_FILES', 'true');
/*
Call the superglobals function like this, for example just before the closing </body> tag of your script:

// BEGIN Superglobals
echo superglobals_echo();
// END Superglobals

The output is buffered, so you can also use the script like this:

$globals = superglobals_echo();

echo $globals;

*/

$showQueryCache = (SHOW_SUPERGLOBALS_QUERYCACHE == 'true') ? true: false;

function superglobals_echo() 
{
    ob_start();
    if (superglobals_check_allowed() === true) {
        echo "\n" . '<div id="superglobals">' . "\n";
        echo '<h4>$GLOBALS:</h4>';
        superglobals_format($GLOBALS);
        if (SHOW_SUPERGLOBALS_GET_DEFINED_CONSTANTS == 'true') {
            echo '<h4>get_defined_constants()</h4>' . "\n";
            superglobals_format(get_defined_constants(), false, true);
        }
        if (SHOW_SUPERGLOBALS_GET_INCLUDED_FILES == 'true'){
            echo '<h4>get_included_files()</h4>' . "\n";
            superglobals_format(get_included_files(), false, true);
        }   
        echo '<h4>The source of the Superglobals Plus script is subject to version 2.0 of the GPL license. Copyright: Paul Mathot, Haarlem The Netherlands.</h4>';
        echo '</div>' . "\n";

        $superglobals_buffer = ob_get_contents();
        ob_end_clean();

        if (!SHOW_SUPERGLOBALS_POPUP == 'false') {
            // add js popup script
            ob_start();
            //-bof-c-v1.4.4-torvista
?>
<script type="text/javascript">
<!--
    function superglobalspopup() {
        var newwindow=window.open('','name', 'status=yes, menubar=yes, scrollbars=1, fullscreen=1, resizable=1, toolbar=yes');
        var tmp = newwindow.document;
        tmp.write('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">\n');
        tmp.write('<html xmlns="http://www.w3.org/1999/xhtml">\n');
        tmp.write('<head>\n');
        tmp.write('<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>" />\n');
        tmp.write('<title>Superglobals Popup<\/title>\n');
        tmp.write('<link rel="stylesheet" type="text/css" href="<?php echo DIR_WS_INCLUDES . 'stylesheet_superglobals.css'; ?>" />\n');    
        tmp.write('<\/head><body>\n');
<?php
        $find = array("\r", "\r\n", "\n"); //steve how to get carriage returns for better-looking html source?
        $replace = array("", "", ""); 
?>
        tmp.write('<?php echo addslashes(str_replace($find, $replace, $superglobals_buffer)) ; ?>\n');
        tmp.write('<\/body>\n<\/html>');
        tmp.close();
    }
    superglobalspopup();
-->
</script>
<?php
            //-eof-c-v1.4.4-torvista
            $superglobals_buffer = ob_get_contents();
            ob_end_clean();
        }
    } else {
        return NULL;
    }
    return $superglobals_buffer;
}

function superglobals_check_allowed() 
{
    if (defined('SHOW_SUPERGLOBALS_FROM_ADMIN')) {
        // the script is being called from the admin
        if ( (SHOW_SUPERGLOBALS_ADMIN == 'true'&& in_array(superglobals_get_ip_address(), explode(',', str_replace(' ', '', SHOW_SUPERGLOBALS_IP)))) || SHOW_SUPERGLOBALS_TO_ALL == 'true') {
            return true;
        }
    } else {
        if ( (SHOW_SUPERGLOBALS == 'true' && in_array(superglobals_get_ip_address(), explode(',', str_replace(' ', '', SHOW_SUPERGLOBALS_IP)))) || SHOW_SUPERGLOBALS_TO_ALL == 'true') {
            return true;
        }
    }
    return false;
}

function superglobals_format(&$superglobals_var, $recursion = false, $show_customvar = false)
{
    global $showQueryCache;
  
    // $recursion = FALSE => reset recursion if the function is not called (a second time) from itself
    static $recursionlevel, $toplevel_key, $class;

    if (!isset($recursionlevel) || $recursion === false) {
        $recursionlevel = 0;
    }
    if ($recursionlevel > (int)SHOW_SUPERGLOBALS_MAX_LEVEL && ((int)SHOW_SUPERGLOBALS_MAX_LEVEL) >= 1) {
        die('Superglobals message: Maximum recursion level exceeded! (Current recursion level:' . $recursionlevel . ')'); // basic recursion protection
    }
    $superglobals_var_type = '';
    if (is_array($superglobals_var)) {
        $superglobals_var_type = 'array';
    }
    if (is_object($superglobals_var)) {
        $superglobals_var_type = 'object';
    }

    if (!isset($class)) {
        $class = '';
    }
    if (!$superglobals_var_type == '') {
        $class = ' class="superglobals_' . $superglobals_var_type . '"';
    }

    // $tabs and $tabs_li are used for html source formatting only
    $tabs = "\n";
    for ($i = 0; $i <= $recursionlevel; $i++) {
        $tabs .= "\t";
    }
    $tabs_li = $tabs . "\t";

    if (sizeof($superglobals_var)) {
        $sg_exclusions = explode(',', SHOW_SUPERGLOBALS_EXCLUSIONS);

        echo $tabs . '<ul' . $class . '>';
        $numLineItems = 0;
        if (is_array($superglobals_var) || is_object($superglobals_var)) {
            foreach ($superglobals_var as $key => $v) {
                if ( (!$showQueryCache && $key === 'queryCache') || ($key != 0 && in_array($key, $sg_exclusions)) ) continue;

                // store the top level key into $toplevel_key (used during recursion to determine if the value should be echoed or not)
                if ($recursionlevel == 0) {
                    $toplevel_key = $key;
                }

                // check if toplevel_key starts with ....
                if (SHOW_SUPERGLOBALS_FILTER_HTTP == 'false' || !strstr($toplevel_key, 'HTTP_') == $toplevel_key) {
                    if (SHOW_SUPERGLOBALS_ALL == 'true' 
                        || $show_customvar 
                        || ($toplevel_key === '_GET' && SHOW_SUPERGLOBALS_GET == 'true') 
                        || ($toplevel_key === '_POST' && SHOW_SUPERGLOBALS_POST == 'true') 
                        || ($toplevel_key === '_COOKIE' && SHOW_SUPERGLOBALS_COOKIE == 'true') 
                        || ($toplevel_key === '_REQUEST' && SHOW_SUPERGLOBALS_REQUEST == 'true') 
                        || ($toplevel_key === '_SESSION' && SHOW_SUPERGLOBALS_SESSION == 'true')
                        || ($toplevel_key === '_SERVER' && SHOW_SUPERGLOBALS_SERVER == 'true') 
                        || ($toplevel_key === '_ENV' && SHOW_SUPERGLOBALS_ENV == 'true') 
                        || ($toplevel_key === '_FILES' && SHOW_SUPERGLOBALS_FILES == 'true')) {
                        $numLineItems++;
                        switch (gettype($v)) {
                            case 'array':
                                echo $tabs_li;
                                echo '<li class="superglobals_array">' . '<strong>' . $key . '</strong> <span class="superglobals_type">(array)</span>';
                                if ($key === 'GLOBALS') {
                                    echo 'Superglobals message: recursion!';
                                }
                                $recursionlevel++;
                                // $GLOBALS is recursive, prevent infinite loop
                                if (!($key === 'GLOBALS')) {
                                    superglobals_format($v, true);
                                }
                                $recursionlevel--;
                                echo '</li>';
                                break;

                            case 'object':
                                echo $tabs_li;
                                echo '<li class="superglobals_object">' . '<strong>' . $key . '</strong> <span class="superglobals_type">(object: '.  get_class($v) . ')</span>';
                                $recursionlevel++;
                                superglobals_format($v, true);
                                $recursionlevel--;
                                echo '</li>';
                                //if (is_object($v)) var_dump($v);
                                break;

                            case 'resource':
                                echo $tabs_li . '<li class="superglobals_resource"><strong>' . htmlspecialchars(strval($key), ENT_COMPAT, CHARSET, true) . '</strong> <span class="superglobals_type">(resource: ' . get_resource_type($v) . ')</span> =&gt; ' . htmlspecialchars(strval($v), ENT_COMPAT, CHARSET, true);
                                // split out if get_resource_type == 'mysql result' ?
                                if (get_resource_type($v) == 'mysql result' && function_exists('mysql_fetch_array')) {
                                    $mysql_array = array();
                                    while ($line = mysql_fetch_array($v, MYSQL_ASSOC)){
                                        $mysql_array[] = $line;
                                    }
                                    superglobals_format($mysql_array, true);
                                }
                                echo '</li>';
                                break;

                            case 'string':
                                echo $tabs_li . '<li><strong>' . htmlspecialchars($key, ENT_COMPAT, CHARSET, true) . '</strong> <span class="superglobals_type">(string)</span> =&gt; ' . htmlentities($v, ENT_COMPAT, CHARSET, true) . '</li>';
                                break;

                            default:
                                echo $tabs_li . '<li><strong>' . $key . '</strong> <span class="superglobals_type">(' . gettype($v) . ')</span> =&gt; ' . htmlspecialchars(strval($v), ENT_COMPAT, CHARSET, true) . '</li>';
                                break;

                        } // end switch
                    }
                } // end check if toplevel_key starts with ....
            } // end foreach
        } else {
//      echo   $tabs_li . '<li><strong>' . 'var'. '</strong> => ' . $superglobals_var. '</li>';
        }
        if ($numLineItems == 0) {
            echo $tabs_li . '<li>&nbsp;</li>';
        }
        echo $tabs . '</ul>';

    } else {
        echo $tabs . '<ul' . $class . '>';
//-bof-c-v1.4.4-torvista
        echo $tabs_li . '<li class="superglobals_empty">';
        if ($superglobals_var_type) {
            echo '<strong>' . $superglobals_var_type . '</strong> ';
        }
        echo 'Superglobals message: empty!</li>';
//-eof-c-v1.4.4-torvista
        echo $tabs . '</ul>';
    }
}

function superglobals_get_ip_address() 
{
    if (!empty($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    } else {
        $ip = 'unknown';
    }
    return $ip;
}