<?php

class WC_Payment_Split_Epayco_PSE extends WC_Payment_Gateway
{
    public function __construct()
    {
        $this->id = 'split_epayco_pse';
        $this->icon = payment_split_epayco_pse()->plugin_url . 'assets/images/logo.png?ver='. PAYMENT_SPLIT_EPAYCO_PSE_VERSION;
        $this->method_title = __('Split ePayco');
        $this->method_description = __('Paga con Split ePayco');
        $this->description  = $this->get_option( 'description' );
        $this->order_button_text = __('Pagar');
        $this->supports = [
            'products'
        ];
        $this->title = $this->get_option('title');
        $this->debug = $this->get_option( 'debug' );
        $this->isTest = (bool)$this->get_option( 'environment' );
        $this->custIdCliente = $this->get_option('custIdCliente');
        $this->pKey = $this->get_option('pKey');
        $this->apiKey = $this->get_option('apiKey');
        $this->privateKey = $this->get_option('privateKey');
        $this->comision = $this->get_option('comision');
        $this->init_form_fields();
        $this->init_settings();
        add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
        add_action('woocommerce_api_'.strtolower(get_class($this)), array($this, 'confirmation_ipn'));
        add_action('woocommerce_receipt_' . $this->id, array($this, 'receipt_page'));
    }

    public function init_form_fields()
    {
        $this->form_fields = require( dirname( __FILE__ ) . '/admin/settings.php' );
    }

    public function is_available()
    {
        $is_available =  parent::is_available() &&
            !empty($this->apiKey) &&
            !empty($this->privateKey);

        return apply_filters( 'payment_' . $this->id . '_is_available', $is_available);
    }

    public function admin_options()
    {
        ?>
        <h3><?php echo $this->title; ?></h3>
        <p><?php echo $this->method_description; ?></p>
        <table class="form-table">
            <?php
            $this->generate_settings_html();
            ?>
        </table>
        <?php
    }

    public function process_payment($order_id)
    {
        $order = new WC_Order($order_id);
        WC()->cart->empty_cart();
        return [
            'result' => 'success',
            'redirect' => $order->get_checkout_payment_url(true)
        ];
    }


    public function getUrlNotify()
    {
        $url = trailingslashit(get_bloginfo( 'url' )) . trailingslashit('wc-api') . strtolower(get_class($this));
        return $url;
    }

    public function receipt_page($orderId)
    {
        $order = new WC_Order($orderId);
        $currency = $order->get_currency();
        $currency = wc_strtolower($currency);
        $descripcionParts = [];
        foreach ($order->get_items() as $product) {
            $clearData = str_replace('_', ' ', $this->string_sanitize($product['name']));
            $descripcionParts[] = $clearData;
        }
        $descripcion = implode(' - ', $descripcionParts);

        $tax=$order->get_total_tax();
        $tax=round($tax,2);
        if((int)$tax>0){
            $base_tax=$order->get_total()-$tax;
        }else{
            $base_tax=$order->get_total();
            $tax=0;
        }

        $basedCountry = WC()->countries->get_base_country();
        $name_billing=$order->get_billing_first_name().' '.$order->get_billing_last_name();
        $address_billing=$order->get_billing_address_1();
        $phone_billing=$order->get_billing_phone();
        $email_billing=$order->get_billing_email();

        ?>
        <script>
            var handler = ePayco.checkout.configure({
                key: '<?php echo $this->apiKey; ?>',
                test: <?php echo $this->isTest ? 'true' : 'false'; ?>
            });
            var data = {
                name: "<?php echo $descripcion; ?>",
                description: "<?php echo $descripcion; ?>",
                invoice: "<?php echo $orderId; ?>",
                currency: "<?php echo $currency; ?>",
                amount: "<?php echo $order->get_total(); ?>",
                tax_base: "<?php echo $base_tax; ?>",
                tax: "<?php echo $tax; ?>",
                country: "<?php echo $basedCountry; ?>",
                lang: "es",
                split_app_id: "<?php echo $this->custIdCliente; ?>",
                split_merchant_id: "<?php echo $this->custIdCliente; ?>",
                split_type: "02",
                split_primary_receiver: "<?php echo $this->custIdCliente; ?>",
                split_primary_receiver_fee: "0",
                splitpayment: "true",
                split_rule: "multiple",
                split_receivers: [
                    { "id": "<?php echo $this->get_client_id_vendor($orderId); ?>",
                      "total": "<?php echo $order->get_total(); ?>",
                      "iva": "",
                      "base_iva": "",
                      "fee": "<?php echo $this->comision; ?>"
                    }
                ],
                external: "false",
                //Los parámetros extras deben ser enviados como un string
                extra1: "<?php echo $this->get_value_extra1($orderId); ?>",
                extra2: "<?php echo $this->get_value_extra2($orderId); ?>",
                extra3: "extra3",
                confirmation: "<?php echo $this->getUrlNotify(); ?>",
                response: "<?php echo $order->get_checkout_order_received_url(); ?>",
                //Atributos cliente
                name_billing: "<?php echo $name_billing; ?>",
                address_billing: "<?php echo $address_billing; ?>",
                email_billing: "<?php echo $email_billing; ?>",
                mobilephone_billing: "<?php echo $phone_billing; ?>"
            }

            const openSplitEpaycoChekout = function () {
                handler.open(data)
            }

            openSplitEpaycoChekout();

        </script>
        <?php
    }


