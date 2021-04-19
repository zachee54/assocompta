<?php
/**
 * CakeLogTest file
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
 * @package       Cake.Test.Case.Log
 * @since         CakePHP(tm) v 1.2.0.5432
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace lib\Cake\Test\Case\Log;



/**
 * CakeLogTest class
 *
 * @package       Cake.Test.Case.Log
 */
class CakeLogTest extends TestCase {

/**
 * Start test callback, clears all streams enabled.
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$streams = Log::configured();
		foreach ($streams as $stream) {
			Log::drop($stream);
		}
	}

/**
 * test importing loggers from app/libs and plugins.
 *
 * @return void
 */
	public function testImportingLoggers() {
		App::build(array(
			'Lib' => array(CAKE . 'Test' . DS . 'test_app' . DS . 'Lib' . DS),
			'Plugin' => array(CAKE . 'Test' . DS . 'test_app' . DS . 'Plugin' . DS)
		), App::RESET);
		Plugin::load('TestPlugin');

		$result = Log::config('libtest', array(
			'engine' => 'TestAppLog'
		));
		$this->assertTrue($result);
		$this->assertEquals(Log::configured(), array('libtest'));

		$result = Log::config('plugintest', array(
			'engine' => 'TestPlugin.TestPluginLog'
		));
		$this->assertTrue($result);
		$this->assertEquals(Log::configured(), array('libtest', 'plugintest'));

		Log::write(LOG_INFO, 'TestPluginLog is not a BaseLog descendant');

		App::build();
		Plugin::unload();
	}

/**
 * test all the errors from failed logger imports
 *
 * @expectedException CakeLogException
 * @return void
 */
	public function testImportingLoggerFailure() {
		Log::config('fail', array());
	}

/**
 * test config() with valid key name
 *
 * @return void
 */
	public function testValidKeyName() {
		Log::config('valid', array('engine' => 'File'));
		$stream = Log::stream('valid');
		$this->assertInstanceOf('FileLog', $stream);
		Log::drop('valid');
	}

/**
 * test config() with valid key name including the deprecated Log suffix
 *
 * @return void
 */
	public function testValidKeyNameLogSuffix() {
		Log::config('valid', array('engine' => 'FileLog'));
		$stream = Log::stream('valid');
		$this->assertInstanceOf('FileLog', $stream);
		Log::drop('valid');
	}

/**
 * test config() with invalid key name
 *
 * @expectedException CakeLogException
 * @return void
 */
	public function testInvalidKeyName() {
		Log::config('1nv', array('engine' => 'File'));
	}

/**
 * test that loggers have to implement the correct interface.
 *
 * @expectedException CakeLogException
 * @return void
 */
	public function testNotImplementingInterface() {
		Log::config('fail', array('engine' => 'stdClass'));
	}

/**
 * Test that Log does not auto create logs when no streams are there to listen.
 *
 * @return void
 */
	public function testNoStreamListenting() {
		if (file_exists(LOGS . 'error.log')) {
			unlink(LOGS . 'error.log');
		}
		$res = Log::write(LOG_WARNING, 'Test warning');
		$this->assertFalse($res);
		$this->assertFalse(file_exists(LOGS . 'error.log'));

		$result = Log::configured();
		$this->assertEquals(array(), $result);
	}

/**
 * test configuring log streams
 *
 * @return void
 */
	public function testConfig() {
		Log::config('file', array(
			'engine' => 'File',
			'path' => LOGS
		));
		$result = Log::configured();
		$this->assertEquals(array('file'), $result);

		if (file_exists(LOGS . 'error.log')) {
			unlink(LOGS . 'error.log');
		}
		Log::write(LOG_WARNING, 'Test warning');
		$this->assertTrue(file_exists(LOGS . 'error.log'));

		$result = file_get_contents(LOGS . 'error.log');
		$this->assertRegExp('/^2[0-9]{3}-[0-9]+-[0-9]+ [0-9]+:[0-9]+:[0-9]+ Warning: Test warning/', $result);
		unlink(LOGS . 'error.log');
	}

/**
 * explicit tests for drop()
 *
 * @return void
 */
	public function testDrop() {
		Log::config('file', array(
			'engine' => 'File',
			'path' => LOGS
		));
		$result = Log::configured();
		$this->assertEquals(array('file'), $result);

		Log::drop('file');
		$result = Log::configured();
		$this->assertSame(array(), $result);
	}

/**
 * testLogFileWriting method
 *
 * @return void
 */
	public function testLogFileWriting() {
		Log::config('file', array(
			'engine' => 'File',
			'path' => LOGS
		));
		if (file_exists(LOGS . 'error.log')) {
			unlink(LOGS . 'error.log');
		}
		$result = Log::write(LOG_WARNING, 'Test warning');
		$this->assertTrue($result);
		$this->assertTrue(file_exists(LOGS . 'error.log'));
		unlink(LOGS . 'error.log');

		Log::write(LOG_WARNING, 'Test warning 1');
		Log::write(LOG_WARNING, 'Test warning 2');
		$result = file_get_contents(LOGS . 'error.log');
		$this->assertRegExp('/^2[0-9]{3}-[0-9]+-[0-9]+ [0-9]+:[0-9]+:[0-9]+ Warning: Test warning 1/', $result);
		$this->assertRegExp('/2[0-9]{3}-[0-9]+-[0-9]+ [0-9]+:[0-9]+:[0-9]+ Warning: Test warning 2$/', $result);
		unlink(LOGS . 'error.log');
	}

/**
 * test selective logging by level/type
 *
 * @return void
 */
	public function testSelectiveLoggingByLevel() {
		if (file_exists(LOGS . 'spam.log')) {
			unlink(LOGS . 'spam.log');
		}
		if (file_exists(LOGS . 'eggs.log')) {
			unlink(LOGS . 'eggs.log');
		}
		Log::config('spam', array(
			'engine' => 'File',
			'types' => 'debug',
			'file' => 'spam',
		));
		Log::config('eggs', array(
			'engine' => 'File',
			'types' => array('eggs', 'debug', 'error', 'warning'),
			'file' => 'eggs',
		));

		$testMessage = 'selective logging';
		Log::write(LOG_WARNING, $testMessage);

		$this->assertTrue(file_exists(LOGS . 'eggs.log'));
		$this->assertFalse(file_exists(LOGS . 'spam.log'));

		Log::write(LOG_DEBUG, $testMessage);
		$this->assertTrue(file_exists(LOGS . 'spam.log'));

		$contents = file_get_contents(LOGS . 'spam.log');
		$this->assertContains('Debug: ' . $testMessage, $contents);
		$contents = file_get_contents(LOGS . 'eggs.log');
		$this->assertContains('Debug: ' . $testMessage, $contents);

		if (file_exists(LOGS . 'spam.log')) {
			unlink(LOGS . 'spam.log');
		}
		if (file_exists(LOGS . 'eggs.log')) {
			unlink(LOGS . 'eggs.log');
		}
	}

/**
 * test enable
 *
 * @expectedException CakeLogException
 * @return void
 */
	public function testStreamEnable() {
		Log::config('spam', array(
			'engine' => 'File',
			'file' => 'spam',
			));
		$this->assertTrue(Log::enabled('spam'));
		Log::drop('spam');
		Log::enable('bogus_stream');
	}

/**
 * test disable
 *
 * @expectedException CakeLogException
 * @return void
 */
	public function testStreamDisable() {
		Log::config('spam', array(
			'engine' => 'File',
			'file' => 'spam',
			));
		$this->assertTrue(Log::enabled('spam'));
		Log::disable('spam');
		$this->assertFalse(Log::enabled('spam'));
		Log::drop('spam');
		Log::enable('bogus_stream');
	}

/**
 * test enabled() invalid stream
 *
 * @expectedException CakeLogException
 * @return void
 */
	public function testStreamEnabledInvalid() {
		Log::enabled('bogus_stream');
	}

/**
 * test disable invalid stream
 *
 * @expectedException CakeLogException
 * @return void
 */
	public function testStreamDisableInvalid() {
		Log::disable('bogus_stream');
	}

/**
 * resets log config
 *
 * @return void
 */
	protected function _resetLogConfig() {
		Log::config('debug', array(
			'engine' => 'File',
			'types' => array('notice', 'info', 'debug'),
			'file' => 'debug',
		));
		Log::config('error', array(
			'engine' => 'File',
			'types' => array('warning', 'error', 'critical', 'alert', 'emergency'),
			'file' => 'error',
		));
	}

/**
 * delete logs
 *
 * @return void
 */
	protected function _deleteLogs() {
		if (file_exists(LOGS . 'shops.log')) {
			unlink(LOGS . 'shops.log');
		}
		if (file_exists(LOGS . 'error.log')) {
			unlink(LOGS . 'error.log');
		}
		if (file_exists(LOGS . 'debug.log')) {
			unlink(LOGS . 'debug.log');
		}
		if (file_exists(LOGS . 'bogus.log')) {
			unlink(LOGS . 'bogus.log');
		}
		if (file_exists(LOGS . 'spam.log')) {
			unlink(LOGS . 'spam.log');
		}
		if (file_exists(LOGS . 'eggs.log')) {
			unlink(LOGS . 'eggs.log');
		}
	}

/**
 * test backward compatible scoped logging
 *
 * @return void
 */
	public function testScopedLoggingBC() {
		$this->_resetLogConfig();

		Log::config('shops', array(
			'engine' => 'File',
			'types' => array('info', 'notice', 'warning'),
			'scopes' => array('transactions', 'orders'),
			'file' => 'shops',
		));
		$this->_deleteLogs();

		Log::write('info', 'info message');
		$this->assertFalse(file_exists(LOGS . 'error.log'));
		$this->assertTrue(file_exists(LOGS . 'debug.log'));

		$this->_deleteLogs();

		Log::write('transactions', 'transaction message');
		$this->assertTrue(file_exists(LOGS . 'shops.log'));
		$this->assertFalse(file_exists(LOGS . 'transactions.log'));
		$this->assertFalse(file_exists(LOGS . 'error.log'));
		$this->assertFalse(file_exists(LOGS . 'debug.log'));

		$this->_deleteLogs();

		Log::write('error', 'error message');
		$this->assertTrue(file_exists(LOGS . 'error.log'));
		$this->assertFalse(file_exists(LOGS . 'debug.log'));
		$this->assertFalse(file_exists(LOGS . 'shops.log'));

		$this->_deleteLogs();

		Log::write('orders', 'order message');
		$this->assertFalse(file_exists(LOGS . 'error.log'));
		$this->assertFalse(file_exists(LOGS . 'debug.log'));
		$this->assertFalse(file_exists(LOGS . 'orders.log'));
		$this->assertTrue(file_exists(LOGS . 'shops.log'));

		$this->_deleteLogs();

		Log::write('warning', 'warning message');
		$this->assertTrue(file_exists(LOGS . 'error.log'));
		$this->assertFalse(file_exists(LOGS . 'debug.log'));

		$this->_deleteLogs();

		Log::drop('shops');
	}

/**
 * Test that scopes are exclusive and don't bleed.
 *
 * @return void
 */
	public function testScopedLoggingExclusive() {
		$this->_deleteLogs();

		Log::config('shops', array(
			'engine' => 'File',
			'types' => array('info', 'notice', 'warning'),
			'scopes' => array('transactions', 'orders'),
			'file' => 'shops.log',
		));
		Log::config('eggs', array(
			'engine' => 'File',
			'types' => array('info', 'notice', 'warning'),
			'scopes' => array('eggs'),
			'file' => 'eggs.log',
		));

		Log::write('info', 'transactions message', 'transactions');
		$this->assertFalse(file_exists(LOGS . 'eggs.log'));
		$this->assertTrue(file_exists(LOGS . 'shops.log'));

		$this->_deleteLogs();

		Log::write('info', 'eggs message', 'eggs');
		$this->assertTrue(file_exists(LOGS . 'eggs.log'));
		$this->assertFalse(file_exists(LOGS . 'shops.log'));
	}

/**
 * test scoped logging
 *
 * @return void
 */
	public function testScopedLogging() {
		$this->_resetLogConfig();
		$this->_deleteLogs();

		Log::config('string-scope', array(
			'engine' => 'File',
			'types' => array('info', 'notice', 'warning'),
			'scopes' => 'string-scope',
			'file' => 'string-scope.log'
		));
		Log::write('info', 'info message', 'string-scope');
		$this->assertTrue(file_exists(LOGS . 'string-scope.log'));

		Log::drop('string-scope');

		Log::config('shops', array(
			'engine' => 'File',
			'types' => array('info', 'notice', 'warning'),
			'scopes' => array('transactions', 'orders'),
			'file' => 'shops.log',
		));

		Log::write('info', 'info message', 'transactions');
		$this->assertFalse(file_exists(LOGS . 'error.log'));
		$this->assertTrue(file_exists(LOGS . 'shops.log'));
		$this->assertTrue(file_exists(LOGS . 'debug.log'));

		$this->_deleteLogs();

		Log::write('transactions', 'transaction message', 'orders');
		$this->assertTrue(file_exists(LOGS . 'shops.log'));
		$this->assertFalse(file_exists(LOGS . 'transactions.log'));
		$this->assertFalse(file_exists(LOGS . 'error.log'));
		$this->assertFalse(file_exists(LOGS . 'debug.log'));

		$this->_deleteLogs();

		Log::write('error', 'error message', 'orders');
		$this->assertTrue(file_exists(LOGS . 'error.log'));
		$this->assertFalse(file_exists(LOGS . 'debug.log'));
		$this->assertFalse(file_exists(LOGS . 'shops.log'));

		$this->_deleteLogs();

		Log::write('orders', 'order message', 'transactions');
		$this->assertFalse(file_exists(LOGS . 'error.log'));
		$this->assertFalse(file_exists(LOGS . 'debug.log'));
		$this->assertFalse(file_exists(LOGS . 'orders.log'));
		$this->assertTrue(file_exists(LOGS . 'shops.log'));

		$this->_deleteLogs();

		Log::write('warning', 'warning message', 'orders');
		$this->assertTrue(file_exists(LOGS . 'error.log'));
		$this->assertTrue(file_exists(LOGS . 'shops.log'));
		$this->assertFalse(file_exists(LOGS . 'debug.log'));

		$this->_deleteLogs();

		Log::drop('shops');
	}

/**
 * test bogus type and scope
 *
 * @return void
 */
	public function testBogusTypeAndScope() {
		$this->_resetLogConfig();
		$this->_deleteLogs();

		Log::config('file', array(
			'engine' => 'File',
			'path' => LOGS
		));

		Log::write('bogus', 'bogus message');
		$this->assertTrue(file_exists(LOGS . 'bogus.log'));
		$this->assertFalse(file_exists(LOGS . 'error.log'));
		$this->assertFalse(file_exists(LOGS . 'debug.log'));
		$this->_deleteLogs();

		Log::write('bogus', 'bogus message', 'bogus');
		$this->assertTrue(file_exists(LOGS . 'bogus.log'));
		$this->assertFalse(file_exists(LOGS . 'error.log'));
		$this->assertFalse(file_exists(LOGS . 'debug.log'));
		$this->_deleteLogs();

		Log::write('error', 'bogus message', 'bogus');
		$this->assertFalse(file_exists(LOGS . 'bogus.log'));
		$this->assertTrue(file_exists(LOGS . 'error.log'));
		$this->assertFalse(file_exists(LOGS . 'debug.log'));
		$this->_deleteLogs();
	}

/**
 * test scoped logging with convenience methods
 *
 * @return void
 */
	public function testConvenienceScopedLogging() {
		if (file_exists(LOGS . 'shops.log')) {
			unlink(LOGS . 'shops.log');
		}
		if (file_exists(LOGS . 'error.log')) {
			unlink(LOGS . 'error.log');
		}
		if (file_exists(LOGS . 'debug.log')) {
			unlink(LOGS . 'debug.log');
		}

		$this->_resetLogConfig();
		Log::config('shops', array(
			'engine' => 'File',
			'types' => array('info', 'debug', 'notice', 'warning'),
			'scopes' => array('transactions', 'orders'),
			'file' => 'shops',
		));

		Log::info('info message', 'transactions');
		$this->assertFalse(file_exists(LOGS . 'error.log'));
		$this->assertTrue(file_exists(LOGS . 'shops.log'));
		$this->assertTrue(file_exists(LOGS . 'debug.log'));

		$this->_deleteLogs();

		Log::error('error message', 'orders');
		$this->assertTrue(file_exists(LOGS . 'error.log'));
		$this->assertFalse(file_exists(LOGS . 'debug.log'));
		$this->assertFalse(file_exists(LOGS . 'shops.log'));

		$this->_deleteLogs();

		Log::warning('warning message', 'orders');
		$this->assertTrue(file_exists(LOGS . 'error.log'));
		$this->assertTrue(file_exists(LOGS . 'shops.log'));
		$this->assertFalse(file_exists(LOGS . 'debug.log'));

		$this->_deleteLogs();

		Log::drop('shops');
	}

/**
 * test convenience methods
 *
 * @return void
 */
	public function testConvenienceMethods() {
		$this->_deleteLogs();

		Log::config('debug', array(
			'engine' => 'File',
			'types' => array('notice', 'info', 'debug'),
			'file' => 'debug',
		));
		Log::config('error', array(
			'engine' => 'File',
			'types' => array('emergency', 'alert', 'critical', 'error', 'warning'),
			'file' => 'error',
		));

		$testMessage = 'emergency message';
		Log::emergency($testMessage);
		$contents = file_get_contents(LOGS . 'error.log');
		$this->assertRegExp('/(Emergency|Critical): ' . $testMessage . '/', $contents);
		$this->assertFalse(file_exists(LOGS . 'debug.log'));
		$this->_deleteLogs();

		$testMessage = 'alert message';
		Log::alert($testMessage);
		$contents = file_get_contents(LOGS . 'error.log');
		$this->assertRegExp('/(Alert|Critical): ' . $testMessage . '/', $contents);
		$this->assertFalse(file_exists(LOGS . 'debug.log'));
		$this->_deleteLogs();

		$testMessage = 'critical message';
		Log::critical($testMessage);
		$contents = file_get_contents(LOGS . 'error.log');
		$this->assertContains('Critical: ' . $testMessage, $contents);
		$this->assertFalse(file_exists(LOGS . 'debug.log'));
		$this->_deleteLogs();

		$testMessage = 'error message';
		Log::error($testMessage);
		$contents = file_get_contents(LOGS . 'error.log');
		$this->assertContains('Error: ' . $testMessage, $contents);
		$this->assertFalse(file_exists(LOGS . 'debug.log'));
		$this->_deleteLogs();

		$testMessage = 'warning message';
		Log::warning($testMessage);
		$contents = file_get_contents(LOGS . 'error.log');
		$this->assertContains('Warning: ' . $testMessage, $contents);
		$this->assertFalse(file_exists(LOGS . 'debug.log'));
		$this->_deleteLogs();

		$testMessage = 'notice message';
		Log::notice($testMessage);
		$contents = file_get_contents(LOGS . 'debug.log');
		$this->assertRegExp('/(Notice|Debug): ' . $testMessage . '/', $contents);
		$this->assertFalse(file_exists(LOGS . 'error.log'));
		$this->_deleteLogs();

		$testMessage = 'info message';
		Log::info($testMessage);
		$contents = file_get_contents(LOGS . 'debug.log');
		$this->assertRegExp('/(Info|Debug): ' . $testMessage . '/', $contents);
		$this->assertFalse(file_exists(LOGS . 'error.log'));
		$this->_deleteLogs();

		$testMessage = 'debug message';
		Log::debug($testMessage);
		$contents = file_get_contents(LOGS . 'debug.log');
		$this->assertContains('Debug: ' . $testMessage, $contents);
		$this->assertFalse(file_exists(LOGS . 'error.log'));
		$this->_deleteLogs();
	}

/**
 * test levels customization
 *
 * @return void
 */
	public function testLevelCustomization() {
		$this->skipIf(DIRECTORY_SEPARATOR === '\\', 'Log level tests not supported on Windows.');

		$levels = Log::defaultLevels();
		$this->assertNotEmpty($levels);
		$result = array_keys($levels);
		$this->assertEquals(array(0, 1, 2, 3, 4, 5, 6, 7), $result);

		$levels = Log::levels(array('foo', 'bar'));
		Log::defaultLevels();
		$this->assertEquals('foo', $levels[8]);
		$this->assertEquals('bar', $levels[9]);

		$levels = Log::levels(array(11 => 'spam', 'bar' => 'eggs'));
		Log::defaultLevels();
		$this->assertEquals('spam', $levels[8]);
		$this->assertEquals('eggs', $levels[9]);

		$levels = Log::levels(array(11 => 'spam', 'bar' => 'eggs'), false);
		Log::defaultLevels();
		$this->assertEquals(array('spam', 'eggs'), $levels);

		$levels = Log::levels(array('ham', 9 => 'spam', '12' => 'fam'), false);
		Log::defaultLevels();
		$this->assertEquals(array('ham', 'spam', 'fam'), $levels);
	}

/**
 * Test writing log files with custom levels
 *
 * @return void
 */
	public function testCustomLevelWrites() {
		$this->_deleteLogs();
		$this->_resetLogConfig();

		Log::levels(array('spam', 'eggs'));

		$testMessage = 'error message';
		Log::write('error', $testMessage);
		Log::defaultLevels();
		$this->assertTrue(file_exists(LOGS . 'error.log'));
		$contents = file_get_contents(LOGS . 'error.log');
		$this->assertContains('Error: ' . $testMessage, $contents);

		Log::config('spam', array(
			'engine' => 'File',
			'file' => 'spam.log',
			'types' => 'spam',
			));
		Log::config('eggs', array(
			'engine' => 'File',
			'file' => 'eggs.log',
			'types' => array('spam', 'eggs'),
			));

		$testMessage = 'spam message';
		Log::write('spam', $testMessage);
		Log::defaultLevels();
		$this->assertTrue(file_exists(LOGS . 'spam.log'));
		$this->assertTrue(file_exists(LOGS . 'eggs.log'));
		$contents = file_get_contents(LOGS . 'spam.log');
		$this->assertContains('Spam: ' . $testMessage, $contents);

		$testMessage = 'egg message';
		Log::write('eggs', $testMessage);
		Log::defaultLevels();
		$contents = file_get_contents(LOGS . 'spam.log');
		$this->assertNotContains('Eggs: ' . $testMessage, $contents);
		$contents = file_get_contents(LOGS . 'eggs.log');
		$this->assertContains('Eggs: ' . $testMessage, $contents);

		Log::drop('spam');
		Log::drop('eggs');

		$this->_deleteLogs();
	}

}
