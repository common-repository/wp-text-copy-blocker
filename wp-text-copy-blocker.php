<?php
/*
Plugin Name: WP Text Copy Blocker
Plugin URI: http://www.siteguarding.com/en/website-extensions
Description: Blocks right mouse button, block Ctrl+C, Blocks possibility to select text on the page  
Version: 1.0
Author: SiteGuarding.com
Author URI: http://www.siteguarding.com
License: GPLv2
*/  
// rev.20200601 

define('TCB_PLUGIN_VERSION', '1.0');

if (!defined('DIRSEP'))
{
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') define('DIRSEP', '\\');
    else define('DIRSEP', '/');
}

//error_reporting(E_ERROR | E_WARNING);
//error_reporting(E_ERROR);
error_reporting(0);



add_action( 'wp_ajax_plgsgtcb_save_stats', 'plgsgtcb_save_stats' );
add_action( 'wp_ajax_nopriv_plgsgtcb_save_stats', 'plgsgtcb_save_stats' );
function plgsgtcb_save_stats() 
{
    $ip = TEXT_SG_Protection::GetMyIP();
    $country_code = TEXT_SG_Protection::GetCountryCode($ip);
    if (isset($_POST['url'])) $url = trim($_POST['url']);
    else $url = '';
    
    $data = array(
        'time' => time(),
        'ip' => $ip,
        'country_code' => $country_code,
        'url' => addslashes($url),
    );
    TEXT_SG_Protection::Save_Block_alert($data);
    
    $params = TEXT_SG_Protection::Get_Params( array('block_ip') );
    if (intval($params['block_ip']) == 1) TEXT_SG_Protection::BlockIPaddress($ip);
    
	wp_die();
}

if( !is_admin() ) 
{
    add_action('wp_head', 'plgsgtcb_action_BlockJScodes', 1);
    
    function plgsgtcb_action_BlockJScodes()
    {
        $settings_file = dirname(__FILE__).'/settings.php';
        if (file_exists($settings_file))
        {
            include_once($settings_file);
            $settings = (array)json_decode($blocker_sg_settings, true);
            
            if (isset($settings['collect_stats']) && $settings['collect_stats'] == 1) 
            {
                TEXT_SG_Protection_HTML::Insert_Code_collect_stats();
                $collect_stats = true;
            }
            else $collect_stats = false;
            
            if (isset($settings['hide_page_content']) && $settings['hide_page_content'] == 1) 
            {
                TEXT_SG_Protection_HTML::Insert_Code_hide_page_content();
                $hide_page_content = true;
            }
            else $hide_page_content = false;
            
            $add_js_code = false;
            if (isset($settings['right_click']) && $settings['right_click'] == 1) $add_js_code = true;
            if (isset($settings['ctrl_keys']) && $settings['ctrl_keys'] == 1) $add_js_code = true;
            if ($add_js_code) TEXT_SG_Protection_HTML::Insert_Code_mixed($settings, $collect_stats, $hide_page_content);
            
            if (isset($settings['select_text']) && $settings['select_text'] == 1) TEXT_SG_Protection_HTML::Insert_Code_select_text($collect_stats, $hide_page_content);
            if (isset($settings['debugger']) && $settings['debugger'] == 1) TEXT_SG_Protection_HTML::Insert_Code_debugger($collect_stats, $hide_page_content);


        }
    }
    
	function plgsgtcb_footer_protectedby() 
	{
        if (strlen($_SERVER['REQUEST_URI']) < 5)
        {
                $params = TEXT_SG_Protection::Get_Params(array('protection_by', 'installation_date'));
                if (!TEXT_SG_Protection::CheckIfPRO()) $params['protection_by'] = 1;
                
                $new_date = date("Y-m-d", mktime(0, 0, 0, date("m")  , date("d")-3, date("Y")));
        		if ( !isset($params['protection_by']) || intval($params['protection_by']) == 1 && $new_date >= $params['installation_date'] )
        		{
        		      $links = array(
                        'https://www.siteguarding.com/en/',
                        'https://www.siteguarding.com/en/website-antivirus',
                        'https://www.siteguarding.com/en/protect-your-website',
                        'https://www.siteguarding.com/en/services/malware-removal-service'
                      );
                      $link = $links[ mt_rand(0, count($links)-1) ];
        			?>
        				<div style="font-size:10px; padding:0 2px;position: fixed;bottom:0;right:0;z-index:1000;text-align:center;background-color:#F1F1F1;color:#222;opacity:0.8;">Protected with <a style="color:#4B9307" href="<?php echo $link; ?>" target="_blank" title="Website Security services. Website Malware removal. Website Antivirus protection.">SiteGuarding</a></div>
        			<?php
        		}
        }	
	}
	add_action('wp_footer', 'plgsgtcb_footer_protectedby', 100);
    
}




