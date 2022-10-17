# payment-split-epayco-woo
Integration of Split ePayco for Woocommerce


```php
add_filter( 'payment_split_epayco_pse_client_id_vendor', 'payment_split_epayco_pse_client_id_vendor', 10, 1 );

function payment_split_epayco_pse_client_id_vendor($order_id)
{
    $client_id = '5048020';
    return $client_id;
}

```
