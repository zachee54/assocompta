<?php


namespace lib\Cake\Test\test_app\Controller;

class TestConfigsController extends CakeErrorController {

	public $components = array(
		'RequestHandler' => array(
			'some' => 'config'
		)
	);

}