if( is_admin() ) {
	
	//error_reporting(0);
	

	add_action( 'admin_footer', 'plgsgtcb_big_dashboard_widget' );

	function plgsgtcb_big_dashboard_widget() 
	{
		if ( get_current_screen()->base !== 'dashboard' || TEXT_SG_Protection::CheckIfPRO()) {
			return;
		}
		?>

		<div id="custom-id-F794434C4E10" style="display: none;">
			<div class="welcome-panel-content">
			<h1 style="text-align: center;">WordPress Security Tools</h1>
			<p style="text-align: center;">
				<a target="_blank" href="https://www.siteguarding.com/en/security-dashboard?pgid=GE2" target="_blank"><img src="<?php echo plugins_url('images/b10.png', __FILE__); ?>" /></a>&nbsp;
				<a target="_blank" href="https://www.siteguarding.com/en/security-dashboard?pgid=GE2" target="_blank"><img src="<?php echo plugins_url('images/b11.png', __FILE__); ?>" /></a>&nbsp;
				<a target="_blank" href="https://www.siteguarding.com/en/security-dashboard?pgid=GE2" target="_blank"><img src="<?php echo plugins_url('images/b12.png', __FILE__); ?>" /></a>&nbsp;
				<a target="_blank" href="https://www.siteguarding.com/en/security-dashboard?pgid=GE2" target="_blank"><img src="<?php echo plugins_url('images/b13.png', __FILE__); ?>" /></a>&nbsp;
				<a target="_blank" href="https://www.siteguarding.com/en/security-dashboard?pgid=GE2" target="_blank"><img src="<?php echo plugins_url('images/b14.png', __FILE__); ?>" /></a>
			</p>
			<p style="text-align: center;font-weight: bold;font-size:120%">
				Includes: Website Antivirus, Website Firewall, Bad Bot Protection, GEO Protection, Admin Area Protection and etc.
			</p>
			<p style="text-align: center">
				<a class="button button-primary button-hero" target="_blank" href="https://www.siteguarding.com/en/security-dashboard?pgid=GE2">Secure Your Website</a>
			</p>
			</div>
		</div>
		<script>
			jQuery(document).ready(function($) {
				$('#welcome-panel').after($('#custom-id-F794434C4E10').show());
			});
		</script>
		
	<?php 
	}

    
    
    
	function register_plgsgtcb_page() 
	{
		add_menu_page('plgsgtcb_protection', 'Text Copy Blocker', 'activate_plugins', 'plgsgtcb_protection', 'register_plgsgtcb_page_callback', plugins_url('images/', __FILE__).'menu-icon.png');
        add_submenu_page( 'plgsgtcb_protection', 'Front-end protection', 'Front-end protection', 'manage_options', 'plgsgtcb_protection', 'register_plgsgtcb_page_callback' );
	}
    add_action('admin_menu', 'register_plgsgtcb_page');
    
	add_action( 'wp_ajax_plgsgtcb_ajax_refresh', 'plgsgtcb_ajax_refresh' );
    function plgsgtcb_ajax_refresh($data) 
    {
	    print TEXT_SG_Protection_HTML::blockPagePreview($data);
        wp_die();
    }   
    
    
    


	add_action('admin_menu', 'register_plgsgtcb_logs_subpage');
	function register_plgsgtcb_logs_subpage() {
		add_submenu_page( 'plgsgtcb_protection', 'Logs & Stats', 'Logs & Stats', 'manage_options', 'plgsgtcb_protection&tab=1', 'register_plgsgtcb_page_callback' ); 
	}
    
	add_action('admin_menu', 'register_plgsgtcb_blockedip_subpage');
	function register_plgsgtcb_blockedip_subpage() {
		add_submenu_page( 'plgsgtcb_protection', 'Blocked IP', 'Blocked IP', 'manage_options', 'plgsgtcb_protection&tab=2', 'register_plgsgtcb_page_callback' ); 
	}
    
	
	add_action('admin_menu', 'register_plgsgtcb_support_subpage');
	function register_plgsgtcb_support_subpage() {
		add_submenu_page( 'plgsgtcb_protection', 'Settings & Support', 'Settings & Support', 'manage_options', 'plgsgtcb_protection&tab=3', 'register_plgsgtcb_page_callback' ); 
	}
    
    
	add_action('admin_menu', 'register_plgsgtcb_extensions_subpage');
	function register_plgsgtcb_extensions_subpage() {
		add_submenu_page( 'plgsgtcb_protection', 'Security Extensions', 'Security Extensions', 'manage_options', 'plgsgtcb_extensions_page', 'plgsgtcb_extensions_page' ); 
	}


	function plgsgtcb_extensions_page() 
	{
        wp_enqueue_style( 'plgsgtcb_LoadStyle' );
	    TEXT_SG_Protection_HTML::ExtensionsPage();
    }
    
    
	add_action('admin_menu', 'register_plgsgtcb_upgrade_subpage');
	function register_plgsgtcb_upgrade_subpage() {
		add_submenu_page( 'plgsgtcb_protection', '<span style="color:#21BA45"><b>Get Full Version</b></span>', '<span style="color:#21BA45"><b>Get Full Version</b></span>', 'manage_options', 'register_plgsgtcb_upgrade_redirect', 'register_plgsgtcb_upgrade_redirect' ); 
	}
    function register_plgsgtcb_upgrade_redirect()
    {
        ?>
        <p style="text-align: center; width: 100%;">
            <img width="120" height="120" src="<?php echo plugins_url('images/ajax_loader.svg', __FILE__); ?>" />
            <br /><br />
            Redirecting.....
        </p>
        <script>
        window.location.href = '<?php echo TEXT_SG_Protection::$upgrade_buy_link.'?domain='.urlencode( get_site_url() ); ?>';
        </script>
        <?php
    }
    


    
    

	function register_plgsgtcb_page_callback() 
	{
	    $action = '';
        if (isset($_REQUEST['action'])) $action = sanitize_text_field(trim($_REQUEST['action']));
        
        // Actions
        if ($action != '')
        {
            $action_message = '';
            switch ($action)
            {
                case 'Save_frontend_protection_params':
                    if (check_admin_referer( 'name_10EFDDE97A00' ))
                    {
                        $data = array();
                        if (isset($_POST['right_click'])) $data['right_click'] = sanitize_text_field(intval($_POST['right_click']));
                        else $data['right_click'] = 0;
                        if (isset($_POST['ctrl_keys'])) $data['ctrl_keys'] = sanitize_text_field(intval($_POST['ctrl_keys']));
                        else $data['ctrl_keys'] = 0;
                        if (isset($_POST['select_text'])) $data['select_text'] = sanitize_text_field(intval($_POST['select_text']));
                        else $data['select_text'] = 0;
                        if (isset($_POST['debugger'])) $data['debugger'] = sanitize_text_field(intval($_POST['debugger']));
                        else $data['debugger'] = 0;
                        if (isset($_POST['collect_stats'])) $data['collect_stats'] = sanitize_text_field(intval($_POST['collect_stats']));
                        else $data['collect_stats'] = 0;
                        if (isset($_POST['block_ip'])) $data['block_ip'] = sanitize_text_field(intval($_POST['block_ip']));
                        else $data['block_ip'] = 0;
                        if (isset($_POST['block_ip_time'])) $data['block_ip_time'] = sanitize_text_field(intval($_POST['block_ip_time']));
                        if (isset($_POST['hide_page_content'])) $data['hide_page_content'] = sanitize_text_field(intval($_POST['hide_page_content']));
                        else $data['hide_page_content'] = 0;
                        
                        
                        if (!TEXT_SG_Protection::CheckIfPRO() /*&& count($data['frontend_country_list']) > 15*/)
                        {
                            if (TEXT_SG_Protection::FreeDaysLeft() <= 0)
                            {
                                $data['collect_stats'] = 0;
                                $data['block_ip'] = 0;
                                $data['hide_page_content'] = 0;
                                
                                $message_data = array(
                                    'type' => 'info',
                                    'header' => 'Free version limits',
                                    'message' => 'Limit is 15 countries. Please upgrade.<br><b>For all websites with our <a href="https://www.siteguarding.com/en/antivirus-site-protection" target="_blank">PRO Antivirus plugin</a>, we provide with free license.</b>',
                                    'button_text' => 'Upgrade',
                                    'button_url' => TEXT_SG_Protection::$upgrade_buy_link.'?domain='.urlencode( get_site_url() ),
                                    'help_text' => ''
                                );
                                echo '<div style="max-width:900px;margin-top: 10px;">';
                                TEXT_SG_Protection_HTML::PrintIconMessage($message_data);
                                echo '</div>';
                            }
                        }
                        
                        $data['frontend_country_list'] = json_encode($data['frontend_country_list']);
                        
                        $action_message = 'Settings saved and applied for your website';
                        
                        TEXT_SG_Protection::Set_Params($data);
                        
                        //TEXT_SG_Protection::CheckWPConfig_file();
                        if ($data['block_ip'] == 0) TEXT_SG_Protection::PatchWPConfig_file(false);
                        else TEXT_SG_Protection::PatchWPConfig_file(true);
                        
                        TEXT_SG_Protection::Delete_expired_blocked_IP();
                    }
                    break;
                
                    
           
                    
                case 'Save_Settings':
                    if (check_admin_referer( 'name_xZU32INTzZM1GFNz' ))
                    {
                        $data = array();
                        if (isset($_POST['registration_code'])) $data['registration_code'] = sanitize_text_field($_POST['registration_code']);
                        if (isset($_POST['protection_by'])) $data['protection_by'] = intval($_POST['protection_by']);
                        else $data['protection_by'] = 0;
                        if (!TEXT_SG_Protection::CheckIfPRO()) $data['protection_by'] = 1;
                        
                        $action_message = 'Settings saved';
                        
                        TEXT_SG_Protection::Set_Params($data);
                        
                        TEXT_SG_Protection::CreateSettingsFile();
                        TEXT_SG_Protection::CheckWPConfig_file();
                    }
                    break;

            }
            
            if ($action_message != '')
            {
                $message_data = array(
                    'type' => 'info',
                    'header' => '',
                    'message' => $action_message,
                    'button_text' => '',
                    'button_url' => '',
                    'help_text' => ''
                );
                echo '<div style="max-width:900px;margin-top: 10px;">';
                TEXT_SG_Protection_HTML::PrintIconMessage($message_data);
                echo '</div>';
            }
        }
        
        wp_enqueue_style( 'plgsgtcb_LoadStyle' );
        
        
        TEXT_SG_Protection_HTML::PluginPage();
    }


    function plgsgtcb_API_Request($type = '')
    {
        $plugin_code = 7;
        $website_url = get_site_url();
        
        $url = "https://www.siteguarding.com/ext/plugin_api/index.php";
        $response = wp_remote_post( $url, array(
            'method'      => 'POST',
            'timeout'     => 600,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => array(),
            'body'        => array(
                'action' => 'inform',
                'website_url' => $website_url,
                'action_code' => $type,
                'plugin_code' => $plugin_code,
            ),
            'cookies'     => array()
            )
        );
    }
    
	function plgsgtcb_activation()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'plgsgtcb_config';
		if( $wpdb->get_var( 'SHOW TABLES LIKE "' . $table_name .'"' ) != $table_name ) {
			$sql = 'CREATE TABLE IF NOT EXISTS '. $table_name . ' (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `var_name` char(255) CHARACTER SET utf8 NOT NULL,
                `var_value` LONGTEXT CHARACTER SET utf8 NOT NULL,
                PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;';

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql ); // Creation of the new TABLE
            
            TEXT_SG_Protection::Set_Params( array('installation_date' => date("Y-m-d")) );
		}
        
        
		$table_name = $wpdb->prefix . 'plgsgtcb_stats';
		if( $wpdb->get_var( 'SHOW TABLES LIKE "' . $table_name .'"' ) != $table_name ) {
			$sql = 'CREATE TABLE IF NOT EXISTS '. $table_name . ' (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `time` int(11) NOT NULL,
              `ip` varchar(15) NOT NULL,
              `country_code` varchar(2) NOT NULL,
              `url` varchar(128) NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;';

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql ); // Creation of the new TABLE
		}
		
        plgsgtcb_API_Request(1);
        add_option('plgsgtcb_activation_redirect', true);
	}
	register_activation_hook( __FILE__, 'plgsgtcb_activation' );
	add_action('admin_init', 'plgsgtcb_activation_do_redirect');
	
	function plgsgtcb_activation_do_redirect() {
		if (get_option('plgsgtcb_activation_redirect', false)) {
			delete_option('plgsgtcb_activation_redirect');
			 wp_redirect("admin.php?page=plgsgtcb_protection");
			 exit;
		}
	}
    
    
	function plgsgtcb_uninstall()
	{
		global $wpdb;
		//$table_name = $wpdb->prefix . 'plgsgtcb_config';
		//$wpdb->query( 'DROP TABLE ' . $table_name );
        
		$table_name = $wpdb->prefix . 'plgsgtcb_stats';
		$wpdb->query( 'DROP TABLE ' . $table_name );
		
        plgsgtcb_API_Request(3);
	}
	register_uninstall_hook( __FILE__, 'plgsgtcb_uninstall' );
    
    
	function plgsgtcb_deactivation()
	{
        plgsgtcb_API_Request(2);
	}
    register_deactivation_hook( __FILE__, 'plgsgtcb_deactivation' );
	
	
    
	add_action( 'admin_init', 'plgsgtcb_admin_init' );
	function plgsgtcb_admin_init()
	{
		wp_enqueue_script( 'plgsgtcb_LoadSemantic', plugins_url( 'js/semantic.min.js', __FILE__ ));
       // wp_register_script( 'plgsgtcb_LoadSemantic', plugins_url('js/semantic.min.js', __FILE__) , '', '', true );
		wp_register_style( 'plgsgtcb_LoadStyle', plugins_url('css/wp-text-copy-blocker.css', __FILE__) );
		
        $js_file = dirname(__FILE__).'/js/javascript.js';	
        $js_file_gz = dirname(__FILE__).'/js/javascript.pack';	
        if (!file_exists($js_file) && file_exists($js_file_gz))
        {
            $filename = $js_file_gz;
            $handle = fopen($filename, "r");
            $contents = fread($handle, filesize($filename));
            fclose($handle);
            
            $contents = gzdecode($contents);
            
            $handle = fopen($js_file, 'w');
            fwrite($handle, $contents);
            fclose($handle);
        }
        wp_register_script( 'plgsgtcb_LoadCharts', plugins_url('js/javascript.js', __FILE__) , '', '', true );

	}




}






/**
 * Functions
 */


class TEXT_SG_Protection_HTML
{
    public static function ShowJSAlertPopup($log_action = false)
    {
        ?>
alert('You are not allowed for this action. Please contact webmaster.<?php if ($log_action) echo ' Your action is logged.'; ?>');
        <?php
    }
    
    public static function RemoveHTMLContent()
    {
        ?>
if(window.jQuery) jQuery(document.body).empty();
        <?php
    }
    
    public static function Insert_Code_mixed($params, $stats = false, $hide_content = false)
    {
        ?>
<script language="JavaScript">
  window.onload = function() {
    
    <?php
    if ($params['right_click']) {
    ?>
    document.addEventListener("contextmenu", function(e){

      e.preventDefault();
              
        <?php if ($stats) echo 'SaveAjaxStats();'."\n"; ?>
        <?php if ($hide_content) self::RemoveHTMLContent(); ?>
        <?php self::ShowJSAlertPopup($stats); ?>

    }, false);
    <?php
    }
    ?>
    
    <?php
    if ($params['ctrl_keys']) {
    ?>
    document.addEventListener("keydown", function(e) {
    //document.onkeydown = function(e) {
      // "I" key
      if (e.ctrlKey && e.shiftKey && e.keyCode == 73) {
        disabledEvent(e);
      }
      // "J" key
      if (e.ctrlKey && e.shiftKey && e.keyCode == 74) {
        disabledEvent(e);
      }
      // "S" key + macOS
      if (e.keyCode == 83 && (navigator.platform.match("Mac") ? e.metaKey : e.ctrlKey)) {
        disabledEvent(e);
      }
      // "U" key
      if (e.ctrlKey && e.keyCode == 85) {
        disabledEvent(e);
      }
      // "C" key
      if (e.ctrlKey && e.keyCode == 67) {
        disabledEvent(e);
      }
      // "F12" key
      if (event.keyCode == 123) {
        disabledEvent(e);
      }
    }, false);
    function disabledEvent(e){
      if (e.stopPropagation){
        e.stopPropagation();
      } else if (window.event){
        window.event.cancelBubble = true;
      }
      
      e.preventDefault();
      
      <?php if ($stats) echo 'SaveAjaxStats();'."\n"; ?>
      <?php self::ShowJSAlertPopup($stats); ?>
      <?php if ($hide_content) self::RemoveHTMLContent(); ?>
      
      return false;
    }
    <?php
    }
    ?>
  };
</script>
        <?php
    }
    
    
    
    public static function Insert_Code_select_text($stats = false, $hide_content = false)
    {
        ?>
<style>
body,.post-inner{-webkit-user-select: none!important;}
</style>
        <?php
    }
    
    public static function Insert_Code_debugger($stats = false, $hide_content = false)
    {
        ?>
<script type="text/javascript">
eval((function () { (function a() {
		try { (function b(i) {
				if (('' + (i / i)).length !== 1 || i === 0) { (function () {}).constructor('debugger')()
				} else {
					debugger
				}
				b(++i)
			})(0)
		} catch(e) {
			setTimeout(a, 5000)
		}
	})()
})());
</script>
        <?php
    }
    
