<?php 

class Bs_Mail_Shortcode {
 	public function Bs_Instance() {	
      add_action('wp_footer', array($this, 'show_shortcode_bs_touch_slideshow'));
	  add_action('wp_enqueue_scripts', array($this, 'bs_popup_scripts'));
	  add_action('wp_ajax_bs_form_mail_submit', array($this,'bs_form_mail_submit'));
	  add_action('wp_ajax_nopriv_bs_form_mail_submit', array($this,'bs_form_mail_submit'));
    }
    public function Bs_GetInstance() {
        $this->Bs_Instance();
    }

    public function bs_form_mail_submit(){
    	if (wp_verify_nonce($_POST['_nonce'], 'bs-mail-nonce')){
    		$option_object=Bs_Mail_Popup_Setting::bs_get_instance();
    		$data=$_POST['data'];
    		parse_str($data, $data);
    		$email=sanitize_text_field($data['ps_email']);
	    	$list_id =$option_object->bs_get_option( 'bs_mail_list', 'bs_mail_popup_basic');
			$api_key = $option_object->bs_get_option( 'bs_mail_api', 'bs_mail_popup_basic');
			$result = json_decode( $this->rudr_mailchimp_subscriber_status($email, 'subscribed', $list_id, $api_key));
			if( $result->status == 400 ){
				foreach( $result->errors as $error ) {
					echo '<p>Error: ' . $error->message . '</p>';
				}
			} 
			elseif( $result->status == 'subscribed' ){
				echo '<div class="ps_success">You have subscribed successfully</div>';
			}
			wp_die();
		}
    }
    public function rudr_mailchimp_subscriber_status( $email, $status, $list_id, $api_key){
		$data = array(
			'apikey'        => $api_key,
	    		'email_address' => $email,
			'status'        => $status,
		);
		$dc = explode('-', $api_key);
 		$datacenter = empty($dc[1]) ? 'us14' : $dc[1];

		$mch_api = curl_init(); // initialize cURL connection
		curl_setopt($mch_api, CURLOPT_URL, 'https://' .$datacenter. '.api.mailchimp.com/3.0/lists/' . $list_id . '/members/' . md5(strtolower($data['email_address'])));
		curl_setopt($mch_api, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Basic '.base64_encode( 'user:'.$api_key )));
		curl_setopt($mch_api, CURLOPT_USERAGENT, 'PHP-MCAPI/2.0');
		curl_setopt($mch_api, CURLOPT_RETURNTRANSFER, true); // return the API response
		curl_setopt($mch_api, CURLOPT_CUSTOMREQUEST, 'PUT'); // method PUT
		curl_setopt($mch_api, CURLOPT_TIMEOUT, 10);
		curl_setopt($mch_api, CURLOPT_POST, true);
		curl_setopt($mch_api, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($mch_api, CURLOPT_POSTFIELDS, json_encode($data) ); // send data in json
	 
		$result = curl_exec($mch_api);
		return $result;
	}	

	public function show_shortcode_bs_touch_slideshow(){ ?>
	<?php 
		 $option_object=Bs_Mail_Popup_Setting::bs_get_instance();
		$ps_pages=$option_object->bs_get_option( 'bs_mail_popup_pages', 'bs_mail_popup_basic','ps_home');
		if($ps_pages=='ps_home'){
			$check='home';
		}
		else{
			$check='all';
		}
		if($check=='all' && is_home() || $check=='all' && is_front_page()){
			return;
			//wp_die($check,true);
		}
		if($check=='home' && !is_home() || $check=='home' && !is_front_page()){
			return;
			//wp_die($check,true);
		}
	?>
	<div class="remodal" data-remodal-id="ps_cupon_popup" role="dialog">
	    <button data-remodal-action="close" class="remodal-close" aria-label="Close"></button>
	    <div class="ps_popup_header">
	        <h2 id="ps_popup_title"><?php echo $ps_pages=$option_object->bs_get_option( 'bs_mail_text', 'bs_mail_popup_basic','Get 10% Discount For Like Our Page');?></h2>
	        <div class="ps_popup_des"><?php echo $ps_pages=$option_object->bs_get_option( 'bs_mail_des', 'bs_mail_popup_basic','Click Like and get 10% discount coupon. Enter the coupon at the shopping cart page.');?></div>
	        <div class="show_shortcode"></div>
	       	<form class="form-horizontal" id="ps_mail_form" method="post">
		        <div class="popup_coupon">
		            <div class="ps_popup_cupon_text"><input type="text" name="ps_email" placeholder="enter your email address"></div>
		            <div class="ps_mail_btn"><input type="submit" name="ps_email" placeholder="enter your email address" value="Subscribe"></div>
		        </div>
	        </form>
	    </div>
	    <br>

	<script type="text/javascript">
	    jQuery(function() {
	        setTimeout(function() {
	            var inst = jQuery('[data-remodal-id=ps_cupon_popup]').remodal();
	            inst.open();
	        }, 4000);
	    });
	</script>
	</div>
	<?php }
	public function bs_popup_scripts() {
       wp_enqueue_style('bs_popup_style', plugin_dir_url(__FILE__) . 'assets/remodal.css');
       wp_enqueue_style('bs_popup_theme', plugin_dir_url(__FILE__) . 'assets/remodal-default-theme.css');
       wp_enqueue_script('bs_popup_script',plugin_dir_url(__FILE__).'assets/remodal.min.js',array('jquery'),true);
       //Ajax Call
	   wp_enqueue_script('bs_mail_submit_ajax', plugin_dir_url(__FILE__).'assets/ajax.js',array('jquery'),true);
	   $value = array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'bs_nonce' => wp_create_nonce( 'bs-mail-nonce') 
		);
		wp_localize_script( 'bs_mail_submit_ajax', 'bs_ajax_object', $value);
	}

}	

$var = new Bs_Mail_Shortcode();

$var->Bs_GetInstance();

?>