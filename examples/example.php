<?php

require_once(dirname(__DIR__).'/vendor/autoload.php');

use Cunchu\Cunchu;
use Cunchu\CunchuException;

$scalaris = new Cunchu([
	'host'=>'localhost',
	'port'=>8000
	//'debug'=>true
]);

$start = microtime(true);
try{
	// SET VALUES
	echo 'Setting values for key1,key2'.PHP_EOL;
	if($scalaris->set(array(
		'key1'=>'value1',
		'key2'=>'value2'
	))){
		echo 'SUCCESS'.PHP_EOL.PHP_EOL;
	}

	// GET VALUES
	echo 'Getting values for key1,key2'.PHP_EOL;
	var_dump($scalaris->get(array('key1','key2')));

	// THIS WILL FAIL
	echo 'This request will fail because key5 does not exist'.PHP_EOL;
	var_dump($scalaris->get(array('key5')));

}catch(CunchuException $e){
	echo $e->getMessage().PHP_EOL;
	var_dump($scalaris->lastResponse);
}

echo PHP_EOL.'FINISHED IN: '.(microtime(true)-$start).PHP_EOL;
