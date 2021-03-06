<?php
// Future-friendly json_encode
if( !function_exists('json_encode') ) {
    function json_encode($data) {
        $json = new Services_JSON();
        return( $json->encode($data) );
    }
}
// Future-friendly json_decode
if( !function_exists('json_decode') ) {
    function json_decode($data) {
        $json = new Services_JSON();
        return( $json->decode($data) );
    }
}

function isGet($parameter)
{
	return isset($_GET[$parameter]) ?  true : false ;
}

function isPost($parameter)
{
	return isset($_POST[$parameter]) ?  true : false ;
}

function Encrypt($str)
{
	return base64_encode($str);
}

function Decrypt($str)
{
	return base64_decode($str);
}

function split_text($string, $nb_caracs, $separator='...'){
    // strip tags to avoid breaking any html
    $string = strip_tags($string);
    // make sure it ends in a word so assassinate doesn't become ass...
    // $string = substr($stringCut, 0, strrpos($stringCut, ' ')).$separator ; 
    return (strlen($string) > $nb_caracs) ? substr($string, 0, $nb_caracs ).$separator : $string;
}

function Tables_Exists()
{
	return (TableExists('stats') && TableExists('settings') && TableExists('files')  && TableExists('users') && TableExists('folders') && TableExists('reports')) ? true :false;	
}

function html_decoder($url)	
{
	$url = str_replace("&amp;", "&", $url);
	$url = str_replace("&quot;", "\"", $url);
	$url = str_replace("&apos;", "'", $url);
	$url = str_replace("&gt;", ">", $url);
	$url = str_replace("&lt;", "<", $url);
	return $url;
}

function CheckConnect()
{
	if(mysqli_connect_errno() || !Tables_Exists() || num_rows(Sql_query("SELECT 1 FROM `settings`"))<2)
	{
		error_reporting (0);
		ob_start();
		define('sitename','upload site');
		define('rtlsitename','موقعي للرفع');
		//define('InterfaceLanguage','ar');
		define('siteclose','1');
		IsRtL() ? define('closemsg',' الموقع مغلق للصيانة ' . mysqli_connect_error()) : define('closemsg','Closed for maintenance ' . mysqli_connect_error()); 
	}

}

/*----------------------------------*/
function mysqliconnect($database = true)
{ 
	return	$database ? @mysqli_connect(dbhost, dbuser, dbpass, dbname) :  @mysqli_connect(dbhost, dbuser, dbpass);
}


function Sql_mode($STRICT_TRANS_TABLES='NO_ENGINE_SUBSTITUTION')
{
	global $conn;
	return $conn ? Sql_query("SET SESSION sql_mode = '$STRICT_TRANS_TABLES'") : false; 
}


function mysqliClose_freeVars()
{ 
    global $conn;
mysqli_close($conn);
foreach (array_keys(get_defined_vars()) as $var) 
	        unset($$var);
			
}
function affected_rows()
{ 
    global $conn;
	return (mysqli_affected_rows($conn)>0) ? true : false;
}

function TableExists($table) {
  return num_rows(Sql_query("SHOW TABLES LIKE '$table'")) > 0;
}

function ColumnExists($column,$table) {
  return num_rows(Sql_query("SHOW COLUMNS FROM `$table` LIKE '$column';"))>0;
}


function protect($string) {
	return htmlspecialchars(trim($string), ENT_QUOTES);
}
function success($text) {
	return '<div class="alert alert-success fade in"><i class="glyphicon glyphicon-ok-circle"></i> '.$text.'</div>';
}

function info($text) {
	return '<div class="alert alert-info fade in"><i class="glyphicon glyphicon-info-sign"></i> '.$text.'</div>';
}

function error($text) {
	return '<div class="alert alert-danger fade in"><i class="glyphicon glyphicon-remove-circle"></i> '.$text.'</div>';
}

function warning($text) {
	return '<div class="alert alert-warning fade in"><i class="glyphicon glyphicon-warning-sign"></i> '.$text.'</div>';
}
function panel($title,$text) {
	return '<div class="panel panel-default"><div class="panel-heading">'.$title.'</div><div class="panel-body">'.$text.'</div></div>';
}

function directionDiv($or=false) {
if($or)
	echo IsRtL() ? 'right' : 'left';
else
	echo IsRtL() ? 'left' : 'right';
}

function directionDiv2($or=false) {
if($or)
	return IsRtL() ? 'right' : 'left';
else
	return IsRtL() ? 'left' : 'right';
}

function isValidURL($url) {
	return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
}

function isValidUsername($str) {
    return preg_match('/^[a-zA-Z0-9-_]+$/',$str);
}

function isValidEmail($str) {
	return filter_var($str, FILTER_VALIDATE_EMAIL);
}

function _Upload_name() {
	return prefixname.date("Y-m-d_His.");
}
function IePrintArray($data){
	(!IsIeBrowser()) ? header('Content-Type: application/json') : header('Content-Type: text/html; charset=UTF-8') ;
	die(json_encode($data)) ;
}

function PrintArray($data){
	header('Content-Type: application/json');
	die(json_encode($data)) ;
}

function glyphiconIsPublic($status)
{
	return	$status ? '<i class="glyphicon glyphicon-eye-open"></i>' : '<i class="glyphicon glyphicon-eye-close"></i>';
}

function iptolong($ip){
    list($ip1,$ip2,$ip3,$ip4)=explode(".",$ip);
    $ipLong= $ip1*pow(256,3)+$ip2*pow(256,2)+$ip3*256+$ip4;
    return $ipLong;
}
function longtoip($ip_long)  {
	    $d = $ip_long%256;
	    $c = (($ip_long-$d)/256)%256;
	    $b = (($ip_long-($c*256)-$d)/(256*256))%256;
	    $a = (($ip_long-($b*256*256)-$c*256-$d)/(256*256*256))%256;
	    return $a.".".$b.".".$c.".".$d;  
}

/* By Grant Burton @ BURTONTECH.COM (11-30-2008): IP-Proxy-Cluster Fix */
function checkIP($ip) {
   if (!empty($ip) && iptolong($ip)!=-1 && iptolong($ip)!=false) {
       $private_ips = array (
       array('0.0.0.0','2.255.255.255'),
       array('10.0.0.0','10.255.255.255'),
       array('127.0.0.0','127.255.255.255'),
       array('169.254.0.0','169.254.255.255'),
       array('172.16.0.0','172.31.255.255'),
       array('192.0.2.0','192.0.2.255'),
       array('192.168.0.0','192.168.255.255'),
       array('255.255.255.0','255.255.255.255')
       );

       foreach ($private_ips as $r) {
           $min = iptolong($r[0]);
           $max = iptolong($r[1]);
           if ((iptolong($ip) >= $min) && (iptolong($ip) <= $max)) return false;
       }
       return true;
   } else { 
       return false;
   }
}

function iplong() { return iptolong(ip());}