    public static function Insert_Code_collect_stats()
    {
        ?>
<script type="text/javascript">
function SaveAjaxStats() {
	var data = {
		"action": "plgsgtcb_save_stats",
		"url": window.location.href
	};
	jQuery.post("<?php echo admin_url('admin-ajax.php'); ?>", data, function(response) {
		//alert("Got this from the server: " + response);
        //jQuery("#sg_report").html(response);
	});
}
</script>
        <?php
    }
    
    
    public static function Insert_Code_hide_page_content()
    {
        
    }
    

    
    
    public static function ExtensionsPage()
    {
        $json_file = dirname(__FILE__).DIRSEP.'extensions.json';
        if (file_exists($json_file))
        {
            $handle = fopen($json_file, "r");
            $contents = fread($handle, filesize($json_file));
            fclose($handle);
            
            $items = (array)json_decode($contents, true);
        }
        
        ?>
        <div class="ui main container" style="float: left;margin-top:20px;">
            <h2 class="ui dividing header">Security extensions</h2>
            
            
            <div class="ui three column grid">
            
            <?php
            foreach ($items as $item) {
            ?>
              <div class="column">
                <div class="ui segment full_h0">
                    <h3 class="ui dividing header"><img src="<?php echo $item['logo']; ?>"/>
                        <?php echo $item['title']; ?></h3>
                    <div class="ui list">
                        <?php
                        foreach ($item['list'] as $row) {
                        ?>
                            <a class="item"><div class="content"><div class="description"><i class="right triangle icon"></i><?php echo $row; ?></div></div></a>
                        <?php
                        }
                        ?>
                    </div>
                    <p style="text-align: center;"><a class="ui medium positive button" href="<?php echo $item['link']; ?>" target="_blank">Learn more</a></p>
                </div>
              </div>
            <?php
            }
            ?>
              
            </div>
            
            
        </div>
        <?php            
    }
    
    

    
    public static function Wait_CSS_Loader()
    {
        ?>
        
		<div id="loader" style="min-height:900px;position: relative"><img style="position: absolute;top: 0; left: 0; bottom: 0; right: 0; margin:auto;" src="<?php  echo plugins_url('images/ajax_loader.svg', __FILE__); ?>"></div>
		            <script>
            jQuery(document).ready(function(){
                jQuery('.ui.accordion').accordion();
                jQuery('.ui.checkbox').checkbox();
                jQuery('#main').css('opacity','0');
                jQuery('#main').css('display','block');
                jQuery('#loader').css('display','none');
				fromBlur();
            });
			
			var i = 0;
			
			function fromBlur() {
				running = true;
					if (running){
					
						jQuery('#main').css("opacity", i);
						
						i = i + 0.02;

					if(i > 1) {
						running = false;
						i = 0;
					}
					if(running) setTimeout("fromBlur()", 5);

				}
			}
            </script>
            
            <?php
    }
    
    
    
