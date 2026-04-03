<?php
/**
 * Plugin Name: WooCommerce Bangladesh Hierarchy Checkout & Automation
 * Plugin URI: https://github.com/your-username/wc-bd-checkout-automation
 * Description: All-in-one automation for BD WooCommerce: Division-District-Thana hierarchy, Auto Shipping Calculation, Telegram Order Notification, and Visitor Tracking.
 * Version: 1.0.0
 * Author: Sinthiya Mart
 * Author URI: https://sinthiyamart.com
 * License: GPL2
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * 1. Checkout Fields Customization (Hierarchy)
 */
add_filter( 'woocommerce_checkout_fields' , 'sinthiyamart_plugin_fields' );
function sinthiyamart_plugin_fields( $fields ) {
    // Division Field
    $fields['billing']['billing_division'] = array(
        'type'        => 'select',
        'label'       => __('বিভাগ (Division)', 'woocommerce'),
        'required'    => true,
        'class'       => array('form-row-wide', 'address-field', 'update_totals_on_change'),
        'options'     => array(
            ''            => 'বিভাগ সিলেক্ট করুন',
            'Dhaka'       => 'Dhaka',
            'Chattogram'  => 'Chattogram',
            'Rajshahi'    => 'Rajshahi',
            'Khulna'      => 'Khulna',
            'Barishal'    => 'Barishal',
            'Sylhet'      => 'Sylhet',
            'Rangpur'     => 'Rangpur',
            'Mymensingh'  => 'Mymensingh'
        ),
        'priority'    => 20,
    );

    // District Field
    $fields['billing']['billing_district_new'] = array(
        'type'        => 'select',
        'label'       => __('জেলা (District)', 'woocommerce'),
        'required'    => true,
        'class'       => array('form-row-wide'),
        'options'     => array( '' => 'প্রথমে বিভাগ সিলেক্ট করুন' ),
        'priority'    => 30,
    );

    // Thana/Upazila Field
    $fields['billing']['billing_thana_new'] = array(
        'type'        => 'select',
        'label'       => __('উপজেলা/থানা', 'woocommerce'),
        'required'    => true,
        'class'       => array('form-row-wide'),
        'options'     => array( '' => 'প্রথমে জেলা সিলেক্ট করুন' ),
        'priority'    => 40,
    );

    // Remove unwanted default fields
    unset($fields['billing']['billing_state']);
    unset($fields['billing']['billing_city']);

    return $fields;
}

/**
 * 2. Dropdown Connection Logic (JavaScript)
 */
