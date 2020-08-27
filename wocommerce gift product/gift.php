<?php
/**
	This is an additional feature for woocommerce that allows 
	the user to send a virtual product as a gift to another user.
*/

/** 
	Store the custom data of product to cart object when data is passed
	throught cart URL
*/
add_filter( 'woocommerce_add_cart_item_data', 'save_custom_product_data', 10, 2 );
function save_custom_product_data( $cart_item_data, $product_id ) {
    $bool = false;
    $data = array();
    if( isset( $_REQUEST['send-as-gift'] ) ) {
        $cart_item_data['custom_data']['send-as-gift'] = $_REQUEST['send-as-gift'];
        $data['send-as-gift'] = $_REQUEST['send-as-gift'];
        $bool = true;
    }
   
    if( $bool ) {
        // below statement make sure every add to cart action as unique line item
        $cart_item_data['custom_data']['unique_key'] = md5( microtime().rand() );
        WC()->session->set( 'custom_variations', $data );
    }
    return $cart_item_data;
}

/**
	Displaying the custom attributes in cart and checkout items
*/
add_filter( 'woocommerce_get_item_data', 'customizing_cart_item_data', 10, 2 );
function customizing_cart_item_data( $cart_data, $cart_item ) {
    $custom_items = array();
    if( ! empty( $cart_data ) ) $custom_items = $cart_data;

    // Get the data (custom attributes) and set them
    if( ! empty( $cart_item['custom_data']['send-as-gift'] ) )
        $custom_items[] = array(
            'name'      => 'Send as Gift',
            'value'     => $cart_item['custom_data']['send-as-gift'] ? 'Yes' : '',
        );
    return $custom_items;
}

add_action( 'woocommerce_after_add_to_cart_button', 'ss_add_gift_button' );
 
/**
	Add send as gift button in single product page
*/
function ss_add_gift_button() {
   global $product;
   $id = $product->id;
   $categories = $product->get_categories();
   $category = "Virtual Workshops";
   if(strpos($categories, $category) !== false){
    echo "<a class='single_send_as_gift_button button alt' href='/cart/?add-to-cart=".$id."&send-as-gift=true'>Send as Gift</a>";
    }
}

/**
	Add notice to the checkout page if both gifting and non-gifting products are added
*/
 function add_notice_checkout_page($checkout)
{
    $total_item = 0;
    $gift_item = 0;
    foreach ( WC()->cart->get_cart() as $cart_item ) {
        $total_item++;
        if($cart_item['custom_data']['send-as-gift'] == true){
            $gift_item++;
        }
        if($total_item > 0 && $gift_item > 0 && $total_item != $gift_item){
            echo "<div class='woocommerce-info' role='alert'><strong>Note:</strong> Please place only the order that you want to send as gift.</div>";
        }
    }
    
}
add_action('woocommerce_before_checkout_form','add_notice_checkout_page');
	
/**
	Add the additional field to the checkout page to get Recipient's data
*/
function customise_checkout_field($checkout)
{
    $has_gift = false;
    $total_item = 0;
    $gift_item = 0;
    foreach ( WC()->cart->get_cart() as $cart_item ) {
        $total_item++;
        if(!$has_gift){
            $has_gift = $cart_item['custom_data']['send-as-gift'];
        }
        if($cart_item['custom_data']['send-as-gift'] == true){
            $gift_item++;
        }
    }
    if($has_gift == true && $total_item == $gift_item){
   echo '<div class="customised-checkout-fields-wrap"><h3>' . __('Buying as a Gift?&nbsp;') . '<small>' . __('Please fill out below.') . '</small></h3><div class="customised-checkout-fields">';
   woocommerce_form_field(
      'gift_first_name', array(
      'type' => 'text',
      'class' => array(
         'form-row-wide'
      ) ,
      'label' => __('Recipient\'s First Name') ,
      'placeholder' => __('') ,
      'required' => false,
   ), $checkout->get_value('gift_first_name')
   );
   woocommerce_form_field(
      'gift_last_name', array(
      'type' => 'text',
      'class' => array(
         'form-row-wide'
      ) ,
      'label' => __('Recipient\'s Last Name') ,
      'placeholder' => __('') ,
      'required' => false,
   ), $checkout->get_value('gift_last_name')
   );
   woocommerce_form_field(
      'gift_email_address', array(
      'type' => 'email',
      'class' => array(
         'form-row-wide'
      ) ,
      'label' => __('Recipient\'s Email address') ,
      'placeholder' => __('') ,
      'required' => false,
   ), $checkout->get_value('gift_email_address')
   );
   woocommerce_form_field(
      'gift_phone', array(
      'type' => 'text',
      'class' => array(
         'form-row-wide'
      ) ,
      'label' => __('Recipient\'s Phone') ,
      'placeholder' => __('') ,
      'required' => false,
   ), $checkout->get_value('gift_phone')
   );
   woocommerce_form_field(
      'gift_sp_msg', array(
      'type' => 'textarea',
      'class' => array(
         'form-row-wide'
      ) ,
      'label' => __('Message to the Recipient') ,
      'placeholder' => __('Write a special message.') ,
      'required' => false,
   ), $checkout->get_value('gift_sp_msg')
   );
   global $wp;
   $cpagelink = $wp->request;
   echo '<p class="form-row form-row-wide hidden" style="display: none;">
            <input type="hidden" class="input-hidden" name="page_link" id="page_link" value="' . $cpagelink . '">
    </p>';
   echo '</div></div>';
    }
}
add_action('woocommerce_after_order_notes', 'customise_checkout_field');