    public static function PluginPage()
    {
		self::Wait_CSS_Loader();
        
        $params = TEXT_SG_Protection::Get_Params();
        //print_r($params);
		
		$isPRO = TEXT_SG_Protection::CheckIfPRO();
        if (!$isPRO)
        {
            $class_disabled = '';
            
            $left_days = TEXT_SG_Protection::FreeDaysLeft();
            if ($left_days <= 0)
            {
                $params['collect_stats'] = 0;
                $params['block_ip'] = 0;
                $params['hide_page_content'] = 0;
                TEXT_SG_Protection::Set_Params($params);
                TEXT_SG_Protection::CreateSettingsFile();
                
                $class_disabled = ' disabled';
            }
        }
		
        
        $myIP = TEXT_SG_Protection::GetMyIP();
        

        $tab_id = isset($_GET['tab']) ? intval($_GET['tab']) : 0;
        $tab_array = array(0 => '', 1 => '', 2 => '', 3 => '', 4 => '', 5 => '' );
        $tab_array[$tab_id] = 'active ';
           ?>
    <script>
    function InfoBlock(id)
    {
        jQuery("#"+id).toggle();
    }
    function SelectCountries(select, uncheck)
    {
        if (select != '') jQuery(select).prop( "checked", true );
        
        if (uncheck != '') jQuery(uncheck).prop( "checked", false );
    }
	function ShowHideForm(v, el)
	{
		if (v == true) jQuery(el).show(300);
		else jQuery(el).hide(300);
	}

	function BlockPage_Refresh()
	{ 
		jQuery('#geo_preview').html('<div style="margin:30px auto; max-width: 400px; max-height: 450px;text-align: center;">Please wait...</div>');
		jQuery('.modal.preview_show').modal('show');
		var myObj = { 
				"custom_status": jQuery('#custom_status').checkbox('is checked'),
				"logo_url": jQuery('#logo_url').val(),
				"text_1": jQuery('#text_1').val(),
				"text_2": jQuery('#text_2').val(),
				"hide_debug": jQuery('#hide_debug').checkbox('is checked'),
				"hide_ipinfo": jQuery('#hide_ipinfo').checkbox('is checked')
		}; 
		var jsonString = JSON.stringify(myObj);
		jQuery.post(
			ajaxurl, 
			{
				'action': 'plgsgtcb_ajax_refresh',
				'data' : jsonString
			}, 
			function(response){
				jQuery('#geo_preview').html(response);
				
			}
		);  
	}
	jQuery(document).ready(function(){

		ShowHideForm(jQuery('#custom_status').checkbox('is checked'), '.show_active')

	}); 
    </script>
    
    <h3 class="ui header title_product">Text Copy Blocker (ver. <?php echo TCB_PLUGIN_VERSION; ?>)</h3>
    
    
    <?php
        if (!$isPRO)  {
            ?>
            <div class="ui large centered leaderboard" style="margin-top: 10px;">
                <a href="https://www.siteguarding.com/en/protect-your-website" target="_blank"><img src="<?php echo plugins_url('images/rek1.png', __FILE__); ?>" /></a>&nbsp;
                <a href="https://www.siteguarding.com/en/secure-web-hosting" target="_blank"><img src="<?php echo plugins_url('images/rek2.png', __FILE__); ?>" /></a>&nbsp;
                <a href="https://www.siteguarding.com/en/importance-of-website-backup" target="_blank"><img src="<?php echo plugins_url('images/rek3.png', __FILE__); ?>" /></a>
            </div>
            <?php
        }
    ?>

    <div class="ui grid max-box">
    <div id="main" class="thirteen wide column row">
    

    
    <div class="ui top attached tabular menu" style="margin-top:0;">
            <a href="admin.php?page=plgsgtcb_protection&tab=0" class="<?php echo $tab_array[0]; ?> item"><i class="desktop icon"></i> Front-end Protection</a>
            <a href="admin.php?page=plgsgtcb_protection&tab=1" class="<?php echo $tab_array[1]; ?> item"><i class="pie chart icon"></i> Logs & Stats</a>
            <a href="admin.php?page=plgsgtcb_protection&tab=2" class="<?php echo $tab_array[2]; ?> item"><i class="user secret icon"></i> Blocked IP</a>
            <a href="admin.php?page=plgsgtcb_protection&tab=3" class="<?php echo $tab_array[3]; ?> item"><i class="settings icon"></i> Settings & Support</a>
            <a href="admin.php?page=plgsgtcb_extensions_page" class="<?php echo $tab_array[4]; ?> item"><i class="shield alternate icon"></i> Security Extensions</a>
    </div>
    <div class="ui bottom attached segment">
    <?php
    if ($tab_id == 0)
    {
        ?>
        
        <?php
        
        if (!TEXT_SG_Protection::CheckAntivirusInstallation()) 
        {
            $action = 'install-plugin';
            //$slug = 'wp-antivirus-site-protection';
            $slug = 'wp-website-antivirus-protection';
            $install_url = wp_nonce_url(
                add_query_arg(
                    array(
                        'action' => $action,
                        'plugin' => $slug
                    ),
                    admin_url( 'update.php' )
                ),
                $action.'_'.$slug
            );
    
            $message_data = array(
                'type' => 'info',
                'header' => '',
                'message' => '<a href="'.$install_url.'">Antivirus is not installed. Try our antivirus to keep your website secured. Click here to open the details.</a>',
                'button_text' => '',
                'button_url' => '',
                'help_text' => ''
            );
            TEXT_SG_Protection_HTML::PrintIconMessage($message_data);
        }
        ?>
        
        
        <form method="post" action="admin.php?page=plgsgtcb_protection&tab=0">
        
        <?php
            $message_data = array(
                'type' => 'info',
                'header' => '',
                'message' => '<b>Please note:</b> these types of protection will block on the visitor browser\'s level. If you have real content scrapping problems, use professional methods and tools.',
                'button_text' => 'Learn more',
                'button_url' => 'https://www.siteguarding.com/en/scraping-prevention',
                'help_text' => ''
            );
            TEXT_SG_Protection_HTML::PrintIconMessage($message_data);
        ?>
        
        <h3 class="ui header">Basic features</h3>
        
        <script>
        jQuery(document).ready(function(){
            jQuery('.ui.checkbox').checkbox();
            jQuery('select.dropdown').dropdown();
        });
        </script>
                
        <div class="ui form">
            <div class="ui four column grid">
                <div class="row">
                    <div class="four wide column">
                        <div class="inline field">
                            <div class="ui toggle checkbox">
                            <input type="checkbox" <?php if ($params['right_click'] == 1) echo 'checked'; ?> name="right_click" value="1" class="hidden">
                            <label>Block right click and context menu</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="twelve wide column">
                        <div class="ui grid">
                          <div class="ten wide column">
                            <p>Blocks right click and context menu in your browser. Doesn't allow to Copy and Save options in menu.</p>
                          </div>
                          <div class="six wide column"><img src="<?php echo plugins_url('images/help_right_click.png', __FILE__); ?>" /></div>
                        </div>
                    </div>
                </div>
                
                <div class="ui divider"></div>
                
                <div class="row">
                    <div class="four wide column">
                        <div class="inline field">
                            <div class="ui toggle checkbox">
                            <input type="checkbox" <?php if ($params['ctrl_keys'] == 1) echo 'checked'; ?> name="ctrl_keys" value="1" class="hidden">
                            <label>Disable the keys CTRL+A, CTRL+C, CTRL+X, CTRL+S or CTRL+V</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="twelve wide column">
                        <p>Disables keyboard copy controls (CTRL A, C, X). MacOS combinations. Page saving option.</p>
                    </div>
                </div>
                
                <div class="ui divider"></div>
                
                <div class="row">
                    <div class="four wide column">
                        <div class="inline field">
                            <div class="ui toggle checkbox">
                            <input type="checkbox" <?php if ($params['select_text'] == 1) echo 'checked'; ?> name="select_text" value="1" class="hidden">
                            <label>Disable text selection on PC and mobile devices</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="twelve wide column">
                        <p>Visitor will not be able to select the text on your website. Blocks selecting on desktop, notebooks and all mobile devices.</p>
                    </div>
                </div>
                
                <div class="ui divider"></div>
                
                <div class="row">
                    <div class="four wide column">
                        <div class="inline field">
                            <div class="ui toggle checkbox">
                            <input type="checkbox" <?php if ($params['debugger'] == 1) echo 'checked'; ?> name="debugger" value="1" class="hidden">
                            <label>Disable Developers Tools (debugger)</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="twelve wide column">
                        <div class="ui grid">
                          <div class="nine wide column">
                            <p>Blocks developer tools in browser. Doesn't allow to see the sources of your website.</p>
                          </div>
                          <div class="seven wide column"><img src="<?php echo plugins_url('images/help_debugger.png', __FILE__); ?>" /></div>
                        </div>
                    </div>
                </div>
                
                <div class="ui divider"></div>
                
            </div>
        </div>
        
        <h3 class="ui header">Advanced features <?php if (!$isPRO) echo '<div class="ui red horizontal label">'.$left_days.' days left</div>'; ?></h3>
        
        <div class="ui form">
            <div class="ui four column grid">
                <div class="row">
                    <div class="four wide column">
                        <div class="inline field">
                            <div class="ui toggle checkbox<?php echo $class_disabled; ?>">
                            <input type="checkbox" <?php if ($params['collect_stats'] == 1) echo 'checked'; ?> name="collect_stats" value="1" class="hidden">
                            <label>Collect statistics</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="twelve wide column">
                        <p>You can see who wanted to copy your content. IP addresses and country of content scrappers.</p>
                    </div>
                </div>
                
                
                <div class="ui divider"></div>
                
                <div class="row">
                    <div class="four wide column">
                        <div class="inline field">
                            <div class="ui toggle checkbox<?php echo $class_disabled; ?>">
                            <input type="checkbox" <?php if ($params['block_ip'] == 1) echo 'checked'; ?> name="block_ip" value="1" class="hidden">
                            <label>Automatically block IP addresses</label>
                            </div>
                        </div>
                        
                      <div class="field">
                          <label>How long to keep IP blocked</label>
                          <select class="ui fluid search dropdown<?php echo $class_disabled; ?>" name="block_ip_time">
                            <option value="5" <?php if ($params['block_ip_time'] == 1) echo 'selected'; ?>>5 minutes</option>
                            <option value="15" <?php if ($params['block_ip_time'] == 15) echo 'selected'; ?>>15 minutes</option>
                            <option value="30" <?php if ($params['block_ip_time'] == 30) echo 'selected'; ?>>30 minutes</option>
                            <option value="60" <?php if ($params['block_ip_time'] == 60) echo 'selected'; ?>>60 minutes</option>
                            <option value="180" <?php if ($params['block_ip_time'] == 180) echo 'selected'; ?>>3 hours</option>
                            <option value="360" <?php if ($params['block_ip_time'] == 360) echo 'selected'; ?>>6 hours</option>
                            <option value="720" <?php if ($params['block_ip_time'] == 720) echo 'selected'; ?>>12 hours</option>
                            <option value="1440" <?php if ($params['block_ip_time'] == 1440) echo 'selected'; ?>>24 hours</option>
                            <option value="9999" <?php if ($params['block_ip_time'] == 9999) echo 'selected'; ?>>Forever</option>
                          </select>
                      </div>
  
                    </div>
                    
                    <div class="twelve wide column">
                        <p>You can block IP address of content scrapper on fly. They will not be able to open your website again.</p>
                    </div>
                </div>
                
                <div class="ui divider"></div>
                
                <div class="row">
                    <div class="four wide column">
                        <div class="inline field">
                            <div class="ui toggle checkbox<?php echo $class_disabled; ?>">
                            <input type="checkbox" <?php if ($params['hide_page_content'] == 1) echo 'checked'; ?> name="hide_page_content" value="1" class="hidden">
                            <label>Hide page content</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="twelve wide column">
                        <p>When someone tries to copy your content, the page will be destroyed in their browser, scrapper will not be able to see the page.</p>
                    </div>
                </div>
                
                <div class="ui divider"></div>
                
            </div>
        </div>

        <p>&nbsp;</p>

        <input type="submit" name="submit" id="submit" class="ui green button" value="Save & Apply">

        
        
		<?php
		wp_nonce_field( 'name_10EFDDE97A00' );
		?>
		<input type="hidden" name="page" value="plgsgtcb_protection"/>
		<input type="hidden" name="action" value="Save_frontend_protection_params"/>
		</form>

        <?php
    }
    
    
    

	
	
	
    if ($tab_id == 1)
    {
        TEXT_SG_Protection::Delete_old_logs(32);
        
        wp_enqueue_script( 'plgsgtcb_LoadCharts' );
        
        ?>
        <h4 class="ui header">Charts</h4>
        
        <p>You can see how many vistors tried to copy content from your website.</p>
        
       
        <?php
        $pie_array = TEXT_SG_Protection::GeneratePieData(1);
        $line_array = TEXT_SG_Protection::GenerateLineData(1);
        $pie_data = TEXT_SG_Protection::PreparePieData($pie_array);
        
        ?>

		<script type="text/javascript">
        jQuery(function () {
            jQuery('#pie_container_1').<?php echo 'high'.'charts'; ?>({
                credits: false,
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    text: 'Activity for the last 24 hours (by Country)'
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                            style: {
                                color: (<?php echo 'High'.'cha'.'rts'; ?>.theme && <?php echo 'High'.'cha'.'rts'; ?>.theme.contrastTextColor) || 'black'
                            }
                        }
                    }
                },
                series: [{
                    name: 'Total',
                    colorByPoint: true,
                    data: [<?php echo implode(", ", $pie_data); ?>]
                }]
            });
        });




        
        jQuery(function () {
            jQuery('#line_container_1').<?php echo 'high'.'charts'; ?>({
        
            chart: {
                type: 'spline'
            },
            credits: false,
            title: {
                text: 'Activity for the last 24 hours (by visitors)'
            },
            xAxis: {
                <?php
                $date_arr = array();
                $tmp_a = array();
                foreach ($line_array as $date => $visitors)
                {
                    $tmp_a[] = "'".$date."'"; 
                }
                ?>
                categories: [<?php echo implode(", ", $tmp_a); ?>]
            },
            yAxis: {
                title: {
                    text: 'Amount of visitors'
                },
                min: 0
            },
            tooltip: {
                headerFormat: '<b>{series.name}</b><br>',
                pointFormat: '{point.y}'
            },
        
            plotOptions: {
                series: {
                    marker: {
                        enabled: true
                    }
                }
            },
        


            series: [{
                name: "Visitors",
                data: [<?php echo implode(", ", $line_array); ?>]
            }],
        
            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 500
                    },
                    chartOptions: {
                        plotOptions: {
                            series: {
                                marker: {
                                    radius: 2.5
                                }
                            }
                        }
                    }
                }]
            }
        });
    });
        		</script>
        	</head>
        	<body>
        
        <div id="pie_container_1" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>
        <div id="line_container_1"></div>
        
        <hr />

        <?php
        $pie_array = TEXT_SG_Protection::GeneratePieData(7);
        $line_array = TEXT_SG_Protection::GenerateLineData(7);
        $pie_data = TEXT_SG_Protection::PreparePieData($pie_array);
        ?>
		<script type="text/javascript">
        jQuery(function () {
            jQuery('#pie_container_2').<?php echo 'high'.'char'.'ts'; ?>({
                credits: false,
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    text: 'Activity for the last 7 days'
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                            style: {
                                color: (<?php echo 'High'.'cha'.'rts'; ?>.theme && <?php echo 'High'.'cha'.'rts'; ?>.theme.contrastTextColor) || 'black'
                            }
                        }
                    }
                },
                series: [{
                    name: 'Total',
                    colorByPoint: true,
                    data: [<?php echo implode(", ", $pie_data); ?>]
                }]
            });
        });
        
        
        jQuery(function () {
            jQuery('#line_container_2').<?php echo 'high'.'charts'; ?>({
        
            chart: {
                type: 'spline'
            },
            credits: false,
            title: {
                text: 'Activity for the last 7 days (by visitors)'
            },
            xAxis: {
                <?php
                $date_arr = array();
                $tmp_a = array();
                foreach ($line_array as $date => $visitors)
                {
                    $tmp_a[] = "'".$date."'"; 
                }
                ?>
                categories: [<?php echo implode(", ", $tmp_a); ?>]
            },
            yAxis: {
                title: {
                    text: 'Amount of visitors'
                },
                min: 0
            },
            tooltip: {
                headerFormat: '<b>{series.name}</b><br>',
                pointFormat: '{point.y}'
            },
        
            plotOptions: {
                series: {
                    marker: {
                        enabled: true
                    }
                }
            },
        


            series: [{
                name: "Visitors",
                data: [<?php echo implode(", ", $line_array); ?>]
            }],
        
            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 500
                    },
                    chartOptions: {
                        plotOptions: {
                            series: {
                                marker: {
                                    radius: 2.5
                                }
                            }
                        }
                    }
                }]
            }
        });
    });
        		</script>
        	</head>
        	<body>
        
        <div id="pie_container_2" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>
        <div id="line_container_2"></div>
        
        <hr />
        
        <?php
        $pie_array = TEXT_SG_Protection::GeneratePieData(30);
        $line_array = TEXT_SG_Protection::GenerateLineData(30);
        $pie_data = TEXT_SG_Protection::PreparePieData($pie_array);
        ?>
		<script type="text/javascript">
        jQuery(function () {
            jQuery('#pie_container_3').<?php echo 'high'.'chart'.'s'; ?>({
                credits: false,
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    type: 'pie'
                },
                title: {
                    text: 'Activity for the last 30 days'
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                            style: {
                                color: (<?php echo 'High'.'cha'.'rts'; ?>.theme && <?php echo 'High'.'cha'.'rts'; ?>.theme.contrastTextColor) || 'black'
                            }
                        }
                    }
                },
                series: [{
                    name: 'Total',
                    colorByPoint: true,
                    data: [<?php echo implode(", ", $pie_data); ?>]
                }]
            });
        });
        
        
        jQuery(function () {
            jQuery('#line_container_3').<?php echo 'high'.'charts'; ?>({
        
            chart: {
                type: 'spline'
            },
            credits: false,
            title: {
                text: 'Activity for the last 30 days (by visitors)'
            },
            xAxis: {
                <?php
                $date_arr = array();
                $tmp_a = array();
                foreach ($line_array as $date => $visitors)
                {
                    $tmp_a[] = "'".$date."'"; 
                }
                ?>
                categories: [<?php echo implode(", ", $tmp_a); ?>]
            },
            yAxis: {
                title: {
                    text: 'Amount of visitors'
                },
                min: 0
            },
            tooltip: {
                headerFormat: '<b>{series.name}</b><br>',
                pointFormat: '{point.y}'
            },
        
            plotOptions: {
                series: {
                    marker: {
                        enabled: true
                    }
                }
            },
        


            series: [{
                name: "Visitors",
                data: [<?php echo implode(", ", $line_array); ?>]
            }],
        
            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 500
                    },
                    chartOptions: {
                        plotOptions: {
                            series: {
                                marker: {
                                    radius: 2.5
                                }
                            }
                        }
                    }
                }]
            }
        });
    });
        		</script>
        	</head>
        	<body>
        
        <div id="pie_container_3" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>
        <div id="line_container_3"></div>
        
        <hr />
        
        <?php
        $amount_records = 50;
        $latest_records_array = TEXT_SG_Protection::GetLatestRecords($amount_records);
        ?>
        <h4 class="ui header">Latest Logs (latest <?php echo $amount_records; ?> records)</h4>
        <?php
        if (count($latest_records_array) == 0) echo '<p>No records</p>';
        else {
            ?>
            <table class="ui celled table small">
              <thead>
                <tr><th>Date</th>
                <th>Country</th>
                <th>IP address</th>
                <th>URL</th>
              </tr></thead>
              <tbody>
                <?php
                foreach ($latest_records_array as $v) {
                ?>
                <tr>
                  <td><?php echo date("Y-m-d H:i:s", $v->time); ?></td>
                  <td><?php echo TEXT_SG_Protection::$country_list[ $v->country_code ].' ['.$v->country_code.']'; ?></td>
                  <td><?php echo $v->ip; ?></td>
                  <td class="tbl_urlrow"><a target="_blank" href="<?php echo $v->url; ?>"><?php echo $v->url; ?></span></td>
                </tr>
                <?php
                }
                ?>
              </tbody>
            </table>
            
            <?php
        }
    }
    

    






    if ($tab_id == 2)
    {
        TEXT_SG_Protection::Delete_expired_blocked_IP();
        
        if (isset($_GET['unblock']))
        {
            $unblock_ip = trim($_GET['unblock']);
            TEXT_SG_Protection::Unblocked_IP($unblock_ip);
            $message_data = array(
                'type' => 'ok',
                'header' => 'Success',
                'message' => 'IP '.$unblock_ip.' unblocked',
                'button_text' => '',
                'button_url' => '',
                'help_text' => ''
            );
            TEXT_SG_Protection_HTML::PrintIconMessage($message_data);
        }
        
        ?>
        <h4 class="ui header">Blocked IP addresses</h4>
        
        <p>You can manage all blocked IP addresses (if autoblocking feature is enabled)</p>
        <?php
        if ($params['block_ip'] == 1) echo '<div class="ui green horizontal label">Autoblocking is enabled</div>';
        else echo '<div class="ui red horizontal label">Autoblocking is disabled</div>';
        ?>
        
       
        
        <?php
        $latest_records_array = TEXT_SG_Protection::GetBlocked_IP_Records();
        ?>
        <h4 class="ui header">Latest active blocked IP addresses</h4>
        <?php
        if (count($latest_records_array) == 0) echo '<p>No records</p>';
        else {
            ?>
            <table class="ui celled table small">
              <thead>
                <tr><th>IP address</th>
                <th>Country</th>
                <th>Expires</th>
                <th></th>
              </tr></thead>
              <tbody>
                <?php
                foreach ($latest_records_array as $row) 
                {
                    $ip = trim($row['ip']);
                    $country_code = TEXT_SG_Protection::GetCountryCode($ip);
                    
                    $time_left = round(($row['left'] - time()) / 60);
                    
                    $link = '/wp-admin/admin.php?page=plgsgtcb_protection&tab=2&unblock='.$ip;
                ?>
                <tr>
                  <td><?php echo $ip; ?></td>
                  <td><?php echo TEXT_SG_Protection::$country_list[ $country_code ].' ['.$country_code.']'; ?></td>
                  <td><?php echo $time_left; ?> minutes left</td>
                  <td><a href="<?php echo $link; ?>"><i class="trash alternate icon"></i> Unblock</span></td>
                </tr>
                <?php
                }
                ?>
              </tbody>
            </table>
            
            <?php
        }
    }







    
    
    if ($tab_id == 3)
    {  
        $isPRO = TEXT_SG_Protection::CheckIfPRO();
        if (!$isPRO) $params['protection_by'] = 1;
        
        if ($isPRO)
        {
            $box_text = 'You have <b>PRO version</b>';
        }
        else {
            $box_text = '<span style="color:#9f3a38">You have <b>Free version</b>. Please note free version has some limits. Please <a href="'.TEXT_SG_Protection::$upgrade_buy_link.'?domain='.urlencode( get_site_url() ).'" target="_blank">Upgrade</a></span><br><i class="thumbs up icon"></i>Try our <a href="https://wordpress.org/plugins/wp-website-antivirus-protection/" target="_blank">WordPress Antivirus scanner</a> PRO version and get your registration code for this plugin for free.';
        }
        ?>
        <h4 class="ui header">Settings</h4>
        
        <div class="ui ignored info message"><center><?php echo $box_text; ?></center></div>
        
        <form method="post" class="ui form" action="admin.php?page=plgsgtcb_protection&tab=4">
        
        <div class="ui fluid form">
        
            <div class="ui input ui-form-row">
              <input class="ui input" size="40" placeholder="Enter your registration code" type="text" name="registration_code" value="<?php if (isset($params['registration_code'])) echo $params['registration_code']; ?>">
            </div><br>
            
          <div class="ui checkbox ui-form-row">
            <input type="checkbox" name="protection_by" value="1" <?php if (!$isPRO) echo 'disabled="disabled"'; ?> <?php if ($params['protection_by'] == 1) echo 'checked="checked"'; ?>>
            <label>Enable 'Protected by' sign</label>
          </div>
        </div>
                
        <input type="submit" name="submit" id="submit" class="ui green button" value="Save Settings">
        <p>&nbsp;</p>
		<?php
		wp_nonce_field( 'name_xZU32INTzZM1GFNz' );
		?>
		<input type="hidden" name="page" value="plgsgtcb_protection"/>
		<input type="hidden" name="action" value="Save_Settings"/>
		</form>
		
        <hr />


        <h4 class="ui header">Support</h4>
        
		<p>
		For more information and details about Text Copy Blocker please <a target="_blank" href="https://www.siteguarding.com/en/wordpress-geo-website-protection">click here</a>.<br /><br />
		<a href="http://www.siteguarding.com/livechat/index.html" target="_blank">
			<img src="<?php echo plugins_url('images/livechat.png', __FILE__); ?>"/>
		</a><br />
		For any questions and support please use LiveChat or this <a href="https://www.siteguarding.com/en/contacts" rel="nofollow" target="_blank" title="SiteGuarding.com - Website Security. Professional security services against hacker activity. Daily website file scanning and file changes monitoring. Malware detecting and removal.">contact form</a>.<br>
		<br>
		<a href="https://www.siteguarding.com/" target="_blank">SiteGuarding.com</a> - Website Security. Professional security services against hacker activity.<br />
		</p>
		<?php
        
        
    }
    


    ?>
    
    </div>
           		        
				
			
				<div class="tiny ui modal preview_show">
				  <div class="content">
						 <center><h4 class="ui header" data-inverted="" data-tooltip="Preview only. It will not save the settings."><i class="tv icon"></i>GEO Block Page Preview</h4></center><hr>
						<div id="geo_preview">	
						</div>
				  </div>
				  <div class="actions">
					<button class="medium ui cancel button">Close Preview</button>
				  </div>
				</div>
        
    </div>
	
	
	
    </div>	

    		<?php

    }
    

    
	
	
    public static function CountryList_checkboxes($selected_array = array())
    {
        $selected = array();
        if (count($selected_array))
        {
            foreach ($selected_array as $v)
            {
                $selected[$v] = $v;
            }
            
        }
        $a = '<div class="ui five column grid country_list">'."\n";

        foreach (TEXT_SG_Protection::$country_list as $country_code => $country_name)
        {
            if (isset($selected[$country_code])) $checked = 'checked="checked"';
            else $checked = '';
            $a .= '<div class="column"><label><input class="country_'.$country_code.' '.TEXT_SG_Protection::$country_type_list[$country_code].'" '.$checked.' type="checkbox" name="country_list[]" value="'.$country_code.'">'.$country_name.'</label></div>'."\n";
        }

        $a .= '</div>';
        
        return $a;
    }
    
	public static function blockPagePreview() {
		$ajaxData = isset($_POST['data']) ? trim($_POST['data']) : false;

		$blockpage_json = array();
        $blockpage_json['logo_url'] = '/wp-content/plugins/wp-geo-website-protection/images/logo_siteguarding.svg';
        $blockpage_json['text_1'] = 'Access is not allowed from your IP or your country.';
        $blockpage_json['text_2'] = 'If you think it\'s a mistake, please contactwith the webmaster of the website';
        $blockpage_json['hide_debug'] = 0;
        $blockpage_json['hide_ipinfo'] = 0;

        if ($ajaxData && TEXT_SG_Protection::CheckIfPRO())  {
					// Replace default settings with customized
			$ajaxData = (array)json_decode(stripslashes($ajaxData), true);
			
			if (isset($ajaxData['custom_status']) && intval($ajaxData['custom_status']) == 1) {
				if ($ajaxData['logo_url'] != '') $blockpage_json['logo_url'] = $ajaxData['logo_url'];
				if ($ajaxData['text_1'] != '') $blockpage_json['text_1'] = $ajaxData['text_1'];
				if ($ajaxData['text_2'] != '') $blockpage_json['text_2'] = $ajaxData['text_2'];
				
				$blockpage_json['hide_debug'] = intval($ajaxData['hide_debug']);
				$blockpage_json['hide_ipinfo'] = intval($ajaxData['hide_ipinfo']);
			}

		} 

		$myIP = TEXT_SG_Protection::GetMyIP();
		$myCountryCode = TEXT_SG_Protection::GetCountryCode($myIP);
        
		$logo_url = '';
        if ($blockpage_json['logo_url'] != '') $logo_url = '<p><img style="max-width:300px;max-height:200px" src="'.$blockpage_json['logo_url'].'" id="logo"></p>';

        $debug_info = '';
        if ($blockpage_json['hide_debug'] == 0) $debug_info = '<p>If you the owner of the website. Please enable DEBUG mode in your WordPress (use FTP) to disable GEO Protection.<br>
            Read more about it on <a target="_blank" href="https://codex.wordpress.org/Debugging_in_WordPress">Debugging in WordPress</a> or contact with <a target="_blank" href="https://www.siteguarding.com/en/contacts">SiteGuarding.com support</a></p>';        

        $ipinfo = '';
        if ($blockpage_json['hide_ipinfo'] == 0) {
			$ipinfo = '<h4>Session details:</h4><p>IP: '.$myIP.'</p>';
			if ($myCountryCode != '') $ipinfo .= '<p>Country: '.TEXT_SG_Protection_2::$country_list[$myCountryCode].'</p>';
		}
		
		?>
		        <div style="margin:30px auto; max-width: 400px; max-height: 450px;text-align: center;">
			<?php echo $logo_url; ?>
            
            <h3 style="color: #de0027; text-align: center;"><?php echo $blockpage_json['text_1']; ?></h3>
            <p><?php echo $blockpage_json['text_2']; ?></p>
            
            
            <?php echo $debug_info; ?>

            <?php echo $ipinfo; ?>
            <p>&nbsp;</p>
            

            <p style="font-size: 70%;">Powered by <a target="_blank" href="https://www.siteguarding.com/">SiteGuarding.com</a></p>


        </div>
		<?php
	}
    
    
    public static function PrintIconMessage($data)
    {
        $rand_id = "id_".rand(1,10000).'_'.rand(1,10000);
        if ($data['type'] == '' || $data['type'] == 'alert') {$type_message = 'negative'; $icon = 'warning sign';}
        if ($data['type'] == 'ok') {$type_message = 'green'; $icon = 'checkmark box';}
        if ($data['type'] == 'info') {$type_message = 'yellow'; $icon = 'info';}
        ?>
        <div class="ui icon <?php echo $type_message; ?> message">
            <i class="<?php echo $icon; ?> icon"></i>
            <div class="msg_block_row">
                <?php
                if ($data['button_text'] != '' || $data['help_text'] != '') {
                ?>
                <div class="msg_block_txt">
                    <?php
                    if ($data['header'] != '') {
                    ?>
                    <div class="header"><?php echo $data['header']; ?></div>
                    <?php
                    }
                    ?>
                    <?php
                    if ($data['message'] != '') {
                    ?>
                    <p><?php echo $data['message']; ?></p>
                    <?php
                    }
                    ?>
                </div>
                <div class="msg_block_btn">
                    <?php
                    if ($data['help_text'] != '') {
                    ?>
                    <a class="link_info" href="javascript:;" onclick="InfoBlock('<?php echo $rand_id; ?>');"><i class="help circle icon"></i></a>
                    <?php
                    }
                    ?>
                    <?php
                    if ($data['button_text'] != '') {
                        if (!isset($data['button_url_target']) || $data['button_url_target'] == true) $new_window = 'target="_blank"';
                        else $new_window = '';
                    ?>
                    <a class="mini ui green button" <?php echo $new_window; ?> href="<?php echo $data['button_url']; ?>"><?php echo $data['button_text']; ?></a>
                    <?php
                    }
                    ?>
                </div>
                    <?php
                    if ($data['help_text'] != '') {
                    ?>
                        <div style="clear: both;"></div>
                        <div id="<?php echo $rand_id; ?>" style="display: none;">
                            <div class="ui divider"></div>
                            <p><?php echo $data['help_text']; ?></p>
                        </div>
                    <?php
                    }
                    ?>
                <?php
                } else {
                ?>
                    <?php
                    if ($data['header'] != '') {
                    ?>
                    <div class="header"><?php echo $data['header']; ?></div>
                    <?php
                    }
                    ?>
                    <?php
                    if ($data['message'] != '') {
                    ?>
                    <p><?php echo $data['message']; ?></p>
                    <?php
                    }
                    ?>
                <?php
                }
                ?>
            </div> 
        </div>
        <?php
    }
    
    
    public static function BlockPage($myIP, $myCountryCode = '')
    {
        ?><html><head>
        <link rel="stylesheet" type="text/css" href="<?php echo plugins_url('images/logo_siteguarding.svg', __FILE__); ?>">
        </head>
        <body>
        <div style="margin:100px auto; max-width: 500px;text-align: center;">
            <p><img src="<?php echo plugins_url('images/logo_siteguarding.svg', __FILE__); ?>"/></p>
            <p>&nbsp;</p>
            <h3 style="color: #de0027; text-align: center;">Access is not allowed from your IP or your country.</h3>
            <p>If you think it's a mistake, please contact with the websmater of the website.</p>
            <p>If you the owner of the website. Please enable DEBUG mode in your WordPress (use FTP) to disable GEO Protection.<br>
            Read more about it on <a target="_blank" href="https://codex.wordpress.org/Debugging_in_WordPress">Debugging in WordPress</a> or contact with <a target="_blank" href="https://www.siteguarding.com/en/contacts">SiteGuarding.com support</a></p>
            <h4>Session details:</h4>
            <p>IP: <?php echo $myIP; ?></p>
            <?php
            if ($myCountryCode != '') echo '<p>Country: '.TEXT_SG_Protection::$country_list[$myCountryCode].'</p>';
            ?>
            <p>&nbsp;</p>
            <p>&nbsp;</p>

            <p style="font-size: 70%;">Powered by <a target="_blank" href="https://www.siteguarding.com/">SiteGuarding.com</a></p>


        </div>
        </body></html>
        <?php

        die();
    }
    
}


