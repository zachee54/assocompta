<?php
/**
 * FlashComponentTest file
 *
 * Series of tests for flash component.
 *
 * CakePHP(tm) Tests <https://book.cakephp.org/2.0/en/development/testing.html>
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://book.cakephp.org/2.0/en/development/testing.html CakePHP(tm) Tests
 * @package       Cake.Test.Case.Controller.Component
 * @since         CakePHP(tm) v 2.7.0-dev
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace lib\Cake\Test\Case\Controller\Component;



/**
 * FlashComponentTest class
 *
 * @package		Cake.Test.Case.Controller.Component
 */
class FlashComponentTest extends TestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Components = new ComponentRegistry();
		$this->Flash = new FlashComponent($this->Components);
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		Session::destroy();
	}

/**
 * testSet method
 *
 * @return void
 */
	public function testSet() {
		$this->assertNull(Session::read('Message.flash'));

		$this->Flash->set('This is a test message');
		$expected = array(
			array(
				'message' => 'This is a test message',
				'key' => 'flash',
				'element' => 'Flash/default',
				'params' => array()
			)
		);
		$result = Session::read('Message.flash');
		$this->assertEquals($expected, $result);
		Session::delete('Message.flash');

		$this->Flash->set('This is the first message');
		$this->Flash->set('This is the second message');
		$expected = array(
			array(
				'message' => 'This is the first message',
				'key' => 'flash',
				'element' => 'Flash/default',
				'params' => array()
			),
			array(
				'message' => 'This is the second message',
				'key' => 'flash',
				'element' => 'Flash/default',
				'params' => array()
			)
		);
		$result = Session::read('Message.flash');
		$this->assertEquals($expected, $result);
		Session::delete('Message.flash');

		$this->Flash->set('This is a test message', array(
			'element' => 'test',
			'params' => array('foo' => 'bar')
		));
		$expected = array(
			array(
				'message' => 'This is a test message',
				'key' => 'flash',
				'element' => 'Flash/test',
				'params' => array('foo' => 'bar')
			)
		);
		$result = Session::read('Message.flash');
		$this->assertEquals($expected, $result);
		Session::delete('Message.flash');

		$this->Flash->set('This is a test message', array('element' => 'MyPlugin.alert'));
		$expected = array(
			array(
				'message' => 'This is a test message',
				'key' => 'flash',
				'element' => 'MyPlugin.Flash/alert',
				'params' => array()
			)
		);
		$result = Session::read('Message.flash');
		$this->assertEquals($expected, $result);
		Session::delete('Message.flash');

		$this->Flash->set('This is a test message', array('key' => 'foobar'));
		$expected = array(
			array(
				'message' => 'This is a test message',
				'key' => 'foobar',
				'element' => 'Flash/default',
				'params' => array()
			)
		);
		$result = Session::read('Message.foobar');
		$this->assertEquals($expected, $result);
		Session::delete('Message.foobar');

		$this->Flash->set('This is the first message');
		$this->Flash->set('This is the second message', array('clear' => true));
		$expected = array(
			array(
				'message' => 'This is the second message',
				'key' => 'flash',
				'element' => 'Flash/default',
				'params' => array()
			)
		);
		$result = Session::read('Message.flash');
		$this->assertEquals($expected, $result);
		Session::delete('Message.flash');
	}

/**
 * testSetWithException method
 *
 * @return void
 */
	public function testSetWithException() {
		$this->assertNull(Session::read('Message.flash'));

		$this->Flash->set(new Exception('This is a test message', 404));
		$expected = array(
			array(
				'message' => 'This is a test message',
				'key' => 'flash',
				'element' => 'Flash/default',
				'params' => array('code' => 404)
			)
		);
		$result = Session::read('Message.flash');
		$this->assertEquals($expected, $result);
		Session::delete('Message.flash');
	}

/**
 * testSetWithComponentConfiguration method
 *
 * @return void
 */
	public function testSetWithComponentConfiguration() {
		$this->assertNull(Session::read('Message.flash'));

		$FlashWithSettings = $this->Components->load('Flash', array('element' => 'test'));
		$FlashWithSettings->set('This is a test message');
		$expected = array(
			array(
				'message' => 'This is a test message',
				'key' => 'flash',
				'element' => 'Flash/test',
				'params' => array()
			)
		);
		$result = Session::read('Message.flash');
		$this->assertEquals($expected, $result);
		Session::delete('Message.flash');
	}

/**
 * Test magic call method.
 *
 * @return void
 */
	public function testCall() {
		$this->assertNull(Session::read('Message.flash'));

		$this->Flash->success('It worked');
		$expected = array(
			array(
				'message' => 'It worked',
				'key' => 'flash',
				'element' => 'Flash/success',
				'params' => array()
			)
		);
		$result = Session::read('Message.flash');
		$this->assertEquals($expected, $result);
		Session::delete('Message.flash');

		$this->Flash->alert('It worked', array('plugin' => 'MyPlugin'));
		$expected = array(
			array(
				'message' => 'It worked',
				'key' => 'flash',
				'element' => 'MyPlugin.Flash/alert',
				'params' => array()
			)
		);
		$result = Session::read('Message.flash');
		$this->assertEquals($expected, $result);
		Session::delete('Message.flash');

		$this->Flash->error('It did not work', array('element' => 'error_thing'));
		$expected = array(
			array(
				'message' => 'It did not work',
				'key' => 'flash',
				'element' => 'Flash/error',
				'params' => array()
			)
		);
		$result = Session::read('Message.flash');
		$this->assertEquals($expected, $result, 'Element is ignored in magic call.');
		Session::delete('Message.flash');
	}
}
