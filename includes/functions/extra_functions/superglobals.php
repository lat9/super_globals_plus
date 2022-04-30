<?php
//
// +----------------------------------------------------------------------+
// | Copyright (c) 2006-2007 Paul Mathot                                  |
// | Haarlem, the Netherlands                                             |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the GPL license,       |
// | that is bundled with this package in the file LICENSE.TXT            |
// +----------------------------------------------------------------------+
// $Id: superglobals.php v2.2.1
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
// -----
// The 'is_countable' function was introduced in PHP 7.3; create a compatible
// instance if the function is not available in the current PHP version.
//
if (!function_exists('is_countable')) {
    function is_countable($c)
    {
        return is_array($c) || $c instanceof Countable;
    }
}

$showQueryCache = (defined('SHOW_SUPERGLOBALS_QUERYCACHE') && SHOW_SUPERGLOBALS_QUERYCACHE === 'true');

function superglobals_echo()
{
    // -----
    // Needed for zc158+, since that $languageLoader results in a circular reference.
    //
    unset($GLOBALS['languageLoader']);

    ob_start();
    if (superglobals_check_allowed() === false) {
        return null;
    }

    $superglobals_stylesheet = DIR_FS_CATALOG . 'includes/templates/template_default/css/superglobals.css';
    if (file_exists($superglobals_stylesheet)) {
        echo "\n" . '<style>' . file_get_contents($superglobals_stylesheet) . '</style>';
    }

    echo "\n" . '<div id="superglobals">' . "\n";
    echo '<h4>$GLOBALS:</h4>';

    superglobals_format($GLOBALS);
    if (SHOW_SUPERGLOBALS_GET_DEFINED_CONSTANTS === 'true') {
        echo '<h4>get_defined_constants()</h4>' . "\n";
        $defined_constants = get_defined_constants();
        superglobals_format($defined_constants, false, true);
        unset($defined_constants);
    }
    if (SHOW_SUPERGLOBALS_GET_INCLUDED_FILES === 'true'){
        echo '<h4>get_included_files()</h4>' . "\n";
        $included_files = get_included_files();
        superglobals_format($included_files, false, true);
        unset($included_files);
    }
    echo '<h4>The source of the Superglobals Plus script is subject to version 2.0 of the GPL license. Copyright: Paul Mathot, Haarlem The Netherlands.</h4>';
    echo '</div>' . "\n";

        $superglobals_buffer = ob_get_clean();

    if (SHOW_SUPERGLOBALS_POPUP !== 'false') {
        // add js popup script
        ob_start();
        //-bof-c-v1.4.4-torvista

        // -----
        // Stylesheet location depends on "environment" in which the plugin has been loaded. From the admin, it's in the base /includes
        // directory; from the storefront, it's in the template-specific CSS directory.
        //
        if (defined('SHOW_SUPERGLOBALS_FROM_ADMIN')) {
            $stylesheet_location = DIR_WS_INCLUDES . 'stylesheet_superglobals.css';
        } else {
            $stylesheet_location = $GLOBALS['template']->get_template_dir('.css', DIR_WS_TEMPLATE, $GLOBALS['current_page_base'], 'css') . '/stylesheet_superglobals.css';
        }
?>
<script>
    function superglobalspopup() {
        let newwindow=window.open('','name', 'status=yes, menubar=yes, scrollbars=1, fullscreen=1, resizable=1, toolbar=yes');
        let tmp = newwindow.document;
        tmp.write('<!doctype html>\n');
        tmp.write('<html <?php echo HTML_PARAMS; ?>>\n');
        tmp.write('<head>\n');
        tmp.write('<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>" />\n');
        tmp.write('<title>Superglobals Popup<\/title>\n');
        tmp.write('<link rel="stylesheet" href="<?php echo $stylesheet_location; ?>" />\n');
        tmp.write('<\/head><body>\n');
<?php
        $find = array("\r", "\r\n", "\n"); //todo how to get carriage returns for better-looking html source?
        $replace = array("", "", "");
?>
        tmp.write('<?php echo addslashes(str_replace($find, $replace, $superglobals_buffer)) ; ?>\n');
        tmp.write('<\/body>\n<\/html>');
        tmp.close();
    }
    superglobalspopup();
</script>
<?php
            //-eof-c-v1.4.4-torvista
            $superglobals_buffer = ob_get_clean();
    }
    return $superglobals_buffer;
}