class TEXT_SG_Protection
{
    public static $upgrade_buy_link = 'https://www.siteguarding.com/en/buy-extention/wordpress-text-copy-blocker';
    
    public static $country_list = array(
        "AF" => "Afghanistan",   // Afghanistan
        "AL" => "Albania",   // Albania
        "DZ" => "Algeria",   // Algeria
        "AS" => "American Samoa",   // American Samoa
        "AD" => "Andorra",   // Andorra 
        "AO" => "Angola",   // Angola
        "AI" => "Anguilla",   // Anguilla
        "AQ" => "Antarctica",   // Antarctica
        "AG" => "Antigua and Barbuda",   // Antigua and Barbuda
        "AR" => "Argentina",   // Argentina
        "AM" => "Armenia",   // Armenia
        "AW" => "Aruba",   // Aruba 
        "AU" => "Australia",   // Australia 
        "AT" => "Austria",   // Austria
        "AZ" => "Azerbaijan",   // Azerbaijan
        "BS" => "Bahamas",   // Bahamas
        "BH" => "Bahrain",   // Bahrain 
        "BD" => "Bangladesh",   // Bangladesh
        "BB" => "Barbados",   // Barbados 
        "BY" => "Belarus",   // Belarus 
        "BE" => "Belgium",   // Belgium
        "BZ" => "Belize",   // Belize
        "BJ" => "Benin",   // Benin
        "BM" => "Bermuda",   // Bermuda
        "BT" => "Bhutan",   // Bhutan
        "BO" => "Bolivia",   // Bolivia
        "BA" => "Bosnia and Herzegovina",   // Bosnia and Herzegovina
        "BW" => "Botswana",   // Botswana
        "BV" => "Bouvet Island",   // Bouvet Island
        "BR" => "Brazil",   // Brazil
        "IO" => "British Indian Ocean Territory",   // British Indian Ocean Territory
        "VG" => "British Virgin Islands",   // British Virgin Islands,
        "BN" => "Brunei Darussalam",   // Brunei Darussalam
        "BG" => "Bulgaria",   // Bulgaria
        "BF" => "Burkina Faso",   // Burkina Faso
        "BI" => "Burundi",   // Burundi
        "KH" => "Cambodia",   // Cambodia 
        "CM" => "Cameroon",   // Cameroon
        "CA" => "Canada",   // Canada 
        "CV" => "Cape Verde",   // Cape Verde
        "KY" => "Cayman Islands",   // Cayman Islands
        "CF" => "Central African Republic",   // Central African Republic
        "TD" => "Chad",   // Chad
        "CL" => "Chile",   // Chile
        "CN" => "China",   // China
        "CX" => "Christmas Island",   // Christmas Island
        "CC" => "Cocos (Keeling Islands)",   // Cocos (Keeling Islands)
        "CO" => "Colombia",   // Colombia
        "KM" => "Comoros",   // Comoros
        "CG" => "Congo",   // Congo 
        "CK" => "Cook Islands",   // Cook Islands
        "CR" => "Costa Rica",   // Costa Rica 
        "HR" => "Croatia (Hrvatska)",   // Croatia (Hrvatska
        "CY" => "Cyprus",   // Cyprus
        "CZ" => "Czech Republic",   // Czech Republic
        "CG" => "Democratic Republic of Congo",   // Democratic Republic of Congo,
        "DK" => "Denmark",   // Denmark
        "DJ" => "Djibouti",   // Djibouti
        "DM" => "Dominica",   // Dominica
        "DO" => "Dominican Republic",   // Dominican Republic
        "TP" => "East Timor",   // East Timor
        "EC" => "Ecuador",   // Ecuador
        "EG" => "Egypt",   // Egypt 
        "SV" => "El Salvador",   // El Salvador 
        "GQ" => "Equatorial Guinea",   // Equatorial Guinea
        "ER" => "Eritrea",   // Eritrea 
        "EE" => "Estonia",   // Estonia 
        "ET" => "Ethiopia",   // Ethiopia
        "FK" => "Falkland Islands (Malvinas)",   // Falkland Islands (Malvinas)
        "FO" => "Faroe Islands",   // Faroe Islands 
        "FM" => "Federated States of Micronesia",   // Federated States of Micronesia,
        "FJ" => "Fiji",   // Fiji
        "FI" => "Finland",   // Finland
        "FR" => "France",   // France
        "GF" => "French Guiana",   // French Guiana
        "PF" => "French Polynesia",   // French Polynesia
        "TF" => "French Southern Territories",   // French Southern Territories
        "GA" => "Gabon",   // Gabon
        "GM" => "Gambia",   // Gambia
        "GE" => "Georgia",   // Georgia
        "DE" => "Germany",   // Germany
        "GH" => "Ghana",   // Ghana
        "GI" => "Gibraltar",   // Gibraltar
        "GR" => "Greece",   // Greece
        "GL" => "Greenland",   // Greenland
        "GD" => "Grenada",   // Grenada 
        "GP" => "Guadeloupe",   // Guadeloupe
        "GU" => "Guam",   // Guam 
        "GT" => "Guatemala",   // Guatemala
        "GN" => "Guinea",   // Guinea
        "GW" => "Guinea-Bissau",   // Guinea-Bissau
        "GY" => "Guyana",   // Guyana
        "HT" => "Haiti",   // Haiti
        "HM" => "Heard and McDonald Islands",   // Heard and McDonald Islands
        "HN" => "Honduras",   // Honduras
        "HK" => "Hong Kong",   // Hong Kong
        "HU" => "Hungary",   // Hungary
        "IS" => "Iceland",   // Iceland
        "IN" => "India",   // India
        "ID" => "Indonesia",   // Indonesia
        "IR" => "Iran",   // Iran
        "IQ" => "Iraq",   // Iraq
        "IE" => "Ireland",   // Ireland
        "IL" => "Israel",   // Israel
        "IT" => "Italy",   // Italy
        "CI" => "Ivory Coast",   // Ivory Coast,
        "JM" => "Jamaica",   // Jamaica
        "JP" => "Japan",   // Japan 
        "JO" => "Jordan",   // Jordan 
        "KZ" => "Kazakhstan",   // Kazakhstan
        "KE" => "Kenya",   // Kenya 
        "KI" => "Kiribati",   // Kiribati 
        "KW" => "Kuwait",   // Kuwait
        "KG" => "Kuwait",   // Kyrgyzstan
        "LA" => "Laos",   // Laos
        "LV" => "Latvia",   // Latvia
        "LB" => "Lebanon",   // Lebanon
        "LS" => "Lesotho",   // Lesotho
        "LR" => "Liberia",   // Liberia 
        "LY" => "Libya",   // Libya
        "LI" => "Liechtenstein",   // Liechtenstein
        "LT" => "Lithuania",   // Lithuania
        "LU" => "Luxembourg",   // Luxembourg 
        "MO" => "Macau",   // Macau
        "MK" => "Macedonia",   // Macedonia
        "MG" => "Madagascar",   // Madagascar
        "MW" => "Malawi",   // Malawi
        "MY" => "Malaysia",   // Malaysia
        "MV" => "Maldives",   // Maldives
        "ML" => "Mali",   // Mali
        "MT" => "Malta",   // Malta
        "MH" => "Marshall Islands",   // Marshall Islands
        "MQ" => "Martinique",   // Martinique
        "MR" => "Mauritania",   // Mauritania
        "MU" => "Mauritius",   // Mauritius
        "YT" => "Mayotte",   // Mayotte
        "MX" => "Mexico",   // Mexico
        "MD" => "Moldova",   // Moldova
        "MC" => "Monaco",   // Monaco
        "MN" => "Mongolia",   // Mongolia
        "MS" => "Montserrat",   // Montserrat
        "MA" => "Morocco",   // Morocco
        "MZ" => "Mozambique",   // Mozambique
        "MM" => "Myanmar",   // Myanmar
        "NA" => "Namibia",   // Namibia
        "NR" => "Nauru",   // Nauru
        "NP" => "Nepal",   // Nepal
        "NL" => "Netherlands",   // Netherlands
        "AN" => "Netherlands Antilles",   // Netherlands Antilles
        "NC" => "New Caledonia",   // New Caledonia
        "NZ" => "New Zealand",   // New Zealand
        "NI" => "Nicaragua",   // Nicaragua
        "NE" => "Nicaragua",   // Niger
        "NG" => "Nigeria",   // Nigeria
        "NU" => "Niue",   // Niue
        "NF" => "Norfolk Island",   // Norfolk Island
        "KP" => "Korea (North)",   // Korea (North)
        "MP" => "Northern Mariana Islands",   // Northern Mariana Islands
        "NO" => "Norway",   // Norway
        "OM" => "Oman",   // Oman
        "PK" => "Pakistan",   // Pakistan
        "PW" => "Palau",   // Palau
        "PA" => "Panama",   // Panama
        "PG" => "Papua New Guinea",   // Papua New Guinea
        "PY" => "Paraguay",   // Paraguay
        "PE" => "Peru",   // Peru
        "PH" => "Philippines",   // Philippines
        "PN" => "Pitcairn",   // Pitcairn
        "PL" => "Poland",   // Poland
        "PT" => "Portugal",   // Portugal
        "PR" => "Puerto Rico",   // Puerto Rico
        "QA" => "Qatar",   // Qatar
        "RE" => "Reunion",   // Reunion
        "RO" => "Romania",   // Romania
        "RU" => "Russian Federation",   // Russian Federation
        "RW" => "Rwanda",   // Rwanda
        "SH" => "Saint Helena and Dependencies",   // Saint Helena and Dependencies,
        "KN" => "Saint Kitts and Nevis",   // Saint Kitts and Nevis
        "LC" => "Saint Lucia",   // Saint Lucia
        "VC" => "Saint Vincent and The Grenadines",   // Saint Vincent and The Grenadines
        "VC" => "Saint Vincent and the Grenadines",   // Saint Vincent and the Grenadines,
        "WS" => "Samoa",   // Samoa
        "SM" => "San Marino",   // San Marino
        "ST" => "Sao Tome and Principe",   // Sao Tome and Principe 
        "SA" => "Saudi Arabia",   // Saudi Arabia
        "SN" => "Senegal",   // Senegal
		"RS" => "Serbia",   // Serbia
        "SC" => "Seychelles",   // Seychelles
        "SL" => "Sierra Leone",   // Sierra Leone
        "SG" => "Singapore",   // Singapore
        "SK" => "Slovak Republic",   // Slovak Republic
        "SI" => "Slovenia",   // Slovenia
        "SB" => "Solomon Islands",   // Solomon Islands
        "SO" => "Somalia",   // Somalia
        "ZA" => "South Africa",   // South Africa
        "GS" => "S. Georgia and S. Sandwich Isls.",   // S. Georgia and S. Sandwich Isls.
        "KR" => "South Korea",   // South Korea,
        "ES" => "Spain",   // Spain
        "LK" => "Sri Lanka",   // Sri Lanka
        "SR" => "Suriname",   // Suriname
        "SJ" => "Svalbard and Jan Mayen Islands",   // Svalbard and Jan Mayen Islands
        "SZ" => "Swaziland",   // Swaziland
        "SE" => "Sweden",   // Sweden
        "CH" => "Switzerland",   // Switzerland
        "SY" => "Syria",   // Syria
        "TW" => "Taiwan",   // Taiwan
        "TJ" => "Tajikistan",   // Tajikistan
        "TZ" => "Tanzania",   // Tanzania
        "TH" => "Thailand",   // Thailand
        "TG" => "Togo",   // Togo
        "TK" => "Tokelau",   // Tokelau
        "TO" => "Tonga",   // Tonga
        "TT" => "Trinidad and Tobago",   // Trinidad and Tobago
        "TN" => "Tunisia",   // Tunisia
        "TR" => "Turkey",   // Turkey
        "TM" => "Turkmenistan",   // Turkmenistan
        "TC" => "Turks and Caicos Islands",   // Turks and Caicos Islands
        "TV" => "Tuvalu",   // Tuvalu
        "UG" => "Uganda",   // Uganda
        "UA" => "Ukraine",   // Ukraine
        "AE" => "United Arab Emirates",   // United Arab Emirates
        "UK" => "United Kingdom",   // United Kingdom
        "GB" => "United Kingdom",   // United Kingdom
        "US" => "United States",   // United States
        "UM" => "US Minor Outlying Islands",   // US Minor Outlying Islands
        "UY" => "Uruguay",   // Uruguay
        "VI" => "US Virgin Islands",   // US Virgin Islands,
        "UZ" => "Uzbekistan",   // Uzbekistan
        "VU" => "Vanuatu",   // Vanuatu
        "VA" => "Vatican City State (Holy See)",   // Vatican City State (Holy See)
        "VE" => "Venezuela",   // Venezuela
        "VN" => "Viet Nam",   // Viet Nam
        "WF" => "Wallis and Futuna Islands",   // Wallis and Futuna Islands
        "EH" => "Western Sahara",   // Western Sahara
        "YE" => "Yemen",   // Yemen
        "ZM" => "Zambia",   // Zambia
        "ZW" => "Zimbabwe",   // Zimbabwe
        "CU" => "Cuba",   // Cuba,
        "IR" => "Iran",   // Iran,
    );




