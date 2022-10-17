<?php
/*
Plugin Name: Payment Split ePayco
Description: Integration of Split ePayco for Woocommerce
Version: 1.0.0
Author: Saul Morales Pacheco
Author URI: https://saulmoralespa.com
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
WC tested up to: 4.8
WC requires at least: 4.0
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if(!defined('PAYMENT_SPLIT_EPAYCO_PSE_VERSION')){
    define('PAYMENT_SPLIT_EPAYCO_PSE_VERSION', '1.0.0');
}

add_action('plugins_loaded','payment_split_epayco_pse_init',0);

function payment_split_epayco_pse_init(){
    if (!requeriments_payment_split_epayco_pse())
        return;

    payment_split_epayco_pse()->run_split_epayco();
}

function requeriments_payment_split_epayco_pse_notices($notice){
    ?>
    <div class="error notice">
        <p><?php echo $notice; ?></p>
    </div>
    <?php
}

function requeriments_payment_split_epayco_pse(){

    if ( !in_array(
        'woocommerce/woocommerce.php',
        apply_filters( 'active_plugins', get_option( 'active_plugins' ) ),
        true
    ) ) {
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
            add_action(
                'admin_notices',
                function() {
                    requeriments_payment_split_epayco_pse_notices('Payment Split ePayco: Woocommerce debe estar instalado y activo');
                }
            );
        }
        return false;
    }

    return true;
}

function payment_split_epayco_pse(){
    static $plugin;
    if (!isset($plugin)){
        require_once('includes/class-payment-split-epayco-woo-pse-plugin.php');
        $plugin = new Payment_Split_Epayco_Woo_Pse_Plugin(__FILE__,PAYMENT_SPLIT_EPAYCO_PSE_VERSION);
    }
    return $plugin;
}