<?php
/**
 * Assess.
 * 
 * A set of rules to assess and rate orders.
 * 
 * @package Built Mighty Protection
 * @since   1.0.0
 */
namespace BuiltMightyProtect;
class builtAssessment {

    /**
     * Construct.
     * 
     * @since   1.0.0
     */
    public function __construct() {

        // Check if order rate is enabled.
        if( get_option( 'built_assess_rate' ) === 'yes' ) {

            // Order rate.
            add_action( 'woocommerce_new_order', [ $this, 'order_assess' ], 10, 1 );

        }

    }
    
    /**
     * Assess the order.
     * 
     * Monitor and assess the order for potential fraud.
     * 
     * @param   int     $order_id   Order ID.
     * 
     * @since   1.0.0
     */
    public function order_assess( $order_id ) {

        // Disable on admin side.
        if( is_admin() ) return;

        // Check if order was placed by admin user.
        if( is_user_logged_in() && current_user_can( 'manage_options' ) ) return;

        // Get the order.
        $order = wc_get_order( $order_id );

        // Set rating.
        $rating = 0;

        // Add rating.
        $rating += $this->billing_phone( $order );
        $rating += $this->international_order( $order );
        $rating += $this->proxy_ip( $order->get_customer_ip_address() );
        $rating += $this->first_order( $order );

        // Save rating.
        update_post_meta( $order_id, 'built_order_rating', $rating );

    }

    /**
     * Check if billing phone matches billing country.
     * 
     * Checks the billing phone against the billing country. Weighted at 10%.
     * 
     * @param   WC_Order    $order  The order.
     * @return  int         The weight.
     * 
     * @since   1.0.0
     */
    public function billing_phone( $order ) {

        // Billing information.
        $country    = $order->get_billing_country();
        $phone      = preg_replace( '/[^A-Za-z0-9\-]/', '', str_replace( ' ', '', trim( $order->get_billing_phone() ) ) );

        // Get the billing phone code.
        $code       = array_search( $country, $this->phone_countries(), true );

        // Check if we have a code.
        if( empty( $code ) ) return 0;

        // Get first part of phone number.
        $first      = substr( $phone, 0, strlen( $code ) );

        // Check if phone matches country.
        if( $first === $code ) return 0;

        // Return true.
        return 10;

    }

    /**
     * Check if an international order.
     * 
     * Checks if the order is international. Weighted at 20%.
     * 
     * @param   WC_Order    $order  The order.
     * @return  int         The weight.
     * 
     * @since   1.0.0
     */
    public function international_order( $order ) {

        // Get the store country.
        $store_country  = wc_get_base_location()['country'];

        // Check if the store country is different from the order country.
        if( $store_country !== $order->get_billing_country() ) return 20;

        // Return false.
        return 0;

    }

    /**
     * Check if IP is proxy.
     * 
     * Check if the IP address is a proxy. Weighted at 30%.
     * 
     * @param   string  $ip     The IP address.
     * @return  int             The weight.
     * 
     * @since   1.0.0
     */
    public function proxy_ip( $ip ) {

        // Check if API key is set.
        if( empty( get_option( 'built_proxycheck_key' ) ) ) return 0;

        // Get the response.
        $response = wp_remote_get( 'http://proxycheck.io/v2/' . $ip . '?key=' . get_option( 'built_proxycheck_key' ) . '&vpn=1&asn=1' );

        // Check if we have a response.
        if( is_wp_error( $response ) ) return 0;

        // Decode the response.
        $response = json_decode( wp_remote_retrieve_body( $response ) );

        // Check if the IP is a proxy.
        if( $response->proxy === 'yes' ) return 30;

        // Return false.
        return 0;

    }

    /**
     * Check for first order.
     * 
     * Check if the order is the first order for the customer. Weighted at 15%.
     * 
     * @param   WC_Order    $order  The order.
     * @return  int         The weight.
     * 
     * @since   1.0.0
     */
    public function first_order( $order ) {

        // Get the customer ID.
        $customer_id = $order->get_customer_id();

        // Get the orders.
        $orders = wc_get_orders( [ 'customer' => $customer_id ] );

        // Check if we have more than one order.
        if( count( $orders ) > 1 ) return 0;

        // Return true.
        return 15;

    }

    /**
     * Free email check.
     * 
     * Check if the email is a free email. Weighted at 25%.
     * 
     * @param   WC_Order    $order  The order.
     * @return  int         The weight.
     * 
     * @since   1.0.0
     */
    public function free_email( $order ) {

        // Get the email.
        $email = $order->get_billing_email();

        // Get the domain.
        $domain = explode( '@', $email );

        // Check if the domain is in the list.
        if( in_array( $domain[1], $this->email_domains() ) ) return 25;

        // Return false.
        return 0;

    }