    public static function CreateSettingsFile()
    {
        $params = self::Get_Params();
        
        $line = '<?php $blocker_sg_settings = "'.addslashes(json_encode($params)).'"; ?>';
        
        $fp = fopen(dirname(__FILE__).'/settings.php', 'w');
        fwrite($fp, $line);
        fclose($fp);
    }


    /*
	public static function CheckWPConfig_file()
	{
	    if (!file_exists(dirname(__FILE__).'/settings.php')) self::CreateSettingsFile();
        
	    if (!defined('DIRSEP'))
        {
    	    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') define('DIRSEP', '\\');
    		else define('DIRSEP', '/');
        }
        
		if (!defined('ABSPATH') || strlen(ABSPATH) < 8) 
		{
			$scan_path = dirname(__FILE__);
			$scan_path = str_replace(DIRSEP.'wp-content'.DIRSEP.'plugins'.DIRSEP.'wp-geo-website-protection', DIRSEP, $scan_path);
    		//echo TEST;
		}
        else $scan_path = ABSPATH;
        
        $filename = $scan_path.DIRSEP.'wp-config.php';
        if (!is_file($filename)) $filename = dirname($scan_path).DIRSEP.'wp-config.php';
        $handle = fopen($filename, "r");
        if ($handle === false) return false;
        $contents = fread($handle, filesize($filename));
        if ($contents === false) return false;
        fclose($handle);
        
        if (stripos($contents, 'CC818B56769E-START') === false)     // Not found
        {
            self::PatchWPConfig_file();
        }
    }
    */
    
