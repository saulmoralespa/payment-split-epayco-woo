<?php

class Payment_Split_Epayco_Woo_Pse_Plugin
{
    /**
     * Filepath of main plugin file.
     *
     * @var string
     */
    public $file;
    /**
     * Plugin version.
     *
     * @var string
     */
    public $version;
    /**
     * Absolute plugin path.
     *
     * @var string
     */
    public $plugin_path;
    /**
     * Absolute plugin URL.
     *
     * @var string
     */
    public $plugin_url;
    /**
     * Absolute path to plugin includes dir.
     *
     * @var string
     */
    public $includes_path;
    /**
     * @var WC_Logger
     */
    public $logger;
    /**
     * @var bool
     */
    private $_bootstrapped = false;

    public function __construct($file, $version)
    {
        $this->file = $file;
        $this->version = $version;
        // Path.
        $this->plugin_path   = trailingslashit( plugin_dir_path( $this->file ) );
        $this->plugin_url    = trailingslashit( plugin_dir_url( $this->file ) );
        $this->includes_path = $this->plugin_path . trailingslashit( 'includes' );
        $this->logger = new WC_Logger();
    }

    public function run_split_epayco()
    {
        try{
            if ($this->_bootstrapped){
                throw new Exception( __( 'Payment Split ePayco can only be called once'));
            }
            $this->_run();
            $this->_bootstrapped = true;
        }catch (Exception $e){
            if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
                requeriments_payment_split_epayco_pse_notices('Payment Split ePayco: ' . $e->getMessage());
            }
        }
    }

    protected function _run()
    {
        require_once ($this->includes_path . 'class-gateway-split-epayco-pse.php');

        add_filter( 'plugin_action_links_' . plugin_basename( $this->file), array( $this, 'plugin_action_links'));
        add_filter( 'woocommerce_payment_gateways', array($this, 'woocommerce_split_epayco_add_gateway'));
        add_action( 'wp_enqueue_scripts', array($this, 'enqueue_scripts' ) );
        add_action( 'template_redirect', array('WC_Payment_Split_Epayco_PSE', 'split_epayco_response'));
    }

    public function plugin_action_links($links)
    {
        $plugin_links = array();
        $plugin_links[] = '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=split_epayco_pse') . '">' . 'Configuraciones' . '</a>';
        return array_merge( $plugin_links, $links );
    }

    public function woocommerce_split_epayco_add_gateway($methods)
    {

        $methods[] = 'WC_Payment_Split_Epayco_PSE';
        return $methods;
    }

    public function enqueue_scripts()
    {
        if(is_checkout() || is_view_order_page()){
            wp_enqueue_script( 'checkout-epayco', 'https://checkout.epayco.co/checkout.js', array( 'jquery' ), $this->version, false );
        }
    }

    public function log($message = '')
    {
        if (is_array($message) || is_object($message))
            $message = print_r($message, true);
        $this->logger->add('split-epayco', $message);
    }
}