    /**
     * Phone countries.
     * 
     * @return  array   The phone countries.
     * 
     * @since   1.0.0
     */
    public function phone_countries() {

        // Return.
        return [
            'AD' => '376',
            'AE' => '971',
            'AF' => '93',
            'AG' => '1268',
            'AI' => '1264',
            'AL' => '355',
            'AM' => '374',
            'AN' => '599',
            'AO' => '244',
            'AQ' => '672',
            'AR' => '54',
            'AS' => '1684',
            'AT' => '43',
            'AU' => '61',
            'AW' => '297',
            'AZ' => '994',
            'BA' => '387',
            'BB' => '1246',
            'BD' => '880',
            'BE' => '32',
            'BF' => '226',
            'BG' => '359',
            'BH' => '973',
            'BI' => '257',
            'BJ' => '229',
            'BL' => '590',
            'BM' => '1441',
            'BN' => '673',
            'BO' => '591',
            'BR' => '55',
            'BS' => '1242',
            'BT' => '975',
            'BW' => '267',
            'BY' => '375',
            'BZ' => '501',
            'CA' => '1',
            'CC' => '61',
            'CD' => '243',
            'CF' => '236',
            'CG' => '242',
            'CH' => '41',
            'CI' => '225',
            'CK' => '682',
            'CL' => '56',
            'CM' => '237',
            'CN' => '86',
            'CO' => '57',
            'CR' => '506',
            'CU' => '53',
            'CV' => '238',
            'CX' => '61',
            'CY' => '357',
            'CZ' => '420',
            'DE' => '49',
            'DJ' => '253',
            'DK' => '45',
            'DM' => '1767',
            'DO' => '1809',
            'DZ' => '213',
            'EC' => '593',
            'EE' => '372',
            'EG' => '20',
            'ER' => '291',
            'ES' => '34',
            'ET' => '251',
            'FI' => '358',
            'FJ' => '679',
            'FK' => '500',
            'FM' => '691',
            'FO' => '298',
            'FR' => '33',
            'GA' => '241',
            'GB' => '44',
            'GD' => '1473',
            'GE' => '995',
            'GH' => '233',
            'GI' => '350',
            'GL' => '299',
            'GM' => '220',
            'GN' => '224',
            'GQ' => '240',
            'GR' => '30',
            'GT' => '502',
            'GU' => '1671',
            'GW' => '245',
            'GY' => '592',
            'HK' => '852',
            'HN' => '504',
            'HR' => '385',
            'HT' => '509',
            'HU' => '36',
            'ID' => '62',
            'IE' => '353',
            'IL' => '972',
            'IM' => '44',
            'IN' => '91',
            'IQ' => '964',
            'IR' => '98',
            'IS' => '354',
            'IT' => '39',
            'JM' => '1876',
            'JO' => '962',
            'JP' => '81',
            'KE' => '254',
            'KG' => '996',
            'KH' => '855',
            'KI' => '686',
            'KM' => '269',
            'KN' => '1869',
            'KP' => '850',
            'KR' => '82',
            'KW' => '965',
            'KY' => '1345',
            'KZ' => '7',
            'LA' => '856',
            'LB' => '961',
            'LC' => '1758',
            'LI' => '423',
            'LK' => '94',
            'LR' => '231',
            'LS' => '266',
            'LT' => '370',
            'LU' => '352',
            'LV' => '371',
            'LY' => '218',
            'MA' => '212',
            'MC' => '377',
            'MD' => '373',
            'ME' => '382',
            'MF' => '1599',
            'MG' => '261',
            'MH' => '692',
            'MK' => '389',
            'ML' => '223',
            'MM' => '95',
            'MN' => '976',
            'MO' => '853',
            'MP' => '1670',
            'MR' => '222',
            'MS' => '1664',
            'MT' => '356',
            'MU' => '230',
            'MV' => '960',
            'MW' => '265',
            'MX' => '52',
            'MY' => '60',
            'MZ' => '258',
            'NA' => '264',
            'NC' => '687',
            'NE' => '227',
            'NG' => '234',
            'NI' => '505',
            'NL' => '31',
            'NO' => '47',
            'NP' => '977',
            'NR' => '674',
            'NU' => '683',
            'NZ' => '64',
            'OM' => '968',
            'PA' => '507',
            'PE' => '51',
            'PF' => '689',
            'PG' => '675',
            'PH' => '63',
            'PK' => '92',
            'PL' => '48',
            'PM' => '508',
            'PN' => '870',
            'PR' => '1',
            'PT' => '351',
            'PW' => '680',
            'PY' => '595',
            'QA' => '974',
            'RO' => '40',
            'RS' => '381',
            'RU' => '7',
            'RW' => '250',
            'SA' => '966',
            'SB' => '677',
            'SC' => '248',
            'SD' => '249',
            'SE' => '46',
            'SG' => '65',
            'SH' => '290',
            'SI' => '386',
            'SK' => '421',
            'SL' => '232',
            'SM' => '378',
            'SN' => '221',
            'SO' => '252',
            'SR' => '597',
            'ST' => '239',
            'SV' => '503',
            'SY' => '963',
            'SZ' => '268',
            'TC' => '1649',
            'TD' => '235',
            'TG' => '228',
            'TH' => '66',
            'TJ' => '992',
            'TK' => '690',
            'TL' => '670',
            'TM' => '993',
            'TN' => '216',
            'TO' => '676',
            'TR' => '90',
            'TT' => '1868',
            'TV' => '688',
            'TW' => '886',
            'TZ' => '255',
            'UA' => '380',
            'UG' => '256',
            'US' => '1',
            'UY' => '598',
            'UZ' => '998',
            'VA' => '379',
            'VC' => '1784',
            'VE' => '58',
            'VG' => '1284',
            'VI' => '1340',
            'VN' => '84',
            'VU' => '678',
            'WF' => '681',
            'WS' => '685',
            'XK' => '383',
            'YE' => '967',
            'YT' => '262',
            'ZA' => '27',
            'ZM' => '260',
            'ZW' => '263', 
        ];

    }

