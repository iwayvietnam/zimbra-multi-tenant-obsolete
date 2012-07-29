<?php
/*
 * PHP Checker
 * ZAAS project
 *
 * 
 */
//ini_set('display_errors',1);

$urli = 'hlp007.com';		// URL of Drual Server
$url = '/dp/';
$secret = '123456az';	// Key pass when connect to Drupal server
$domain = 'zaasmhst.com,';	// Domain of this server
$server = '119.15.161.24';	// Name of this server

$url2 = $url.'sv/get/'.$server.'/'.$secret;
include_once('HttpClient.class.php');
echo 'Connect to '.$urli.'<br>';
$client = new HttpClient($urli);
//$client->setMaxRedirects(20);
//$client->setDebug(true);
$client->handle_redirects =false;
//$client->get($url);
//$client->handle_redirects =true;
//$client->setHost($urli);
$client->get($url2);
$str = $client->getContent();

$xml = @simplexml_load_string($str);
if( empty($xml) ) {
echo 	$this->error  .= 'Khong the tim duoc nguon du lieu, vui long lien lac voi trung tam cham soc khach hang';
}
else {
		if( empty($xml->command) ) {
			echo 'Khong co lenh';
		}
		else {
			echo '<br><br>Lenh:<br><pre>';
			$str_id = '';
			$str_com= '';
			foreach ($xml->command as $value) {
					echo '<br>'.$value->id.': '.$value->request.': Ket qua<br><br>';
				$str_id .= $value->id.',';
				if ( substr($value->request,0,6 ) == '-l gaa') {
					$str_com_str = 'for i in `/opt/zimbra/bin/./zmprov '.$value->request.'`; do /opt/zimbra/bin/./zmprov ga $i displayName;done';
					$str_com .= shell_exec($str_com_str).'::zaasmhst::';
				}
				elseif ( substr($value->request,0,5 ) == 'alias') {
					$str_com_str = 'for i in `/opt/zimbra/bin/./zmprov '.substr($value->request,6 ).'`; do echo ""; echo "$i:";/opt/zimbra/bin/./zmprov ga $i | grep MailAlias; done';
					$str_com .= shell_exec($str_com_str).'::zaasmhst::';
				}
				else	$str_com .= shell_exec('/opt/zimbra/bin/./zmprov '.$value->request).'::zaasmhst::';
			}
			echo 'Execute command...<br>';
			$data = array('error'=>'0','result'=>$str_com,'id'=>$str_id);
			echo $str_com.'<br>';
			$url3 = $url.'sv/return/'.$server.'/'.$secret;
			$client->post($url3,$data);
			$str = $client->getContent();
			echo 'Send result to '.$url3.'<br/>';
			echo $str;
		}
}


