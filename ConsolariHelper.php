<?php
/**
 * Consolari helper
 *
 */
class ConsolariHelper{
    private static $instance;
    public static $logs;
    private static $url = 'http://consolari.localhost';
    public static $userKey = '';
    
    const SQL = 'sql';
    const XML = 'xml';
    const HTML = 'html';
    const ARR = 'array';
    const STRING = 'string';
    
    /**
     * Private constructor as class is of pattern singleton
     */
    private function __construct(){
    	
    }
    
    /**
     * Return the obj instance of class
     */
    public static function GetInstance(){
        if (!isset(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c;
        }

        return self::$instance;
    }
    
    /**
     * Set the user key to Consolari
     * 
     * @param string $key
     */
    public function SetKey($key = ''){
    	self::$userKey = $key;
    }
    
    public function GetKey(){
    	return self::$userKey;
    }
    
    public function GetLogs(){
    	return self::$logs;
    }
    
    public function add($groupName = 'default', $value = '', $label = '', $type = 'string'){

    	$data = array(
    		'label'=>$label,
    		'value'=>$value,
    		'type'=>$type,
    		'time'=>time(),
    	);
    	
   		self::$logs[$groupName]['label'] = $groupName;
   		self::$logs[$groupName]['entries'][] = $data;
    }
    
    public function merge($groupName = 'default', $value = '', $label = '', $type = 'string'){

    	$match = false;
    	foreach(self::$logs[$groupName]['entries'] as $key=>$entry){
    		if($entry['label'] == $label){
    			
    			if($type == self::STRING){
    				self::$logs[$groupName]['entries'][$key]['value'] .= $value;	
    			}elseif ($type == self::SQL){
    				self::$logs[$groupName]['entries'][$key]['value'][] = array(
    					'sql'=>$value,
    					'time'=>time(),
    				);
    			}elseif($type == self::ARR){
    				self::$logs[$groupName]['entries'][$key]['value'][] = $value;
    			}elseif($type == self::XML){
    				self::$logs[$groupName]['entries'][$key]['value'] .= $value;
    			}elseif($type == self::HTML){
    				self::$logs[$groupName]['entries'][$key]['value'] .= $value;
    			}
    			
    			$match = true;
    			break;
    		}
    	}
    	
    	if(!$match){
			self::add($groupName, $value, $label, $type);    		
    	}
    }
    
    public function __destruct(){
    	self::SendLog();
    }
    
    public function SendLog(){
    	$data = array();
    	
    	$data['report']= array(
			'key'=>self::$userKey,
			'source'=>$_SERVER['HTTP_HOST'],
			'url'=>$_SERVER['REQUEST_URI'],
		);
		
		$data['groups'] = self::$logs;
		
		self::Post(self::$url.'/reports/save', $data);
    }
    
	public function Post($url='' , $fields = array()){
		
		$fields_string = http_build_query($fields);
		
		$ch = curl_init();
		
		curl_setopt($ch,CURLOPT_URL, $url);
		curl_setopt($ch,CURLOPT_POST, count($fields));
		curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); 
		
		$result = curl_exec($ch);
		
		curl_close($ch);
		
		return $result;
	}
}
?>