function ip() {
   if (!empty($_SERVER['HTTP_CLIENT_IP']) && checkIP($_SERVER["HTTP_CLIENT_IP"])) 
       return protect($_SERVER["HTTP_CLIENT_IP"]);
   
   if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
	   foreach (explode(",",$_SERVER["HTTP_X_FORWARDED_FOR"]) as $ip)
           if (checkIP(trim($ip))) 
			   return protect($ip);
       
   
   if (!empty($_SERVER['HTTP_X_FORWARDED']) && checkIP($_SERVER["HTTP_X_FORWARDED"])) {
       return protect($_SERVER["HTTP_X_FORWARDED"]);
   } elseif (!empty($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && checkIP($_SERVER["HTTP_X_CLUSTER_CLIENT_IP"])) {
       return protect($_SERVER["HTTP_X_CLUSTER_CLIENT_IP"]);
   } elseif (!empty($_SERVER['HTTP_FORWARDED_FOR']) && checkIP($_SERVER["HTTP_FORWARDED_FOR"])) {
       return protect($_SERVER["HTTP_FORWARDED_FOR"]);
   } elseif (!empty($_SERVER['HTTP_FORWARDED']) && checkIP($_SERVER["HTTP_FORWARDED"])) {
       return protect($_SERVER["HTTP_FORWARDED"]);
   } else {
       return protect($_SERVER["REMOTE_ADDR"]);
   }
}

//$_SERVER["REMOTE_ADDR"] = determineIP();

/*
function ip()
{
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    return protect($_SERVER['HTTP_CLIENT_IP']);
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    return protect($_SERVER['HTTP_X_FORWARDED_FOR']);
} else {
    return protect($_SERVER['REMOTE_ADDR']);
}
}*/

function getLocationInfoByIp(){

	$result  = array('countryName'=>'Unknown', 'countryCode'=>'UN', 'city'=>'Unknown','ip'=>ip());
	/*---------------------------------------------------------------------------------------------*/
	if((VisitorIp==ip()) && defined('VisitorCountryName') && defined('VisitorCity') && defined('VisitorCountryCode') )
		return array('countryName'=>VisitorCountryName, 
		             'countryCode'=>VisitorCountryCode,
					 'city'       =>VisitorCity,
					 'ip'         =>VisitorIp,	
					 'iptolong'   =>iptolong(VisitorIp));						 
    /*---------------------------------------------------------------------------------------------*/
	
    $ip_data = @json_decode(Request('http://api.ipinfodb.com/v3/ip-city/?key=5bc65f0d26fe7528505afee445955f985034798b87348688d3f87eb43a4675cb&format=json&ip='. ip() ));    
	
    if($ip_data && $ip_data->countryName != "-" && $ip_data->statusCode!='ERROR'){
        $result['countryName'] = $ip_data['countryName'];
		$result['countryCode'] = $ip_data['countryCode'];
		$result['city']        = $ip_data['cityName'];	
		$result['ip']          = $ip_data['ipAddress'];
		$result['iptolong']     = iptolong( $ip_data['ipAddress'] );
		}
		
	 $_SESSION['settings']["visitor"]["ip"]          = $result['ip'];
	 $_SESSION['settings']["visitor"]["countryName"] = $result['countryName'];
     $_SESSION['settings']["visitor"]["countryCode"] = $result['countryCode'];
	 $_SESSION['settings']["visitor"]["city"]        = $result['city'];
    return $result;
}


 function getLocationInfoByIp_2()//Geo
 {
	 
	 $result  = array('countryName'=>'Unknown', 'countryCode'=>'UN', 'city'=>'Unknown', 'ip'=>ip() ,'iptolong' =>iptolong(VisitorIp));

	/*---------------------------------------------------------------------------------------------*/
	if((VisitorIp==ip()) && defined('VisitorCountryName') && defined('VisitorCity') && defined('VisitorCountryCode') )
		return array('countryName'=>VisitorCountryName, 
		             'countryCode'=>VisitorCountryCode,
					 'city'       =>VisitorCity,
					 'ip'         =>VisitorIp,
					 'iptolong'   =>iptolong(VisitorIp)			 
					 );												
    /*---------------------------------------------------------------------------------------------*/
	
	$data = @json_decode(Request('http://ip-api.com/json/'.ip()),true);	
		if($data && is_array($data) && $data['status']=='success') 
		{
	       $result['countryName'] = $data['country'];
		   $result['countryCode'] = $data['countryCode'];
		   $result['city']        = $data['city'];
		   $result['ip']          = ip();          /*$data['query'];*/
		   $result['iptolong']     = iplong();
		
		}
		
	 $_SESSION['settings']["visitor"]["ip"]          = $result['ip'];
	 $_SESSION['settings']["visitor"]["countryName"] = $result['countryName'];
     $_SESSION['settings']["visitor"]["countryCode"] = $result['countryCode'];
	 $_SESSION['settings']["visitor"]["city"]        = $result['city'];
	 
	 return $result;	
 }


function Sql_Insert_Stat($file_id,$referrer)
{
	if(!statistics) return ;
	
	$info  = Sql_Get_info($file_id);
	
	if(!$info['status']) return ;
	$file_id  =(int)$file_id;
	
	$referrer = isValidURL($referrer) ? protect($referrer) : '';
	/*------------------------------------------*/
	require_once('ua_parser.php');
	$browser  = protect($infos['browser_name']);
	$platfrm  = protect($infos['platfrm_name']);
	/*------------------------------------------*/
	//$InfoByIp = getLocationInfoByIp();
	//$country  = protect($InfoByIp['countryName']);
	$InfoByIp = getLocationInfoByIp_2();
	$country  = protect($InfoByIp['countryCode']);
	$ip       = (int)($InfoByIp['iptolong']);
	/*------------------------------------------*/
    /*if ($ip == -1 || $ip === FALSE) $ip   = iplong();*/
	
	$date     = timestamp();
	
	return Sql_query ("INSERT INTO `stats` (`referrer`, `country_code`, `browser`, `platform`, `file_id`, `date`, `ip`) VALUES ('$referrer', '$country', '$browser', '$platfrm', '$file_id', '$date', '$ip');");
}


function Auto_detect_language($default = 'en')
{
	global $supportedLangs;
	$languages = (isset($_SERVER["HTTP_ACCEPT_LANGUAGE"]))  ? explode(',',$_SERVER["HTTP_ACCEPT_LANGUAGE"]) : array($default);
	
	foreach($languages as $lang)
	    $_languages[] = strtolower(substr($lang,0,2));
	   
	$_languages   = array_unique($_languages);
	if(count($_languages ) == 1) 
		return (in_array($_languages[0], $supportedLangs))  ? $_languages[0] : $default;
	foreach($_languages as $lang)
	    if(in_array($lang, $supportedLangs))
		{
			return $lang ;
			break;
		}
	
}

function icon($filename)
{
	return "<i class='".iconClass($filename)."'></i>";			
}


function iconClass($filename)
{
	$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
	if ($ext =='png' || $ext =='jpg' || $ext =='jpeg' ||$ext =='gif' || $ext =='bmp' || $ext =='ico' || $ext =='psd') 
        return "icon-file-image";
	elseif($ext =='rar' || $ext =='zip' || $ext =='7z' || $ext =='iso')
	    return "icon-file-archive";
	elseif($ext =='mkv' || $ext =='3gp' || $ext =='flv' || $ext =='mp4')
	    return "icon-file-video";
	elseif($ext =='doc' || $ext =='docm' || $ext =='docx')
	    return "icon-file-word";
	elseif($ext =='xlsm' || $ext =='xlsx' || $ext =='xlsx' || $ext =='xlt')
	    return "icon-file-excel";
	elseif($ext =='pdf')
	    return "icon-file-pdf";
	elseif($ext =='pptx' || $ext =='pptm' || $ext =='ppt')
	    return "icon-file-powerpoint";	
	elseif($ext =='mp3' || $ext =='ogg' || $ext =='wma' || $ext =='wav' || $ext =='rm')
	    return "icon-file-audio";		
	elseif($ext =='css' || $ext =='html' || $ext =='php' || $ext =='js' || $ext =='pas')
	    return "icon-file-code";		
	elseif($ext =='txt' || $ext =='srt')
	    return "icon-doc-text";	
	else
	    return "icon-doc";			
}

function actv($par){if(isGet($par)) echo ' class= "active" ';}
function actv2($par){if(isGet($par)) echo ' active';}
function user_level($_level){global $lang; return ($_level==1) ? $lang[57] : $lang[56];	}  
function user_plan($_plan){global $lang; if($_plan==0) return $lang[226]; elseif($_plan==1) return $lang[227];  elseif($_plan==2) return $lang[228];	}       
function GetDateTxt(){return date('Y-m-d H:i:s');}
function timestamp(){return strtotime(date('Y-m-d H:i:s'));}
function GenerateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) 
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    return $randomString;
}


function FooterInfo($directory){
	global  $lang;

$_FooterInfo  = $lang[107] ;

if(FooterInfo) /*IsAdmin || */
{
	$_Total_Files = Sql_Get_Files_Count(true) ;
	$_Total_Users = Sql_Get_Users_Count() ; 
	$_Total_Size  = ($_Total_Files==0) ? 0 : FileSizeConvert(folderSize( $directory ));	
}

$_Total_Files = (!isset($_Total_Files)) ?  0 :$_Total_Files ;
$_Total_Users = (!isset($_Total_Users)) ?  0 :$_Total_Users ;
$_Total_Size =  (!isset($_Total_Size))  ?  0 :$_Total_Size  ;

/*IsAdmin || */
return (FooterInfo) ? $lang[107] .'&nbsp; | &nbsp;<span class="text-color">'.$lang[28] .'</span> : <b>'.$lang[42].'</b> '.$_Total_Size. ' , <b>' . $lang[44]. '</b> '.$_Total_Files .' , <b>'.$lang[73].'</b> '.$_Total_Users : $_FooterInfo;

}

