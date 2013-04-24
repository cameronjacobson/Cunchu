<?php

namespace Cunchu;

use \SimpleHttpClient\SimpleHttpClient;
use \SimpleJsonRPC2\SimpleJsonRPC2;

class Cunchu
{
	private $client;
	private $keys;

	public function __construct(Array $options){
		$options['contentType'] = 'application/json';
		$this->client = new SimpleJsonRPC2(
			new SimpleHttpClient($options)
		);
	}

	public function get(Array $keys, $id = 100){
		$this->keys = $keys;
		$result = $this->client->request('/api/tx.yaws', array(
			'method'=>'req_list',
			'params'=>$this->setParams('read', $keys),
			'id'=>$id
		));
		return $this->formatResult('read',$result);
	}

	public function set(Array $values, $id = 100){
		$result = $this->client->request('/api/tx.yaws', array(
			'method'=>'req_list',
			'params'=>$this->setParams('write', $values),
			'id'=>$id
		));
		return $this->formatResult('write',$result);
	}

	private function setParams($type, Array $data){
		$params = array();
		switch($type){
			case 'read':
				foreach($data as $key){
					$params[] = array('read'=>$key);
				}
				break;
			case 'write':
				foreach($data as $key=>$value){
					$params[] = array('write'=>array(
						$key=>array('type'=>'as_is', 'value'=>$value)
					));
				}
				$params[] = array('commit'=>'');
				break;
		}
		return array($params);
	}

	private function formatResult($type, Array $results){
		$this->lastResponse = $results['result']['results'];
		$return = array();
		foreach($results['result']['results'] as $key=>$result){
			if($result['status'] !== 'ok'){
				switch($result['reason']){
					case 'not_found':
						break;
					default:
						throw new CunchuException('part or all of the request failed');
						break;
				}
			}
			switch($type){
				case 'read':
					$return[$this->keys[$key]] = @$result['value']['value'] ?: null;
					break;
				case 'write':
				default:
					break;
			}
		}
		return empty($return) ? true : $return;
	}
}
