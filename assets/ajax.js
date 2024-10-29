jQuery(document).ready(function($){
	$('#ps_mail_form').submit(function($) {
		console.log('hello');
		event.preventDefault();
		jQuery.ajax({
	      	url: bs_ajax_object.ajax_url,
	      	type: 'POST',
	      	data: {data: jQuery(this).serialize(), _nonce: bs_ajax_object.bs_nonce, action: 'bs_form_mail_submit'},
	      	success: function(data, textStatus, xhr) {
	        	jQuery('.show_shortcode').html(data);	  
	        	//console.log(data);   
	      	},
	      	error: function(xhr, textStatus, errorThrown) {
	        	//called when there is an error
	      	}
		});	
	});
});

