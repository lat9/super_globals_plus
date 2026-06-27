<?php
// -----
// Part of the Super Globals Plus plugin, provided by lat9.
// Copyright (c) 2019-2026, Vinos de Frutas Tropicales
//
use Zencart\Traits\InteractsWithPlugins;

class SuperGlobalsObserver extends \base
{
    use InteractsWithPlugins;
    
    protected bool $showQueryCache;
    protected int $showMaxLevel;
    protected array $exclusions;
    protected bool $showFilterHttp;
    protected bool $showAll;
    protected bool $showGet;
    protected bool $showPost;
    protected bool $showCookie;
    protected bool $showRequest;
    protected bool $showSession;
    protected bool $showServer;
    protected bool $showEnv;
    protected bool $showFiles;

    // -----
    // Class constructor.
    //
    public function __construct()
    {
        // -----
        // If not enabled for the environment it's in (admin vs. catalog), nothing
        // further to do.
        //
        if ($this->checkAllowed() === false) {
            return;
        }

        // -----
        // Set class properties based on configuration settings.
        //
        $this->showQueryCache = $this->zenConfig('SHOW_SUPERGLOBALS_QUERYCACHE') === 'true';
        $this->showMaxLevel = (int)$this->zenConfig('SHOW_SUPERGLOBALS_MAX_LEVEL');
        $this->exclusions = explode(',', $this->zenConfig('SHOW_SUPERGLOBALS_EXCLUSIONS'));
        $this->showFilterHttp = $this->zenConfig('SHOW_SUPERGLOBALS_FILTER_HTTP') === 'true';
        $this->showAll = $this->zenConfig('SHOW_SUPERGLOBALS_ALL') === 'true';
        $this->showGet = $this->zenConfig('SHOW_SUPERGLOBALS_GET') === 'true';
        $this->showPost = $this->zenConfig('SHOW_SUPERGLOBALS_POST') === 'true';
        $this->showCookie = $this->zenConfig('SHOW_SUPERGLOBALS_COOKIE') === 'true';
        $this->showRequest = $this->zenConfig('SHOW_SUPERGLOBALS_REQUEST') === 'true';
        $this->showSession = $this->zenConfig('SHOW_SUPERGLOBALS_SESSION') === 'true';
        $this->showServer = $this->zenConfig('SHOW_SUPERGLOBALS_SERVER') === 'true';
        $this->showEnv = $this->zenConfig('SHOW_SUPERGLOBALS_ENV') === 'true';
        $this->showFiles = $this->zenConfig('SHOW_SUPERGLOBALS_FILES') === 'true';

        // -----
        // Watch for the end-of-page indicators from both the admin and storefront.
        //
        if (IS_ADMIN_FLAG === true) {
            $this->attach(
                $this,
                [
                    //- From /admin/footer.php
                    'NOTIFY_ADMIN_FOOTER_END',

                    //- From /admin/index.php
                    'NOTIFY_ADMIN_INDEX_END',
                ]
            );
        } else {
            $this->attach(
                $this,
                [
                    //- From /includes/templates/YOUR_TEMPLATE/common/tpl_main_page.php
                    'NOTIFY_FOOTER_END',
                ]
            );
        }
    }

    // -----
    // For versions prior to v3.1.0, this was the superglobals_check_allowed function.
    //
    protected function checkAllowed(): bool
    {
        // -----
        // If the current IP address isn't in the list of allowed addresses,
        // nothing further to do.
        //
        if ($this->ipCheck() === false) {
            return false;
        }

        // -----
        // If we're being called from the admin and output is enabled for the
        // admin, indicate that the output can be viewed.
        //
        if (IS_ADMIN_FLAG === true && $this->zenConfig('SHOW_SUPERGLOBALS_ADMIN') === 'true') {
            return true;
        }

        // -----
        // Otherwise, we're being called from the storefront and output is enabled
        // for the storefrone, indicate that the output can be viewed.
        //
        if (IS_ADMIN_FLAG === false && $this->zenConfig('SHOW_SUPERGLOBALS') === 'true') {
            return true;
        }

        // -----
        // If we got here, the output isn't enabled for the current environment.
        //
        return false;
    }

    // -----
    // Function, called from checkAllowed, that validates whether the plugin's output should
    // be generated for the current IP address.
    //
    // For versions prior to v3.1.0, this was the super_globals_ip_check function.
    //
    protected function ipCheck(): bool
    {
        $ip_valid = false;
         if ($this->zenConfig('SHOW_SUPERGLOBALS_TO_ALL') === 'true' || in_array($this->getIpAddress(), explode(',', str_replace(' ', '', $this->zenConfig('SHOW_SUPERGLOBALS_IP'))))) {
            $ip_valid = true;
        }
        return $ip_valid;
    }

    // -----
    // For versions prior to v3.1.0, this was the superglobals_get_ip_address function.
    //
    protected function getIpAddress(): string
    {
        return !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown';
    }

