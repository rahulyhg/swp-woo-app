<?php
require_once( 'class-wooconnector-core-mobile-detect.php' );
class Wooconnector_Detect{
	public function output($customer_userAgent){
		$detect = new WooConnector_Mobile_Detect;
		$userAgent = $customer_userAgent;
		if(empty($userAgent)){
			return null;
		}
		$detect->setUserAgent($userAgent);
		$header = $detect->getHttpHeaders();
		$br = "";
		$de = "";
		$os = "";
		if($detect->isMobile($userAgent,$header)){
			$browsers = $detect->getBrowsers();
			$devices = $detect->getPhoneDevices();
			$listOs = $detect->getOperatingSystems();
			foreach($browsers as $browser => $value){
				$value = strtolower($value);
				if($detect->match($value,$userAgent)){
					$br = $browser;
				}
			}
			foreach($devices as $device => $value){
				$value = strtolower($value);
				if($detect->match($value,$userAgent)){
					$de = $device;
				}		
			}
			foreach($listOs as $los => $value){
				$value = strtolower($value);
				if($detect->match($value,$userAgent)){
					$os = $los;
				}		
			}	
			$keyos = $this->getKeyOs($os);
			$versionbrowser = $detect->version($br);	
			$versionos = $detect->version($keyos);
			return array(
				'tb' => 'Mobile',
				'browser' => $br,
				'device' => $de,
				'os' => $os,
				'version' => $versionbrowser,
				'version-os' => $versionos
			);
		}elseif($detect->isTablet($userAgent,$header)){
			$browsers = $detect->getBrowsers();
			$devices = $detect->getTabletDevices();
			$listOs = $detect->getOperatingSystems();
			foreach($browsers as $browser => $value){
				$value = strtolower($value);
				if($detect->match($value,$userAgent)){
					$br = $browser;
				}
			}
			foreach($devices as $device => $value){
				$value = strtolower($value);
				if($detect->match($value,$userAgent)){
					$de = $device;
				}		
			}
			foreach($listOs as $los => $value){
				$value = strtolower($value);
				if($detect->match($value,$userAgent)){
					$os = $los;
				}		
			}	
			$keyos = $this->getKeyOs($os);
			$versionbrowser = $detect->version($br);	
			$versiondevice = $detect->version($de);
			$versionos = $detect->version($keyos);
			return array(
				'tb' => 'Table',
				'browser' => $br,
				'device' => $de,
				'os' => $os,
				'version' => $versionbrowser,
				'version-device' => $versiondevice,
				'version-os' => $versionos
			);
		}
		else{			
			return $this->getBrowser($userAgent);
		}
	}	

	private function getBrowser($userAgent){ 
		$u_agent = $userAgent; 
		$bname = 'Unknown';
		$platform = 'Unknown';
		$version= "";
		$argss = array(
			'/windows nt 10.0/i'    =>  'Windows 10',
			'/windows nt 6.2/i'     =>  'Windows 8',
            '/windows nt 6.1/i'     =>  'Windows 7',
            '/windows nt 6.0/i'     =>  'Windows Vista',
            '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
            '/windows nt 5.1/i'     =>  'Windows XP',
            '/windows xp/i'         =>  'Windows XP',
            '/windows nt 5.0/i'     =>  'Windows 2000',
            '/windows me/i'         =>  'Windows ME',
            '/win98/i'              =>  'Windows 98',
            '/win95/i'              =>  'Windows 95',
            '/win16/i'              =>  'Windows 3.11',
            '/macintosh|mac os x/i' =>  'Mac OS X',
            '/mac_powerpc/i'        =>  'Mac OS 9',
            '/linux/i'              =>  'Linux',
            '/ubuntu/i'             =>  'Ubuntu',          
		);
		foreach($argss as $args => $value){
			if(preg_match($args,$u_agent)){
				$platform = $value;
			}
		}		
		$browsers = array(
			'/msie/i'       =>  'Internet Explorer',
            '/firefox/i'    =>  'Firefox',
            '/safari/i'     =>  'Safari',
            '/chrome/i'     =>  'Chrome',
            '/opera/i'      =>  'Opera',
            '/netscape/i'   =>  'Netscape',
            '/maxthon/i'    =>  'Maxthon',
            '/konqueror/i'  =>  'Konqueror',
            '/mobile/i'     =>  'Handheld Browser'
		);
		foreach($browsers as $browser => $value){
			if(preg_match($browser,$u_agent)){ 
				$bname = strtolower($value); 				
			} 
		}
		
		// finally get the correct version number
		$known = array('version', $bname, 'other');
		$pattern = '#(?<browser>' . join('|', $known) .
		')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
		if (!preg_match_all($pattern, $u_agent, $matches)) {
			// we have no matching number just continue
		}
		
		// see how many we have
		$i = count($matches['browser']);
		if ($i != 1) {
			if(!empty($matches['version'])){
				//we will have two since we are not using 'other' argument yet
				//see if version is before or after the name
				if (strripos($u_agent,"version") < strripos($u_agent,$bname)){
					$version= $matches['version'][0];
				}
				else {
					$version= $matches['version'][1];
				}
			}			
		}
		else {
			$version= $matches['version'][0];
		}
		
		// check if we have a number
		if ($version==null || $version=="") {$version="?";}
		
		return array(			
			'browser'      => ucfirst($bname),
			'version'   => $version,
			'device'  => $platform,			
		);
	} 
	
	private function getKeyOs($codes){
		if($codes == 'WindowsMobileOS'){
			return "Mobile";
		}elseif($codes == 'WindowsPhoneOS'){
			return "Windows Phone OS";
		}elseif($codes == 'webOS'){
			return $codes;
		}elseif($codes == "iOS"){
			return $codes;
		}else{
			$out = str_replace("OS","",$codes);
			return $out;
		}
	}
}	

?>