    public function get_client_id_vendor($order_id)
    {
        $client_id = 0;
        return apply_filters( 'payment_' . $this->id . '_client_id_vendor', $client_id, $order_id);
    }


    public function get_value_extra1($order_id)
    {
        $user_id = '';
        return apply_filters( 'payment_' . $this->id . '_value_extra1', $user_id, $order_id);
    }

    public function get_value_extra2($order_id)
    {
        $user_id = '';
        return apply_filters( 'payment_' . $this->id . '_value_extra2', $user_id, $order_id);
    }


    public function string_sanitize($string) {

        $clean = preg_replace('/\s+/', "_", $string);
        return preg_replace("/[^a-zA-Z0-9]/", "", $clean);
    }

    public function confirmation_ipn()
    {
        $body = file_get_contents('php://input');
        parse_str($body, $data);

        if($this->debug === 'yes')
            payment_split_epayco_pse()->log('confirmation_ipn: ' . print_r($data, true));

        $x_signature = $data['x_signature'];
        $x_cod_transaction_state = $data['x_cod_transaction_state'];
        $order_id = $data['x_id_factura'];

        if ($x_cod_transaction_state == 3) return;

        $signature = $this->generate_signature($data);
        if ($x_signature === $signature){

            $order = new WC_Order($order_id);

            if ($x_cod_transaction_state == 1){
                $order->payment_complete();


            }else{
                $order->update_status('failed');
            }
        }

        header("HTTP/1.1 200 OK");
    }

    public static function split_epayco_response()
    {
        if( ! is_wc_endpoint_url( 'order-received' ) ||
            empty( $_GET[ 'key' ] ) ||
            empty( $_GET[ 'ref_payco' ] )) {
            return;
        }

        $instance = new self();

        $ref_payco = sanitize_text_field($_GET['ref_payco']);
        $url = 'https://secure.epayco.co/validation/v1/reference/'.$ref_payco;
        $response = wp_remote_get(  $url );
        $body = wp_remote_retrieve_body( $response );

        if (is_wp_error($body)) return;

        $jsonData = json_decode($body, true);
        $data = $jsonData['data'];
        $x_signature = $data['x_signature'];
        $x_cod_transaction_state = $data['x_cod_transaction_state'];
        $order_id = $data['x_id_factura'];

        $signature = $instance->generate_signature($data);
        $order = new WC_Order($order_id);

        if ($x_signature === $signature){

            if ($x_cod_transaction_state == 1){
                $order->payment_complete();
            }
        }
    }

    public function generate_signature(array $data)
    {
        return hash('sha256',
            $this->custIdCliente.'^'
            .$this->pKey.'^'
            .$data['x_ref_payco'].'^'
            .$data['x_transaction_id'].'^'
            .$data['x_amount'].'^'
            .$data['x_currency_code']
        );
    }
}