    // -----
    // This function is invoked when the attached notifiers "fires" and results in the superglobals'
    // output being rendered.
    //
    // Note: The notifiers' attachment is dealt with in the class constructor now, so
    // if we get here, the output is the same!
    //
    public function update(&$class, string $eventID)
    {
        echo $this->output();
    }

    // -----
    // Prior to v3.1.0, this was the super_globals_echo function.
    //
    protected function output(): string
    {
        // -----
        // Needed for zc158+, since that $languageLoader results in a circular reference.  If the languageLoader's
        // debug is enabled, don't render the SuperGlobals, as it results in an error from application_bottom.php ...
        // which is where that developer-define is used.
        //
        if (defined('DEV_SHOW_APPLICATION_BOTTOM_DEBUG') && DEV_SHOW_APPLICATION_BOTTOM_DEBUG === true) {
            return '<h2>SuperGlobals output disabled, due to &quot;DEV_SHOW_APPLICATION_BOTTOM_DEBUG&quot; being set!</h2>';
        }

        unset($GLOBALS['languageLoader']);

        ob_start();

        // -----
        // Use the base trait to determine this plugin's directory location.
        //
        $this->detectZcPluginDetails(__DIR__);
        $catalog_dir = $this->pluginManagerInstalledVersionDirectory . 'catalog/';

        // -----
        // Determine the location of the plugin's CSS file, and load it, if found.
        //
        $stylesheet = $catalog_dir . 'includes/templates/default/css/superglobals.css';
        if (is_file($stylesheet)) {
            echo "\n" . '<style>' . file_get_contents($stylesheet) . '</style>';
        }

        echo "\n" . '<div id="superglobals">' . "\n";
        echo '<h4>$GLOBALS:</h4>';

        $this->format($GLOBALS);
        if ($this->zenConfig('SHOW_SUPERGLOBALS_GET_DEFINED_CONSTANTS') === 'true') {
            echo '<h4>get_defined_constants()</h4>' . "\n";
            $defined_constants = get_defined_constants();
            $this->format($defined_constants, false, true);
            unset($defined_constants);
        }
        if ($this->zenConfig('SHOW_SUPERGLOBALS_GET_INCLUDED_FILES') === 'true'){
            echo '<h4>get_included_files()</h4>' . "\n";
            $included_files = get_included_files();
            $this->format($included_files, false, true);
            unset($included_files);
        }
        echo '<h4>The source of the Superglobals Plus script is subject to version 2.0 of the GPL license. Copyright: Paul Mathot, Haarlem The Netherlands.</h4>';
        echo '</div>' . "\n";

        $superglobals_buffer = ob_get_clean();

        if ($this->zenConfig('SHOW_SUPERGLOBALS_POPUP') !== 'false') {
            // add js popup script
            ob_start();
            //-bof-c-v1.4.4-torvista
?>
    <script>
        function superglobalspopup() {
            let newwindow=window.open('','name', 'status=yes, menubar=yes, scrollbars=1, fullscreen=1, resizable=1, toolbar=yes');
            let tmp = newwindow.document;
            tmp.write('<!doctype html>\n');
            tmp.write('<html <?= HTML_PARAMS ?>>\n');
            tmp.write('<head>\n');
            tmp.write('<meta http-equiv="Content-Type" content="text/html; charset=<?= CHARSET ?>">\n');
            tmp.write('<title>Superglobals Popup<\/title>\n');
            tmp.write('<link rel="stylesheet" href="<?= $superglobals_stylesheet ?>">\n');
            tmp.write('<\/head><body>\n');
<?php
            $find = ["\r", "\r\n", "\n"]; //steve how to get carriage returns for better-looking html source?
            $replace = ['', '', ''];
?>
            tmp.write('<?= addslashes(str_replace($find, $replace, $superglobals_buffer)) ?>\n');
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

    // -----
    // For versions prior to v3.1.0, this was the superglobals_format function.
    //
    // Note that all output is echoed, as the expectation is that the calling method
    // has enabled PHP output-buffering!
    //
    protected function format(mixed $superglobals_var, bool $recursion = false, bool $show_customvar = false): void
    {
        // $recursion = FALSE => reset recursion if the function is not called (a second time) from itself
        static $recursionlevel, $toplevel_key, $class;

        if (!isset($class)) {
            $class = '';
        }

        if (!isset($recursionlevel) || $recursion === false) {
            $recursionlevel = 0;
        }
        if ($recursionlevel > $this->showMaxLevel && $this->showMaxLevel >= 1) {
            die('Superglobals message: Maximum recursion level exceeded! (Current recursion level:' . $recursionlevel . ')'); // basic recursion protection
        }
        
        $superglobals_var_type = '';
        if (is_array($superglobals_var)) {
            $superglobals_var_type = 'array';
        } elseif (is_object($superglobals_var)) {
            $superglobals_var_type = 'object';
        }
        if ($superglobals_var_type !== '') {
            $class = ' class="superglobals_' . $superglobals_var_type . '"';
        }

        // $tabs and $tabs_li are used for html source formatting only
        $tabs = "\n";
        $tabs .= str_repeat("\t", $recursionlevel + 1);
        $tabs_li = $tabs . "\t";

        if (is_object($superglobals_var) || (is_countable($superglobals_var) && count($superglobals_var) !== 0)) {
            echo $tabs . '<ul' . $class . '>';
            $numLineItems = 0;
            if (is_array($superglobals_var) || is_object($superglobals_var)) {
                foreach ($superglobals_var as $key => $v) {
                    if (($this->showQueryCache === false && $key === 'queryCache') || ($key !== 0 && in_array($key, $this->exclusions))) {
                        continue;
                    }

                    // store the top level key into $toplevel_key (used during recursion to determine if the value should be echoed or not)
                    if ($recursionlevel === 0) {
                        $toplevel_key = $key;
                    }

                    // check if toplevel_key starts with ....
                    if ($this->showFilterHttp === false || !strstr($toplevel_key, 'HTTP_') == $toplevel_key) {
                        if ($this->showAll === true
                            || $show_customvar
                            || ($toplevel_key === '_GET' && $this->showGet === true)
                            || ($toplevel_key === '_POST' && $this->showPost === true)
                            || ($toplevel_key === '_COOKIE' && $this->showCookie === true)
                            || ($toplevel_key === '_REQUEST' && $this->showRequest === true)
                            || ($toplevel_key === '_SESSION' && $this->showSession === true)
                            || ($toplevel_key === '_SERVER' && $this->showServer === true)
                            || ($toplevel_key === '_ENV' && $this->showEnv === true)
                            || ($toplevel_key === '_FILES' && $this->showFiles === true)
                        ) {
                            $numLineItems++;
                            switch (gettype($v)) {
                                case 'array':
                                    echo $tabs_li .
                                        '<li class="superglobals_array">' .
                                            '<strong>' . $key . '</strong>' .
                                            ' ' .
                                            '<span class="superglobals_type">(array)</span>';
                                    if ($key === 'GLOBALS') {
                                        echo 'Superglobals message: recursion!';
                                    }
                                    $recursionlevel++;
                                    // $GLOBALS is recursive, prevent infinite loop
                                    if ($key !== 'GLOBALS') {
                                        $this->format($v, true);
                                    }
                                    $recursionlevel--;
                                    echo '</li>';
                                    break;

                                case 'object':
                                    echo $tabs_li .
                                        '<li class="superglobals_object">' .
                                            '<strong>' . $key . '</strong>' .
                                            ' ' .
                                            '<span class="superglobals_type">(object: '.  get_class($v) . ')</span>';
                                    $recursionlevel++;
                                    $this->format($v, true);
                                    $recursionlevel--;
                                    echo '</li>';
                                    break;

                                case 'resource':
                                    echo $tabs_li .
                                        '<li class="superglobals_resource">' .
                                            '<strong>' . htmlspecialchars((string)$key, ENT_COMPAT, CHARSET) . '</strong>' .
                                            ' ' .
                                            '<span class="superglobals_type">(resource: ' . get_resource_type($v) . ')</span>' .
                                            ' =&gt; ' . htmlspecialchars((string)$v, ENT_COMPAT, CHARSET) .
                                        '</li>';
                                    break;

                                case 'string':
                                    echo $tabs_li .
                                        '<li>' .
                                            '<strong>' . htmlspecialchars((string)$key, ENT_COMPAT, CHARSET) . '</strong>' .
                                            ' ' .
                                            '<span class="superglobals_type">(string)</span>' .
                                            ' =&gt; ' . htmlentities($v, ENT_COMPAT, CHARSET) .
                                        '</li>';
                                    break;

                                default:
                                    echo $tabs_li .
                                        '<li>' .
                                            '<strong>' . $key . '</strong>' .
                                            ' ' .
                                            '<span class="superglobals_type">(' . gettype($v) . ')</span>' .
                                            ' =&gt; ' . htmlspecialchars((string) $v, ENT_COMPAT, CHARSET) .
                                        '</li>';
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
            if ($superglobals_var_type !== '') {
                echo '<strong>' . $superglobals_var_type . '</strong> ';
            }
            echo 'Superglobals message: empty!</li>';
    //-eof-c-v1.4.4-torvista
            echo $tabs . '</ul>';
        }
    }

    private function zenConfig(string $config_key, $default_value = null)
    {
        static $zen_config_present;
        if (!isset($zen_config_present)) {
            $zen_config_present = function_exists('zen_config');
        }

        if ($zen_config_present) {
            return zen_config($config_key, $default_value);
        }

        if (defined($config_key)) {
            return constant($config_key);
        }

        return $default_value;
    }
}