function unlinkRecursive($dir, $RemoveRootToo)
{
	 if (is_file($dir))
     return @unlink($dir);  
    if(!$dh = @opendir($dir))
     return ;   
    while (false !== ($obj = readdir($dh)))
    {
        if($obj == '.' || $obj == '..')
        continue;
        if (!@unlink($dir . '/' . $obj))
        unlinkRecursive($dir.'/'.$obj, true);       
    }
    closedir($dh);
    if ($RemoveRootToo)
     @rmdir($dir);    
    return;
}
function folderSize ($dir)
{
    $size  = 0;
	/*Return files as they appear in the directory (no sorting). When this flag is not used, the pathnames are sorted alphabetically */
	$files = glob(rtrim($dir, '/').'/*', GLOB_NOSORT);
	if (is_array($files)) 
    foreach ($files as $each) {
        $size += is_file($each) ? filesize($each) : folderSize($each);
    }
	unset($files);
    return $size;
}

function Need_Login() {
	global $lang;
	echo '<i class="glyphicon glyphicon-question-sign"></i> ' . $lang[49] ;
}

function Need_Logout() {
	global $lang;
	echo '<i class="glyphicon glyphicon-question-sign"></i> ' . $lang[196] ;
}
function Get_Ads($ads_page) {
	global $conn,$lang; 
	include ('./modals/poster.php') ;
}

function get_main_title($_param ='') {
	global $lang;

    if(isGet($_param.'download') && (isGet('confirm') || isGet('notfound')) )
	    return $lang[198] ;
 	elseif(isGet($_param.'plans')) 
	    return $lang[230] ;
 	elseif(isGet($_param.'download')) 
		return $lang[31] ;
	elseif(isGet($_param.'files') )
	    return  $lang[48] ;
	elseif(isGet($_param.'profile') )	
		return (!IsLogin) ? $lang[30] : $lang[30] .' - '. UserName ;
	elseif(isGet($_param.'register') )		
		return $lang[39] ;
	elseif(isGet($_param.'about'))
	    return  $lang[19] ;
	elseif(isGet($_param.'authorized')) 
	    return  $lang[148] ;
	elseif(isGet($_param.'login') ) 
	    return  $lang[20] ;	
	elseif(isGet($_param.'forgot') ) 
	    return  $lang[41] ;	
	elseif(isGet($_param.'contact') ) 
	    return  $lang[52] ;	
    elseif(isGet($_param.'index') ) 
	    return  $lang[4] ;			
	elseif(defined('MainTitle')) 
	    return MainTitle ;
	else
		return $lang[4] ;
}

function extensionsStr($split=true)
{
	global $lang;
	return (!$split) ? $lang[24].' <code>'.FileSizeConvert(MaxFileSize).'</code> , '.$lang[25].' <code>'.extensions.'</code>' : $lang[24].' <code>'.FileSizeConvert(MaxFileSize).'</code> , '.$lang[25].' <code>'.split_text(extensions,50,'... <a href="javascript:void(0)" onclick="ExtReadMore()" >'.$lang[244].'</a>').'</code>';
}

function Get_Page_Title() {
	global $lang;
	if(isGet('download'))
	{
		//$id   = (is_numeric($_GET['download'])) ? (int)$_GET['download'] : protect(Decrypt($_GET['download']));
		$id        =  protect(Decrypt($_GET['download']));
		$confirm   = (isGet('confirm')) ? true : false ;
		$notfound  = (isGet('notfound')) ? true : false ;
		$_Filename = Sql_Get_originalFilename($id);
		if($_Filename=='' && $confirm ) return $lang[198] ;
		if($_Filename=='' && $notfound ) return $lang[198] ;
		return ($_Filename=='') ? $lang[31].' - '.$lang[46] : $lang[31].' - '.$_Filename ;
	}
	/*elseif(isGet('view']))
	{
		$_Filename = Sql_Get_Filename($_GET['view']);
		return i($_Filename=='') ? $lang[164].' - '.$lang[46] : $lang[164].' - '.$_Filename ;
	}*/
	elseif(isGet('403'))
	    return $lang[168];
	elseif(isGet('404'))
	    return $lang[111];   
    else
		return SiteName() ;//$lang[1];	
	   
   
       
}
function SiteName()
{
	return IsRtL() ? rtlsitename : sitename;
}

function return_bytes ($size_str)
{
    switch (substr ($size_str, -1))
    {
		case 'K': case 'k': return (int)$size_str * 1024;
        case 'M': case 'm': return (int)$size_str * pow(1024, 2);
        case 'G': case 'g': return (int)$size_str * pow(1024, 3);
		case 'T': case 't': return (int)$size_str * pow(1024, 4);
        default: return $size_str;
    }
};
function return_Kilobyte ($size_str)
{
    switch (substr ($size_str, -1))
    {
		case 'K': case 'k': return (int)$size_str ;
        case 'M': case 'm': return (int)$size_str * 1024;
        case 'G': case 'g': return (int)$size_str * pow(1024, 2);
		case 'T': case 't': return (int)$size_str * pow(1024, 3);
        default: return $size_str;
    }
};

/**
* Converts bytes into human readable file size.
*
* @param string $bytes
* @return string human readable file size (2,87 Мб)
* @author Mogilev Arseny
*/ 

function FileSizeConvert($bytes) 
{
    $bytes = floatval($bytes);
	if(InterfaceLanguage=='ar')
		  $arBytes = array(
            0 => array(
                "UNIT" => "تيرا",
                "VALUE" => pow(1024, 4)
            ),
            1 => array(
                "UNIT" => "جيقا",
                "VALUE" => pow(1024, 3)
            ),
            2 => array(
                "UNIT" => "ميقا",
                "VALUE" => pow(1024, 2)
            ),
            3 => array(
                "UNIT" => "كيلو",
                "VALUE" => 1024
            ),
            4 => array(
                "UNIT" => "بايت",
                "VALUE" => 1
            ),
        );
		else
        $arBytes = array(
            0 => array(
                "UNIT" => "TB",
                "VALUE" => pow(1024, 4)
            ),
            1 => array(
                "UNIT" => "GB",
                "VALUE" => pow(1024, 3)
            ),
            2 => array(
                "UNIT" => "MB",
                "VALUE" => pow(1024, 2)
            ),
            3 => array(
                "UNIT" => "KB",
                "VALUE" => 1024
            ),
            4 => array(
                "UNIT" => "B",
                "VALUE" => 1
            ),
        );

    foreach($arBytes as $arItem)
    {
        if($bytes >= $arItem["VALUE"])
        {
            $result = $bytes / $arItem["VALUE"];
            $result = str_replace(".", "," , strval(round($result, 2)))." ".$arItem["UNIT"];
            break;
        }
    }
    return (isset($result)) ? $result : 0;
}
/*-------------------------------------*/
function OptionSizeHtml($id,$value='M')
{
	$value=strtoupper(strOnly($value));
	$units = (InterfaceLanguage=='ar') ? array('K'=>'كيلو','M'=>'ميقا','G'=>'جيقا','T'=>'تيرا') : array('K'=>'KB','M'=>'MB','G'=>'GB','T'=>'TB'); 
	$html  = '<select name="'.$id.'"  class="selectpicker" >';
	foreach($units as $index => $unit)
		$html.=($value==$index) ? '<option value="'.$index .'" selected>'.$unit .'</option>	':'<option value="'.$index .'">'.$unit .'</option>';
		$html.='</select>';
		echo $html;
		unset($html);

}	 
function strOnly($s)
{
	return preg_replace("/[^a-zA-Z]+/", "", $s) ;
}
function nbrOnly($s)
{
	return preg_replace("/[^0-9]+/", "", $s) ;
}

function Sql_query($query)
{
global $conn;	 
return @mysqli_query($conn,$query);                 		 
}

function num_rows($query)
{ 
   return mysqli_num_rows($query);                 		 
}

function fetch_assoc($query,$data)
{ 
$var= mysqli_fetch_assoc($query); 
return $var[$data] ;               		 
}
 
 function fetch_row($query,$data)
{ 
$var= mysqli_fetch_row($query); 
return $var[$data] ;               		 
}
 

function WriteHtaccessThumbnailFolder($dir)
{
if($fp = fopen("$dir/.htaccess",'w')){
	fwrite($fp,'Allow from all');
	fclose($fp);
	}
}	
function WriteHtaccessUploadFolder($dir,$deny = true)
{
	if($deny) $denystr = 'deny from all'; else  $denystr = '';
 if($fp = fopen("$dir/.htaccess",'w')){
$content = '<Files ~ "^.*\.(php|php*|cgi|pl|phtml|shtml|sql|asp|aspx)">
    Order allow,deny
    Deny from all
</Files>

#Disable PHP engine in this folder
<IfModule mod_php4.c>
php_flag engine off
</IfModule>

#Disable PHP engine in this folder
<IfModule mod_php5.c>
php_flag engine off
</IfModule>

#Dont handle those types in this folder
RemoveType .php .php* .phtml .pl .cgi .asp .aspx .sql
'.$denystr;
fwrite($fp,$content);
fclose($fp);
}
}


