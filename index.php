<?php
/**
 * Plugin name: WOO Package Cost Extra Fee
 * Description: Package Cost Extra Fee in woocommerce checkout fields
 * Author: Shahadat Shuvo
 * Plugin URI: https://github.com/mshshuvooo/WOO-Package-Cost-Extra-Fee
 * Author URI: https://shahadatshuvo.com
 * text-domain: package-cost-extra-fee
 */

add_action( 'woocommerce_after_checkout_billing_form', 'mshshuvooo_add_package_cancel_button' );

function mshshuvooo_add_package_cancel_button( $checkout ) {
    echo '<div id="package-cancel">'; 
    
    woocommerce_form_field(
        'mshshuvooo_package_cancel',
        array(
            'label'  => 'Do you want a Gift Wrap?',
            'class'  => array( 'package-cancel-button' ),
            'type'   => 'checkbox'
        ),
        $checkout->get_value( 'mshshuvooo_package_cancel' )
    );
    
    echo '</div>';    
}

add_action( 'wp_footer', 'mshshuvooo_package_cancel_ajax' );

function mshshuvooo_package_cancel_ajax() {
    
    if ( is_checkout() ) {
        ?>
        <script type="text/javascript">
            jQuery( document ).ready(
                function($) {
                    $('#mshshuvooo_package_cancel').click(
                        function() {
                            jQuery('body').trigger('update_checkout');
                        }    
                    );
                }
            );
        </script>
        <?php
    }
}

function lab_pacakge_cost() {
    
    global $woocommerce;
    
    $package_fee = get_option( 'mshshuvooo_package_pricing_package_fee' );
    
    if ( ! $_POST || ( is_admin() && ! is_ajax() ) ) {
        return;
    }
    
    if ( isset( $_POST['post_data'] ) ) {
        parse_str( $_POST['post_data'], $post_data );
    } else {
        $post_data = $_POST;
    }
    
    if ( !isset( $post_data['mshshuvooo_package_cancel'] ) ) {
        return;
    }
    

    $woocommerce->cart->add_fee( __( 'Wraping', 'package-cost-extra-fee' ), $package_fee );
    
}

add_action( 'woocommerce_cart_calculate_fees', 'lab_pacakge_cost');


add_filter( 'woocommerce_settings_tabs_array', 'mshshuvooo_add_package_pricing', 50 );

function mshshuvooo_add_package_pricing( $settings_tab ) {
    
    $settings_tab['mshshuvooo_package_pricing'] = __( 'Package Pricing', 'package-cost-extra-fee' );
    
    return $settings_tab;
}


add_action( 'woocommerce_settings_tabs_mshshuvooo_package_pricing', 'mshshuvooo_add_package_pricing_settings' );

function mshshuvooo_add_package_pricing_settings() {
    woocommerce_admin_fields( get_mshshuvooo_package_pricing_settings() );
}

add_action( 'woocommerce_update_options_mshshuvooo_package_pricing', 'mshshuvooo_update_options_package_pricing_settings' );

function mshshuvooo_update_options_package_pricing_settings() {
    woocommerce_update_options( get_mshshuvooo_package_pricing_settings() );
}

function get_mshshuvooo_package_pricing_settings() {
    
    $settings = array(
        
        'section_title' => array(
            'id'   => 'mshshuvooo_package_pricing_settings_title',
            'desc' => 'Section for handlign package information',
            'type' => 'title',
            'name' => 'Package Pricing Information',
        ),
        
        'package_pricing_package_fee' => array(
            'id'   => 'mshshuvooo_package_pricing_package_fee',
            'desc' => 'Enter The Packaging Fee',
            'type' => 'text',
            'name' => 'Packaging Fee',
        ),
        
        'section_end' => array(
            'id'   => 'mshshuvooo_package_pricing_sectionend',
            'type' => 'sectionend',
        ),
    );
    
    return apply_filters( 'filter_mshshuvooo_package_pricing_settings', $settings );
}