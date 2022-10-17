jQuery( function( $ ) {

    const $checkout_form = $( 'form.checkout, form#order_review' );

    function splitEpaycoFormHandler(){
        if(!$('form[name="checkout"] input[name="payment_method"]:checked').val() === 'split_epayco_pse') return true;

        openSplitEpaycoChekout();
        $checkout_form.submit();
        return false;

    }

    $( 'form.checkout' ).on( 'checkout_place_order', splitEpaycoFormHandler );
    $( 'form#order_review' ).on( 'submit', splitEpaycoFormHandler );
});