function WriteHtaccessHomeFolder($dir,$deny = true)
{
 if($fp = fopen("$dir/.htaccess",'w')){
$content = 'Options -Indexes
AddDefaultCharset UTF-8
<files ~ "^.*\.([Hh][Tt][Aa])">
order allow,deny
deny from all
satisfy all
</files>

<IfModule mod_deflate.c>
  AddOutputFilterByType DEFLATE text/html
  AddOutputFilterByType DEFLATE text/css
  AddOutputFilterByType DEFLATE text/javascript
  AddOutputFilterByType DEFLATE text/xml
  AddOutputFilterByType DEFLATE text/plain
  AddOutputFilterByType DEFLATE image/x-icon
  AddOutputFilterByType DEFLATE image/svg+xml
  AddOutputFilterByType DEFLATE image/png
  AddOutputFilterByType DEFLATE image/gif
  AddOutputFilterByType DEFLATE image/jpg
  AddOutputFilterByType DEFLATE image/jpeg
  AddOutputFilterByType DEFLATE application/rss+xml
  AddOutputFilterByType DEFLATE application/javascript
  AddOutputFilterByType DEFLATE application/x-javascript
  AddOutputFilterByType DEFLATE application/xml
  AddOutputFilterByType DEFLATE application/xhtml+xml
  AddOutputFilterByType DEFLATE application/x-font
  AddOutputFilterByType DEFLATE application/x-font-truetype
  AddOutputFilterByType DEFLATE application/x-font-ttf
  AddOutputFilterByType DEFLATE application/x-font-otf
  AddOutputFilterByType DEFLATE application/x-font-opentype
  AddOutputFilterByType DEFLATE application/vnd.ms-fontobject
  AddOutputFilterByType DEFLATE font/ttf
  AddOutputFilterByType DEFLATE font/otf
  AddOutputFilterByType DEFLATE font/opentype

# For Olders Browsers Which Cant Handle Compression
  BrowserMatch ^Mozilla/4 gzip-only-text/html
  BrowserMatch ^Mozilla/4\.0[678] no-gzip
  BrowserMatch \bMSIE !no-gzip !gzip-only-text/html
</IfModule>

ErrorDocument 404 '.siteurl.'/?404
ErrorDocument 403 '.siteurl.'/?403';	
fwrite($fp,$content);
fclose($fp);
}
}

function percent($number){
    return round( ($number * 100) , 2) . '%';
}

 	function Request($host) {

		if ( function_exists('curl_init') ) {			
			//use cURL to fetch data
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $host);
			curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			//ssl
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
			//curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT'] );
			$response = curl_exec($ch);
			curl_close ($ch);
			
		} elseif ( ini_get('allow_url_fopen') ) {
			$response = file_get_contents($host, 'r');	
		} else {
			trigger_error ('Cannot retrieve data. Either compile PHP with cURL support or enable allow_url_fopen in php.ini ', E_USER_ERROR);
			return;
		}
		
		return $response;
	}

function GetUrlHost($url)
{
	$_result = parse_url($url);
	return isset($_result['host']) ? $_result['host'] : '--' ;
}


function Sql_File_Insert($filename,$size,$String,$password,$orgfilename,$ispublic ,$folder_id=0)
{

 $String      = protect($String);
 $filename    = protect($filename);
 $password    = protect($password);
 $orgfilename = protect($orgfilename);
 $ispublic    = (int)$ispublic ;
 $folder_id   = (int)$folder_id;
 $uploadedip  = iplong();
 return Sql_query( "INSERT INTO `files`  ( `userId`, `filename`, `fileSize`, `uploadedDate`, `last_access` , `deleteHash`, `folderId` , `uploadedIP` , `accessPassword` ,`originalFilename` ,`isPublic`) VALUES ('".UserID."' , '$filename' , '$size' , '".timestamp()."' ,  '".timestamp()."' ,'$String' , '$folder_id' , '$uploadedip' , '$password' , '$orgfilename' , '$ispublic' );") ;	

}


function Sql_Update_File($filename,$size,$passwordfile,$ispublic)
{
 $filename = protect($filename);
 $passwordfile = protect($passwordfile);
 $ispublic = (int)$ispublic;
 return Sql_query( "UPDATE `files` SET `uploadedDate` = '".timestamp()."', `fileSize` = '$size', `accessPassword` = '$passwordfile', `isPublic` = '$ispublic' WHERE `filename` = '$filename';");	
}

function Sql_Last_query_id()
{
 global $conn;
	 return $conn ? mysqli_insert_id($conn) : 0; 
}

function Sql_Delete_File($id)
{
$id = (int)$id;
return Sql_query( "DELETE FROM `files`  WHERE `id`= '$id'" ) ;	
}

function Sql_Delete_User($id)
{
$id = (int)$id;
return Sql_query( "DELETE FROM `users`  WHERE `id`= '$id'" ) ;	
}

function Sql_Delete_Comment($id)
{
$id = (int)$id;
return Sql_query( "DELETE FROM `comments`  WHERE `id`= '$id'" ) ;	
}

function Sql_Delete_Folder($id)
{
$id = (int)$id;
return Sql_query( "DELETE FROM `folders`  WHERE `id`= '$id'" ) ;	
}

function Sql_Delete_Stat_File_Id($id)
{
$id = (int)$id;
return Sql_query( "DELETE FROM `stats`  WHERE `file_id`= '$id'" ) ;	
}

function glyphiconOK($status,$colored=false)
{
	if($colored)
		return $status ? '<i class="glyphicon glyphicon-ok-circle text-success"></i>' : '<i class="glyphicon glyphicon-remove-circle text-danger"></i>';	
	else
		return $status ? '<i class="glyphicon glyphicon-ok-circle"></i>' : '<i class="glyphicon glyphicon-remove-circle"></i>';	
}
function IntToIcon($str)
{
	return ($str =='0' || $str =='1' ) ? glyphiconOK($str,true) : (($str=='')? '/' : $str);
}

function siteURL()
{  
	 $_siteurl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);
	 return  ( substr($_siteurl, -1)=='/') ? substr($_siteurl,0, strlen($_siteurl)-1) : $_siteurl;
}


function Sql_Create_Folder()
{

if(!Tables_Exists()) 
	return ;

$userId    = 0;

if(defined('enable_userfolder') && defined('folderupload') && enable_userfolder && IsLogin )
{
	$uploadDir = folderupload .'/'.UserName;
	$userId    = UserID ;
}	
elseif(defined('folderupload'))
{
	$uploadDir = folderupload ;	
} 
	
$folderExists = num_rows(Sql_query("SELECT 1 FROM `folders` WHERE `folderName` = '".$uploadDir."' AND `userId` = '$userId'")) ;

		if($folderExists==0)
		{
			Sql_query("INSERT INTO `folders` (`userId`, `folderName`, `isPublic`, `accessPassword`, `date_added`) VALUES ( '$userId', '$uploadDir', '1', '', '".timestamp()."');");
			$_SESSION['login']['folder_id'] = (int)Sql_Last_query_id() ;
			$_SESSION['login']['folder_name'] = $uploadDir;
		}
/*			
if (isset($uploadDir))
	if (!file_exists('..'.$uploadDir )) 
		@mkdir('..'.$uploadDir , 0777, true);	*/
}

function Sql_Delete_Report($id)
{
$id = (int)$id;
return Sql_query( "DELETE FROM `reports`  WHERE `id`= '$id'" ) ;	
}

function Sql_Delete_Report_File_Id($id)
{
$id = (int)$id;
return Sql_query( "DELETE FROM `reports`  WHERE `file_id`= '$id'" ) ;	
}

function Sql_Delete_Comment_Id($id)
{
$id = (int)$id;
return Sql_query( "DELETE FROM `comments`  WHERE `file_id`= '$id'" ) ;	
}


function Sql_Update_Report($id)
{
$id = (int)$id;
return Sql_query( "UPDATE `reports` SET `status` =  NOT `status` WHERE `id`= '$id'" ) ;	
}


