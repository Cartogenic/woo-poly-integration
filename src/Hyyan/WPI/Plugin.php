<?php

/**
 * This file is part of the hyyan/woo-poly-integration plugin.
 * (c) Hyyan Abo Fakher <tiribthea4hyyan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hyyan\WPI;

use Hyyan\WPI\Tools\FlashMessages;

/**
 * Plugin
 *
 * @author Hyyan Abo Fakher <tiribthea4hyyan@gmail.com>
 */
class Plugin
{

    /**
     * Construct the plugin
     */
    public function __construct() {

        // Register flash messages
        FlashMessages::register();

        // Activate plugin
        add_action('init', array($this, 'activate'));

        // Load textdomain
        $this->loadTextDomain();

    }

    /**
     * Load plugin language file
     */
    public function loadTextDomain() {
        load_plugin_textdomain( 'woopoly', false, plugin_basename( WOOPOLY_DIR ) . '/languages' );
    }

    /**
     * Activate plugin
     *
     * The plugin will register its core if the dependencies are met, otherwise
     * it will show an admin error message
     *
     * @return boolean false if plugin can not be activated
     */
    public function activate() {
        if ( ! static::canActivate() ) {
            FlashMessages::remove(MessagesInterface::MSG_SUPPORT);
            FlashMessages::add(
                    MessagesInterface::MSG_ACTIVATE_ERROR
                    , static::getView('Messages/activateError')
                    , array('error')
                    , true
            );

            deactivate_plugins( plugin_basename( WOOPOLY_FILE ) );
            return false;
        }

        FlashMessages::remove(MessagesInterface::MSG_ACTIVATE_ERROR);
        FlashMessages::add(
                MessagesInterface::MSG_SUPPORT
                , static::getView('Messages/support')
        );

        $this->registerCore();
    }

    /**
     * Check if the plugin can be activated
     *
     * @return boolean true if can be activated , false otherwise
     */
    public static function canActivate()
    {
        $requiredPlugins = array(
            'polylang/polylang.php',
            'woocommerce/woocommerce.php'
        );

        $plugins = apply_filters('active_plugins', get_option('active_plugins'));

        foreach ($requiredPlugins as $name) {
            if (!in_array($name, $plugins)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get plugin version
     *
     * @return integer
     */
    public static function getVersion() {
        $version = get_option( WOOPOLY_TOKEN . '-version' );

        if ( false != $version ) {
            return $version;
        } else {
            $data = get_plugin_data( WOOPOLY_FILE );
            return $data['Version'];
        }
    }

    /**
     * Get plugin view
     *
     * @param string $name view name
     * @param array  $vars array of vars to pass to the view
     *
     * @return string the view content
     */
    public static function getView($name, array $vars = array())
    {
        $result = '';
        $path = WOOPOLY_DIR . 'src/Hyyan/WPI/Views/' . $name . '.php';
        if (file_exists($path)) {
            ob_start();
            include($path);
            $result = ob_get_clean();
        }

        return $result;
    }

    /**
     * Add plugin core classes
     */
    protected function registerCore()
    {
        new Admin\Settings();
        new Emails();
        new Cart();
        new Login();
        new Order();
        new Pages();
        new Endpoints();
        new Product\Product();
        new Taxonomies\Taxonomies();
        new Media();
        new Permalinks();
        new Language();
        new Coupon();
        new Reports();
        new Widgets\SearchWidget();
        new Widgets\LayeredNav();
        new Gateways();
        new Shipping();
    }

}