/**
	Update the meta value given in custom fieldin checkout page
*/
function custom_checkout_field_update_order_meta($order_id){
   if (!empty($_POST['gift_first_name'])) {
      update_post_meta($order_id, 'gift_first_name',sanitize_text_field($_POST['gift_first_name']));
   }
   if (!empty($_POST['gift_last_name'])) {
      update_post_meta($order_id, 'gift_last_name',sanitize_text_field($_POST['gift_last_name']));
   }
   if (!empty($_POST['gift_email_address'])) {
      update_post_meta($order_id, 'gift_email_address',sanitize_text_field($_POST['gift_email_address']));
   }
   if (!empty($_POST['gift_phone'])) {
      update_post_meta($order_id, 'gift_phone',sanitize_text_field($_POST['gift_phone']));
   }
   if (!empty($_POST['gift_sp_msg'])) {
      update_post_meta($order_id, 'gift_sp_msg',sanitize_text_field($_POST['gift_sp_msg']));
   }
}
add_action('woocommerce_checkout_update_order_meta', 'custom_checkout_field_update_order_meta');

/**
	Create user if not exists, send gift email to the Recipient's email and transfer order to the Recipient's account once the order status is complete
*/
add_action("woocommerce_order_status_completed", "ss_gift_additional_notification");
function ss_gift_additional_notification($order_id, $checkout=null) {
   global $woocommerce;
   $order = new WC_Order( $order_id );
   $items = $order->get_items();
   $gift_fname = get_post_meta( $order->get_order_number(), 'gift_first_name', true );
   $gift_lname = get_post_meta( $order->get_order_number(), 'gift_last_name', true );
   $gift_email = get_post_meta( $order->get_order_number(), 'gift_email_address', true );
   $gift_phone = get_post_meta( $order->get_order_number(), 'gift_phone', true );
   $gift_sp_message = get_post_meta( $order->get_order_number(), 'gift_sp_msg', true );
   if(''!=$gift_email ) {
       if(empty(get_user_by( 'email', $gift_email ))){
		   //Create new user if not exists
			$user_id = wc_create_new_customer( $gift_email, $gift_email, $gift_email );
			update_user_meta( $user_id, "billing_first_name", $gift_fname );
			update_user_meta( $user_id, "billing_last_name", $gift_lname );
			$text = "Login using following detail to receive the gift. <br> <a href='https://siteurl.com/my-account/orders/' target='blank'>https://siteurl.com/my-account/orders/</a><br> Username: " . $gift_email . " <br>Password: ". $gift_email;
       }else{
		   //Get user if already exists
           $user_id = get_user_by( 'email', $gift_email )->ID;
           $text = "It seems that " . $gift_email . " already has an account. Please login to receive the gift. <br> <a href='https://siteurl.com/my-account/orders/' target='blank'>https://siteurl.com/my-account/orders/</a>";
       }
       iF($user_id){
		   //Transfer order to the Recipient's account
           update_post_meta( $order_id, '_customer_user', $user_id );
       }
        
      // Create a mailer
      $mailer = $woocommerce->mailer();
      $message_body = '';
         $message_body .=  __( 'SOMEONE GAVE YOU A GIFT.' );
         foreach($items as $item){
             $message_body .= sprintf( __( '<br>%1$s'),$item['name']);
         }
      $message_body .= __( "<h3>HERE'S WHAT YOU NEED TO KNOW</h3>" );
      $message_body .= __( '<ul style="list-style: none;">' );
            $message_body .= sprintf( __( '<li>GIFT: %1$s%2$s</li>' ), get_woocommerce_currency_symbol( $order->get_currency() ), $order->get_total() );
        $message_body .= sprintf(__( '<li>ORDER: #%s</li>'), $order->get_order_number());
        $message_body .= sprintf(__( '<li>GIVEN BY: %s</li>'), $order->get_formatted_billing_full_name() );
        if($gift_fname || $gift_lname){
           $message_body .= sprintf(__( '<li>GIVEN TO: %1$s %2$s</li>'), $gift_fname, $gift_lname );
        }$message_body .= __( '</ul>' );
      if($gift_sp_message){
            $message_body .= __( '<h3>A LITTLE NOTE FROM YOUR GIFTER</h3>' );
            $message_body .= __( '<p>'.$gift_sp_message.'</p>' );
      }
      if($text){
          $message_body .= $text;
      }
      $message = $mailer->wrap_message(
      // Message head and message body.
        sprintf( __( 'CONGRATULATIONS' ), $order->get_order_number() ), $message_body );
      // Cliente email, email subject and message.
      $mailer->send( $gift_email, sprintf( __( '%s sent you a gift to Wedding Workshop & More!' ), $order->get_formatted_billing_full_name() ), $message );
   }
}

/**
	Remove Optional label from the checkout form label
*/
add_filter( 'wp_footer' , 'remove_checkout_optional_fields_label_script' );
function remove_checkout_optional_fields_label_script() {
    // Only on checkout page
    if( ! ( is_checkout() && ! is_wc_endpoint_url() ) ) return;

    $optional = '&nbsp;<span class="optional">(' . esc_html__( 'optional', 'woocommerce' ) . ')</span>';
    ?>
    <script>
    jQuery(function($){
        // On "update" checkout form event
        $(document.body).on('update_checkout', function(){
            $('#gift_first_name_field label > .optional').remove();
            $('#gift_last_name_field label > .optional').remove();
            $('#gift_email_address_field label > .optional').remove();
            $('#gift_phone_field label > .optional').remove();
            $('#gift_sp_msg_field label > .optional').remove();
        });
    });
    </script>
    <?php
}