function Sql_Get_folder($folder_id)
{	
$folder_id = (int)$folder_id;
return fetch_assoc(Sql_query("SELECT `folderName` FROM `folders` WHERE `id`= '$folder_id'"),'folderName') ;
}
function Sql_Get_Last_date_Download($file_id)
{	
$file_id = (int)$file_id;
return fetch_assoc(Sql_query("SELECT `date` FROM `stats` WHERE `file_id`= '$file_id' ORDER BY id DESC LIMIT 1"),'date') ;
}


function Get_folderId_by_Filename($filename)
{	
$filename = protect($filename);
return fetch_assoc(Sql_query("SELECT `folderId` FROM `files` WHERE `filename`= '$filename'"),'folderId') ;
}

function Get_folderName_By_FolderId($folder_id) //!!
{	
$folder_id = (int)$folder_id;
return fetch_assoc(Sql_query("SELECT `folderName` FROM `folders` WHERE `id`= '$folder_id'"),'folderName') ;
}

function Get_FolderId_By_folderName($folderName) 
{	
$folderName = protect($folderName);
return fetch_assoc(Sql_query("SELECT `id` FROM `folders` WHERE `folderName`= '$folderName'"),'id') ;
}

function Sql_Get_publicity($name)
{	
$name  = protect($name);
$qry = Sql_query("SELECT * FROM `settings` WHERE `name`= '$name'");
if(num_rows($qry)>0)
    {
	$row=mysqli_fetch_assoc($qry);	
	return array('status' => true,
	             'name' => $name,
	             'content' => Decrypt($row['value']) ,
				 'title'=>$row['parameter'] 			 
				 );
	} 
	else
	return array('status' => false,
                 'name' => $name);
mysqli_free_result($qry);
}

function get_date_Strings($index,$diff,$strings,$string)
{
	global $lang;
	if($lang[0]=='ar')
	{	

       if($diff==2)
		   return str_replace("ة","ت",$string[$index].'ين') ;
	   else
		return ($diff>10) ? $string[$index] : $strings[$index];	
	} else return $strings[$index];						
}

function time_elapsed_string($datetime, $full = false) {
	global $lang;
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;
	
	    $date_string = array(
        'y' => $lang[210],
        'm' => $lang[211],
        'w' => $lang[212],
        'd' => $lang[213],
        'h' => $lang[214],
        'i' => $lang[215],
        's' => $lang[216]);


        $date_strings = array(
        'y' => $lang[219],
        'm' => $lang[220],
        'w' => $lang[221],
        'd' => $lang[222],
        'h' => $lang[223],
        'i' => $lang[224],
        's' => $lang[225]);
		
			
    foreach ($date_string as $index => &$v) {
        if ($diff->$index) {
            $v = $diff->$index . ' ' . ($diff->$index > 1 ? get_date_Strings($index,$diff->$index,$date_strings,$date_string) : $v);
			
        } else {
            unset($date_string[$index]);
        }
    }

    if (!$full) $date_string = array_slice($date_string, 0, 1);
	if($lang[0]=='ar')
		return $date_string ? $lang[217] .' '. implode(', ', $date_string) : $lang[218];
	else
		return $date_string ? implode(', ', $date_string) . ' '.$lang[217] : $lang[218];
}




function get_thumbnail($img)
{
	$img_parts = pathinfo( $img );
	return md5(basename($img)).'.'.$img_parts['extension'];  //md5($img_parts['filename']).'.'.$img_parts['extension'];	
}

function Get_folderName_By_UserId($user_id = UserID) 
{	
$user_id = (int)($user_id);
return fetch_assoc(Sql_query("SELECT `folderName` FROM `folders` WHERE `userId`= '$user_id'"),'folderName') ;
}

function Get_folderId_By_UserId($user_id = UserID) 
{	
$user_id = (int)$user_id;
return fetch_assoc(Sql_query("SELECT `id` FROM `folders` WHERE `userId`= '$user_id'"),'id') ;
}

function Get_comment_status($id) 
{	
$id = (int)$id;
return fetch_assoc(Sql_query("SELECT `status` FROM `comments` WHERE `id`= '$id'"),'status') ;
}

function Sql_Get_info($id,$directory='.')
{	
$id  = (int)$id;
$qry = Sql_query("SELECT * FROM `files` WHERE `id`= '$id'");
if(num_rows($qry)>0)
    {
	$row            = mysqli_fetch_assoc($qry);	
	$folder         = Sql_Get_folder($row['folderId']) ;
	$thumbnail      = ((file_exists($directory.$folder.'/'.$row["filename"])) && (ext_is_image($directory.$folder.'/'.$row["filename"]))) ? true : false ;
	$thumbnail_dir	= $thumbnail ? $folder .'/_thumbnail/'. get_thumbnail($row['filename']) : '';
	$thumbnail_dir	= file_exists($directory.$thumbnail_dir) ? $thumbnail_dir : '' ;
	$thumbnail	    = empty($thumbnail_dir) ? false : true ;
	$isFile         = is_file($directory.$folder.'/'.$row["filename"]) ? true : false ;
	$statsCount     = (IsAdmin || statistics) ? num_rows(Sql_query("SELECT 1 FROM `stats` WHERE `file_id`=$id")) : 0;
	
	return array('status'        => true,
	             'filename'      => $row['filename'] ,
				 'folder'        => $folder,
				 'folderId'      => $row['folderId'] ,
				 'fullpath'      => $folder.'/'.$row['filename'],
				 'deleteHash'    => $row['deleteHash'],
				 'user_id'       => $row['userId'],
				 'username'      => Sql_Get_Username($row['userId']),
				 'id'            => $row['id'],
				 'date'          => $row['uploadedDate'],
				 'download'      => $row['totalDownload'],
				 'size'          => $row['fileSize'],
				 'public'        => $row['isPublic'],
				 'ip'            => $row['uploadedIP'],
				 'password'      => $row['accessPassword'],
				 'orgfilename'   => $row['originalFilename'],
				 'thumbnail'     => $thumbnail,
				 'thumbnail_dir' => $thumbnail_dir,
				 'stats'         => $statsCount,
				 'isfile'        => $isFile
				 );
	} 
	else
	return array('status'        => false ,
                 'isfile'        => false 
				 );
mysqli_free_result($qry);
}

function Sql_Update_Count_Access($id)
{
$id   = (int)$id;
$time = timestamp();
return Sql_query("UPDATE `files` SET `totalDownload` = `totalDownload` + 1 , `last_access` ='$time' WHERE `id` ='$id';");	
}

function jsonSave($directory,$filename,$orgfilename,$file_id,$file_size,$last_access,$uploaded_date,$user_id)
{
	
 (!file_exists($directory.'/logs' )) ? @mkdir($directory.'/logs' , 0777, true) : '';
  $saveName = $directory.'/logs/deleted_files_'.date("Y_m").'.json' ;
  $filename = basename($filename) ;
  $json = @json_decode(file_get_contents($saveName),TRUE);  
  
  @$json['Files']['total']++;
  
  $json['Files'][$file_id]['file_id']   = $file_id;
  $json['Files'][$file_id]['file_size'] = $file_size;
  $json['Files'][$file_id]['user_id']   = $user_id;
  $json['Files'][$file_id]['orgfilename']   = $orgfilename;
  $json['Files'][$file_id]['last_access']   = $last_access;
  $json['Files'][$file_id]['uploaded_date'] = $uploaded_date;
  $json['Files'][$file_id]['delete_date']= timestamp();
  $json['Files'][$file_id]['delete_ip']  = iplong();
  file_put_contents($saveName, json_encode($json,TRUE));
}

function Sql_Get_Filename($id)
{
$id = (int)$id;
//return mysqli_fetch_assocSql_query("SELECT `filename` FROM `files` WHERE  `id`= '$id'"))['filename'];   
return fetch_assoc(Sql_query("SELECT `filename` FROM `files` WHERE  `id`= '$id'"),'filename');                		 
}

function Sql_Get_originalFilename($id)
{
$id = (int)$id; 
return fetch_assoc(Sql_query("SELECT `originalFilename` FROM `files` WHERE  `id`= '$id'"),'originalFilename');                		 
}

function Sql_file_exsist($filename)
{
$filename = protect($filename);
return num_rows(Sql_query("SELECT 1 FROM `files` WHERE  `filename`= '$filename'"));                		 
}

function Sql_Get_Reports_Count($file_id)
{
$file_id = (int)$file_id;
return num_rows(Sql_query("SELECT 1 FROM `reports` WHERE  `file_id`= '$file_id'"));                		 
}
function Sql_Get_Comments_Count($file_id)
{
$file_id = (int)$file_id;
return num_rows(Sql_query("SELECT 1 FROM `comments` WHERE  `file_id`= '$file_id'"));                		 
}


