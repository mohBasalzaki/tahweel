<?php

/**
 * Plugin Name: Tahweel for Woocommerce
 * Plugin URI: https://mohamedalzaki.com
 * Author Name: MOHAMED ALZAKI
 * Author URI: https://mohamedalzaki.com
 * Description: This plugin allows for local content payment systems.
 * Version: 1.5
 * License: 0.1.0
 * License URL: http://www.gnu.org/licenses/gpl-2.0.txt
 * text-domain: tahweel-woo
*/ 

if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) return;

add_action( 'plugins_loaded', 'tahweel_init', 11 );

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'tahweel-bundle-css', plugin_dir_url( __FILE__ ) . 'inc/css/bundle.css' );
	wp_enqueue_script( 'tahweel-bundle-js', plugin_dir_url( __FILE__ ) . 'inc/js/bundle.js' );
});

function tahweel_init() {
    if( class_exists( 'WC_Payment_Gateway' ) ) {
        class WC_bank_of_khartoum_pay_Gateway extends WC_Payment_Gateway {
            public function __construct() {
                $this->id   = 'tahweel';
                $this->icon = apply_filters( 'woocommerce_bank_of_khartoum_icon', plugins_url('/inc/image/logo.svg', __FILE__ ) );
                $this->has_fields = false;
                $this->method_title = __( 'Tahweel', 'tahweel-woo');
                $this->method_description = __( 'Tahweel is a Bank Transfer Plugin.', 'tahweel-woo');

                $this->title = $this->get_option( 'title' );
                $this->description = $this->get_option( 'description' );
                $this->instructions = $this->get_option( 'instructions', $this->description );

                $this->bank_name = $this->get_option( 'bank_name' );
                $this->account_name = $this->get_option( 'account_name' );
                $this->account_number = $this->get_option( 'account_number' );
                $this->iban = $this->get_option( 'iban' );

                $this->init_form_fields();
                $this->init_settings();

                apply_filters( 'tahweel-woo', plugins_url('/languages', __FILE__ ) );

                add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
                add_action( 'woocommerce_thank_you_' . $this->id, array( $this, 'thank_you_page' ) );
            }

            public function init_form_fields() {
                $this->form_fields = apply_filters( 'woo_bank_of_khartoum_pay_fields', array(
                    'enabled' => array(
                        'title' => __( 'Enable/Disable', 'tahweel-woo'),
                        'type' => 'checkbox',
                        'label' => __( 'Enable or Disable Tahweel', 'tahweel-woo'),
                        'default' => 'yes'
                    ),
                    'title' => array(
                        'title' => __( 'Bank Transfer', 'tahweel-woo'),
                        'type' => 'text',
                        'default' => __( 'Bank Transfer', 'tahweel-woo')
                    ),
                    'description' => array(
                        'title' => __( 'Tahweel Description', 'tahweel-woo'),
                        'type' => 'textarea',
                        'default' => __( 'Please remit your payment to the shop to allow for the delivery to be made', 'tahweel-woo')
                    ),
                    'instructions' => array(
                        'title' => __( 'Instructions', 'tahweel-woo'),
                        'type' => 'textarea',
                        'default' => __( 'Your order will be executed only after checking the payment process', 'tahweel-woo')
                    ),
                    'bank_name' => array(
                        'title' => __( 'Bank Name', 'tahweel-woo'),
                        'type' => 'select',
                        'options' => array(
                            '' => __( '- Choose your bank -', 'tahweel-woo' ),
                            'National Bank of Sudan' => __('National Bank of Sudan', 'tahweel-woo'),
                            'National Bank of Egypt' => __('National Bank of Egypt', 'tahweel-woo'),
                            'Sudanese Islamic Bank' => __('Sudanese Islamic Bank', 'tahweel-woo'),
                            'Agricultural Bank of Sudan' => __('Agricultural Bank of Sudan', 'tahweel-woo'),
                            'Saudi Sudanese Bank' => __('Saudi Sudanese Bank', 'tahweel-woo'),
                            'Sudanese French Bank' => __('Sudanese French Bank', 'tahweel-woo'),
                            'Sudanese Egyptian Bank' => __('Sudanese Egyptian Bank', 'tahweel-woo'),
                            'Arabic Sudanese Bank' => __('Arabic Sudanese Bank', 'tahweel-woo'),
                            'Real Estate Commercail Bank' => __('Real Estate Commercail Bank', 'tahweel-woo'),
                            'Arab Bank for Economic Development in Africa (BADEA)' => __('Arab Bank for Economic Development in Africa (BADEA)', 'tahweel-woo'),
                            'Ivory Bank' => __('Ivory Bank', 'tahweel-woo'),
                            'Omdurman National Bank' => __('Omdurman National Bank', 'tahweel-woo'),
                            'Family Bank' => __('Family Bank', 'tahweel-woo'),
                            'Financial Investment Bank' => __('Financial Investment Bank', 'tahweel-woo'),
                            'AlBaraka Bank Sudan' => __('AlBaraka Bank Sudan', 'tahweel-woo'),
                            'Tadamon Bank' => __('Tadamon Bank', 'tahweel-woo'),
                            'Animal Resources Bank' => __('Animal Resources Bank', 'tahweel-woo'),
                            'Aljazeera Sudanese Jordanian Bank' => __('Aljazeera Sudanese Jordanian Bank', 'tahweel-woo'),
                            'Bank of Khartoum' => __('Bank of Khartoum', 'tahweel-woo'),
                            'Workers National Bank' => __('Workers National Bank', 'tahweel-woo'),
                            'United Capital Bank' => __('United Capital Bank', 'tahweel-woo'),
                            'AlNile Bank' => __('AlNile Bank', 'tahweel-woo'),
                            'Blue Nile Mashreg Bank' => __('Blue Nile Mashreg Bank', 'tahweel-woo'),
                            'El Nilein Bank' => __('El Nilein Bank', 'tahweel-woo'),
                            'Byblos Bank' => __('Byblos Bank', 'tahweel-woo'),
                            'Export Development Bank' => __('Export Development Bank', 'tahweel-woo'),
                            'Faisal Islamic Bank Sudan' => __('Faisal Islamic Bank Sudan', 'tahweel-woo'),
                            'Savings and Social Development Bank' => __('Savings and Social Development Bank', 'tahweel-woo'),
                            'Balad Bank Sudan' => __('Balad Bank Sudan', 'tahweel-woo'),
                            'Industrial Development Bank' => __('Industrial Development Bank', 'tahweel-woo'),
                            'BSIC Bank' => __('BSIC Bank', 'tahweel-woo'),
                            'Al Salam Bank' => __('Al Salam Bank', 'tahweel-woo'),
                            'Commercial Farmer Bank' => __('Commercial Farmer Bank', 'tahweel-woo')
                        ),
                        'required' => true
                    ),
                    'account_name' => array(
                        'title' => __( 'Account Name', 'tahweel-woo'),
                        'type' => 'text',
                        'required' => true
                    ),
                    'account_number' => array(
                        'title' => __( 'Account Number', 'tahweel-woo'),
                        'type' => 'number',
                        'required' => true
                    ),
                    'iban' => array(
                        'title' => __( 'IBAN', 'tahweel-woo'),
                        'type' => 'number',
                        'required' => false
                    ),
                ));
            }

            public function payment_fields() {
             
                // I will echo() the form, but you can close PHP tags and print it directly in HTML
                echo '<fieldset id="wc-' . esc_attr( $this->id ) . '-cc-form" class="wc-credit-card-form wc-payment-form" style="background:transparent;">';
             
                // Add this action hook if you want your custom payment gateway to support it
                do_action( 'woocommerce_credit_card_form_start', $this->id );
             
                // I recommend to use inique IDs, because other gateways could already use #ccNo, #expdate, #cvc
                echo '
                <h6>'.__('Vendor Details', 'tahweel-woo').'</h6>
                <fieldset class="bank-vendor">
                    <div class="form-row form-row-wide">
                        <p style="margin: 0;">'.__('Bank Name', 'tahweel-woo').'</p>
                        <h6 style="margin: 0;">'.esc_html( $this->bank_name ).'</h6>
                    </div>
                    
                    <div class="form-row form-row-first">
                        <p style="margin: 0;">'.__('Vendor Name', 'tahweel-woo').'</p>
                        <h6 style="margin: 0;">'.esc_html( $this->account_name ).'</h6>
                    </div>
                    
                    <div class="form-row form-row-last">
                        <p style="margin: 0;">'.__('Account Number', 'tahweel-woo').'</p>
                        <h6 style="margin: 0;">'.esc_html( $this->account_number, $this->iban ).'</h6>
                    </div>
                </fieldset>

                <h6>'.__('Customer Details', 'tahweel-woo').'</h6>
                <div class="form-row form-row-wide">
                    <label>'.__('Bank Name', 'tahweel-woo').' <span class="required">*</span></label>
                    <select name="" id="select_bank">
                        <option disabled selected>- '.__('Choose your bank', 'tahweel-woo').' -</option>
                        <option value="'.__('National Bank of Sudan', 'tahweel-woo').'">'.__('National Bank of Sudan', 'tahweel-woo').'</option>
                        <option value="'.__('National Bank of Egypt', 'tahweel-woo').'">'.__('National Bank of Egypt', 'tahweel-woo').'</option>
                        <option value="'.__('Sudanese Islamic Bank', 'tahweel-woo').'">'.__('Sudanese Islamic Bank', 'tahweel-woo').'</option>
                        <option value="'.__('Agricultural Bank of Sudan', 'tahweel-woo').'">'.__('Agricultural Bank of Sudan', 'tahweel-woo').'</option>
                        <option value="'.__('Saudi Sudanese Bank', 'tahweel-woo').'">'.__('Saudi Sudanese Bank', 'tahweel-woo').'</option>
                        <option value="'.__('Sudanese French Bank', 'tahweel-woo').'">'.__('Sudanese French Bank', 'tahweel-woo').'</option>
                        <option value="'.__('Sudanese Egyptian Bank', 'tahweel-woo').'">'.__('Sudanese Egyptian Bank', 'tahweel-woo').'</option>
                        <option value="'.__('Arabic Sudanese Bank', 'tahweel-woo').'">'.__('Arabic Sudanese Bank', 'tahweel-woo').'</option>
                        <option value="'.__('Real Estate Commercail Bank', 'tahweel-woo').'">'.__('Real Estate Commercail Bank', 'tahweel-woo').'</option>
                        <option value="'.__('Arab Bank for Economic Development in Africa (BADEA)', 'tahweel-woo').'">'.__('Arab Bank for Economic Development in Africa (BADEA)', 'tahweel-woo').'</option>
                        <option value="'.__('Ivory Bank', 'tahweel-woo').'">'.__('Ivory Bank', 'tahweel-woo').'</option>
                        <option value="'.__('Omdurman National Bank', 'tahweel-woo').'">'.__('Omdurman National Bank', 'tahweel-woo').'</option>
                        <option value="'.__('Family Bank', 'tahweel-woo').'">'.__('Family Bank', 'tahweel-woo').'</option>
                        <option value="'.__('Financial Investment Bank', 'tahweel-woo').'">'.__('Financial Investment Bank', 'tahweel-woo').'</option>
                        <option value="'.__('AlBaraka Bank Sudan', 'tahweel-woo').'">'.__('AlBaraka Bank Sudan', 'tahweel-woo').'</option>
                        <option value="'.__('Tadamon Bank', 'tahweel-woo').'">'.__('Tadamon Bank', 'tahweel-woo').'</option>
                        <option value="'.__('Animal Resources Bank', 'tahweel-woo').'">'.__('Animal Resources Bank', 'tahweel-woo').'</option>
                        <option value="'.__('Aljazeera Sudanese Jordanian Bank', 'tahweel-woo').'">'.__('Aljazeera Sudanese Jordanian Bank', 'tahweel-woo').'</option>
                        <option value="'.__('Bank of Khartoum', 'tahweel-woo').'">'.__('Bank of Khartoum', 'tahweel-woo').'</option>
                        <option value="'.__('Workers National Bank', 'tahweel-woo').'">'.__('Workers National Bank', 'tahweel-woo').'</option>
                        <option value="'.__('United Capital Bank', 'tahweel-woo').'">'.__('United Capital Bank', 'tahweel-woo').'</option>
                        <option value="'.__('AlNile Bank', 'tahweel-woo').'">'.__('AlNile Bank', 'tahweel-woo').'</option>
                        <option value="'.__('Blue Nile Mashreg Bank', 'tahweel-woo').'">'.__('Blue Nile Mashreg Bank', 'tahweel-woo').'</option>
                        <option value="'.__('El Nilein Bank', 'tahweel-woo').'">'.__('El Nilein Bank', 'tahweel-woo').'</option>
                        <option value="'.__('Byblos Bank', 'tahweel-woo').'">'.__('Byblos Bank', 'tahweel-woo').'</option>
                        <option value="'.__('Export Development Bank', 'tahweel-woo').'">'.__('Export Development Bank', 'tahweel-woo').'</option>
                        <option value="'.__('Faisal Islamic Bank Sudan', 'tahweel-woo').'">'.__('Faisal Islamic Bank Sudan', 'tahweel-woo').'</option>
                        <option value="'.__('Savings and Social Development Bank', 'tahweel-woo').'">'.__('Savings and Social Development Bank', 'tahweel-woo').'</option>
                        <option value="'.__('Balad Bank Sudan', 'tahweel-woo').'">'.__('Balad Bank Sudan', 'tahweel-woo').'</option>
                        <option value="'.__('Industrial Development Bank', 'tahweel-woo').'">'.__('Industrial Development Bank', 'tahweel-woo').'</option>
                        <option value="'.__('BSIC Bank', 'tahweel-woo').'">'.__('BSIC Bank', 'tahweel-woo').'</option>
                        <option value="'.__('Al Salam Bank', 'tahweel-woo').'">'.__('Al Salam Bank', 'tahweel-woo').'</option>
                        <option value="'.__('Commercial Farmer Bank', 'tahweel-woo').'">'.__('Commercial Farmer Bank', 'tahweel-woo').'</option>
                    </select>
                </div>

                <div class="form-row form-row-wide">
                    <label>'.__('Customer Account Name', 'tahweel-woo').' <span class="required">*</span></label>
                    <input id="customer_name" type="text" placeholder="'.__('Full Name As Registered With Bank', 'tahweel-woo').'" autocomplete="off" required>
                </div>

                <div class="form-row form-row-wide">
                    <label>'.__('Account Number/or IBAN', 'tahweel-woo').' <span class="required">*</span></label>
                    <input id="account_iban_number" type="number" placeholder="'.__('Write Account Number/or IBAN', 'tahweel-woo').'" autocomplete="off" required>
                </div>

                <div class="form-row form-row-wide">
                    <label>'.__('Process Reference Number', 'tahweel-woo').' <span class="required">*</span></label>
                    <input id="reference_number" type="number" autocomplete="off" placeholder="'.__('Write Transfer Reference Number', 'tahweel-woo').'" required>
                </div>

                <div class="form-row form-row-wide">
                    <span><span class="required">*</span> <small>'.esc_html( $this->instructions ).'.</small></span>
                </div>
                ';
             
                do_action( 'woocommerce_credit_card_form_end', $this->id );
             
                echo '<div class="clear"></div></fieldset>';

                echo "<script>
                        $(document).ready(function() {
                            $('#select_bank').select2();
                        });
                    </script>";
             
            }

            public function validate_fields(){
 
                if( empty( $_POST[ 'billing_first_name' ] ) ) {
                    wc_add_notice( 'First name is required!', 'error' );
                    return false;
                }
                return true;
             
            }

            public function process_payments( $order_id ) {
                
                $order = wc_get_order( $order_id );

                $order->update_status( 'on-hold',  __( 'Awaiting Tahweel Payment', 'tahweel-woo') );

                // if ( $order->get_total() > 0 ) {
                    // Mark as on-hold (we're awaiting the cheque).
                // } else {
                    // $order->payment_complete();
                // }

               // $this->clear_payment_with_api();

                $order->reduce_order_stock();

                WC()->cart->empty_cart();

                return array(
                    'result'   => 'success',
                    'redirect' => $this->get_return_url( $order ),
                );
            }

            // public function clear_payment_with_api() {

            // }

            public function thank_you_page(){
                if( $this->instructions ){
                    echo wpautop( $this->instructions );
                }
            }
        }
    }
}

add_filter( 'woocommerce_payment_gateways', 'add_to_woo_tahweel_gateway');

/*
Load plugin textdomain.
*/
function tahweel_load_textdomain() {
    load_plugin_textdomain( 'tahweel-woo', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'tahweel_load_textdomain' );


function add_to_woo_tahweel_gateway( $gateways ) {
    $gateways[] = 'WC_bank_of_khartoum_pay_Gateway';
    return $gateways;
}