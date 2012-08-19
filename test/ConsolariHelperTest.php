<?php
include_once '../ConsolariHelper.php';

class ConsolariHelperTest extends PHPUnit_Framework_TestCase{
	
	public function testSetKey(){
		$key = 'xxx123';
		
		ConsolariHelper::SetKey($key);
		
		$this->assertEquals($key, ConsolariHelper::GetKey());
	}
	
	public function testInitialSetLog(){
		$logs = ConsolariHelper::GetLogs();
		
		$this->assertTrue(empty($logs));
	}
	
	public function testConst(){
		$this->assertEquals('sql', ConsolariHelper::SQL);
		$this->assertEquals('html', ConsolariHelper::HTML);
		$this->assertEquals('xml', ConsolariHelper::XML);
		$this->assertEquals('array', ConsolariHelper::ARR);
		$this->assertEquals('string', ConsolariHelper::STRING);
	}
	
	public function testSetLogGroup(){
		$logs = ConsolariHelper::GetLogs();
		
		$this->assertTrue(empty($logs));
		
		ConsolariHelper::add('test', 'test value', 'test label');
		
		$logs = ConsolariHelper::GetLogs();
		
		$this->assertTrue(isset($logs['test']));
		$this->assertEquals('test', $logs['test']['label']);
		$this->assertCount(1, $logs['test']['entries']);
	}
	
	public function testLogWithStringEntry(){
		
		ConsolariHelper::add('default', 'test value', 'test label', ConsolariHelper::STRING);
		
		$logs = ConsolariHelper::GetLogs();
		
		$this->assertTrue(isset($logs['default']));
		
		$log = array_pop($logs['default']['entries']);
		
		$this->assertEquals(ConsolariHelper::STRING, $log['type']);
		$this->assertEquals('test value', $log['value']);
	}
	
	public function testLogWithArrayEntry(){
		
		ConsolariHelper::add('default', array(0=>'value1', 1=>'value2'), 'test label', ConsolariHelper::ARR);
		
		$logs = ConsolariHelper::GetLogs();
		
		$this->assertTrue(isset($logs['default']));
		
		$log = array_pop($logs['default']['entries']);
		
		$this->assertEquals(ConsolariHelper::ARR, $log['type']);
		$this->assertTrue(is_array($log['value']));
	}
	
	public function testLogWithSqlEntry(){
		
		ConsolariHelper::add('default', 'select * from test', 'test label', ConsolariHelper::SQL);
		
		$logs = ConsolariHelper::GetLogs();
		
		$this->assertTrue(isset($logs['default']));
		
		$log = array_pop($logs['default']['entries']);
		
		$this->assertEquals(ConsolariHelper::SQL, $log['type']);
		$this->assertEquals('select * from test', $log['value']);
	}
	
	public function testMergeString(){
		
		ConsolariHelper::add('string-merge', 'string1', 'test label', ConsolariHelper::STRING);
		
		$logs = ConsolariHelper::GetLogs();
		
		$this->assertTrue(isset($logs['string-merge']));
		
		$log = array_pop($logs['string-merge']['entries']);
		
		$this->assertEquals('string1', $log['value']);
		
		//merge with second string
		ConsolariHelper::merge('string-merge', 'string2', 'test label', ConsolariHelper::STRING);
		
		$logs = ConsolariHelper::GetLogs();
		
		$log = array_pop($logs['string-merge']['entries']);
		
		$this->assertEquals('string1string2', $log['value']);
	}
	
	
}
?>