function superglobals_check_allowed()
{
    // -----
    // Indicate, initially, that the output should not be generated.
    //
    $is_enabled = false;

    // -----
    // If we're being called from the admin ...
    //
    if (defined('SHOW_SUPERGLOBALS_FROM_ADMIN')) {
        // -----
        // ... and the admin-display is enabled, indicate that the output should be generated.
        //
        if (SHOW_SUPERGLOBALS_ADMIN === 'true' && superglobals_ip_check()) {
            $is_enabled = true;
        }
    // -----
    // Otherwise, we're being called from the storefront ...
    //
    } else {
        // -----
        // ... if the storefront-display is enabled, indicate that the output should be generated.
        //
        if (SHOW_SUPERGLOBALS === 'true' && superglobals_ip_check()) {
            $is_enabled = true;
        }
    }
    return $is_enabled;
}

// -----
// Function, called from superglobals_check_allowed, that validates whether the plugin's output should
// be generated for the current IP address.
//
// Note: v2.1.0 and later of the plugin operates on Zen Cart notifications, but it's possible that the
// store's files have included an in-line call to generate that output ... and we don't want to double-up
// the output.  This function sets a global variable to indicate that it's been run to prevent that
// double output.
//
function superglobals_ip_check()
{
    $ip_valid = false;
    if (!isset($GLOBALS['superglobals_output'])) {
        if (SHOW_SUPERGLOBALS_TO_ALL === 'true' || in_array(superglobals_get_ip_address(), explode(',', str_replace(' ', '', SHOW_SUPERGLOBALS_IP)))) {
            $ip_valid = true;
        }
        $GLOBALS['superglobals_output'] = true;
    }
    return $ip_valid;
}

function superglobals_format($superglobals_var, $recursion = false, $show_customvar = false)
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
    if (!($superglobals_var_type === '')) {
        $class = ' class="superglobals_' . $superglobals_var_type . '"';
    }

    // $tabs and $tabs_li are used for html source formatting only
    $tabs = "\n";
    $tabs .= str_repeat("\t", $recursionlevel + 1);
    $tabs_li = $tabs . "\t";

    if (is_object($superglobals_var) || (is_countable($superglobals_var) && count($superglobals_var) !== 0)) {
        $sg_exclusions = explode(',', SHOW_SUPERGLOBALS_EXCLUSIONS);

        echo $tabs . '<ul' . $class . '>';
        $numLineItems = 0;
        if (is_array($superglobals_var) || is_object($superglobals_var)) {
            foreach ($superglobals_var as $key => $v) {
                if ( (!$showQueryCache && $key === 'queryCache') || ($key !== 0 && in_array($key, $sg_exclusions)) ) continue;

                // store the top level key into $toplevel_key (used during recursion to determine if the value should be echoed or not)
                if ($recursionlevel === 0) {
                    $toplevel_key = $key;
                }

                // check if toplevel_key starts with ....
                if (SHOW_SUPERGLOBALS_FILTER_HTTP === 'false' || !strstr($toplevel_key, 'HTTP_') == $toplevel_key) {
                    if (SHOW_SUPERGLOBALS_ALL === 'true'
                        || $show_customvar
                        || ($toplevel_key === '_GET' && SHOW_SUPERGLOBALS_GET === 'true')
                        || ($toplevel_key === '_POST' && SHOW_SUPERGLOBALS_POST === 'true')
                        || ($toplevel_key === '_COOKIE' && SHOW_SUPERGLOBALS_COOKIE === 'true')
                        || ($toplevel_key === '_REQUEST' && SHOW_SUPERGLOBALS_REQUEST === 'true')
                        || ($toplevel_key === '_SESSION' && SHOW_SUPERGLOBALS_SESSION === 'true')
                        || ($toplevel_key === '_SERVER' && SHOW_SUPERGLOBALS_SERVER === 'true')
                        || ($toplevel_key === '_ENV' && SHOW_SUPERGLOBALS_ENV === 'true')
                        || ($toplevel_key === '_FILES' && SHOW_SUPERGLOBALS_FILES === 'true')) {
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
                                break;

                            case 'resource':
                                echo $tabs_li . '<li class="superglobals_resource"><strong>' . htmlspecialchars((string)$key, ENT_COMPAT, CHARSET) . '</strong> <span class="superglobals_type">(resource: ' . get_resource_type($v) . ')</span> =&gt; ' . htmlspecialchars((string)$v, ENT_COMPAT, CHARSET) . '</li>';
                                break;

                            case 'string':
                                echo $tabs_li . '<li><strong>' . htmlspecialchars((string)$key, ENT_COMPAT, CHARSET) . '</strong> <span class="superglobals_type">(string)</span> =&gt; ' . htmlentities($v, ENT_COMPAT, CHARSET) . '</li>';
                                break;

                            default:
                                echo $tabs_li . '<li><strong>' . $key . '</strong> <span class="superglobals_type">(' . gettype($v) . ')</span> =&gt; ' . htmlspecialchars((string) $v, ENT_COMPAT, CHARSET) . '</li>';
                                break;

                        } // end switch
                    }
                } // end check if toplevel_key starts with ....
            } // end foreach
        }
        if ($numLineItems === 0) {
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