function Sql_Get_Username($id)
{
$id = (int)$id;
// return mysqli_fetch_assocSql_query("SELECT `username` FROM `users` WHERE  `id`= '$id'"))['username'];   
$user = fetch_assoc(Sql_query("SELECT `username` FROM `users` WHERE  `id`= '$id'"),'username');  
return ($user=='') ?  '/' : $user ;
}

function Sql_Get_Top_Downloads()
{
$data = array();
if ($result=Sql_query("SELECT `id` , `originalFilename` , `totalDownload` FROM `files` WHERE `isPublic` = '1' ORDER BY `totalDownload` desc LIMIT 1, ".rowsperpage)){
  while($row = mysqli_fetch_assoc($result))
  {

	  $_file_id   = $row["id"];
	  $data['file_'.$_file_id]['Filename']  = $row['originalFilename'] ;
	  $data['file_'.$_file_id]['Downloads'] = (int)$row['totalDownload'] ;
	  $data['file_'.$_file_id]['Url']       = '/?download='.Encrypt($_file_id)  ;

  }} 

if($result)
mysqli_free_result($result);
return $data ;
}
	

function Sql_Get_User_Plan_id($id)
{
$id = (int)$id; 
return fetch_assoc(Sql_query("SELECT `plan_id` FROM `users` WHERE  `id`= '$id'"),'plan_id');  
}

function Sql_Get_Username_id($username)
{	
$username = protect($username);
return fetch_assoc(Sql_query("SELECT `id` FROM `users` WHERE  `username`= '$username'"),'id');                     		 
}

function Sql_Get_Username_level($username)
{
$username = protect($username);
return fetch_assoc(Sql_query("SELECT `level` FROM `users` WHERE  `username`= '$username'"),'level');                   		 
}

function Sql_Get_Files_Count($_is_total = false)
{
	return $_is_total ? num_rows(Sql_query("SELECT 1 FROM `files`")) : num_rows(Sql_query("SELECT 1 FROM `files` WHERE `userId`='".UserID."'")) ;
}

function Sql_Get_Downloads_Count($_is_total = false)
{
	return $_is_total ? (int)fetch_assoc(Sql_query("SELECT SUM(`totalDownload`) AS `value_sum` FROM `files`"),"value_sum") : (int)fetch_assoc(Sql_query("SELECT SUM(`totalDownload`) AS `value_sum` FROM `files` WHERE `userId` = '".UserID."'"),"value_sum");
}


function Sql_Get_Files_user_Count($UserId)
{
	return num_rows(Sql_query("SELECT 1 FROM `files` WHERE `userId`='$UserId'")); 
}
	
function Sql_Get_Users_Count()
{
	return num_rows(Sql_query("SELECT 1 FROM `users`"));
}
// find out total pages

function Sql_totalpages($_is_total = false)
{
	$totalpages = ceil(Sql_Get_Files_Count($_is_total) / rowsperpage);
	return ($totalpages < 1) ? 1 : $totalpages  ;
}	

function Get_user_space_used($_User_ID = UserID )
{
return fetch_assoc(Sql_query("SELECT SUM(`fileSize`) AS `value_sum` FROM `files` WHERE `userId` = '".$_User_ID."'"),"value_sum");
}

function mysqlversion()
{
return fetch_row(Sql_query('SHOW VARIABLES LIKE "%version%";'),1);
}


function resetPassword($code)
{
	global $lang;
	$pass = GenerateRandomString();
	$md5p = md5($pass);
	$code = Decrypt(protect($code));
	
	if(num_rows(Sql_query("SELECT 1 FROM `users` WHERE `last_visit`='$code'"))>0)
	{
		
		$email  = fetch_assoc(Sql_query("SELECT `email` FROM `users` WHERE  `last_visit`='$code'"),'email'); 	
        $user   = fetch_assoc(Sql_query("SELECT `username` FROM `users` WHERE  `last_visit`='$code'"),'username'); 
		
		Sql_query("UPDATE `users` SET `password` = '$md5p' WHERE `last_visit`='$code';");
$message = $lang[72].' : '.siteurl.'/index.php <br> '.$lang[35].' . '.$user.' <br> '.$lang[37].' : '.$pass;
$subject = $lang[41].' - ( '.SiteName().' )';

// Always set content-type when sending HTML email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
// More headers
$headers .= 'Reply-To: '.sitemail . "\r\n" .
$headers .= 'X-Mailer: PHP/' . phpversion();

return @mail($email,$subject,$message,$headers);
		
	}	else return false;	
		 		
}

function LoadTimeZones()
{
	$_str_time_zones ='';
	require_once('timezones.php');
	foreach ($time_zones as $var) 
	{
	$_str_time_zones.='<option>'.$var.'</option>';
	}
	return $_str_time_zones;
}

function LoadLanguageCodes()
{
	$_str_language_codes ='';
	require_once('languagecodes.php');
	foreach ($language_codes as $key => $val) 
	{
	  $_str_language_codes.='<option value="'.$key.'">'.$val.'</option>';
	}
	return $_str_language_codes;
}

function GetLanguageCode($Language)
{
	require_once('languagecodes.php');
	return array_search($Language, $language_codes); 

	/*
	foreach ($language_codes as $key => $val) 
	{
		if($Language == $key)
			return $val ;
	}
	*/
}


function GetCountryCode($country,$Language = InterfaceLanguage)
{
	require('countrycodes.php');
	return (in_array($country, $countrycodes)) ? array_search($country, $countrycodes) : 'UN'; 
}

function GetCountryName($countryCode,$Language = InterfaceLanguage)
{
	require('countrycodes.php');
	return (array_key_exists($countryCode,$countrycodes)) ? $countrycodes[$countryCode] : 'Unknown';  
	/*
	$names = json_decode(file_get_contents('country_names.json'), true);
	$CountryName = $names[$countryCode];
	unset($names );
	return $CountryName ;*/
}

function  AJAX_request()
{
return ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')|| (IsIeBrowser()) )? true : false; 
}

function AJAX_check()
{ 
global $lang;
(!AJAX_request()) ?	PrintArray(array('error_msg' => $lang[99])) : '';
}



function IsIeBrowser()
{
return (preg_match('/(?i)msie [5-8]/',$_SERVER['HTTP_USER_AGENT'])) ? true : false ;
}
function translate()
{
global $lang;
return array('free'=>$lang[226],'premium'=>$lang[227],'gold'=>$lang[228],'register'=>$lang[55],'deletelink'=>$lang[26],'maxUploads'=>$lang[237],'days_older'=>$lang[236],'maxsize'=>$lang[24],'extensions'=>$lang[25],'Interval'=>$lang[78],'directdownload'=>$lang[51],'statistics'=>$lang[28],'userspacemax'=>$lang[173],'thumbnail'=>$lang[172],'display_ads'=>$lang[183],'price'=>$lang[231],'enable_userfolder'=>$lang[65],'speed'=>$lang[234],'multiple'=>$lang[248]);
}
function LoadUserSettings()
{
global $_plans;
if(defined('IsAdminPage') && IsAdminPage) return ;
$result = Sql_query("SELECT * FROM `plans`");
if($result)
while($row = mysqli_fetch_assoc($result))
{
	 $planStr = $_plans[PlanId] ;
	(!defined($row['name']) && (trim($row[$planStr]) !=='')) ? define($row['name'], $row[$planStr]) : ''; 
}
@mysqli_free_result($result);
}