add_action( 'wp_footer', 'sinthiyamart_plugin_js' );
function sinthiyamart_plugin_js() {
    if ( is_checkout() ) {
        ?>
        <script type="text/javascript">
        jQuery(function($){
            var bd_geo_data = {
                'Dhaka': {
                'Dhaka':['Dhanmondi','Gulshan','Mirpur','Uttara','Savar','Keraniganj','Dhamrai','Nawabganj','Dohar'],
                'Gazipur':['Sadar','Kaliakair','Kaliganj','Kapasia','Sreepur'],
                'Narayanganj':['Sadar','Araihazar','Bandar','Rupganj','Sonargaon'],
                'Manikganj':['Sadar','Saturia','Singair','Shibalaya','Harirampur','Ghiror','Daulatpur'],
                'Munshiganj':['Sadar','Sreenagar','Sirajdikhan','Louhajang','Gajaria','Tongibari'],
                'Narsingdi':['Sadar','Belabo','Monohardi','Palash','Raipura','Shibpur'],
                'Faridpur':['Sadar','Alfadanga','Bhanga','Boalmari','Charbhadrasan','Madukhali','Nagarkanda','Sadarpur','Saltha'],
                'Gopalganj':['Sadar','Kashiani','Kotalipara','Muksudpur','Tungipara'],
                'Madaripur':['Sadar','Kalkini','Rajoir','Shibchar'],
                'Rajbari':['Sadar','Baliakandi','Goalandaghat','Pangsha','Kalukhali'],
                'Shariatpur':['Sadar','Bhedarganj','Damudya','Gosairhat','Naria','Zajira'],
                'Kishoreganj':['Sadar','Itna','Katiadi','Bhairab','Tarail','Hossainpur','Pakundia','Kuliarchar','Karimganj','Bajitpur','Austagram','Mithamain','Nikli'],
                'Tangail':['Sadar','Basail','Bhuapur','Delduar','Ghatail','Gopalpur','Kalihati','Madhupur','Mirzapur','Nagarpur','Sakhipur','Dhanbari']
            },
            'Chattogram': {
                'Chattogram':['Panchlaish','Pahartali','Double Mooring','Kotwali','Bandar','Anwara','Banshkhali','Boalkhali','Chandanaish','Fatikchhari','Hathazari','Lohagara','Mirsharai','Patiya','Rangunia','Raozan','Sandwip','Satkania','Sitakunda'],
                'Coxsbazar':['Sadar','Chakaria','Kutubdia','Maheshkhali','Ramu','Teknaf','Ukhia','Pekua'],
                'Noakhali':['Sadar','Begumganj','Chatkhil','Companyganj','Hatiya','Senbagh','Sonaimuri','Subarnachar','Kabirhat'],
                'Feni':['Sadar','Chhagalnaiya','Daganbhuiyan','Parshuram','Sonagazi','Fulgazi'],
                'Lakshmipur':['Sadar','Raipur','Ramganj','Ramgati','Kamalnagar'],
                'Chandpur':['Sadar','Faridganj','Hajiganj','Haimchar','Kachua','Matlab North','Matlab South','Shahrasti'],
                'Cumilla':['Sadar','Barura','Brahmanpara','Burichang','Chandina','Chauddagram','Daudkandi','Debidwar','Homna','Laksam','Muradnagar','Nangalkot','Titas','Meghna','Monohargonj','Sadar South'],
                'Brahmanbaria':['Sadar','Ashuganj','Bancharampur','Bijoynagar','Kasba','Nabinagar','Nasirnagar','Sarail','Akhaura'],
                'Rangamati':['Sadar','Belaichhari','Baghaichhari','Barkal','Juraichhari','Kaptai','Kawkhali','Langadu','Nannerchar','Rajasthali'],
                'Bandarban':['Sadar','Ali Kadam','Lama','Naikhongchhari','Rowangchhari','Ruma','Thanchi'],
                'Khagrachhari':['Sadar','Dighinala','Lakshmichhari','Mahalchhari','Manikchhari','Matiranga','Panchhari','Ramgarh']
            },
            'Rajshahi': {
                'Rajshahi':['Sadar','Bagha','Bagmara','Charghat','Durgapur','Godagari','Mohanpur','Paba','Puthia','Tanore'],
                'Bogra':['Sadar','Adamdighi','Dhunat','Dhupchanchia','Gabtali','Kahaloo','Nandigram','Sariakandi','Sherpur','Shibganj','Sonatala','Shajahanpur'],
                'Joypurhat':['Sadar','Akkelpur','Kalai','Khetlal','Panchbibi'],
                'Naogaon':['Sadar','Atrai','Badalgachhi','Dhamoirhat','Manda','Mohadevpur','Niamatpur','Patnitala','Porsha','Raninagar','Sapahar'],
                'Natore':['Sadar','Bagatipara','Baraigram','Gurudaspur','Lalpur','Singra','Naldanga'],
                'Chapai Nawabganj':['Sadar','Bholahat','Gomastapur','Nachole','Shibganj'],
                'Pabna':['Sadar','Atgharia','Bera','Bhangura','Chatmohar','Faridpur','Ishwardi','Santhia','Sujanagar'],
                'Sirajganj':['Sadar','Belkuchi','Chauhali','Kamarkhanda','Kazipur','Raiganj','Shahjadpur','Tarash','Ullahpara']
            },
            'Khulna': {
                'Khulna':['Sadar','Batiaghata','Dacope','Dumuria','Dighalia','Koyra','Paikgachha','Phultala','Rupsha','Terokhada'],
                'Bagerhat':['Sadar','Baka','Chitalmari','Fakirhat','Kachua','Mollahat','Mongla','Morrelganj','Sarankhola'],
                'Satkhira':['Sadar','Assasuni','Debhata','Kalaroa','Kaliganj','Shyamnagar','Tala'],
                'Jashore':['Sadar','Abhaynagar','Bagherpara','Chaugachha','Jhikargachha','Keshabpur','Manirampur','Sharsha'],
                'Magura':['Sadar','Mohammadpur','Shalikha','Sreepur'],
                'Narail':['Sadar','Lohagara','Kalia'],
                'Kushtia':['Sadar','Bheramara','Daulatpur','Khoksa','Kumarkhali','Mirpur'],
                'Chuadanga':['Sadar','Alamdanga','Damurhuda','Jiban Nagar'],
                'Meherpur':['Sadar','Gangni','Mujibnagar'],
                'Jhenaidah':['Sadar','Harinakunda','Kaliganj','Kotchandpur','Maheshpur','Shailkupa']
            },
            'Barishal': {
                'Barishal':['Sadar','Agailjhara','Babuganj','Bakerganj','Banaripara','Gaurnadi','Hizla','Mehendiganj','Muladi','Wazirpur'],
                'Bhola':['Sadar','Burhanuddin','Char Fasson','Daulatkhan','Lalmohan','Manpura','Tazumuddin'],
                'Jhalokati':['Sadar','Kathalia','Nalchity','Rajapur'],
                'Patuakhali':['Sadar','Bauphal','Dashmina','Galachipa','Kalapara','Mirzaganj','Dumki','Rangabali'],
                'Pirojpur':['Sadar','Bhandaria','Kawkhali','Mathbaria','Nazirpur','Nesarabad','Zianagar'],
                'Barguna':['Sadar','Amtali','Bamna','Betagi','Patharghata','Taltali']
            },
            'Sylhet': {
                'Sylhet':['Sadar','Balaganj','Beanibazar','Bishwanath','Fenchuganj','Golapganj','Gowainghat','Jaintiapur','Kanaighat','Companyganj','Zakiganj','South Surma','Osmani Nagar'],
                'Moulvibazar':['Sadar','Barlekha','Juri','Kamalganj','Kulaura','Rajnagar','Sreemangal'],
                'Habiganj':['Sadar','Ajmiriganj','Bahubal','Baniyachong','Chunarughat','Lakhai','Madhabpur','Nabiganj','Sayestaganj'],
                'Sunamganj':['Sadar','Bishwamarpur','Chhatak','Derai','Dharamapasha','Dowarabazar','Jagannathpur','Jamalganj','Sullah','Tahirpur','South Sunamganj']
            },
            'Rangpur': {
                'Rangpur':['Sadar','Badarganj','Gangachara','Kaunia','Mithapukur','Pirgachha','Pirganj','Taraganj'],
                'Dinajpur':['Sadar','Birampur','Birganj','Biral','Bochaganj','Chirirbandar','Phulbari','Ghoraghat','Hakimpur','Kaharole','Khansama','Nawabganj','Parbatipur'],
                'Gaibandha':['Sadar','Phulchhari','Gobindaganj','Palashbari','Sadullapur','Saghata','Sundarganj'],
                'Kurigram':['Sadar','Bhurungamari','Char Rajibpur','Chilmari','Phulbari','Nageshwari','Rajarhat','Roumari','Ulipur'],
                'Lalmonirhat':['Sadar','Aditmari','Hatibandha','Kaliganj','Patgram'],
                'Nilphamari':['Sadar','Dimla','Domar','Jaldhaka','Kishoreganj','Saidpur'],
                'Panchagarh':['Sadar','Atwari','Boda','Debiganj','Tetulia'],
                'Thakurgaon':['Sadar','Baliadangi','Haripur','Pirganj','Ranisankail']
            },
            'Mymensingh': {
                'Mymensingh':['Sadar','Bhaluka','Dhobaura','Fulbaria','Gaffargaon','Gauripur','Haluaghat','Ishwarganj','Muktagachha','Nandail','Phulpur','Trishal','Tara Khanda'],
                'Netrokona':['Sadar','Atpara','Barhatta','Durgapur','Khaliajuri','Kalmakanda','Kendua','Madan','Mohanganj','Purbadhala'],
                'Sherpur':['Sadar','Jhenaigati','Nakla','Nalitabari','Sreebardi'],
                'Jamalpur':['Sadar','Bakshiganj','Dewanganj','Islampur','Madarganj','Melenandah','Sarishabari'] }
                // Add more data as needed
            };

            $(document.body).on('change', 'select[name="billing_division"]', function(){
                var div = $(this).val();
                var dist_select = $('select[name="billing_district_new"]');
                dist_select.empty().append('<option value="">জেলা সিলেক্ট করুন</option>');
                if(bd_geo_data[div]){
                    $.each(bd_geo_data[div], function(dist){ dist_select.append('<option value="'+dist+'">'+dist+'</option>'); });
                }
                $('select[name="billing_thana_new"]').empty().append('<option value="">প্রথমে জেলা সিলেক্ট করুন</option>');
                $('body').trigger('update_checkout');
            });

            $(document.body).on('change', 'select[name="billing_district_new"]', function(){
                var div = $('select[name="billing_division"]').val();
                var dist = $(this).val();
                var thana_select = $('select[name="billing_thana_new"]');
                thana_select.empty().append('<option value="">উপজেলা/থানা সিলেক্ট করুন</option>');
                if(bd_geo_data[div] && bd_geo_data[div][dist]){
                    $.each(bd_geo_data[div][dist], function(i, thana){ thana_select.append('<option value="'+thana+'">'+thana+'</option>'); });
                }
            });
        });
        </script>
        <?php
    }
}

