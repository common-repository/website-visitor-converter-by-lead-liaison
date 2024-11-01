<?php
/*
Plugin Name: Website Visitor Converter by Lead Liaison
Plugin URI: ...
Description: Convert more website visitors using various modal windows.
Author: Ryan Schefke
Author URI: https://www.leadliaison.com/
Version: 1.0.1
*/


if ( ! defined( 'ABSPATH' ) ) { return; } // Exit if accessed directly

/**
 * Define common constants
 */
if ( ! defined( 'WVC_PLUGIN' ) )   define( 'WVC_PLUGIN',   __FILE__ );
if ( ! defined( 'WVC_DIR_URL' ) )  define( 'WVC_DIR_URL',  plugins_url( '', __FILE__ ) );
if ( ! defined( 'WVC_DIR_PATH' ) ) define( 'WVC_DIR_PATH', plugin_dir_path( __FILE__ ) );


// Include required files
require_once WVC_DIR_PATH . '/include/helper-functions.php';
require_once WVC_DIR_PATH . '/include/post-type.php';
require_once WVC_DIR_PATH . '/include/meta-fields.php';
require_once WVC_DIR_PATH . '/include/plugin-activation.php';
require_once WVC_DIR_PATH . '/include/class-codes.php';
require_once WVC_DIR_PATH . '/include/form.php';
require_once WVC_DIR_PATH . '/include/settings.php';
require_once WVC_DIR_PATH . '/include/codes-list.php';
require_once WVC_DIR_PATH . '/include/submissions.php';
require_once WVC_DIR_PATH . '/include/branding.php';