	public static function PatchWPConfig_file($action = true)   // true - insert, false - remove
	{
	    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') define('DIRSEP', '\\');
		else define('DIRSEP', '/');
        
		$file = dirname(__FILE__).DIRSEP."ip_blocker.php";

        $integration_code = '<?php /* Siteguarding Block CC818B56769E-START */ if (file_exists("'.$file.'"))include_once("'.$file.'");/* Siteguarding Block CC818B56769E-END */?>';
        
        // Insert code
		if (!defined('ABSPATH') || strlen(ABSPATH) < 8) 
		{
			$scan_path = dirname(__FILE__);
			$scan_path = str_replace(DIRSEP.'wp-content'.DIRSEP.'plugins'.DIRSEP.'wp-geo-website-protection', DIRSEP, $scan_path);
    		//echo TEST;
		}
        else $scan_path = ABSPATH;
        
        $filename = $scan_path.DIRSEP.'wp-config.php';
        if (!is_file($filename)) $filename = dirname($scan_path).DIRSEP.'wp-config.php';
        $handle = fopen($filename, "r");
        if ($handle === false) return false;
        $contents = fread($handle, filesize($filename));
        if ($contents === false) return false;
        fclose($handle);
        
        $pos_code = stripos($contents, 'CC818B56769E');
        
        if ($action === false)
        {
            // Remove block
            $contents = str_replace($integration_code, "", $contents);
        }
        else {
            // Insert block
            if ( $pos_code !== false/* && $pos_code == 0*/)
            {
                // Skip double code injection
                return true;
            }
            else {
                // Insert
                $contents = $integration_code.$contents;
            }
        }
        
        $handle = fopen($filename, 'w');
        if ($handle === false) 
        {
            // 2nd try , change file permssion to 666
            $status = chmod($filename, 0666);
            if ($status === false) return false;
            
            $handle = fopen($filename, 'w');
            if ($handle === false) return false;
        }
        
        $status = fwrite($handle, $contents);
        if ($status === false) return false;
        fclose($handle);

        
        return true;
	}
    
    
    public static function GetBlocked_IP_Records()
    {
        self::Delete_expired_blocked_IP();
        
        $file = dirname(__FILE__)."/ip_blocked.txt";
        
        $fp = fopen($file, "r");
        $contents = fread($fp, filesize($file));
        fclose($fp);
        
        $a = array();
        
        $contents = explode("\n", $contents);
        
        if (count($contents))
        {
            foreach ($contents as $k => $row)
            {
                $row = trim($row);
                if ($row == '') continue;
                
                $row = explode("|", $row);
                $a[] = array('ip' => $row[1], 'left' => $row[2]);
            }
        }
        
        return $a;
    }



    public static function BlockIPaddress($ip)
    {
        self::Delete_expired_blocked_IP();
        
        $file = dirname(__FILE__)."/ip_blocked.txt";
        
        $fp = fopen($file, "r");
        $contents = fread($fp, filesize($file));
        fclose($fp);
        
        if (strpos($contents, '|'.$ip.'|') !== false) return;   // IP already in blocked
        
        $params = self::Get_Params( array('block_ip_time') );
        $block_ip_time = intval($params['block_ip_time']);
        if ($block_ip_time == 0) $block_ip_time = 15;
        
        $new_time = time() + $block_ip_time * 60;
        
        if ($block_ip_time == 9999) $new_time = 0;
        
        $line = '|'.$ip.'|'.$new_time."\n";
        
        $fp = fopen($file, 'a');
        fwrite($fp, $line);
        fclose($fp);
    }
    
    
    public static function Unblocked_IP($unblock_ip)
    {
        $file = dirname(__FILE__)."/ip_blocked.txt";
        
        $fp = fopen($file, "r");
        $contents = fread($fp, filesize($file));
        fclose($fp);
        
        $contents = explode("\n", $contents);
        
        if (count($contents))
        {
            $save_flag = false;
            $time = time();
            foreach ($contents as $k => $row)
            {
                $row = explode("|", $row);
                if (trim($row[1]) == $unblock_ip)
                {
                    unset($contents[$k]);
                    $save_flag = true;
                }
            }
            
            if ($save_flag)
            {
                $contents = implode("\n", $contents)."\n";
                
                $fp = fopen($file, 'w');
                fwrite($fp, $contents);
                fclose($fp);
            }
        }
    }
    
    
    public static function Delete_expired_blocked_IP()
    {
        $file = dirname(__FILE__)."/ip_blocked.txt";
        
        $fp = fopen($file, "r");
        $contents = fread($fp, filesize($file));
        fclose($fp);
        
        $contents = explode("\n", $contents);
        
        if (count($contents))
        {
            $save_flag = false;
            $time = time();
            foreach ($contents as $k => $row)
            {
                $row = explode("|", $row);
                if (intval($row[2]) < $time) 
                {
                    unset($contents[$k]);
                    $save_flag = true;
                }
            }
            
            if ($save_flag)
            {
                $contents = implode("\n", $contents)."\n";
                
                $fp = fopen($file, 'w');
                fwrite($fp, $contents);
                fclose($fp);
            }
        }
    }
    
    
    