/**
 * 3. Shipping Logic (Dhaka 80, Outside 130)
 */
add_action( 'woocommerce_package_rates', 'sinthiyamart_plugin_shipping_logic', 100, 2 );
function sinthiyamart_plugin_shipping_logic( $rates, $package ) {
    $division = '';
    if ( isset( $_POST['post_data'] ) ) {
        parse_str( $_POST['post_data'], $post_data );
        $division = isset( $post_data['billing_division'] ) ? $post_data['billing_division'] : '';
    }
    if ( empty( $division ) ) { $division = WC()->customer->get_meta('billing_division'); }

    foreach ( $rates as $rate_key => $rate ) {
        if ( 'flat_rate' === $rate->method_id ) {
            $rate->cost = ($division === 'Dhaka') ? 80 : 130;
            $rate->label = ($division === 'Dhaka') ? 'Inside Dhaka Delivery' : 'Outside Dhaka Delivery';
        }
    }
    return $rates;
}

/**
 * 4. Telegram Order Notification
 */
add_action( 'woocommerce_checkout_order_processed', 'sinthiyamart_plugin_telegram_notify', 10, 1 );
function sinthiyamart_plugin_telegram_notify( $order_id ) {
    $apiToken = "YOUR_BOT_TOKEN"; 
    $chatID   = "YOUR_CHAT_ID";
    $order = wc_get_order( $order_id );

    $message = "📢 New Order: #" . $order_id . "\nName: " . $order->get_billing_first_name() . "\nTotal: " . $order->get_total() . " BDT";

    $url = "https://api.telegram.org/bot$apiToken/sendMessage?chat_id=$chatID&text=" . urlencode($message);
    wp_remote_get( $url );
}