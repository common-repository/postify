<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://sakhawat.vercel.app/
 * @since             1.0.0
 * @package           Postify
 *
 * @wordpress-plugin
 * Plugin Name:       Postify
 * Plugin URI:        https://wp-plugins.themeatelier.net/postify/postify-free/
 * Description:       Porfessional Blog Post Layouts For WordPress
 * Version:           1.0.0
 * Author:            ThemeAtelier
 * Author URI:        https://themeatelier.net/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       postify
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

define('POSTIFY_VERSION', '1.0.0');
define('POSTIFY_PLUGIN_URL', trailingslashit(plugins_url('/', __FILE__)));
function postify_activate()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-postify-activator.php';
	Postify_Activator::activate();
}


function postify_deactivate()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-postify-deactivator.php';
	Postify_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'postify_activate');
register_deactivation_hook(__FILE__, 'postify_deactivate');


require plugin_dir_path(__FILE__) . 'includes/class-postify.php';

function postify_run()
{

	$plugin = new Postify();
	$plugin->run();
}
postify_run();

