<?php
if (
    ( stripos($_SERVER['REQUEST_URI'], '/wp-login.php') !== false && stripos($_SERVER['REQUEST_URI'], '/wp-login.php') == 0 ) ||
    ( stripos($_SERVER['REQUEST_URI'], '/wp-admin/') !== false && stripos($_SERVER['REQUEST_URI'], '/wp-admin/') == 0 ) 
) return;

$file = dirname(__FILE__)."/ip_blocked.txt";

$fp = fopen($file, "r");
$contents = fread($fp, filesize($file));
fclose($fp);

$ip = GetMyIP();

if (strpos($contents, $ip) !== false)
{
    Delete_expired_blocked_IP();
    
    $fp = fopen($file, "r");
    $contents = fread($fp, filesize($file));
    fclose($fp);
    
    if (strpos($contents, '|'.$ip.'|') !== false)
    {
        BlockPage($ip);
    }
}

function GetMyIP()
{
	$ip_address = $_SERVER["REMOTE_ADDR"];
	if (isset($_SERVER["HTTP_X_REAL_IP"])) $ip_address = $_SERVER["HTTP_X_REAL_IP"];
	if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) $ip_address = $_SERVER["HTTP_CF_CONNECTING_IP"];
    return $ip_address ;
}


function Delete_expired_blocked_IP()
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


function BlockPage($myIP)
{
    $blockpage_json = array();
    $blockpage_json['logo_url'] = '/wp-content/plugins/wp-text-copy-blocker/images/logo_siteguarding.svg';
    $blockpage_json['text_1'] = 'Access is not allowed from your IP.';
    $blockpage_json['text_2'] = 'If you think it\'s a mistake, please contactwith the webmaster of the website';



	$logo_url = '';
    if ($blockpage_json['logo_url'] != '') $logo_url = '<p><img style="max-width:500px;max-height:400px" src="'.$blockpage_json['logo_url'].'" id="logo"></p>';

    $ipinfo = '';
    if ($blockpage_json['hide_ipinfo'] == 0) {
		$ipinfo = '<h4>Session details:</h4><p>IP: '.$myIP.' [copy text blocker plugin]</p>';
	}
	
	
    ?><html><head>
    </head>
    <body>
    <div style="margin:100px auto; max-width: 500px;text-align: center;">
		<?php echo $logo_url; ?>
        <p>&nbsp;</p>
        <h3 style="color: #de0027; text-align: center;"><?php echo $blockpage_json['text_1']; ?></h3>
        <p><?php echo $blockpage_json['text_2']; ?></p>

        <?php echo $ipinfo; ?>
        <p>&nbsp;</p>
        <p>&nbsp;</p>

        <p style="font-size: 70%;">Powered by <a target="_blank" href="https://www.siteguarding.com/">SiteGuarding.com</a></p>

    </div>
    </body></html>
    <?php

    die();
}