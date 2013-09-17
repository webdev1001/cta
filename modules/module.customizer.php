<?php	// Add to edit screen admin view	add_action( 'wp_before_admin_bar_render', 'wp_cta_customizer_admin_bar' );	function wp_cta_customizer_admin_bar() {		global $post;		global $wp_admin_bar;		$screen = get_permalink(); 		if (isset($_GET['cta-template-customize'])&&$_GET['cta-template-customize']=='on') 		{ 			$menu_title = "Turn Off Editor";			$var_number = $_GET['wp-cta-variation-id']; 			$link = apply_filters('wp_cta_customizer_admin_bar_link', $screen.'?wp-cta-variation-id='.$var_number.'');		} 		else 		{ 			$menu_title = "Launch Visual Editor"; 						if (preg_match("/wp-cta-variation-id\=0/i", $screen)) {			  	$screen = get_permalink() . "?cta-template-customize=on"; 			} 			else if (isset($_GET['wp-cta-variation-id']))			{				$var_number = $_GET['wp-cta-variation-id'];			  	$screen = get_permalink() . "?cta-template-customize=on&wp-cta-variation-id=$var_number"; 			}			else			{				$screen = get_permalink() . "?cta-template-customize=on";			}						$link = apply_filters('wp_cta_customizer_admin_bar_link', $screen);		}						if (!is_admin() && $post->post_type=='wp-call-to-action') 		{			$wp_admin_bar->add_menu( array(				'id' => 'launch-wp-cta-front-end-customizer',				'title' => $menu_title,				'href' => $link			) );			$wp_admin_bar->add_menu( array(				'id' => 'launch-wp-cta-measure',				'title' => "Measure CTA",				'href' => "#"			) );		}	}	// Add Link to landing page list	add_action( 'wp_before_admin_bar_render', 'wp_cta_list_page_admin_bar' );	function wp_cta_list_page_admin_bar() {		global $post;		global $wp_admin_bar;				if (!is_admin() && $post->post_type=='wp-call-to-action') 		{			$wp_admin_bar->add_menu( array(			'id' => 'wp-cta-list-pages',			'title' => "View Call to Action List",			'href' => '/wp-admin/edit.php?post_type=wp-call-to-action'			) );		}	}	// Kill admin bar on visual editor preview window	if (isset($_GET['cache_bust']))	{		show_admin_bar( false );	}	// Admin Side Print out varaitions toggles for preview iframes	if (isset($_GET['wp_cta_iframe_window']))	{		add_action('admin_enqueue_scripts','wp_cta_ab_previewer_enqueue');		function wp_cta_ab_previewer_enqueue()		{			wp_enqueue_style('wp_cta_ab_testing_customizer_css', WP_CTA_URLPATH . 'css/customizer-ab-testing.css');		}				show_admin_bar( false );				add_action('wp_head', 'wp_cta_preview_iframe');	    function wp_cta_preview_iframe() 		{				$wp_cta_variation = (isset($_GET['wp-cta-variation-id'])) ? $_GET['wp-cta-variation-id'] : '0';			$postid = $_GET['post_id'];						$variations = get_post_meta($postid,'wp-cta-ab-variations', true);			$variations_array = explode(",", $variations);			$post_type_is = get_post_type($postid); ?>						<link rel="stylesheet" href="<?php echo WP_CTA_URLPATH . 'css/customizer-ab-testing.css';?>" />			<style type="text/css">						#variation-list {				position: absolute;				top: 0px;				left:0px;				padding-left: 5px;			}			#variation-list h3 {				text-decoration: none;				border-bottom: none;			}			#variation-list div {				display: inline-block;			}			#current_variation_id, #current-post-id {				display: none !important;			}			<?php if ($post_type_is !== "wp-call-to-action") {			echo "#variation-list {display:none !important;}";			} ?>			</style>			<script type="text/javascript">				jQuery(document).ready(function($) {				var current_page = jQuery("#current_variation_id").text();						// reload the iframe preview page (for option toggles)					jQuery('.variation-wp-cta').on('click', function (event) {						varaition_is = jQuery(this).attr("id");						var original_url = jQuery(parent.document).find("#TB_iframeContent").attr("src");						var current_id = jQuery("#current-post-id").text();						someURL = original_url;						splitURL = someURL.split('?'); 						someURL = splitURL[0];						new_url = someURL + "?wp-cta-variation-id=" + varaition_is + "&wp_cta_iframe_window=on&post_id=" + current_id;						//console.log(new_url);						jQuery(parent.document).find("#TB_iframeContent").attr("src", new_url);					});				 });				</script>			<?php			if ($variations_array[0] === "")			{				echo '<div id="variation-list" class="no-abtests"><h3>No A/B Tests running for this page</h3>';			} 			else 			{				echo '<div id="variation-list"><h3>Variations:</h3>';				echo '<div id="current_variation_id">'.$wp_cta_variation.'</div>';			}						foreach ($variations_array as $key => $val) 			{				$current_view = ($val == $wp_cta_variation) ? 'current-variation-view' : '';				echo "<div class='variation-wp-cta ".$current_view."' id=". $val . ">";				echo wp_cta_ab_key_to_letter($val);			   				// echo $val; number				echo "</div>";			}			echo "<span id='current-post-id'>$postid</span>";						echo '</div>';		}	}	// NEED ADMIN CHECK HERE	// The loadtiny is specifically to load thing in the module.customizer-display.php iframe (not really working for whatever reason)	if (isset($_GET['page'])&&$_GET['page']=='wp-cta-frontend-editor')	{		add_action('init','wp_cta_customizer_enqueue');		add_action('wp_enqueue_scripts', 'wp_cta_customizer_enqueue');		function wp_cta_customizer_enqueue($hook)		{						wp_enqueue_script(array('jquery', 'editor', 'thickbox', 'media-upload'));			wp_dequeue_script('jquery-cookie');			wp_enqueue_script('jquery-cookie', WP_CTA_URLPATH . 'js/jquery.cookie.js');			wp_enqueue_style( 'wp-admin' );			wp_admin_css('thickbox');			add_thickbox(); 			wp_enqueue_style('wp-cta-admin-css', WP_CTA_URLPATH . 'css/admin-style.css');			wp_enqueue_script('wp-cta-post-edit-ui', WP_CTA_URLPATH . 'js/admin/admin.post-edit.js');			wp_enqueue_script('wp-cta-frontend-editor-js', WP_CTA_URLPATH . 'js/customizer.save.js');			// Ajax Localize			wp_localize_script( 'wp-cta-post-edit-ui', 'wp_cta_post_edit_ui', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'wp_call_to_action_meta_nonce' => wp_create_nonce('wp-call-to-action-meta-nonce') ) );			wp_enqueue_script('wp-cta-js-isotope', WP_CTA_URLPATH . 'js/libraries/isotope/jquery.isotope.js', array('jquery'), '1.0' );			wp_enqueue_style('wp-cta-css-isotope', WP_CTA_URLPATH . 'js/libraries/isotope/css/style.css');			//jpicker - color picker			wp_enqueue_script('jpicker', WP_CTA_URLPATH . 'js/libraries/jpicker/jpicker-1.1.6.min.js');			wp_localize_script( 'jpicker', 'jpicker', array( 'thispath' => WP_CTA_URLPATH.'js/libraries/jpicker/images/' ));			wp_enqueue_style('jpicker-css', WP_CTA_URLPATH . 'js/libraries/jpicker/css/jPicker-1.1.6.min.css');			wp_enqueue_style('jpicker-css', WP_CTA_URLPATH . 'js/libraries/jpicker/css/jPicker.css');    			wp_enqueue_style('wp-cta-customizer-frontend', WP_CTA_URLPATH . 'css/customizer.frontend.css'); 			wp_dequeue_script('form-population');			wp_dequeue_script('funnel-tracking');			wp_enqueue_script('jquery-easing', WP_CTA_URLPATH . 'js/jquery.easing.min.js');					}	}    function wp_cta_customizer_show_metabox($post,$key)     {        global $wp_cta_data;        //print_r($wp_cta_data);exit;        $key = $key['args']['key'];                $wp_cta_custom_fields = $wp_cta_data[$key]['options'];        $wp_cta_custom_fields = apply_filters('wp_cta_show_metabox',$wp_cta_custom_fields, $key);                wp_cta_customizer_render_metabox($key,$wp_cta_custom_fields,$post);    }        function wp_cta_customizer_render_metabox($key,$custom_fields,$post)    {		if (isset($custom_fields))		{			// Use nonce for verification			echo "<input type='hidden' name='wp_cta_{$key}_custom_fields_nonce' value='".wp_create_nonce('wp-cta-nonce')."' />";			// Begin the field table and loop			echo '<div class="form-table" >';			//print_r($custom_fields);exit;			foreach ($custom_fields as $field) {				$raw_option_id = str_replace($key . "-", "", $field['id']);				$label_class = $raw_option_id . "-label";				// get value of this field if it exists for this post				$meta = get_post_meta($post->ID, $field['id'], true);				if ((!isset($meta)&&isset($field['default'])&&!is_numeric($meta))||isset($meta)&&empty($meta)&&isset($field['default'])&&!is_numeric($meta))				{					//echo $field['id'].":".$meta;					//echo "<br>";					$meta = $field['default'];				}							echo '<div class="'.$field['id'].' '.$raw_option_id.' wp-call-to-action-option-row">						<div class="wp-call-to-action-table-header '.$label_class.'"><label for="'.$field['id'].'">'.$field['label'].' <div class="wp_cta_tooltip" title="'.$field['desc'].'"></div></label></div>						<div class="wp-call-to-action-option-td"><a id="click-'.$field['id'].'" class="click-this" href="#'.$field['id'].'">anchor</a>';						switch($field['type']) {							// default content for the_content							case 'default-content':								echo '<span id="overwrite-content" class="button-secondary">Insert Default Content into main Content area</span><div style="display:none;"><textarea name="'.$field['id'].'" id="'.$field['id'].'" class="default-content" cols="106" rows="6" style="width: 75%; display:hidden;">'.$meta.'</textarea></div>';								break;							// text							case 'colorpicker':								if (!$meta)								{									$meta = $field['default'];								}								echo '<input type="text" class="jpicker" style="background-color:#'.$meta.'" name="'.$field['id'].'" id="'.$field['id'].'" value="'.$meta.'" data-old="'.$meta.'" size="5" /><span class="button-primary new-save-wp-cta-frontend" id="'.$field['id'].'" style="margin-left:10px; display:none;">Update</span>';								break;							case 'datepicker':								echo '<div class="jquery-date-picker" id="date-picking">    								<span class="datepair" data-language="javascript">  											Date: <input type="text" id="date-picker-'.$key.'" class="date start" /></span>											Time: <input id="time-picker-'.$key.'" type="text" class="time time-picker" />											<input type="hidden" name="'.$field['id'].'" id="'.$field['id'].'" value="'.$meta.'" data-old="'.$meta.'" class="new-date" value="" >																				</div>';        								break;                      							case 'text':								echo '<input type="text" name="'.$field['id'].'" id="'.$field['id'].'" value="'.$meta.'" data-old="'.$meta.'" size="30" />';								break;							// textarea							case 'textarea':								echo '<textarea name="'.$field['id'].'" id="'.$field['id'].'" data-old="'.$meta.'" cols="106" rows="6" style="width: 75%;">'.$meta.'</textarea>';								break;							// wysiwyg							case 'wysiwyg':								echo '<div id="poststuff" class="wysiwyg-editor-area ">';								wp_editor( $meta, $field['id'], $settings = array() );  								echo "</div>";								break;							// media                    							case 'media':								//echo 1; exit;								echo '<label for="upload_image">';								echo '<input name="'.$field['id'].'"  id="'.$field['id'].'" type="text" size="36" name="upload_image" value="'.$meta.'" data-old="'.$meta.'" />';								echo '<input class="upload_image_button" id="uploader_'.$field['id'].'" type="button" value="Upload Image" /><span class="uploader-save"></span>';								//echo '<p class="description">'.$field['desc'].'</p>'; 								break;							// checkbox							case 'checkbox':								$i = 1;								echo '<table class="wp_cta_check_box_table" data-old="'.$meta.'">';                      								if (!isset($meta)){$meta=array();}								elseif (!is_array($meta)){									$meta = array($meta);								}								foreach ($field['options'] as $value=>$label) {									if ($i==5||$i==1)									{										echo "<tr>";										$i=1;									}										echo '<td><input type="checkbox" name="'.$field['id'].'[]" id="'.$field['id'].'" value="'.$value.'" ',in_array($value,$meta) ? ' checked="checked"' : '','/>';										echo '<label for="'.$value.'">&nbsp;&nbsp;'.$label.'</label></td>';                 									if ($i==4)									{										echo "</tr>";									}									$i++;								}								echo "</table>";							   // echo '<div class="wp_cta_tooltip tool_checkbox" title="'.$field['desc'].'"></div>';							break;							// radio							case 'radio':								foreach ($field['options'] as $value=>$label) {									//echo $meta.":".$field['id'];									//echo "<br>";									echo '<input type="radio" name="'.$field['id'].'" id="'.$field['id'].'" data-old="'.$meta.'" value="'.$value.'" ',$meta==$value ? ' checked="checked"' : '','/>';									echo '<label for="'.$value.'">&nbsp;&nbsp;'.$label.'</label> &nbsp;&nbsp;&nbsp;&nbsp;';                             								}							  //  echo '<div class="wp_cta_tooltip" title="'.$field['desc'].'"></div>';							break;							// select							case 'dropdown':								echo '<select name="'.$field['id'].'" id="'.$field['id'].'" data-old="'.$meta.'" class="'.$raw_option_id.'">';								foreach ($field['options'] as $value=>$label) {									echo '<option', $meta == $value ? ' selected="selected"' : '', ' value="'.$value.'">'.$label.'</option>';								}								echo '</select>';							break;													} //end switch				echo '</div></div>';			} // end foreach			echo '</div>'; // end table		}    }/* Admin Settings page Function */function wp_cta_frontend_editor_screen() {	// show on screen else redirect to another page	if (isset($_GET['frontend-go'])&&$_GET['frontend-go']=='on')	{		$wp_cta_id = $_GET['wp_cta_id'];		$post_type_is = get_post_type($_GET['wp_cta_id']);		$post = get_post($wp_cta_id);		$admin_title = $post->post_title;		$main_headline = wp_cta_main_headline($post,null,true);		$content = wp_cta_content_area($post,null,true);		$wp_cta_conversion_area = wp_cta_conversion_area($post,null,true,false);		$letter = (isset($_GET['letter'])) ? '<span class="variation-letter-top">'.$_GET['letter'].'</span>' : '';		do_action('wp_cta_frontend_editor_screen_pre',$post);		$wp_cta_variation = (isset($_GET['wp-cta-variation-id'])) ? $_GET['wp-cta-variation-id'] : '0';		if ($post_type_is !== "wp-call-to-action") { 			echo "<style type='text/css'>.variation-letter-top {display:none;} #wp-cta-top-box{height:0px;} h1 {margin-top:35px;}</style>";		}		?>		<div id="wp-cta-top-box"><div id='wp-cta-options-controls'>  <a style="float:right; margin-right:5px;" class="reload">Reload Preview</a>			<a style="float:right; margin-right:5px;" class="full-size-view">View fullsize</a>			<a style="float:right; margin-right:5px; display:none;" class="shrink-view">Shrink View</a>		 </div> </div>		<!-- The classes/id are important for jquery ajax to fire. don't change -->		<div id="wp-cta-frontend-options-container" class="wp-cta-options-customizer-area">			<h1><?php echo $letter;?><?php echo $admin_title;?></h1>			<div id="post_ID"><?php echo $wp_cta_id;?></div>	  					<form action="<?php echo $_SERVER["REQUEST_URI"] ?>" method="POST">				<div class="the-title wp-call-to-action-option-row">					<a id="click-the-title"  class="click-this" style="display:none;" href="#the-title">anchor</a>						<div class="wp-call-to-action-table-header logo-label">							<label for="the-title">Main Headline</label>						</div>						<div class="wp-call-to-action-option-td">						<?php if ($post_type_is === "wp-call-to-action") { 						wp_cta_display_headline_input('wp-cta-main-headline',$main_headline);						} else {							echo '<input type="text" name="main_title" id="main_title" value="'.$admin_title.'" data-old="'.$admin_title.'" size="30" />';						}						?>													</div>				</div>				<?php         								$template = get_post_meta($post->ID, 'wp-cta-selected-template', true); 				$template = strtolower($template);  				$key = array();				$key['args']['key'] = $template;								wp_cta_customizer_show_metabox($post,$key) ;								?>				<div class="the-content content-<?php echo $wp_cta_variation; ?> wp-call-to-action-option-row">					<a id="click-the-content" class="click-this" style="display:none;" href="#the-content">anchor</a>						<div class="wp-call-to-action-table-header the-content-label">							<label for="the-content">							The Main Content Area							</label>						</div>					<div>						<div class="wp-call-to-action-option-td" id="the-content">							<?php 							wp_cta_wp_editor( $content, 'wp_content', $settings = array('media_buttons' => TRUE, 'teeny' => FALSE) ); 							?>      						</div>					</div>				</div>				<?php if ($post_type_is === "wp-call-to-action") { } ?>		     										<!-- Need form submit button here -->   			</form>					</div>		<?php 	} else {  		$url = site_url();		header("Location: " . $url . "/wp-admin/edit.php?post_type=wp-call-to-action&notice=edit-note");	}     }if (isset($_GET['notice'])&&$_GET['notice']=='edit-note'){	echo "<div style='font-size:28px; text-align:center; position:absolute; left:33%; top:59px;'>Head into the landing page and click on frontend editor button!</div>";}/* End Hidden Settings Page *//************ Main Page Window* This is the page window behind the frames***************//* Not working for some reason: function wp_cta_customizer_preview_window($hook){        wp_register_script('wp-cta-customizer-load-js', WP_CTA_URLPATH . 'js/customizer.load.js', array('jquery'));        wp_enqueue_script('wp-cta-customizer-load-js');}*/if (isset($_GET['cta-template-customize'])&&$_GET['cta-template-customize']=='on'){	add_filter('wp_head', 'wp_cta_launch_customizer');}			// need filter to not load the actual page behind the frames. AKA kill the botton contentfunction wp_cta_launch_customizer() {	//echo "here";exit;	global $post;		$page_id = $post->ID;	$permalink = get_permalink( $page_id );		$randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 10);	$wp_cta_variation = (isset($_GET['wp-cta-variation-id'])) ? $_GET['wp-cta-variation-id'] : '0';		$params = '?wp-cta-variation-id='.$wp_cta_variation.'&cache_bust='.$randomString.'&live-preview-area='.$randomString;		$preview_link = $permalink.$params;	$preview_link = apply_filters('wp_cta_customizer_preview_link',$preview_link);		//$customizer_link = '/wp-admin/edit.php?post_type=wp-call-to-action&page=wp-cta-frontend-editor&frontend-go=on&wp_cta_id='.$page_id.'&loadwp-ctadata=yes';	$customizer_link = '/wp-admin/post.php?post='.$page_id.'&wp-cta-variation-id='.$wp_cta_variation.'&action=edit&frontend=true';	$customizer_link = apply_filters('wp_cta_customizer_customizer_link',$customizer_link);	//echo $customizer_link;exit;	do_action('wp_cta_launch_customizer_pre',$post);	?>		<style type="text/css">		#wpadminbar {			z-index: 99999999999 !important; 		}		#wp-cta-live-preview #wpadminbar {			margin-top:0px;		}		.wp-cta-load-overlay {			position: absolute;			z-index: 9999999999 !important; 			z-index: 999999;			background-color: #000;			opacity: 0;			background: -moz-radial-gradient(center,ellipse cover,rgba(0,0,0,0.4) 0,rgba(0,0,0,0.9) 100%);			background: -webkit-gradient(radial,center center,0px,center center,100%,color-stop(0%,rgba(0,0,0,0.4)),color-stop(100%,rgba(0,0,0,0.9)));			background: -webkit-radial-gradient(center,ellipse cover,rgba(0,0,0,0.4) 0,rgba(0,0,0,0.9) 100%);			background: -o-radial-gradient(center,ellipse cover,rgba(0,0,0,0.4) 0,rgba(0,0,0,0.9) 100%);			background: -ms-radial-gradient(center,ellipse cover,rgba(0,0,0,0.4) 0,rgba(0,0,0,0.9) 100%);			background: radial-gradient(center,ellipse cover,rgba(0,0,0,0.4) 0,rgba(0,0,0,0.9) 100%);			filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#66000000',endColorstr='#e6000000',GradientType=1);			-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=50)";			filter: alpha(opacity=50);		}	</style>	<script type="text/javascript">	jQuery(document).ready(function($) {		jQuery("#wp-admin-bar-edit a").text("Main Edit Screen"); 				setTimeout(function() {		      jQuery(document).find("#wp-cta-live-preview").contents().find("#wpadminbar").hide()				   jQuery(document).find("#wp-cta-live-preview").contents().find("html").css("margin-bottom", "-28px");		}, 2000);			 });	</script>   	<?php 			echo '<div class="wp-cta-load-overlay" style="top: 0;bottom: 0; left: 0;right: 0;position: fixed;opacity: .8; display:none;"></div><iframe id="wp_cta_customizer_options" src="'.$customizer_link.'" style="width: 32%; height: 100%; position: fixed; left: 0px; z-index: 999999999; top: 26px;"></iframe>';	echo '<iframe id="wp-cta-live-preview" src="'.$preview_link.'" style="width: 68%; height: 100%; position: fixed; right: 0px; top: 26px; z-index: 999999999; background-color: #eee;	//background-image: linear-gradient(45deg, rgb(194, 194, 194) 25%, transparent 25%, transparent 75%, rgb(194, 194, 194) 75%, rgb(194, 194, 194)), linear-gradient(-45deg, rgb(194, 194, 194) 25%, transparent 25%, transparent 75%, rgb(194, 194, 194) 75%, rgb(194, 194, 194));	//background-size:25px 25px; background-position: initial initial; background-repeat: initial initial;"></iframe>';	wp_footer();	exit;}