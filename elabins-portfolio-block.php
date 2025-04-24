<?php

/**
 * Plugin Name: E-Lab Portfolio Blocks
 * Plugin URI: https://elabins.com
 * Description: Gutenberg blocks for displaying a professional portfolio with GitHub stats, projects, and more.
 * Version: 1.0.0
 * Author: Mohammed Ashad MM
 * Author URI: https://elabins.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: elabins-portfolio-blocks
 * Domain Path: /languages
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
  exit;
}

// Define plugin constants
define('ELABINS_PORTFOLIO_BLOCKS_PATH', plugin_dir_path(__FILE__));
define('ELABINS_PORTFOLIO_BLOCKS_URL', plugin_dir_url(__FILE__));
define("DOMAIN", "elabins-portfolio-blocks");

// Enqueue scripts
function elabins_portfolio_blocks_enqueue_script($name) {
  $manifest = require ELABINS_PORTFOLIO_BLOCKS_PATH . '/build/scripts/' . $name . '/index.asset.php';
  wp_enqueue_script(
    DOMAIN . '-' . $name,
    ELABINS_PORTFOLIO_BLOCKS_URL . '/build/scripts/' . $name . '/index.js',
    $manifest['dependencies'],
    $manifest['version']
  );
}

function elabins_portfolio_blocks_load_assets() {
  elabins_portfolio_blocks_enqueue_script("main");
  wp_enqueue_style(
    DOMAIN . "-main",
    ELABINS_PORTFOLIO_BLOCKS_URL . "/build/index.css",
    [],
    "1.0",
    "all"
  );
}
add_action("wp_enqueue_scripts", "elabins_portfolio_blocks_load_assets");

// Register blocks
function register_blocks() {
  register_block_type(ELABINS_PORTFOLIO_BLOCKS_PATH . "/build/blocks/portfolio-01");
}
add_action("init", "register_blocks");

//Add new block categories
function add_new_block_categories($categories) {
  $categories[] = [
    "slug" => "elabins-portfolio-blocks",
    "title" => "E-Lab Portfolio Blocks",
  ];

  return $categories;
}
add_filter("block_categories_all", "add_new_block_categories");