    /**
     * Free email domains.
     * 
     * @return  array   The free email domains.
     * 
     * @since   1.0.0
     */
    public function email_domains() {

        // Return.
        return [
            'guerrillamail.com',
            'guerrillamailblock.com',
            'sharklasers.com',
            'guerrillamail.net',
            'guerrillamail.org',
            'guerrillamail.biz',
            'spam4.me',
            'grr.la',
            'guerrillamail.de',
            'trbvm.com',
            'mailinator.com',
            'reallymymail.com',
            'mailismagic.com',
            'mailtothis.com',
            'monumentmail.com',
            'imgof.com',
            'fammix.com',
            '6paq.com',
            'grandmamail.com',
            'daintly.com',
            'evopo.com',
            'lackmail.net',
            'alivance.com',
            'bigprofessor.so',
            'walkmail.net',
            'thisisnotmyrealemail.com',
            'mailmetrash.com',
            'mytrashmail.com',
            'trashymail.com',
            'mt2009.com',
            'trash2009.com',
            'thankyou2010.com',
            'guerrillamailblock',
            'meltmail.com',
            'mintemail.com',
            'tempinbox.com',
            'fatflap.com',
            'dingbone.com',
            'fudgerub.com',
            'beefmilk.com',
            'lookugly.com',
            'smellfear.com',
            'yopmail.com',
            'jnxjn.com',
            'example.com',
            'spamgourmet.com',
            'jetable.org',
            'dunflimblag.mailexpire.com',
            'spambox.us',
            'tempomail.fr',
            'tempemail.net',
            'spamfree24.org',
            'spamfree24.de',
            'spamfree.info',
            'spamfree.com',
            'spamfree.eu',
            'spamavert.com',
            'maileater.com',
            'mailexpire.com',
            'spammotel.com',
            'spamspot.com',
            'spam.la',
            'hushmail.com',
            'hushmail.me',
            'hush.com',
            'hush.ai',
            'mac.hush.com',
            'abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijk.com',
            'mailnull.com',
            'sneakemail.com',
            'e4ward.com',
            'spamcero.com',
            'mytempemail.com',
            'incognitomail.org',
            'mailcatch.com',
            'deadaddress.com',
            'mailscrap.com',
            'anonymbox.com',
            'soodonims.com',
            'tempail.com',
            '20minutemail.com',
            'deagot.com',
            'demail.tk',
            'yestoa.com',
            'anontext.com',
            'shieldemail.com',
            'temporaryemail.net',
            'disposeamail.com',
            'mailmoat.com',
            'noclickemail.com',
            'trashmail.net',
            'kurzepost.de',
            'objectmail.com',
            'proxymail.eu',
            'rcpt.at',
            'trash-mail.at',
            'trashmail.at',
            'trashmail.me',
            'wegwerfmail.de',
            'wegwerfmail.net',
            'wegwerfmail.org',
            'yopmail.fr',
            'yopmail.net',
            'cool.fr.nf',
            'jetable.fr.nf',
            'nospam.ze.tc',
            'nomail.xl.cx',
            'mega.zik.dj',
            'speed.1s.fr',
            'courriel.fr.nf',
            'moncourrier.fr.nf',
            'monemail.fr.nf',
            'monmail.fr.nf',
            'emailias.com',
            'zoemail.com',
            'wh4f.org',
            'despam.it',
            'disposableinbox.com',
            'fakeinbox.com',
            'quickinbox.com',
            'emailthe.net',
            'tempalias.com',
            'explodemail.com',
            'xyzfree.net',
            '10×9.com',
            '12minutemail.com',
            'we.nispam.it',
            'no-spam.ws',
            'mytemporarymail.com',
            'yxzx.net',
            'goemailgo.com',
            'filzmail.com',
            'webemail.me',
            'temp.emeraldwebmail.com',
            'fakemail.fr',
            'my-inbox.in',
            'mail-it24.com',
            'tittbit.in',
            'mail.tittbit.in',
            'temporaryemailaddress.com',
            'temporaryemailid.com',
            'mail.cz.cc',
            '10minutemail.com',
        ];

    }

}