function Loadconfig()
{ 

$result = Sql_query("SELECT `value`,`name`,`parameter` FROM `settings`");
if($result)
while($row = mysqli_fetch_assoc($result))
{
	(!defined($row['name'])) ? define($row['name'], $row['value']) : '';  //$_SESSION[$row['name']]   = $row['value'];
	(trim($row['parameter']) !== '' && !defined($row['name'].'_parameter')) ? define($row['name'].'_parameter', $row['parameter']) : ''; 
//echo $row['name']  .' = <code>'. $row['value'].'</code><br>';
}
	
		
/*---------------------------------*/				
if(defined('enable_userfolder') && enable_userfolder && IsLogin)
	define('uploadDir', FolderUploadName );
elseif(defined('folderupload'))
    define('uploadDir', folderupload);
/*---------------------------------*/	

/*---------------------------------*/
(defined('userspacemax')) ? define('user_space_max', return_bytes(userspacemax)) : '';
/*---------------------------------*/
(!defined('language')) ? define('language', Auto_detect_language()) : '';
/*---------------------------------*/
define('InterfaceLanguage', (language=='') ? Auto_detect_language() : language );
/*---------------------------------*/
$_maxFileSize   = function_exists('ini_get') ? return_bytes(@ini_get('upload_max_filesize')) : 0;
$_maxPostSize   = function_exists('ini_get') ? return_bytes(@ini_get('post_max_size')) : 0;
$_memory_limit  = function_exists('ini_get') ? return_bytes(@ini_get('memory_limit')) : 0;
$_maxsize       = (defined('maxsize'))       ? return_bytes(trim(maxsize)) : -1;
/*---------------------------------*/
//define('multiple',(defined('maxUploads') && maxUploads>0) ? true : false);
/*---------------------------------*/
define('MaxFileSize', ($_maxsize!==-1) ? min($_maxsize ,$_maxFileSize,$_maxPostSize , $_memory_limit) : min($_maxFileSize,$_maxPostSize , $_memory_limit) );
/*---------------------------------*/
@mysqli_free_result($result);

}

/**************************updateSite**********************/

function getUpdate($_url_json_file)
{ 
    global $lang;
	
    $results = json_decode( @Request($_url_json_file , true) ,true);
	
	$_json_no_error = true ;
	if(function_exists('json_last_error'))
	{
		if (json_last_error() === JSON_ERROR_NONE)
			$_json_no_error = true ;
		else
			$_json_no_error = false ;
	}
	 
	
	if ( $_json_no_error && $results !='' ) {
		
	$_get_version      = $results['settings']['version'];
    $_get_url          = $results['settings']['url'];
    $_get_lastupdate   = $results['settings']['lastupdate'];
	$_get_update_infos = "";
	
	$_SESSION['settings']['update']['url']        =  $_get_url ;
	$_SESSION['settings']['update']['version']    =  $_get_version ;
	$_SESSION['settings']['update']['lastupdate'] =  $_get_lastupdate ;
	
	
    $_get_update_infos.= $lang[135]." : <code>$_get_lastupdate </code><br>";
	$_get_update_infos.= $lang[136]." : <ul>";
	   


     foreach ($results['settings']['info_'.InterfaceLanguage] as $key => $value) 
              $_get_update_infos.= '<li>'.$value."</li>";
 
    unset($result);
    $_get_update_infos.="</ul>";
	
	$_get_update_infos ="<div class='editable'>$_get_update_infos</div><br>";
	
    $status_html="";
	
	if(IsAdmin) {
		
		
	if(scriptversion!==$_get_version)
      $status_html = '<div class="alert alert-success" >'.$lang[139].' <a href="?ZipArchive&update"  > <strong>'.$lang[140].' '.$_get_version.' </strong></a> ؟ </div>';
    else
	  $status_html = '<div class="alert alert-danger" >'.$lang[138].' </div>';
	}
	WriteHtaccessHomeFolder('..');
	return	(array('url' =>  $_get_url ,
	                     'version' => $_get_version , 
					     'lastupdate' => $_get_lastupdate,
					     'update_infos' => $_get_update_infos,
						 'status_html' => $status_html,
						 'status' => IsAdmin,
					 
					 )); //PrintArray
	

	}
} 


function delete_file($id,$deleteHash,$directory='..')
{
	//$id  = (is_numeric($id)) ? (int)$id : protect(Decrypt($id));
	$id  = protect(Decrypt($id));
	$info = Sql_Get_info($id);
    if( $info['status'] && $info['deleteHash'] == $deleteHash )
	{
	(file_exists($directory. $info["fullpath"] ))	   ? @unlink($directory. $info["fullpath"] ) : '';
	(file_exists($directory. $info["thumbnail_dir"] )) ? @unlink($directory. $info["thumbnail_dir"]  ) : '';
	Sql_Delete_File($id);
	Sql_Delete_Stat_File_Id($id);
	Sql_Delete_Report_File_Id($id);
	Sql_Delete_Comment_Id($id);
	IsLogin ? $_SESSION['login']['user_space_used'] = Get_user_space_used() : '';
	IsLogin ? $_SESSION['login']['user_space_left'] = user_space_max-(int)$_SESSION['login']['user_space_used'] : '';
	return true ;
	} else return false ;
}

function delete_file_older_than($isMember = false , $days=30 ,$directory='..')
{
	if($days ==0 || $days =='') return false ;
	
	$str = ($isMember) ? "" : "`userId`='0' AND" ;
	
	$successfully_deleted = '';
	
	$qry = Sql_query("SELECT `id`,`userId`, `filename`, `fileSize`, `uploadedDate`,`originalFilename`,`last_access` FROM `files` WHERE $str `last_access` < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL $days DAY))");
	
	if(num_rows($qry)>0)
	while($row = mysqli_fetch_assoc($qry))
	{
	   $id = $row['id'] ;
	   $successfully_deleted.=",$id" ;
	   delete_file_without_confirmation($id,$directory);
	   jsonSave($directory,$row['filename'],$row['originalFilename'],$id,$row['fileSize'],$row['last_access'],$row['uploadedDate'],$row['userId']);
	}
	
	define('SuccessfullyDeleted',$successfully_deleted) ;
	mysqli_free_result($qry);
}

function delete_file_without_confirmation($id,$directory='..')
{
	//$id  = (is_numeric($id)) ? (int)$id : protect(Decrypt($id));
	$id  = (int)$id;
	$info = Sql_Get_info($id);
    if( $info['status'])
	{
	(file_exists($directory. $info["fullpath"] ))	   ? @unlink($directory. $info["fullpath"] ) : '';
	(file_exists($directory. $info["thumbnail_dir"] )) ? @unlink($directory. $info["thumbnail_dir"]  ) : '';
	Sql_Delete_File($id);
	Sql_Delete_Stat_File_Id($id);
	Sql_Delete_Report_File_Id($id);
	Sql_Delete_Comment_Id($id);
	return true ;
	} else return false ;
}

function sqlUpdate($_url_json_sql)
{ 
    global $lang;
	
    $results = json_decode( @Request($_url_json_sql , true) ,true);
	
	$_json_no_error = true ;
	if(function_exists('json_last_error'))
		$_json_no_error = (json_last_error() === JSON_ERROR_NONE) ?  true : false ;
	
	
	 
	
	if ( $_json_no_error && $results !='' ) {
		
	$_get_version      = $results['settings']['version'];
    $_get_lastupdate   = $results['settings']['lastupdate'];
	$_get_update_infos = "";
	

	
	
    $_get_update_infos.= $lang[135]." : <code>$_get_lastupdate </code><br>";
	$_get_update_infos.= $lang[145]." : <ul>";
	   


     foreach ($results['settings']['mysqli'] as $key => $value) 
	 {
		 $result = $lang[179] ;
		 if(Sql_query(Decrypt($value)))
			 $result = $lang[178] ;
		 
		 $_get_update_infos.= '<li>'.$lang[79]." -> ".Decrypt($value)." -> $result</li>";
		 
	 }
              
 
    unset($result);
    $_get_update_infos.="</ul>";
	
	$_get_update_infos ="<div class='editable'>$_get_update_infos</div><br>";
	
    $status_html="";
	
	if(IsAdmin) {
		if(version!=$_get_version)
			$status_html = '<div class="alert alert-success" >'.$lang[79].' <strong>'.$lang[104].'</strong> </div>';
	}
	
	return	(array(      'version' => $_get_version , 
					     'lastupdate' => $_get_lastupdate,
					     'update_infos' => $_get_update_infos,
						 'status_html' => $status_html,
						 'status' => IsAdmin,
					 
					 )); //PrintArray
	

	}
} 
/***************************************************/

function extractUpdate($_url_zip_file,$_extractTo,$_temp_dir_zip)
{ 
    global $lang;
	
 if(!IsAdmin)
	 return array('status'=>false,'html'=> ''  );
 
 if (class_exists('ZipArchive')) 
   {

	@file_put_contents($_temp_dir_zip, @file_get_contents($_url_zip_file) );
	
	$zip = new ZipArchive;
if ($zip->open($_temp_dir_zip) === TRUE) 
{
    $zip->extractTo($_extractTo);
    
    $html= "<div class='editable'>".$lang[143]." :  <ul>";
	for ($i = 0; $i < $zip->numFiles; $i++) 
         $html.= '<li>'.$_extractTo.'<code>' . $zip->getNameIndex($i).'</code></li>';
    
	$html.= '</div><br>';
	$html.= '<div class="alert alert-success" >'.$lang[144].' <strong>'.$lang[104].'</strong></div>';
	$zip->close();
	
	@unlink($_temp_dir_zip);
	
	return array('status'=>false,'html'=> $html );
	
} 
else return array('status'=>false,'html'=> '<div class="alert alert-danger" ><strong>'.$lang[14].'</strong> '.$lang[141].'</div>' );

   } else return array('status'=>false,'html'=>'<div class="alert alert-danger" ><strong>'.$lang[14].'</strong> '.$lang[142].' </div>') ;
   
} 