    public static function Get_Params($vars = array())
    {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'plgsgtcb_config';
        
        $ppbv_table = $wpdb->get_results("SHOW TABLES LIKE '".$table_name."'" , ARRAY_N);
        if(!isset($ppbv_table[0])) return false;
        
        if (count($vars) == 0)
        {
            $rows = $wpdb->get_results( 
            	"
            	SELECT *
            	FROM ".$table_name."
            	"
            );
        }
        else {
            foreach ($vars as $k => $v) $vars[$k] = "'".$v."'";
            
            $rows = $wpdb->get_results( 
            	"
            	SELECT * 
            	FROM ".$table_name."
                WHERE var_name IN (".implode(',',$vars).")
            	"
            );
        }
        
        $a = array();
        if (count($rows))
        {
            foreach ( $rows as $row ) 
            {
            	$a[trim($row->var_name)] = trim($row->var_value);
            }
        }
    
        return $a;
    }
    
    
    public static function Set_Params($data = array())
    {
		global $wpdb;
		$table_name = $wpdb->prefix . 'plgsgtcb_config';
    
        if (count($data) == 0) return;   
        
        foreach ($data as $k => $v)
        {
            $tmp = $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM ' . $table_name . ' WHERE var_name = %s LIMIT 1;', $k ) );
            
            if ($tmp == 0)
            {
                // Insert    
                $wpdb->insert( $table_name, array( 'var_name' => $k, 'var_value' => $v ) ); 
            }
            else {
                // Update
                $data = array('var_value'=>$v);
                $where = array('var_name' => $k);
                $wpdb->update( $table_name, $data, $where );
            }
        } 
    }
    
    public static function GetMyIP()
    {
		$ip_address = $_SERVER["REMOTE_ADDR"];
		if (isset($_SERVER["HTTP_X_REAL_IP"])) $ip_address = $_SERVER["HTTP_X_REAL_IP"];
		if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) $ip_address = $_SERVER["HTTP_CF_CONNECTING_IP"];
        return $ip_address ;
    }
    
    public static function GetCountryCode($ip)
    {
        if (isset($_COOKIE["GEO_country_code"]) && isset($_COOKIE["GEO_country_code_hash"]))
        {
            $cookie_GEO_country_code = trim($_COOKIE["GEO_country_code"]);
            $cookie_GEO_country_code_hash = trim($_COOKIE["GEO_country_code_hash"]);
            
            $hash = md5($ip.'-'.$cookie_GEO_country_code);
            if ($cookie_GEO_country_code_hash == $hash) return $cookie_GEO_country_code;
        }
        
        if (!class_exists('sg_Geo_IP2Country'))
        {
            include_once(dirname(__FILE__).DIRSEP.'geo.php');
        }
        
        $geo = new sg_Geo_IP2Country;
        $country_code = $geo->getCountryByIP($ip); 
        
        if ($country_code != '')
        {
            // Set cookie
            $hash = md5($ip.'-'.$country_code);
            setcookie("GEO_country_code", $country_code, time()+3600*24);
            setcookie("GEO_country_code_hash", $hash, time()+3600*24);
        }
        
        return $country_code;
    }
    
    
    public static function Check_if_User_allowed($myCountryCode, $blocked_country_list = array())
    {
        if (count($blocked_country_list) && in_array($myCountryCode, $blocked_country_list)) return false;
        return true;
    }
    
    
    public static function Check_if_User_IP_allowed($ip, $ip_list = '')
    {
        if ($ip_list == '') return true;
        
        $ip_list = str_replace(array(".*.*.*", ".*.*", ".*"), ".", trim($ip_list));
        $ip_list = explode("\n", $ip_list);
        if (count($ip_list))
        {
            foreach ($ip_list as $rule_ip)
            {
                if (strpos($ip, $rule_ip) === 0) 
                {
                    // match
                    return false;
                }
            }
        }
        
        return true;
    }
    
    public static function Check_IP_in_list($ip, $ip_list = '')
    {
        if ($ip_list == '') return false;   // IP is not in the list
        
        $ip_list = str_replace(array(".*.*.*", ".*.*", ".*"), ".", trim($ip_list));
        $ip_list = explode("\n", $ip_list);
        if (count($ip_list))
        {
            foreach ($ip_list as $rule_ip)
            {
                if (strpos($ip, $rule_ip) === 0) 
                {
                    // match
                    return true;    // IP is in the list
                }
            }
        }
        
        return  false;   // IP is not in the list
    }
    
    

    public static function Save_Block_alert($alert_data)
    {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'plgsgtcb_stats';
        
        $sql_array = array(
            'time' => intval($alert_data['time']),
            'ip' => $alert_data['ip'],
            'country_code' => $alert_data['country_code'],
            'url' => addslashes($alert_data['url']),
        );
        
        $wpdb->insert( $table_name, $sql_array ); 
    }
    
    
    public static function Delete_old_logs($days)
    {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'plgsgtcb_stats';
        
        $old_time = time() - $days*24*60*60;
        
        $sql = 'DELETE FROM '.$table_name.' WHERE time < '.$old_time;
        $wpdb->query($sql); 
    }


	public static function PrepareDomain($domain)
	{
	    $host_info = parse_url($domain);
	    if ($host_info == NULL) return false;
	    $domain = $host_info['host'];
	    if ($domain[0] == "w" && $domain[1] == "w" && $domain[2] == "w" && $domain[3] == ".") $domain = str_replace("www.", "", $domain);
	    //$domain = str_replace("www.", "", $domain);
	    
	    return $domain;
	}
    
    public static function CheckIfPRO()
    {
        $domain = self::PrepareDomain(get_site_url());
        
        $params = self::Get_Params(array('registration_code'));
        if (!empty($params)) $registration_code = strtoupper( $params['registration_code'] );
		else return false;
        
        $check_code = strtoupper( md5( md5( md5($domain)."Version 568B2BF9ABFF" )."F80E6711B97D" ) );
        
        if ($check_code == $registration_code) return true;
        else return false;
    }
    
    // DEVELOP
    public static function FreeDaysLeft()
    {
        $params = self::Get_Params(array('installation_date'));
        $t = strtotime($params['installation_date']);
        $t = $t + 30*24*60*60;

        $t = ( $t - time() )/ 60 / 60 / 24;
        if ($t < 0) return 0;
        else return round($t);
    }
    
    public static function CheckAntivirusInstallation()
    {
        // Check for wp-antivirus-site-protection
        $avp_path = dirname(__FILE__);
		$avp_path = str_replace('wp-geo-website-protection', 'wp-antivirus-site-protection', $avp_path);
        if ( file_exists($avp_path) ) return true;
        
        // Check for wp-antivirus-website-protection-and-website-firewall
        $avp_path = dirname(__FILE__);
		$avp_path = str_replace('wp-geo-website-protection', 'wp-antivirus-website-protection-and-website-firewall', $avp_path);
        if ( file_exists($avp_path) ) return true;
        
        // Check for wp-website-antivirus-protection
        $avp_path = dirname(__FILE__);
		$avp_path = str_replace('wp-geo-website-protection', 'wp-website-antivirus-protection', $avp_path);
        if ( file_exists($avp_path) ) return true;

        return false;
    }
    
    public static function GeneratePieData($days = 1)
    {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'plgsgtcb_stats';
        
        $new_time = time() - $days * 24 * 60 * 60;
        
        $query = "SELECT country_code, count(*) AS country_num
            FROM ".$table_name."
            WHERE time > '".$new_time."' 
            GROUP BY country_code
            ORDER BY count(*) desc";

        $rows = $wpdb->get_results($query);
        
        //print_r($rows);

        
        $data = array();
        if (count($rows))
        {
            $total = 0;
            $i_limit = 10;
            foreach ( $rows as $row ) 
            {
                $total = $total + $row->country_num;
                if ($i_limit > 0) $data[ $row->country_code ] = $row->country_num;
                else $data[ 'Other' ] += $row->country_num;
                
                $i_limit--;
            }
            
            //print_r($data);
            
            foreach ($data as $k => $v)
            {
                $data[$k] = round( 100 * $v / $total, 2);
            }
            
            //print_r($data);
        }
        
        return $data;
    }


    public static function GenerateLineData($days = 1)
    {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'plgsgtcb_stats';
        
        $new_time = time() - $days * 24 * 60 * 60;
        
        $query = "SELECT time
            FROM ".$table_name."
            WHERE time > '".$new_time."' ";

        $rows = $wpdb->get_results($query);
        
        //print_r($rows);

        
        $data = array();
        if (count($rows))
        {
            // Fill massive
            if ($days == 1)
            {
                for ($i = 24; $i >= 0; $i--)
                {
                    $date = date("d M H:00", mktime(date("H") - $i, 0, 0, date("m")  , date("d"), date("Y")));
                    $data[$date] = 0;
                }
            }
            else {
                for ($i = $days; $i >= 0; $i--)
                {
                    $date = date("d M", mktime(0, 0, 0, date("m")  , date("d") - $i, date("Y")));
                    $data[$date] = 0;
                }
            }
            //print_r($data);

            foreach ( $rows as $row ) 
            {
                if ($days == 1) $date = date("d M H:00", $row->time);
                else $date = date("d M", $row->time);
                
                $data[$date]++;
            }
        }
        
        return $data;
    }


    public static function PreparePieData($pie_array, $slice_flag = true)
    {
        $a = array();
        if (count($pie_array))
        {
            foreach ($pie_array as $country_code => $country_proc)
            {
                if ($country_code == "Other") $country_name_txt = "Other";
                else $country_name_txt = self::$country_list[ $country_code ];
                if ($country_name_txt == "") $country_name_txt = $country_code;
                
                if ($slice_flag) $txt = "{name: '".addslashes($country_name_txt)."', y: ".$country_proc.", sliced: true, selected: true}";
                else $txt = "{name: '".addslashes($country_name_txt)."', y: ".$country_proc."}";
                $a[] = $txt;
                
                $slice_flag = false;
            }
        }
        
        return $a;
    }
    

    
    public static function GetLatestRecords($amount)
    {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'plgsgtcb_stats';
        
        $new_time = time() - $days * 24 * 60 * 60;
        
        $query = "SELECT *
            FROM ".$table_name."
            ORDER BY id DESC
            LIMIT ".$amount;

        $rows = $wpdb->get_results($query);
        
        return $rows;
    }

}