/***************************************************/
function ext_is_image( $filename )
{
	$filename = basename($filename) ;
	$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
	return (in_array($ext , array('png' , 'jpg' ,'jpeg' , 'gif', 'bmp' ,'jpeg' , 'ico'))) ? true : false;
}

function is_image( $filename )
{
		
	if (!file_exists( $filename )) 
		return false ;
	
	if (filesize( $filename )>33554432) /*32MB*/
		return false ;
		
	if(function_exists('getimagesize'))
		$getimagesize = getimagesize( $filename );
	else
	{
		$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
		return (in_array($ext , array('png' , 'jpg' ,'jpeg' , 'gif', 'bmp' ,'jpeg' , 'ico'))) ? true : false;	
	}
	
	if ( is_array( $getimagesize ) )
		$image_type = $getimagesize[2]; 
	else
		return 	false ;
	
    return (in_array($image_type , array(IMAGETYPE_GIF , IMAGETYPE_JPEG ,IMAGETYPE_PNG , IMAGETYPE_BMP))) ? true : false ;
}

function forceView($id)
{	
@ini_set('memory_limit', '32M'); //max size 32m
//$id  = (is_numeric($id)) ? (int)$id : protect(Decrypt($id));
$id    = protect(Decrypt($id));
$info  = Sql_Get_info($id);

$file  = ($info['status'])     ?  '.'.$info["fullpath"]  : '' ; 
$file  = (!file_exists($file)) ?  './assets/css/images/notfound.png' : $file;
$file  = ($info['status'] && (!$info['public']) && ($info['user_id']!==UserID))  ?  './assets/css/images/notpublic.png' : $file;
$file  = ($info['status'] && ($info['password']!==''))  ?  './assets/css/images/accesspassword.png' : $file;

if(!ext_is_image($file)) return ;

$referrer = isGet('referrer') &&  !empty($_GET['referrer']) ? protect(Decrypt($_GET['referrer'])) : '' ;

($info['status'] && $info['public'] && (empty($info['password'])) ) ? Sql_Update_Count_Access( $info["id"] ) : '';
($info['status'] && $info['public'] && (empty($info['password'])) ) ? Sql_Insert_Stat( $info["id"] , $referrer) : '';

  //start buffered download

if (file_exists($file))
{
    $size = filesize($file);
    $fp = fopen($file, 'rb');
    if (($size>0) and $fp)
    {
        header('Content-Type: '.$size['mime']);
        header('Content-Length: '.filesize($file));
        while(!feof($fp) and (connection_status()==0)) {
              //reset time limit for big files
			  set_time_limit(0);
              print(fread($fp, 1024*8));
              flush();
              ob_flush();
        }
		fclose ($fp);
		function_exists('ini_get') ? set_time_limit(ini_get("max_execution_time")) : '';
        exit;
    }
}
}

function getReason($id)
{
	global $lang;
	return (in_array($id, array(1, 2, 3, 4))) ? $lang[200+$id] : '/';
}

function forceDownload($id)
{


//$id  = (is_numeric($id)) ? (int)$id : protect(Decrypt($id));
$id     = protect(Decrypt($id));
$string = (isGet('unq')) ? protect($_GET['unq'])	: '' ;
	 	
(!isset($_SESSION['settings']['files'][$id])) ? exit(header("HTTP/1.0 404 Not Found")) : '';


if(isset($_SESSION['settings']['files'][$id]))
	( $_SESSION['settings']['files'][$id] !== $string ) ? exit(header("HTTP/1.0 404 Not Found")) : '';



$info     = Sql_Get_info($id);

$filename = ($info['status'])    ?  '.'.$info["fullpath"]  : '' ; 
$filesize = ($info['status'])    ?  '.'.$info["size"]  : '' ; 
$orgfilename = ($info['status']) ?  $info['orgfilename'] : ''; 
$fileSize = ($info['status'])    ?  $info['size'] : filesize($filename); 

( !file_exists($filename) ) ? exit(header("Location:./?download=".Encrypt($id))) : '';

if( function_exists('ini_get') && function_exists('ini_set') && @ini_get('zlib.output_compression') )
	@ini_set('zlib.output_compression', 'Off');

require_once ('downloader.php');
ob_end_clean();
				$object = new downloader;
				$object->set_byfile($filename);
				$object->set_szParameter = szParameter;
				$object->set_filesize = $filesize;
				$object->use_resume = true;
				$object->filename = $orgfilename;
				$object->speed =  return_Kilobyte (speed) ;
				$object->download();
				exit;
/*


$pathinfo = pathinfo($filename);

$fileExtension = isset($pathinfo['extension']) ? '.'.strtolower($pathinfo['extension']) : '.'.strtolower(substr(strrchr($filename,"."),1)) ;

require_once ('mimetypes.php');
$mime = $mime_types[$fileExtension] ;

//2- Check for request, is the client support this method?
if (isset($_SERVER['HTTP_RANGE']) || isset($HTTP_SERVER_VARS['HTTP_RANGE'])) {
    $ranges_str = (isset($_SERVER['HTTP_RANGE']))?$_SERVER['HTTP_RANGE']:$HTTP_SERVER_VARS['HTTP_RANGE'];
    $ranges_arr = explode('-', substr($ranges_str, strlen('bytes=')));
    //Now its time to check the ranges
    if ((intval($ranges_arr[0]) >= intval($ranges_arr[1]) && $ranges_arr[1] != "" && $ranges_arr[0] != "" ) || ($ranges_arr[1] == "" && $ranges_arr[0] == "")) {
        //Just serve the file normally request is not valid :( 
        $ranges_arr[0] = 0;
        $ranges_arr[1] = $fileSize - 1;
    }
} else { //The client dose not request HTTP_RANGE so just use the entire file
    $ranges_arr[0] = 0;
    $ranges_arr[1] = $fileSize - 1;
}
//Now its time to serve file 
$file = fopen($filename, 'rb');
$start = $stop = 0;
if ($ranges_arr[0] === "") { //No first range in array
    //Last n1 byte
    $stop = $fileSize - 1;
    $start = $fileSize - intval($ranges_arr[1]);
} elseif ($ranges_arr[1] === "") { //No last
    //first n0 byte
    $start = intval($ranges_arr[0]);
    $stop = $fileSize - 1;
} else {
    // n0 to n1
    $stop = intval($ranges_arr[1]);
    $start = intval($ranges_arr[0]);
}    
//Make sure the range is correct by checking the file
fseek($file, $start, SEEK_SET);
$start = ftell($file);
fseek($file, $stop, SEEK_SET);
$stop = ftell($file);
$data_len = $stop - $start;
//Lets send headers 
if (isset($_SERVER['HTTP_RANGE']) || isset($HTTP_SERVER_VARS['HTTP_RANGE'])) {
    header('HTTP/1.0 206 Partial Content');
    header('Status: 206 Partial Content');
}
header('Accept-Ranges: bytes');
header("Content-type: $mime");
header('Content-Disposition: attachment; filename="'.$orgfilename.'";' );
header("Content-Range: bytes $start-$stop/" . $fileSize );
header("Content-Length: " . ($data_len + 1));
//Finally serve data and done ~!
fseek($file, $start, SEEK_SET);
$bufsize = 2048000;
ignore_user_abort(true);
@set_time_limit(0);
while (!(connection_aborted() || connection_status() == 1) && $data_len > 0) {
    echo fread($file, $bufsize);
    $data_len -= $bufsize;
    flush();
}
fclose($file);
exit();

*/
}


function IsRtL()
{
	$rtlLanguages  = array('ar','arc','bcc','bqi','ckb','dv','fa','glk','he','lrc','mzn','pnb','ps','sd','ug','ur','yi');
	return in_array(InterfaceLanguage,$rtlLanguages) ? true : false; 	
}

function LoadFont($dir='.')
{
	if(InterfaceLanguage=='ar')
	{
		
	}
}


	
?>