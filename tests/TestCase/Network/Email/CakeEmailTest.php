<?php
/**
 * CakeEmailTest file
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
 * @package       Cake.Test.Case.Network.Email
 * @since         CakePHP(tm) v 2.0.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace lib\Cake\Test\Case\Network\Email;



/**
 * Help to test Email
 */
class TestCakeEmail extends Email {

/**
 * Config class name.
 *
 * Use a the testing config class in this file.
 *
 * @var string
 */
	protected $_configClass = 'TestEmailConfig';

/**
 * Config
 */
	protected $_config = array();

/**
 * Wrap to protected method
 *
 * @return array
 */
	public function formatAddress($address) {
		return parent::_formatAddress($address);
	}

/**
 * Wrap to protected method
 *
 * @return array
 */
	public function wrap($text, $length = Email::LINE_LENGTH_MUST) {
		return parent::_wrap($text, $length);
	}

/**
 * Get the boundary attribute
 *
 * @return string
 */
	public function getBoundary() {
		return $this->_boundary;
	}

/**
 * Encode to protected method
 *
 * @return string
 */
	public function encode($text) {
		return $this->_encode($text);
	}

/**
 * Render to protected method
 *
 * @return array
 */
	public function render($content) {
		return $this->_render($content);
	}

}

/**
 * EmailConfig class
 */
class TestEmailConfig {

/**
 * default config
 *
 * @var array
 */
	public $default = array(
		'subject' => 'Default Subject',
	);

/**
 * test config
 *
 * @var array
 */
	public $test = array(
		'from' => array('some@example.com' => 'My website'),
		'to' => array('test@example.com' => 'Testname'),
		'subject' => 'Test mail subject',
		'transport' => 'Debug',
		'theme' => 'TestTheme',
		'helpers' => array('Html', 'Form'),
	);

/**
 * test config 2
 *
 * @var array
 */
	public $test2 = array(
		'from' => array('some@example.com' => 'My website'),
		'to' => array('test@example.com' => 'Testname'),
		'subject' => 'Test mail subject',
		'transport' => 'Smtp',
		'host' => 'cakephp.org',
		'timeout' => 60
	);

}

/**
 * ExtendTransport class
 * test class to ensure the class has send() method
 */
class ExtendTransport {

}

/**
 * CakeEmailTest class
 *
 * @package       Cake.Test.Case.Network.Email
 */
class CakeEmailTest extends TestCase {

/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		$this->_configFileExists = true;
		$emailConfig = new File(CONFIG . 'email.php');
		if (!$emailConfig->exists()) {
			$this->_configFileExists = false;
			$emailConfig->create();
		}

		$this->Email = new TestCakeEmail();

		App::build(array(
			'View' => array(CAKE . 'Test' . DS . 'test_app' . DS . 'View' . DS)
		));
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		App::build();

		if (!$this->_configFileExists) {
			unlink(CONFIG . 'email.php');
		}
	}

/**
 * Test if the EmailConfig::$default configuration is read when present
 *
 * @return void
 */
	public function testDefaultConfig() {
		$this->assertEquals('Default Subject', $this->Email->subject());
	}

/**
 * testFrom method
 *
 * @return void
 */
	public function testFrom() {
		$this->assertSame(array(), $this->Email->from());

		$this->Email->from('cake@cakephp.org');
		$expected = array('cake@cakephp.org' => 'cake@cakephp.org');
		$this->assertSame($expected, $this->Email->from());

		$this->Email->from(array('cake@cakephp.org'));
		$this->assertSame($expected, $this->Email->from());

		$this->Email->from('cake@cakephp.org', 'CakePHP');
		$expected = array('cake@cakephp.org' => 'CakePHP');
		$this->assertSame($expected, $this->Email->from());

		$result = $this->Email->from(array('cake@cakephp.org' => 'CakePHP'));
		$this->assertSame($expected, $this->Email->from());
		$this->assertSame($this->Email, $result);

		$this->setExpectedException('SocketException');
		$this->Email->from(array('cake@cakephp.org' => 'CakePHP', 'fail@cakephp.org' => 'From can only be one address'));
	}

/**
 * Test that from addresses using colons work.
 *
 * @return void
 */
	public function testFromWithColonsAndQuotes() {
		$address = array(
			'info@example.com' => '70:20:00 " Forum'
		);
		$this->Email->from($address);
		$this->assertEquals($address, $this->Email->from());
		$this->Email->to('info@example.com')
			->subject('Test email')
			->transport('Debug');

		$result = $this->Email->send();
		$this->assertContains('From: "70:20:00 \" Forum" <info@example.com>', $result['headers']);
	}

/**
 * testSender method
 *
 * @return void
 */
	public function testSender() {
		$this->Email->reset();
		$this->assertSame(array(), $this->Email->sender());

		$this->Email->sender('cake@cakephp.org', 'Name');
		$expected = array('cake@cakephp.org' => 'Name');
		$this->assertSame($expected, $this->Email->sender());

		$headers = $this->Email->getHeaders(array('from' => true, 'sender' => true));
		$this->assertFalse($headers['From']);
		$this->assertSame('Name <cake@cakephp.org>', $headers['Sender']);

		$this->Email->from('cake@cakephp.org', 'CakePHP');
		$headers = $this->Email->getHeaders(array('from' => true, 'sender' => true));
		$this->assertSame('CakePHP <cake@cakephp.org>', $headers['From']);
		$this->assertSame('', $headers['Sender']);
	}

/**
 * testTo method
 *
 * @return void
 */
	public function testTo() {
		$this->assertSame(array(), $this->Email->to());

		$result = $this->Email->to('cake@cakephp.org');
		$expected = array('cake@cakephp.org' => 'cake@cakephp.org');
		$this->assertSame($expected, $this->Email->to());
		$this->assertSame($this->Email, $result);

		$this->Email->to('cake@cakephp.org', 'CakePHP');
		$expected = array('cake@cakephp.org' => 'CakePHP');
		$this->assertSame($expected, $this->Email->to());

		$this->Email->to('cake@cake_php.org', 'CakePHPUnderscore');
		$expected = array('cake@cake_php.org' => 'CakePHPUnderscore');
		$this->assertSame($expected, $this->Email->to());

		$list = array(
			'root@localhost' => 'root',
			'bjørn@hammeröath.com' => 'Bjorn',
			'cake.php@cakephp.org' => 'Cake PHP',
			'cake-php@googlegroups.com' => 'Cake Groups',
			'root@cakephp.org'
		);
		$this->Email->to($list);
		$expected = array(
			'root@localhost' => 'root',
			'bjørn@hammeröath.com' => 'Bjorn',
			'cake.php@cakephp.org' => 'Cake PHP',
			'cake-php@googlegroups.com' => 'Cake Groups',
			'root@cakephp.org' => 'root@cakephp.org'
		);
		$this->assertSame($expected, $this->Email->to());

		$this->Email->addTo('jrbasso@cakephp.org');
		$this->Email->addTo('mark_story@cakephp.org', 'Mark Story');
		$this->Email->addTo('foobar@ætdcadsl.dk');
		$result = $this->Email->addTo(array('phpnut@cakephp.org' => 'PhpNut', 'jose_zap@cakephp.org'));
		$expected = array(
			'root@localhost' => 'root',
			'bjørn@hammeröath.com' => 'Bjorn',
			'cake.php@cakephp.org' => 'Cake PHP',
			'cake-php@googlegroups.com' => 'Cake Groups',
			'root@cakephp.org' => 'root@cakephp.org',
			'jrbasso@cakephp.org' => 'jrbasso@cakephp.org',
			'mark_story@cakephp.org' => 'Mark Story',
			'foobar@ætdcadsl.dk' => 'foobar@ætdcadsl.dk',
			'phpnut@cakephp.org' => 'PhpNut',
			'jose_zap@cakephp.org' => 'jose_zap@cakephp.org'
		);
		$this->assertSame($expected, $this->Email->to());
		$this->assertSame($this->Email, $result);
	}

/**
 * Data provider function for testBuildInvalidData
 *
 * @return array
 */
	public static function invalidEmails() {
		return array(
			array(1.0),
			array(''),
			array('string'),
			array('<tag>'),
			array(array('ok@cakephp.org', 1.0, '', 'string'))
		);
	}

/**
 * testBuildInvalidData
 *
 * @expectedException SocketException
 * @expectedExceptionMessage The email set for "_to" is empty.
 * @return void
 */
	public function testInvalidEmail() {
		$this->Email->to('');
	}

/**
 * testBuildInvalidData
 *
 * @expectedException SocketException
 * @expectedExceptionMessage Invalid email set for "_from". You passed "cake.@"
 * @return void
 */
	public function testInvalidFrom() {
		$this->Email->from('cake.@');
	}

/**
 * testBuildInvalidData
 *
 * @expectedException SocketException
 * @expectedExceptionMessage Invalid email set for "_to". You passed "1"
 * @return void
 */
	public function testInvalidEmailAdd() {
		$this->Email->addTo('1');
	}

/**
 * test emailPattern method
 *
 * @return void
 */
	public function testEmailPattern() {
		$regex = '/.+@.+\..+/i';
		$this->assertSame($regex, $this->Email->emailPattern($regex)->emailPattern());
	}

/**
 * Tests that it is possible to set email regex configuration to a Email object
 *
 * @return void
 */
	public function testConfigEmailPattern() {
		$regex = '/.+@.+\..+/i';
		$email = new Email(array('emailPattern' => $regex));
		$this->assertSame($regex, $email->emailPattern());
	}

/**
 * Tests that it is possible set custom email validation
 *
 * @return void
 */
	public function testCustomEmailValidation() {
		$regex = '/^[\.a-z0-9!#$%&\'*+\/=?^_`{|}~-]+@[-a-z0-9]+(\.[-a-z0-9]+)*\.[a-z]{2,6}$/i';

		$this->Email->emailPattern($regex)->to('pass.@example.com');
		$this->assertSame(array(
			'pass.@example.com' => 'pass.@example.com',
		), $this->Email->to());

		$this->Email->addTo('pass..old.docomo@example.com');
		$this->assertSame(array(
			'pass.@example.com' => 'pass.@example.com',
			'pass..old.docomo@example.com' => 'pass..old.docomo@example.com',
		), $this->Email->to());

		$this->Email->reset();
		$emails = array(
			'pass.@example.com',
			'pass..old.docomo@example.com'
		);
		$additionalEmails = array(
			'.extend.@example.com',
			'.docomo@example.com'
		);
		$this->Email->emailPattern($regex)->to($emails);
		$this->assertSame(array(
			'pass.@example.com' => 'pass.@example.com',
			'pass..old.docomo@example.com' => 'pass..old.docomo@example.com',
		), $this->Email->to());

		$this->Email->addTo($additionalEmails);
		$this->assertSame(array(
			'pass.@example.com' => 'pass.@example.com',
			'pass..old.docomo@example.com' => 'pass..old.docomo@example.com',
			'.extend.@example.com' => '.extend.@example.com',
			'.docomo@example.com' => '.docomo@example.com',
		), $this->Email->to());
	}

/**
 * Tests that it is possible to unset the email pattern and make use of filter_var() instead.
 *
 * @return void
 *
 * @expectedException SocketException
 * @expectedExceptionMessage Invalid email set for "_to". You passed "fail.@example.com"
 */
	public function testUnsetEmailPattern() {
		$email = new Email();
		$this->assertSame(Email::EMAIL_PATTERN, $email->emailPattern());

		$email->emailPattern(null);
		$this->assertNull($email->emailPattern());

		$email->to('pass@example.com');
		$email->to('fail.@example.com');
	}

/**
 * testFormatAddress method
 *
 * @return void
 */
	public function testFormatAddress() {
		$result = $this->Email->formatAddress(array('cake@cakephp.org' => 'cake@cakephp.org'));
		$expected = array('cake@cakephp.org');
		$this->assertSame($expected, $result);

		$result = $this->Email->formatAddress(array('cake@cakephp.org' => 'cake@cakephp.org', 'php@cakephp.org' => 'php@cakephp.org'));
		$expected = array('cake@cakephp.org', 'php@cakephp.org');
		$this->assertSame($expected, $result);

		$result = $this->Email->formatAddress(array('cake@cakephp.org' => 'CakePHP', 'php@cakephp.org' => 'Cake'));
		$expected = array('CakePHP <cake@cakephp.org>', 'Cake <php@cakephp.org>');
		$this->assertSame($expected, $result);

		$result = $this->Email->formatAddress(array('me@example.com' => '"Last" First'));
		$expected = array('"\"Last\" First" <me@example.com>');
		$this->assertSame($expected, $result);

		$result = $this->Email->formatAddress(array('me@example.com' => 'Last First'));
		$expected = array('Last First <me@example.com>');
		$this->assertSame($expected, $result);

		$result = $this->Email->formatAddress(array('cake@cakephp.org' => 'ÄÖÜTest'));
		$expected = array('=?UTF-8?B?w4TDlsOcVGVzdA==?= <cake@cakephp.org>');
		$this->assertSame($expected, $result);

		$result = $this->Email->formatAddress(array('cake@cakephp.org' => '日本語Test'));
		$expected = array('=?UTF-8?B?5pel5pys6KqeVGVzdA==?= <cake@cakephp.org>');
		$this->assertSame($expected, $result);
	}

/**
 * Test that addresses are quoted correctly when they contain unicode and
 * commas
 *
 * @return void
 */
	public function testFormatAddressEncodeAndEscape() {
		$result = $this->Email->formatAddress(array(
			'test@example.com' => 'Website, ascii'
		));
		$expected = array('"Website, ascii" <test@example.com>');
		$this->assertSame($expected, $result);

		$result = $this->Email->formatAddress(array(
			'test@example.com' => 'Wébsite, unicode'
		));
		$expected = array('=?UTF-8?B?V8OpYnNpdGUsIHVuaWNvZGU=?= <test@example.com>');
		$this->assertSame($expected, $result);

		$result = $this->Email->formatAddress(array(
			'test@example.com' => 'Website, électric'
		));
		$expected = array('"Website, =?UTF-8?B?w6lsZWN0cmlj?=" <test@example.com>');
		$this->assertSame($expected, $result);
	}

/**
 * testFormatAddressJapanese
 *
 * @return void
 */
	public function testFormatAddressJapanese() {
		$this->skipIf(!function_exists('mb_convert_encoding'));

		$this->Email->headerCharset = 'ISO-2022-JP';
		$result = $this->Email->formatAddress(array('cake@cakephp.org' => '日本語Test'));
		$expected = array('=?ISO-2022-JP?B?GyRCRnxLXDhsGyhCVGVzdA==?= <cake@cakephp.org>');
		$this->assertSame($expected, $result);

		$result = $this->Email->formatAddress(array('cake@cakephp.org' => '寿限無寿限無五劫の擦り切れ海砂利水魚の水行末雲来末風来末食う寝る処に住む処やぶら小路の藪柑子パイポパイポパイポのシューリンガンシューリンガンのグーリンダイグーリンダイのポンポコピーのポンポコナーの長久命の長助'));
		$expected = array("=?ISO-2022-JP?B?GyRCPHc4Qkw1PHc4Qkw1OF45ZSROOyQkakBaJGwzJDo9TXg/ZTV7GyhC?=\r\n" .
			" =?ISO-2022-JP?B?GyRCJE4/ZTlUS3YxQE1oS3ZJd01oS3Y/KSQmPzIkaz1oJEs9OyRgGyhC?=\r\n" .
			" =?ISO-2022-JP?B?GyRCPWgkZCRWJGk+Lk8pJE5pLjQ7O1IlUSUkJV0lUSUkJV0lUSUkGyhC?=\r\n" .
			" =?ISO-2022-JP?B?GyRCJV0kTiU3JWUhPCVqJXMlLCVzJTclZSE8JWolcyUsJXMkTiUwGyhC?=\r\n" .
			" =?ISO-2022-JP?B?GyRCITwlaiVzJUAlJCUwITwlaiVzJUAlJCROJV0lcyVdJTMlVCE8GyhC?=\r\n" .
			" =?ISO-2022-JP?B?GyRCJE4lXSVzJV0lMyVKITwkTkQ5NVdMPyRORDk9dRsoQg==?= <cake@cakephp.org>");
		$this->assertSame($expected, $result);
	}

/**
 * testAddresses method
 *
 * @return void
 */
	public function testAddresses() {
		$this->Email->reset();
		$this->Email->from('cake@cakephp.org', 'CakePHP');
		$this->Email->replyTo('replyto@cakephp.org', 'ReplyTo CakePHP');
		$this->Email->readReceipt('readreceipt@cakephp.org', 'ReadReceipt CakePHP');
		$this->Email->returnPath('returnpath@cakephp.org', 'ReturnPath CakePHP');
		$this->Email->to('to@cakephp.org', 'To, CakePHP');
		$this->Email->cc('cc@cakephp.org', 'Cc CakePHP');
		$this->Email->bcc('bcc@cakephp.org', 'Bcc CakePHP');
		$this->Email->addTo('to2@cakephp.org', 'To2 CakePHP');
		$this->Email->addCc('cc2@cakephp.org', 'Cc2 CakePHP');
		$this->Email->addBcc('bcc2@cakephp.org', 'Bcc2 CakePHP');

		$this->assertSame($this->Email->from(), array('cake@cakephp.org' => 'CakePHP'));
		$this->assertSame($this->Email->replyTo(), array('replyto@cakephp.org' => 'ReplyTo CakePHP'));
		$this->assertSame($this->Email->readReceipt(), array('readreceipt@cakephp.org' => 'ReadReceipt CakePHP'));
		$this->assertSame($this->Email->returnPath(), array('returnpath@cakephp.org' => 'ReturnPath CakePHP'));
		$this->assertSame($this->Email->to(), array('to@cakephp.org' => 'To, CakePHP', 'to2@cakephp.org' => 'To2 CakePHP'));
		$this->assertSame($this->Email->cc(), array('cc@cakephp.org' => 'Cc CakePHP', 'cc2@cakephp.org' => 'Cc2 CakePHP'));
		$this->assertSame($this->Email->bcc(), array('bcc@cakephp.org' => 'Bcc CakePHP', 'bcc2@cakephp.org' => 'Bcc2 CakePHP'));

		$headers = $this->Email->getHeaders(array_fill_keys(array('from', 'replyTo', 'readReceipt', 'returnPath', 'to', 'cc', 'bcc'), true));
		$this->assertSame($headers['From'], 'CakePHP <cake@cakephp.org>');
		$this->assertSame($headers['Reply-To'], 'ReplyTo CakePHP <replyto@cakephp.org>');
		$this->assertSame($headers['Disposition-Notification-To'], 'ReadReceipt CakePHP <readreceipt@cakephp.org>');
		$this->assertSame($headers['Return-Path'], 'ReturnPath CakePHP <returnpath@cakephp.org>');
		$this->assertSame($headers['To'], '"To, CakePHP" <to@cakephp.org>, To2 CakePHP <to2@cakephp.org>');
		$this->assertSame($headers['Cc'], 'Cc CakePHP <cc@cakephp.org>, Cc2 CakePHP <cc2@cakephp.org>');
		$this->assertSame($headers['Bcc'], 'Bcc CakePHP <bcc@cakephp.org>, Bcc2 CakePHP <bcc2@cakephp.org>');
	}

/**
 * testMessageId method
 *
 * @return void
 */
	public function testMessageId() {
		$this->Email->messageId(true);
		$result = $this->Email->getHeaders();
		$this->assertTrue(isset($result['Message-ID']));

		$this->Email->messageId(false);
		$result = $this->Email->getHeaders();
		$this->assertFalse(isset($result['Message-ID']));

		$result = $this->Email->messageId('<my-email@localhost>');
		$this->assertSame($this->Email, $result);
		$result = $this->Email->getHeaders();
		$this->assertSame('<my-email@localhost>', $result['Message-ID']);

		$result = $this->Email->messageId();
		$this->assertSame('<my-email@localhost>', $result);
	}

/**
 * testMessageIdInvalid method
 *
 * @return void
 * @expectedException SocketException
 */
	public function testMessageIdInvalid() {
		$this->Email->messageId('my-email@localhost');
	}

/**
 * testDomain method
 *
 * @return void
 */
	public function testDomain() {
		$result = $this->Email->domain();
		$expected = env('HTTP_HOST') ? env('HTTP_HOST') : php_uname('n');
		$this->assertSame($expected, $result);

		$this->Email->domain('example.org');
		$result = $this->Email->domain();
		$expected = 'example.org';
		$this->assertSame($expected, $result);
	}

/**
 * testMessageIdWithDomain method
 *
 * @return void
 */
	public function testMessageIdWithDomain() {
		$this->Email->domain('example.org');
		$result = $this->Email->getHeaders();
		$expected = '@example.org>';
		$this->assertTextContains($expected, $result['Message-ID']);

		$_SERVER['HTTP_HOST'] = 'example.org';
		$result = $this->Email->getHeaders();
		$this->assertTextContains('example.org', $result['Message-ID']);

		$_SERVER['HTTP_HOST'] = 'example.org:81';
		$result = $this->Email->getHeaders();
		$this->assertTextNotContains(':81', $result['Message-ID']);
	}

/**
 * testSubject method
 *
 * @return void
 */
	public function testSubject() {
		$this->Email->subject('You have a new message.');
		$this->assertSame('You have a new message.', $this->Email->subject());

		$this->Email->subject('You have a new message, I think.');
		$this->assertSame($this->Email->subject(), 'You have a new message, I think.');
		$this->Email->subject(1);
		$this->assertSame('1', $this->Email->subject());

		$this->Email->subject('هذه رسالة بعنوان طويل مرسل للمستلم');
		$expected = '=?UTF-8?B?2YfYsNmHINix2LPYp9mE2Kkg2KjYudmG2YjYp9mGINi32YjZitmEINmF2LE=?=' . "\r\n" . ' =?UTF-8?B?2LPZhCDZhNmE2YXYs9iq2YTZhQ==?=';
		$this->assertSame($expected, $this->Email->subject());
	}

/**
 * testSubjectJapanese
 *
 * @return void
 */
	public function testSubjectJapanese() {
		$this->skipIf(!function_exists('mb_convert_encoding'));
		mb_internal_encoding('UTF-8');

		$this->Email->headerCharset = 'ISO-2022-JP';
		$this->Email->subject('日本語のSubjectにも対応するよ');
		$expected = '=?ISO-2022-JP?B?GyRCRnxLXDhsJE4bKEJTdWJqZWN0GyRCJEskYkJQMX4kOSRrJGgbKEI=?=';
		$this->assertSame($expected, $this->Email->subject());

		$this->Email->subject('長い長い長いSubjectの場合はfoldingするのが正しいんだけどいったいどうなるんだろう？');
		$expected = "=?ISO-2022-JP?B?GyRCRDkkJEQ5JCREOSQkGyhCU3ViamVjdBskQiROPmw5ZyRPGyhCZm9s?=\r\n" .
			" =?ISO-2022-JP?B?ZGluZxskQiQ5JGskTiQsQDUkNyQkJHMkQCQxJEkkJCRDJD8kJCRJGyhC?=\r\n" .
			" =?ISO-2022-JP?B?GyRCJCYkSiRrJHMkQCRtJCYhKRsoQg==?=";
		$this->assertSame($expected, $this->Email->subject());
	}

/**
 * testHeaders method
 *
 * @return void
 */
	public function testHeaders() {
		$this->Email->messageId(false);
		$this->Email->setHeaders(array('X-Something' => 'nice'));
		$expected = array(
			'X-Something' => 'nice',
			'X-Mailer' => 'CakePHP Email',
			'Date' => date(DATE_RFC2822),
			'MIME-Version' => '1.0',
			'Content-Type' => 'text/plain; charset=UTF-8',
			'Content-Transfer-Encoding' => '8bit'
		);
		$this->assertSame($expected, $this->Email->getHeaders());

		$this->Email->addHeaders(array('X-Something' => 'very nice', 'X-Other' => 'cool'));
		$expected = array(
			'X-Something' => 'very nice',
			'X-Other' => 'cool',
			'X-Mailer' => 'CakePHP Email',
			'Date' => date(DATE_RFC2822),
			'MIME-Version' => '1.0',
			'Content-Type' => 'text/plain; charset=UTF-8',
			'Content-Transfer-Encoding' => '8bit'
		);
		$this->assertSame($expected, $this->Email->getHeaders());

		$this->Email->from('cake@cakephp.org');
		$this->assertSame($expected, $this->Email->getHeaders());

		$expected = array(
			'From' => 'cake@cakephp.org',
			'X-Something' => 'very nice',
			'X-Other' => 'cool',
			'X-Mailer' => 'CakePHP Email',
			'Date' => date(DATE_RFC2822),
			'MIME-Version' => '1.0',
			'Content-Type' => 'text/plain; charset=UTF-8',
			'Content-Transfer-Encoding' => '8bit'
		);
		$this->assertSame($expected, $this->Email->getHeaders(array('from' => true)));

		$this->Email->from('cake@cakephp.org', 'CakePHP');
		$expected['From'] = 'CakePHP <cake@cakephp.org>';
		$this->assertSame($expected, $this->Email->getHeaders(array('from' => true)));

		$this->Email->to(array('cake@cakephp.org', 'php@cakephp.org' => 'CakePHP'));
		$expected = array(
			'From' => 'CakePHP <cake@cakephp.org>',
			'To' => 'cake@cakephp.org, CakePHP <php@cakephp.org>',
			'X-Something' => 'very nice',
			'X-Other' => 'cool',
			'X-Mailer' => 'CakePHP Email',
			'Date' => date(DATE_RFC2822),
			'MIME-Version' => '1.0',
			'Content-Type' => 'text/plain; charset=UTF-8',
			'Content-Transfer-Encoding' => '8bit'
		);
		$this->assertSame($expected, $this->Email->getHeaders(array('from' => true, 'to' => true)));

		$this->Email->charset = 'ISO-2022-JP';
		$expected = array(
			'From' => 'CakePHP <cake@cakephp.org>',
			'To' => 'cake@cakephp.org, CakePHP <php@cakephp.org>',
			'X-Something' => 'very nice',
			'X-Other' => 'cool',
			'X-Mailer' => 'CakePHP Email',
			'Date' => date(DATE_RFC2822),
			'MIME-Version' => '1.0',
			'Content-Type' => 'text/plain; charset=ISO-2022-JP',
			'Content-Transfer-Encoding' => '7bit'
		);
		$this->assertSame($expected, $this->Email->getHeaders(array('from' => true, 'to' => true)));

		$result = $this->Email->setHeaders(array());
		$this->assertInstanceOf('Email', $result);
	}

/**
 * Data provider function for testInvalidHeaders
 *
 * @return array
 */
	public static function invalidHeaders() {
		return array(
			array(10),
			array(''),
			array('string'),
			array(false),
			array(null)
		);
	}

/**
 * testInvalidHeaders
 *
 * @dataProvider invalidHeaders
 * @expectedException SocketException
 * @return void
 */
	public function testInvalidHeaders($value) {
		$this->Email->setHeaders($value);
	}

/**
 * testInvalidAddHeaders
 *
 * @dataProvider invalidHeaders
 * @expectedException SocketException
 * @return void
 */
	public function testInvalidAddHeaders($value) {
		$this->Email->addHeaders($value);
	}

/**
 * testTemplate method
 *
 * @return void
 */
	public function testTemplate() {
		$this->Email->template('template', 'layout');
		$expected = array('template' => 'template', 'layout' => 'layout');
		$this->assertSame($expected, $this->Email->template());

		$this->Email->template('new_template');
		$expected = array('template' => 'new_template', 'layout' => 'layout');
		$this->assertSame($expected, $this->Email->template());

		$this->Email->template('template', null);
		$expected = array('template' => 'template', 'layout' => null);
		$this->assertSame($expected, $this->Email->template());

		$this->Email->template(null, null);
		$expected = array('template' => null, 'layout' => null);
		$this->assertSame($expected, $this->Email->template());
	}

/**
 * testTheme method
 *
 * @return void
 */
	public function testTheme() {
		$this->assertNull($this->Email->theme());

		$this->Email->theme('default');
		$expected = 'default';
		$this->assertSame($expected, $this->Email->theme());
	}

/**
 * testViewVars method
 *
 * @return void
 */
	public function testViewVars() {
		$this->assertSame(array(), $this->Email->viewVars());

		$this->Email->viewVars(array('value' => 12345));
		$this->assertSame(array('value' => 12345), $this->Email->viewVars());

		$this->Email->viewVars(array('name' => 'CakePHP'));
		$this->assertSame(array('value' => 12345, 'name' => 'CakePHP'), $this->Email->viewVars());

		$this->Email->viewVars(array('value' => 4567));
		$this->assertSame(array('value' => 4567, 'name' => 'CakePHP'), $this->Email->viewVars());
	}

/**
 * testAttachments method
 *
 * @return void
 */
	public function testAttachments() {
		$this->Email->attachments(CAKE . 'basics.php');
		$expected = array(
			'basics.php' => array(
				'file' => CAKE . 'basics.php',
				'mimetype' => 'text/x-php'
			)
		);
		$this->assertSame($expected, $this->Email->attachments());

		$this->Email->attachments(array());
		$this->assertSame(array(), $this->Email->attachments());

		$this->Email->attachments(array(
			array('file' => CAKE . 'basics.php', 'mimetype' => 'text/plain')
		));
		$this->Email->addAttachments(CAKE . 'bootstrap.php');
		$this->Email->addAttachments(array(CAKE . 'bootstrap.php'));
		$this->Email->addAttachments(array('other.txt' => CAKE . 'bootstrap.php', 'license' => CAKE . 'LICENSE.txt'));
		$expected = array(
			'basics.php' => array('file' => CAKE . 'basics.php', 'mimetype' => 'text/plain'),
			'bootstrap.php' => array('file' => CAKE . 'bootstrap.php', 'mimetype' => 'text/x-php'),
			'other.txt' => array('file' => CAKE . 'bootstrap.php', 'mimetype' => 'text/x-php'),
			'license' => array('file' => CAKE . 'LICENSE.txt', 'mimetype' => 'text/plain')
		);
		$this->assertSame($expected, $this->Email->attachments());

		$this->setExpectedException('SocketException');
		$this->Email->attachments(array(array('nofile' => CAKE . 'basics.php', 'mimetype' => 'text/plain')));
	}

/**
 * testTransport method
 *
 * @return void
 */
	public function testTransport() {
		$result = $this->Email->transport('Debug');
		$this->assertSame($this->Email, $result);
		$this->assertSame('Debug', $this->Email->transport());

		$result = $this->Email->transportClass();
		$this->assertInstanceOf('DebugTransport', $result);

		$this->setExpectedException('SocketException');
		$this->Email->transport('Invalid');
		$this->Email->transportClass();
	}

/**
 * testExtendTransport method
 *
 * @return void
 */
	public function testExtendTransport() {
		$this->setExpectedException('SocketException');
		$this->Email->transport('Extend');
		$this->Email->transportClass();
	}

/**
 * testConfig method
 *
 * @return void
 */
	public function testConfig() {
		$transportClass = $this->Email->transport('debug')->transportClass();

		$config = array('test' => 'ok', 'test2' => true);
		$this->Email->config($config);
		$this->assertSame($config, $transportClass->config());
		$expected = $config + array('subject' => 'Default Subject');
		$this->assertSame($expected, $this->Email->config());

		$this->Email->config(array());
		$this->assertSame($config, $transportClass->config());

		$config = array('test' => 'test@example.com', 'subject' => 'my test subject');
		$this->Email->config($config);
		$expected = array('test' => 'test@example.com', 'subject' => 'my test subject', 'test2' => true);
		$this->assertSame($expected, $this->Email->config());
		$this->assertSame(array('test' => 'test@example.com', 'test2' => true), $transportClass->config());
	}

/**
 * testConfigString method
 *
 * @return void
 */
	public function testConfigString() {
		$configs = new TestEmailConfig();
		$this->Email->config('test');

		$result = $this->Email->to();
		$this->assertEquals($configs->test['to'], $result);

		$result = $this->Email->from();
		$this->assertEquals($configs->test['from'], $result);

		$result = $this->Email->subject();
		$this->assertEquals($configs->test['subject'], $result);

		$result = $this->Email->theme();
		$this->assertEquals($configs->test['theme'], $result);

		$result = $this->Email->transport();
		$this->assertEquals($configs->test['transport'], $result);

		$result = $this->Email->transportClass();
		$this->assertInstanceOf('DebugTransport', $result);

		$result = $this->Email->helpers();
		$this->assertEquals($configs->test['helpers'], $result);
	}

/**
 * Test updating config doesn't reset transport's config.
 *
 * @return void
 */
	public function testConfigMerge() {
		$this->Email->config('test2');

		$expected = array(
			'host' => 'cakephp.org',
			'port' => 25,
			'timeout' => 60,
			'username' => null,
			'password' => null,
			'client' => null,
			'tls' => false,
			'ssl_allow_self_signed' => false
		);
		$this->assertEquals($expected, $this->Email->transportClass()->config());

		$this->Email->config(array('log' => true));
		$this->Email->transportClass()->config();
		$expected += array('log' => true);
		$this->assertEquals($expected, $this->Email->transportClass()->config());

		$this->Email->config(array('timeout' => 45));
		$result = $this->Email->transportClass()->config();
		$this->assertEquals(45, $result['timeout']);
	}

/**
 * Calling send() with no parameters should not overwrite the view variables.
 *
 * @return void
 */
	public function testSendWithNoContentDoesNotOverwriteViewVar() {
		$this->Email->reset();
		$this->Email->transport('Debug');
		$this->Email->from('cake@cakephp.org');
		$this->Email->to('you@cakephp.org');
		$this->Email->subject('My title');
		$this->Email->emailFormat('text');
		$this->Email->template('default');
		$this->Email->viewVars(array(
			'content' => 'A message to you',
		));

		$result = $this->Email->send();
		$this->assertContains('A message to you', $result['message']);
	}

/**
 * testSendWithContent method
 *
 * @return void
 */
	public function testSendWithContent() {
		$this->Email->reset();
		$this->Email->transport('Debug');
		$this->Email->from('cake@cakephp.org');
		$this->Email->to(array('you@cakephp.org' => 'You'));
		$this->Email->subject('My title');
		$this->Email->config(array('empty'));

		$result = $this->Email->send("Here is my body, with multi lines.\nThis is the second line.\r\n\r\nAnd the last.");
		$expected = array('headers', 'message');
		$this->assertEquals($expected, array_keys($result));
		$expected = "Here is my body, with multi lines.\r\nThis is the second line.\r\n\r\nAnd the last.\r\n\r\n";

		$this->assertEquals($expected, $result['message']);
		$this->assertTrue((bool)strpos($result['headers'], 'Date: '));
		$this->assertTrue((bool)strpos($result['headers'], 'Message-ID: '));
		$this->assertTrue((bool)strpos($result['headers'], 'To: '));

		$result = $this->Email->send("Other body");
		$expected = "Other body\r\n\r\n";
		$this->assertSame($expected, $result['message']);
		$this->assertTrue((bool)strpos($result['headers'], 'Message-ID: '));
		$this->assertTrue((bool)strpos($result['headers'], 'To: '));

		$this->Email->reset();
		$this->Email->transport('Debug');
		$this->Email->from('cake@cakephp.org');
		$this->Email->to(array('you@cakephp.org' => 'You'));
		$this->Email->subject('My title');
		$this->Email->config(array('empty'));
		$result = $this->Email->send(array('Sending content', 'As array'));
		$expected = "Sending content\r\nAs array\r\n\r\n\r\n";
		$this->assertSame($expected, $result['message']);
	}

/**
 * testSendWithoutFrom method
 *
 * @return void
 */
	public function testSendWithoutFrom() {
		$this->Email->transport('Debug');
		$this->Email->to('cake@cakephp.org');
		$this->Email->subject('My title');
		$this->Email->config(array('empty'));
		$this->setExpectedException('SocketException');
		$this->Email->send("Forgot to set From");
	}

/**
 * testSendWithoutTo method
 *
 * @return void
 */
	public function testSendWithoutTo() {
		$this->Email->transport('Debug');
		$this->Email->from('cake@cakephp.org');
		$this->Email->subject('My title');
		$this->Email->config(array('empty'));
		$this->setExpectedException('SocketException');
		$this->Email->send("Forgot to set To");
	}

/**
 * Test send() with no template.
 *
 * @return void
 */
	public function testSendNoTemplateWithAttachments() {
		$this->Email->transport('debug');
		$this->Email->from('cake@cakephp.org');
		$this->Email->to('cake@cakephp.org');
		$this->Email->subject('My title');
		$this->Email->emailFormat('text');
		$this->Email->attachments(array(CAKE . 'basics.php'));
		$result = $this->Email->send('Hello');

		$boundary = $this->Email->getBoundary();
		$this->assertContains('Content-Type: multipart/mixed; boundary="' . $boundary . '"', $result['headers']);
		$expected = "--$boundary\r\n" .
			"Content-Type: text/plain; charset=UTF-8\r\n" .
			"Content-Transfer-Encoding: 8bit\r\n" .
			"\r\n" .
			"Hello" .
			"\r\n" .
			"\r\n" .
			"\r\n" .
			"--$boundary\r\n" .
			"Content-Type: text/x-php\r\n" .
			"Content-Transfer-Encoding: base64\r\n" .
			"Content-Disposition: attachment; filename=\"basics.php\"\r\n\r\n";
		$this->assertContains($expected, $result['message']);
	}

/**
 * Test send() with no template and data string attachment
 *
 * @return void
 */

	public function testSendNoTemplateWithDataStringAttachment() {
		$this->Email->transport('debug');
		$this->Email->from('cake@cakephp.org');
		$this->Email->to('cake@cakephp.org');
		$this->Email->subject('My title');
		$this->Email->emailFormat('text');
		$data = file_get_contents(CAKE . 'Console/Templates/skel/webroot/img/cake.icon.png');
		$this->Email->attachments(array('cake.icon.png' => array(
				'data' => $data,
				'mimetype' => 'image/png'
		)));
		$result = $this->Email->send('Hello');

		$boundary = $this->Email->getBoundary();
		$this->assertContains('Content-Type: multipart/mixed; boundary="' . $boundary . '"', $result['headers']);
		$expected = "--$boundary\r\n" .
				"Content-Type: text/plain; charset=UTF-8\r\n" .
				"Content-Transfer-Encoding: 8bit\r\n" .
				"\r\n" .
				"Hello" .
				"\r\n" .
				"\r\n" .
				"\r\n" .
				"--$boundary\r\n" .
				"Content-Type: image/png\r\n" .
				"Content-Transfer-Encoding: base64\r\n" .
				"Content-Disposition: attachment; filename=\"cake.icon.png\"\r\n\r\n";
		$expected .= chunk_split(base64_encode($data), 76, "\r\n");
		$this->assertContains($expected, $result['message']);
	}

/**
 * Test send() with no template and data string attachment, no mimetype
 *
 * @return void
 */
	public function testSendNoTemplateWithDataStringAttachmentNoMime() {
		$this->Email->transport('debug');
		$this->Email->from('cake@cakephp.org');
		$this->Email->to('cake@cakephp.org');
		$this->Email->subject('My title');
		$this->Email->emailFormat('text');
		$data = file_get_contents(CAKE . 'Console/Templates/skel/webroot/img/cake.icon.png');
		$this->Email->attachments(array('cake.icon.png' => array(
			'data' => $data
		)));
		$result = $this->Email->send('Hello');

		$boundary = $this->Email->getBoundary();
		$this->assertContains('Content-Type: multipart/mixed; boundary="' . $boundary . '"', $result['headers']);
		$expected = "--$boundary\r\n" .
			"Content-Type: text/plain; charset=UTF-8\r\n" .
			"Content-Transfer-Encoding: 8bit\r\n" .
			"\r\n" .
			"Hello" .
			"\r\n" .
			"\r\n" .
			"\r\n" .
			"--$boundary\r\n" .
			"Content-Type: application/octet-stream\r\n" .
			"Content-Transfer-Encoding: base64\r\n" .
			"Content-Disposition: attachment; filename=\"cake.icon.png\"\r\n\r\n";
		$expected .= chunk_split(base64_encode($data), 76, "\r\n");
		$this->assertContains($expected, $result['message']);
	}

/**
 * Test send() with no template as both
 *
 * @return void
 */
	public function testSendNoTemplateWithAttachmentsAsBoth() {
		$this->Email->transport('debug');
		$this->Email->from('cake@cakephp.org');
		$this->Email->to('cake@cakephp.org');
		$this->Email->subject('My title');
		$this->Email->emailFormat('both');
		$this->Email->attachments(array(CAKE . 'VERSION.txt'));
		$result = $this->Email->send('Hello');

		$boundary = $this->Email->getBoundary();
		$this->assertContains('Content-Type: multipart/mixed; boundary="' . $boundary . '"', $result['headers']);
		$expected = "--$boundary\r\n" .
			"Content-Type: multipart/alternative; boundary=\"alt-$boundary\"\r\n" .
			"\r\n" .
			"--alt-$boundary\r\n" .
			"Content-Type: text/plain; charset=UTF-8\r\n" .
			"Content-Transfer-Encoding: 8bit\r\n" .
			"\r\n" .
			"Hello" .
			"\r\n" .
			"\r\n" .
			"\r\n" .
			"--alt-$boundary\r\n" .
			"Content-Type: text/html; charset=UTF-8\r\n" .
			"Content-Transfer-Encoding: 8bit\r\n" .
			"\r\n" .
			"Hello" .
			"\r\n" .
			"\r\n" .
			"\r\n" .
			"--alt-{$boundary}--\r\n" .
			"\r\n" .
			"--$boundary\r\n" .
			"Content-Type: text/plain\r\n" .
			"Content-Transfer-Encoding: base64\r\n" .
			"Content-Disposition: attachment; filename=\"VERSION.txt\"\r\n\r\n";
		$this->assertContains($expected, $result['message']);
	}

/**
 * Test setting inline attachments and messages.
 *
 * @return void
 */
	public function testSendWithInlineAttachments() {
		$this->Email->transport('debug');
		$this->Email->from('cake@cakephp.org');
		$this->Email->to('cake@cakephp.org');
		$this->Email->subject('My title');
		$this->Email->emailFormat('both');
		$this->Email->attachments(array(
			'cake.png' => array(
				'file' => CAKE . 'VERSION.txt',
				'contentId' => 'abc123'
			)
		));
		$result = $this->Email->send('Hello');

		$boundary = $this->Email->getBoundary();
		$this->assertContains('Content-Type: multipart/mixed; boundary="' . $boundary . '"', $result['headers']);
		$expected = "--$boundary\r\n" .
			"Content-Type: multipart/related; boundary=\"rel-$boundary\"\r\n" .
			"\r\n" .
			"--rel-$boundary\r\n" .
			"Content-Type: multipart/alternative; boundary=\"alt-$boundary\"\r\n" .
			"\r\n" .
			"--alt-$boundary\r\n" .
			"Content-Type: text/plain; charset=UTF-8\r\n" .
			"Content-Transfer-Encoding: 8bit\r\n" .
			"\r\n" .
			"Hello" .
			"\r\n" .
			"\r\n" .
			"\r\n" .
			"--alt-$boundary\r\n" .
			"Content-Type: text/html; charset=UTF-8\r\n" .
			"Content-Transfer-Encoding: 8bit\r\n" .
			"\r\n" .
			"Hello" .
			"\r\n" .
			"\r\n" .
			"\r\n" .
			"--alt-{$boundary}--\r\n" .
			"\r\n" .
			"--rel-$boundary\r\n" .
			"Content-Type: text/plain\r\n" .
			"Content-Transfer-Encoding: base64\r\n" .
			"Content-ID: <abc123>\r\n" .
			"Content-Disposition: inline; filename=\"cake.png\"\r\n\r\n";
		$this->assertContains($expected, $result['message']);
		$this->assertContains('--rel-' . $boundary . '--', $result['message']);
		$this->assertContains('--' . $boundary . '--', $result['message']);
	}

/**
 * Test setting inline attachments and HTML only messages.
 *
 * @return void
 */
	public function testSendWithInlineAttachmentsHtmlOnly() {
		$this->Email->transport('debug');
		$this->Email->from('cake@cakephp.org');
		$this->Email->to('cake@cakephp.org');
		$this->Email->subject('My title');
		$this->Email->emailFormat('html');
		$this->Email->attachments(array(
			'cake.png' => array(
				'file' => CAKE . 'VERSION.txt',
				'contentId' => 'abc123'
			)
		));
		$result = $this->Email->send('Hello');

		$boundary = $this->Email->getBoundary();
		$this->assertContains('Content-Type: multipart/mixed; boundary="' . $boundary . '"', $result['headers']);
		$expected = "--$boundary\r\n" .
			"Content-Type: multipart/related; boundary=\"rel-$boundary\"\r\n" .
			"\r\n" .
			"--rel-$boundary\r\n" .
			"Content-Type: text/html; charset=UTF-8\r\n" .
			"Content-Transfer-Encoding: 8bit\r\n" .
			"\r\n" .
			"Hello" .
			"\r\n" .
			"\r\n" .
			"\r\n" .
			"--rel-$boundary\r\n" .
			"Content-Type: text/plain\r\n" .
			"Content-Transfer-Encoding: base64\r\n" .
			"Content-ID: <abc123>\r\n" .
			"Content-Disposition: inline; filename=\"cake.png\"\r\n\r\n";
		$this->assertContains($expected, $result['message']);
		$this->assertContains('--rel-' . $boundary . '--', $result['message']);
		$this->assertContains('--' . $boundary . '--', $result['message']);
	}

/**
 * Test disabling content-disposition.
 *
 * @return void
 */
	public function testSendWithNoContentDispositionAttachments() {
		$this->Email->transport('debug');
		$this->Email->from('cake@cakephp.org');
		$this->Email->to('cake@cakephp.org');
		$this->Email->subject('My title');
		$this->Email->emailFormat('text');
		$this->Email->attachments(array(
			'cake.png' => array(
				'file' => CAKE . 'VERSION.txt',
				'contentDisposition' => false
			)
		));
		$result = $this->Email->send('Hello');

		$boundary = $this->Email->getBoundary();
		$this->assertContains('Content-Type: multipart/mixed; boundary="' . $boundary . '"', $result['headers']);
		$expected = "--$boundary\r\n" .
			"Content-Type: text/plain; charset=UTF-8\r\n" .
			"Content-Transfer-Encoding: 8bit\r\n" .
			"\r\n" .
			"Hello" .
			"\r\n" .
			"\r\n" .
			"\r\n" .
			"--{$boundary}\r\n" .
			"Content-Type: text/plain\r\n" .
			"Content-Transfer-Encoding: base64\r\n" .
			"\r\n";

		$this->assertContains($expected, $result['message']);
		$this->assertContains('--' . $boundary . '--', $result['message']);
	}
/**
 * testSendWithLog method
 *
 * @return void
 */
	public function testSendWithLog() {
		Log::config('email', array(
			'engine' => 'File',
			'path' => TMP
		));
		Log::drop('default');
		$this->Email->transport('Debug');
		$this->Email->to('me@cakephp.org');
		$this->Email->from('cake@cakephp.org');
		$this->Email->subject('My title');
		$this->Email->config(array('log' => 'cake_test_emails'));
		$result = $this->Email->send("Logging This");

				$File = new File(TMP . 'cake_test_emails.log');
		$log = $File->read();
		$this->assertTrue(strpos($log, $result['headers']) !== false);
		$this->assertTrue(strpos($log, $result['message']) !== false);
		$File->delete();
		Log::drop('email');
	}

/**
 * testSendWithLogAndScope method
 *
 * @return void
 */
	public function testSendWithLogAndScope() {
		Log::config('email', array(
			'engine' => 'File',
			'path' => TMP,
			'types' => array('cake_test_emails'),
			'scopes' => array('email')
		));
		Log::drop('default');
		$this->Email->transport('Debug');
		$this->Email->to('me@cakephp.org');
		$this->Email->from('cake@cakephp.org');
		$this->Email->subject('My title');
		$this->Email->config(array('log' => array('level' => 'cake_test_emails', 'scope' => 'email')));
		$result = $this->Email->send("Logging This");

				$File = new File(TMP . 'cake_test_emails.log');
		$log = $File->read();
		$this->assertTrue(strpos($log, $result['headers']) !== false);
		$this->assertTrue(strpos($log, $result['message']) !== false);
		$File->delete();
		Log::drop('email');
	}

/**
 * testSendRender method
 *
 * @return void
 */
	public function testSendRender() {
		$this->Email->reset();
		$this->Email->transport('debug');

		$this->Email->from('cake@cakephp.org');
		$this->Email->to(array('you@cakephp.org' => 'You'));
		$this->Email->subject('My title');
		$this->Email->config(array('empty'));
		$this->Email->template('default', 'default');
		$result = $this->Email->send();

		$this->assertContains('This email was sent using the CakePHP Framework', $result['message']);
		$this->assertContains('Message-ID: ', $result['headers']);
		$this->assertContains('To: ', $result['headers']);
	}

/**
 * test sending and rendering with no layout
 *
 * @return void
 */
	public function testSendRenderNoLayout() {
		$this->Email->reset();
		$this->Email->transport('debug');

		$this->Email->from('cake@cakephp.org');
		$this->Email->to(array('you@cakephp.org' => 'You'));
		$this->Email->subject('My title');
		$this->Email->config(array('empty'));
		$this->Email->template('default', null);
		$result = $this->Email->send('message body.');

		$this->assertContains('message body.', $result['message']);
		$this->assertNotContains('This email was sent using the CakePHP Framework', $result['message']);
	}

/**
 * testSendRender both method
 *
 * @return void
 */
	public function testSendRenderBoth() {
		$this->Email->reset();
		$this->Email->transport('debug');

		$this->Email->from('cake@cakephp.org');
		$this->Email->to(array('you@cakephp.org' => 'You'));
		$this->Email->subject('My title');
		$this->Email->config(array('empty'));
		$this->Email->template('default', 'default');
		$this->Email->emailFormat('both');
		$result = $this->Email->send();

		$this->assertContains('Message-ID: ', $result['headers']);
		$this->assertContains('To: ', $result['headers']);

		$boundary = $this->Email->getBoundary();
		$this->assertContains('Content-Type: multipart/alternative; boundary="' . $boundary . '"', $result['headers']);

		$expected = "--$boundary\r\n" .
			"Content-Type: text/plain; charset=UTF-8\r\n" .
			"Content-Transfer-Encoding: 8bit\r\n" .
			"\r\n" .
			"\r\n" .
			"\r\n" .
			"This email was sent using the CakePHP Framework, https://cakephp.org." .
			"\r\n" .
			"\r\n" .
			"--$boundary\r\n" .
			"Content-Type: text/html; charset=UTF-8\r\n" .
			"Content-Transfer-Encoding: 8bit\r\n" .
			"\r\n" .
			"<!DOCTYPE html";
		$this->assertStringStartsWith($expected, $result['message']);

		$expected = "</html>\r\n" .
			"\r\n" .
			"\r\n" .
			"--$boundary--\r\n";
		$this->assertStringEndsWith($expected, $result['message']);
	}

/**
 * testSendRender method for ISO-2022-JP
 *
 * @return void
 */
	public function testSendRenderJapanese() {
		$this->skipIf(!function_exists('mb_convert_encoding'));

		$this->Email->reset();
		$this->Email->transport('debug');

		$this->Email->from('cake@cakephp.org');
		$this->Email->to(array('you@cakephp.org' => 'You'));
		$this->Email->subject('My title');
		$this->Email->config(array('empty'));
		$this->Email->template('default', 'japanese');
		$this->Email->charset = 'ISO-2022-JP';
		$result = $this->Email->send();

		$expected = mb_convert_encoding('CakePHP Framework を使って送信したメールです。 https://cakephp.org.', 'ISO-2022-JP');
		$this->assertContains($expected, $result['message']);
		$this->assertContains('Message-ID: ', $result['headers']);
		$this->assertContains('To: ', $result['headers']);
	}

/**
 * testSendRenderThemed method
 *
 * @return void
 */
	public function testSendRenderThemed() {
		$this->Email->reset();
		$this->Email->transport('debug');

		$this->Email->from('cake@cakephp.org');
		$this->Email->to(array('you@cakephp.org' => 'You'));
		$this->Email->subject('My title');
		$this->Email->config(array('empty'));
		$this->Email->theme('TestTheme');
		$this->Email->template('themed', 'default');
		$result = $this->Email->send();

		$this->assertContains('In TestTheme', $result['message']);
		$this->assertContains('Message-ID: ', $result['headers']);
		$this->assertContains('To: ', $result['headers']);
		$this->assertContains('/theme/TestTheme/img/test.jpg', $result['message']);
	}

/**
 * testSendRenderWithHTML method and assert line length is kept below the required limit
 *
 * @return void
 */
	public function testSendRenderWithHTML() {
		$this->Email->reset();
		$this->Email->transport('debug');

		$this->Email->from('cake@cakephp.org');
		$this->Email->to(array('you@cakephp.org' => 'You'));
		$this->Email->subject('My title');
		$this->Email->config(array('empty'));
		$this->Email->emailFormat('html');
		$this->Email->template('html', 'default');
		$result = $this->Email->send();

		$this->assertTextContains('<h1>HTML Ipsum Presents</h1>', $result['message']);
		$this->assertLineLengths($result['message']);
	}

/**
 * testSendRenderWithVars method
 *
 * @return void
 */
	public function testSendRenderWithVars() {
		$this->Email->reset();
		$this->Email->transport('debug');

		$this->Email->from('cake@cakephp.org');
		$this->Email->to(array('you@cakephp.org' => 'You'));
		$this->Email->subject('My title');
		$this->Email->config(array('empty'));
		$this->Email->template('custom', 'default');
		$this->Email->viewVars(array('value' => 12345));
		$result = $this->Email->send();

		$this->assertContains('Here is your value: 12345', $result['message']);
	}

/**
 * testSendRenderWithVars method for ISO-2022-JP
 *
 * @return void
 */
	public function testSendRenderWithVarsJapanese() {
		$this->skipIf(!function_exists('mb_convert_encoding'));
		$this->Email->reset();
		$this->Email->transport('debug');

		$this->Email->from('cake@cakephp.org');
		$this->Email->to(array('you@cakephp.org' => 'You'));
		$this->Email->subject('My title');
		$this->Email->config(array('empty'));
		$this->Email->template('japanese', 'default');
		$this->Email->viewVars(array('value' => '日本語の差し込み123'));
		$this->Email->charset = 'ISO-2022-JP';
		$result = $this->Email->send();

		$expected = mb_convert_encoding('ここにあなたの設定した値が入ります: 日本語の差し込み123', 'ISO-2022-JP');
		$this->assertTrue((bool)strpos($result['message'], $expected));
	}

/**
 * testSendRenderWithHelpers method
 *
 * @return void
 */
	public function testSendRenderWithHelpers() {
		$this->Email->reset();
		$this->Email->transport('debug');

		$timestamp = time();
		$this->Email->from('cake@cakephp.org');
		$this->Email->to(array('you@cakephp.org' => 'You'));
		$this->Email->subject('My title');
		$this->Email->config(array('empty'));
		$this->Email->template('custom_helper', 'default');
		$this->Email->viewVars(array('time' => $timestamp));

		$result = $this->Email->helpers(array('Time'));
		$this->assertInstanceOf('Email', $result);

		$result = $this->Email->send();
		$this->assertTrue((bool)strpos($result['message'], 'Right now: ' . date('Y-m-d\TH:i:s\Z', $timestamp)));

		$result = $this->Email->helpers();
		$this->assertEquals(array('Time'), $result);
	}

/**
 * testSendRenderWithImage method
 *
 * @return void
 */
	public function testSendRenderWithImage() {
		$this->Email->reset();
		$this->Email->transport('Debug');

		$this->Email->from('cake@cakephp.org');
		$this->Email->to(array('you@cakephp.org' => 'You'));
		$this->Email->subject('My title');
		$this->Email->config(array('empty'));
		$this->Email->template('image');
		$this->Email->emailFormat('html');
		$server = env('SERVER_NAME') ? env('SERVER_NAME') : 'localhost';

		if (env('SERVER_PORT') && env('SERVER_PORT') != 80) {
			$server .= ':' . env('SERVER_PORT');
		}

		$expected = '<img src="http://' . $server . '/img/image.gif" alt="cool image" width="100" height="100"/>';
		$result = $this->Email->send();
		$this->assertContains($expected, $result['message']);
	}

/**
 * testSendRenderPlugin method
 *
 * @return void
 */
	public function testSendRenderPlugin() {
		App::build(array(
			'Plugin' => array(CAKE . 'Test' . DS . 'test_app' . DS . 'Plugin' . DS)
		));
		Plugin::load(array('TestPlugin', 'TestPluginTwo'));

		$this->Email->reset();
		$this->Email->transport('debug');
		$this->Email->from('cake@cakephp.org');
		$this->Email->to(array('you@cakephp.org' => 'You'));
		$this->Email->subject('My title');
		$this->Email->config(array('empty'));

		$result = $this->Email->template('TestPlugin.test_plugin_tpl', 'default')->send();
		$this->assertContains('Into TestPlugin.', $result['message']);
		$this->assertContains('This email was sent using the CakePHP Framework', $result['message']);

		$result = $this->Email->template('TestPlugin.test_plugin_tpl', 'TestPlugin.plug_default')->send();
		$this->assertContains('Into TestPlugin.', $result['message']);
		$this->assertContains('This email was sent using the TestPlugin.', $result['message']);

		$result = $this->Email->template('TestPlugin.test_plugin_tpl', 'plug_default')->send();
		$this->assertContains('Into TestPlugin.', $result['message']);
		$this->assertContains('This email was sent using the TestPlugin.', $result['message']);

		$this->Email->template(
			'TestPlugin.test_plugin_tpl',
			'TestPluginTwo.default'
		);
		$result = $this->Email->send();
		$this->assertContains('Into TestPlugin.', $result['message']);
		$this->assertContains('This email was sent using TestPluginTwo.', $result['message']);

		// test plugin template overridden by theme
		$this->Email->theme('TestTheme');
		$result = $this->Email->send();

		$this->assertContains('Into TestPlugin. (themed)', $result['message']);

		$this->Email->viewVars(array('value' => 12345));
		$result = $this->Email->template('custom', 'TestPlugin.plug_default')->send();
		$this->assertContains('Here is your value: 12345', $result['message']);
		$this->assertContains('This email was sent using the TestPlugin.', $result['message']);

		$this->setExpectedException('MissingViewException');
		$this->Email->template('test_plugin_tpl', 'plug_default')->send();
	}

/**
 * testSendMultipleMIME method
 *
 * @return void
 */
	public function testSendMultipleMIME() {
		$this->Email->reset();
		$this->Email->transport('debug');

		$this->Email->from('cake@cakephp.org');
		$this->Email->to(array('you@cakephp.org' => 'You'));
		$this->Email->subject('My title');
		$this->Email->template('custom', 'default');
		$this->Email->config(array());
		$this->Email->viewVars(array('value' => 12345));
		$this->Email->emailFormat('both');
		$this->Email->send();

		$message = $this->Email->message();
		$boundary = $this->Email->getBoundary();
		$this->assertFalse(empty($boundary));
		$this->assertContains('--' . $boundary, $message);
		$this->assertContains('--' . $boundary . '--', $message);

		$this->Email->attachments(array('fake.php' => __FILE__));
		$this->Email->send();

		$message = $this->Email->message();
		$boundary = $this->Email->getBoundary();
		$this->assertFalse(empty($boundary));
		$this->assertContains('--' . $boundary, $message);
		$this->assertContains('--' . $boundary . '--', $message);
		$this->assertContains('--alt-' . $boundary, $message);
		$this->assertContains('--alt-' . $boundary . '--', $message);
	}

/**
 * testSendAttachment method
 *
 * @return void
 */
	public function testSendAttachment() {
		$this->Email->reset();
		$this->Email->transport('debug');
		$this->Email->from('cake@cakephp.org');
		$this->Email->to(array('you@cakephp.org' => 'You'));
		$this->Email->subject('My title');
		$this->Email->config(array());
		$this->Email->attachments(array(CAKE . 'basics.php'));
		$result = $this->Email->send('body');
		$this->assertContains("Content-Type: text/x-php\r\nContent-Transfer-Encoding: base64\r\nContent-Disposition: attachment; filename=\"basics.php\"", $result['message']);

		$this->Email->attachments(array('my.file.txt' => CAKE . 'basics.php'));
		$result = $this->Email->send('body');
		$this->assertContains("Content-Type: text/x-php\r\nContent-Transfer-Encoding: base64\r\nContent-Disposition: attachment; filename=\"my.file.txt\"", $result['message']);

		$this->Email->attachments(array('file.txt' => array('file' => CAKE . 'basics.php', 'mimetype' => 'text/plain')));
		$result = $this->Email->send('body');
		$this->assertContains("Content-Type: text/plain\r\nContent-Transfer-Encoding: base64\r\nContent-Disposition: attachment; filename=\"file.txt\"", $result['message']);

		$this->Email->attachments(array('file2.txt' => array('file' => CAKE . 'basics.php', 'mimetype' => 'text/plain', 'contentId' => 'a1b1c1')));
		$result = $this->Email->send('body');
		$this->assertContains("Content-Type: text/plain\r\nContent-Transfer-Encoding: base64\r\nContent-ID: <a1b1c1>\r\nContent-Disposition: inline; filename=\"file2.txt\"", $result['message']);
	}

/**
 * testDeliver method
 *
 * @return void
 */
	public function testDeliver() {
		$instance = Email::deliver('all@cakephp.org', 'About', 'Everything ok', array('from' => 'root@cakephp.org'), false);
		$this->assertInstanceOf('Email', $instance);
		$this->assertSame($instance->to(), array('all@cakephp.org' => 'all@cakephp.org'));
		$this->assertSame($instance->subject(), 'About');
		$this->assertSame($instance->from(), array('root@cakephp.org' => 'root@cakephp.org'));

		$config = array(
			'from' => 'cake@cakephp.org',
			'to' => 'debug@cakephp.org',
			'subject' => 'Update ok',
			'template' => 'custom',
			'layout' => 'custom_layout',
			'viewVars' => array('value' => 123),
			'cc' => array('cake@cakephp.org' => 'Myself')
		);
		$instance = Email::deliver(null, null, array('name' => 'CakePHP'), $config, false);
		$this->assertSame($instance->from(), array('cake@cakephp.org' => 'cake@cakephp.org'));
		$this->assertSame($instance->to(), array('debug@cakephp.org' => 'debug@cakephp.org'));
		$this->assertSame($instance->subject(), 'Update ok');
		$this->assertSame($instance->template(), array('template' => 'custom', 'layout' => 'custom_layout'));
		$this->assertSame($instance->viewVars(), array('value' => 123, 'name' => 'CakePHP'));
		$this->assertSame($instance->cc(), array('cake@cakephp.org' => 'Myself'));

		$configs = array('from' => 'root@cakephp.org', 'message' => 'Message from configs', 'transport' => 'Debug');
		$instance = Email::deliver('all@cakephp.org', 'About', null, $configs, true);
		$message = $instance->message();
		$this->assertEquals($configs['message'], $message[0]);
	}

/**
 * testMessage method
 *
 * @return void
 */
	public function testMessage() {
		$this->Email->reset();
		$this->Email->transport('debug');
		$this->Email->from('cake@cakephp.org');
		$this->Email->to(array('you@cakephp.org' => 'You'));
		$this->Email->subject('My title');
		$this->Email->config(array('empty'));
		$this->Email->template('default', 'default');
		$this->Email->emailFormat('both');
		$this->Email->send();

		$expected = '<p>This email was sent using the <a href="https://cakephp.org">CakePHP Framework</a></p>';
		$this->assertContains($expected, $this->Email->message(Email::MESSAGE_HTML));

		$expected = 'This email was sent using the CakePHP Framework, https://cakephp.org.';
		$this->assertContains($expected, $this->Email->message(Email::MESSAGE_TEXT));

		$message = $this->Email->message();
		$this->assertContains('Content-Type: text/plain; charset=UTF-8', $message);
		$this->assertContains('Content-Type: text/html; charset=UTF-8', $message);

		// UTF-8 is 8bit
		$this->assertTrue($this->_checkContentTransferEncoding($message, '8bit'));

		$this->Email->charset = 'ISO-2022-JP';
		$this->Email->send();
		$message = $this->Email->message();
		$this->assertContains('Content-Type: text/plain; charset=ISO-2022-JP', $message);
		$this->assertContains('Content-Type: text/html; charset=ISO-2022-JP', $message);

		// ISO-2022-JP is 7bit
		$this->assertTrue($this->_checkContentTransferEncoding($message, '7bit'));
	}

/**
 * testReset method
 *
 * @return void
 */
	public function testReset() {
		$this->Email->to('cake@cakephp.org');
		$this->Email->theme('TestTheme');
		$this->Email->emailPattern('/.+@.+\..+/i');
		$this->assertSame(array('cake@cakephp.org' => 'cake@cakephp.org'), $this->Email->to());

		$this->Email->reset();
		$this->assertSame(array(), $this->Email->to());
		$this->assertNull($this->Email->theme());
		$this->assertSame(Email::EMAIL_PATTERN, $this->Email->emailPattern());
	}

/**
 * testReset with charset
 *
 * @return void
 */
	public function testResetWithCharset() {
		$this->Email->charset = 'ISO-2022-JP';
		$this->Email->reset();

		$this->assertSame('utf-8', $this->Email->charset, $this->Email->charset);
		$this->assertNull($this->Email->headerCharset, $this->Email->headerCharset);
	}

/**
 * testWrap method
 *
 * @return void
 */
	public function testWrap() {
		$text = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec ac turpis orci, non commodo odio. Morbi nibh nisi, vehicula pellentesque accumsan amet.';
		$result = $this->Email->wrap($text, Email::LINE_LENGTH_SHOULD);
		$expected = array(
			'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec ac turpis orci,',
			'non commodo odio. Morbi nibh nisi, vehicula pellentesque accumsan amet.',
			''
		);
		$this->assertSame($expected, $result);

		$text = 'Lorem ipsum dolor sit amet, consectetur < adipiscing elit. Donec ac turpis orci, non commodo odio. Morbi nibh nisi, vehicula > pellentesque accumsan amet.';
		$result = $this->Email->wrap($text, Email::LINE_LENGTH_SHOULD);
		$expected = array(
			'Lorem ipsum dolor sit amet, consectetur < adipiscing elit. Donec ac turpis',
			'orci, non commodo odio. Morbi nibh nisi, vehicula > pellentesque accumsan',
			'amet.',
			''
		);
		$this->assertSame($expected, $result);

		$text = '<p>Lorem ipsum dolor sit amet,<br> consectetur adipiscing elit.<br> Donec ac turpis orci, non <b>commodo</b> odio. <br /> Morbi nibh nisi, vehicula pellentesque accumsan amet.<hr></p>';
		$result = $this->Email->wrap($text, Email::LINE_LENGTH_SHOULD);
		$expected = array(
			'<p>Lorem ipsum dolor sit amet,<br> consectetur adipiscing elit.<br> Donec ac',
			'turpis orci, non <b>commodo</b> odio. <br /> Morbi nibh nisi, vehicula',
			'pellentesque accumsan amet.<hr></p>',
			''
		);
		$this->assertSame($expected, $result);

		$text = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec ac <a href="https://cakephp.org">turpis</a> orci, non commodo odio. Morbi nibh nisi, vehicula pellentesque accumsan amet.';
		$result = $this->Email->wrap($text, Email::LINE_LENGTH_SHOULD);
		$expected = array(
			'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec ac',
			'<a href="https://cakephp.org">turpis</a> orci, non commodo odio. Morbi nibh',
			'nisi, vehicula pellentesque accumsan amet.',
			''
		);
		$this->assertSame($expected, $result);

		$text = 'Lorem ipsum <a href="http://www.cakephp.org/controller/action/param1/param2" class="nice cool fine amazing awesome">ok</a>';
		$result = $this->Email->wrap($text, Email::LINE_LENGTH_SHOULD);
		$expected = array(
			'Lorem ipsum',
			'<a href="http://www.cakephp.org/controller/action/param1/param2" class="nice cool fine amazing awesome">',
			'ok</a>',
			''
		);
		$this->assertSame($expected, $result);

		$text = 'Lorem ipsum withonewordverybigMorethanthelineshouldsizeofrfcspecificationbyieeeavailableonieeesite ok.';
		$result = $this->Email->wrap($text, Email::LINE_LENGTH_SHOULD);
		$expected = array(
			'Lorem ipsum',
			'withonewordverybigMorethanthelineshouldsizeofrfcspecificationbyieeeavailableonieeesite',
			'ok.',
			''
		);
		$this->assertSame($expected, $result);
	}

/**
 * testRender method
 *
 * @return void
 */
	public function testRenderWithLayoutAndAttachment() {
		$this->Email->emailFormat('html');
		$this->Email->template('html', 'default');
		$this->Email->attachments(array(CAKE . 'basics.php'));
		$result = $this->Email->render(array());
		$this->assertNotEmpty($result);

		$result = $this->Email->getBoundary();
		$this->assertNotEmpty($result);
	}

/**
 * testConstructWithConfigArray method
 *
 * @return void
 */
	public function testConstructWithConfigArray() {
		$configs = array(
			'from' => array('some@example.com' => 'My website'),
			'to' => 'test@example.com',
			'subject' => 'Test mail subject',
			'transport' => 'Debug',
		);
		$this->Email = new Email($configs);

		$result = $this->Email->to();
		$this->assertEquals(array($configs['to'] => $configs['to']), $result);

		$result = $this->Email->from();
		$this->assertEquals($configs['from'], $result);

		$result = $this->Email->subject();
		$this->assertEquals($configs['subject'], $result);

		$result = $this->Email->transport();
		$this->assertEquals($configs['transport'], $result);

		$result = $this->Email->transportClass();
		$this->assertTrue($result instanceof DebugTransport);

		$result = $this->Email->send('This is the message');

		$this->assertTrue((bool)strpos($result['headers'], 'Message-ID: '));
		$this->assertTrue((bool)strpos($result['headers'], 'To: '));
	}

/**
 * testConfigArrayWithLayoutWithoutTemplate method
 *
 * @return void
 */
	public function testConfigArrayWithLayoutWithoutTemplate() {
		$configs = array(
			'from' => array('some@example.com' => 'My website'),
			'to' => 'test@example.com',
			'subject' => 'Test mail subject',
			'transport' => 'Debug',
			'layout' => 'custom'
		);
		$this->Email = new Email($configs);

		$result = $this->Email->template();
		$this->assertEquals('', $result['template']);
		$this->assertEquals($configs['layout'], $result['layout']);
	}

/**
 * testConstructWithConfigString method
 *
 * @return void
 */
	public function testConstructWithConfigString() {
		$configs = new TestEmailConfig();
		$this->Email = new TestCakeEmail('test');

		$result = $this->Email->to();
		$this->assertEquals($configs->test['to'], $result);

		$result = $this->Email->from();
		$this->assertEquals($configs->test['from'], $result);

		$result = $this->Email->subject();
		$this->assertEquals($configs->test['subject'], $result);

		$result = $this->Email->transport();
		$this->assertEquals($configs->test['transport'], $result);

		$result = $this->Email->transportClass();
		$this->assertTrue($result instanceof DebugTransport);

		$result = $this->Email->send('This is the message');

		$this->assertTrue((bool)strpos($result['headers'], 'Message-ID: '));
		$this->assertTrue((bool)strpos($result['headers'], 'To: '));
	}

/**
 * testViewRender method
 *
 * @return void
 */
	public function testViewRender() {
		$result = $this->Email->viewRender();
		$this->assertEquals('View', $result);

		$result = $this->Email->viewRender('Theme');
		$this->assertInstanceOf('Email', $result);

		$result = $this->Email->viewRender();
		$this->assertEquals('Theme', $result);
	}

/**
 * testEmailFormat method
 *
 * @return void
 */
	public function testEmailFormat() {
		$result = $this->Email->emailFormat();
		$this->assertEquals('text', $result);

		$result = $this->Email->emailFormat('html');
		$this->assertInstanceOf('Email', $result);

		$result = $this->Email->emailFormat();
		$this->assertEquals('html', $result);

		$this->setExpectedException('SocketException');
		$this->Email->emailFormat('invalid');
	}

/**
 * Tests that it is possible to add charset configuration to a Email object
 *
 * @return void
 */
	public function testConfigCharset() {
		$email = new Email();
		$this->assertEquals(Configure::read('App.encoding'), $email->charset);
		$this->assertEquals(Configure::read('App.encoding'), $email->headerCharset);

		$email = new Email(array('charset' => 'iso-2022-jp', 'headerCharset' => 'iso-2022-jp-ms'));
		$this->assertEquals('iso-2022-jp', $email->charset);
		$this->assertEquals('iso-2022-jp-ms', $email->headerCharset);

		$email = new Email(array('charset' => 'iso-2022-jp'));
		$this->assertEquals('iso-2022-jp', $email->charset);
		$this->assertEquals('iso-2022-jp', $email->headerCharset);

		$email = new Email(array('headerCharset' => 'iso-2022-jp-ms'));
		$this->assertEquals(Configure::read('App.encoding'), $email->charset);
		$this->assertEquals('iso-2022-jp-ms', $email->headerCharset);
	}

/**
 * Tests that the header is encoded using the configured headerCharset
 *
 * @return void
 */
	public function testHeaderEncoding() {
		$this->skipIf(!function_exists('mb_convert_encoding'));
		$email = new Email(array('headerCharset' => 'iso-2022-jp-ms', 'transport' => 'Debug'));
		$email->subject('あれ？もしかしての前と');
		$headers = $email->getHeaders(array('subject'));
		$expected = "?ISO-2022-JP?B?GyRCJCIkbCEpJGIkNyQrJDckRiROQTAkSBsoQg==?=";
		$this->assertContains($expected, $headers['Subject']);

		$email->to('someone@example.com')->from('someone@example.com');
		$result = $email->send('ってテーブルを作ってやってたらう');
		$this->assertContains('ってテーブルを作ってやってたらう', $result['message']);
	}

/**
 * Tests that the body is encoded using the configured charset
 *
 * @return void
 */
	public function testBodyEncoding() {
		$this->skipIf(!function_exists('mb_convert_encoding'));
		$email = new Email(array(
			'charset' => 'iso-2022-jp',
			'headerCharset' => 'iso-2022-jp-ms',
			'transport' => 'Debug'
		));
		$email->subject('あれ？もしかしての前と');
		$headers = $email->getHeaders(array('subject'));
		$expected = "?ISO-2022-JP?B?GyRCJCIkbCEpJGIkNyQrJDckRiROQTAkSBsoQg==?=";
		$this->assertContains($expected, $headers['Subject']);

		$email->to('someone@example.com')->from('someone@example.com');
		$result = $email->send('ってテーブルを作ってやってたらう');
		$this->assertContains('Content-Type: text/plain; charset=ISO-2022-JP', $result['headers']);
		$this->assertContains(mb_convert_encoding('ってテーブルを作ってやってたらう', 'ISO-2022-JP'), $result['message']);
	}

/**
 * Tests that the body is encoded using the configured charset (Japanese standard encoding)
 *
 * @return void
 */
	public function testBodyEncodingIso2022Jp() {
		$this->skipIf(!function_exists('mb_convert_encoding'));
		$email = new Email(array(
			'charset' => 'iso-2022-jp',
			'headerCharset' => 'iso-2022-jp',
			'transport' => 'Debug'
		));
		$email->subject('あれ？もしかしての前と');
		$headers = $email->getHeaders(array('subject'));
		$expected = "?ISO-2022-JP?B?GyRCJCIkbCEpJGIkNyQrJDckRiROQTAkSBsoQg==?=";
		$this->assertContains($expected, $headers['Subject']);

		$email->to('someone@example.com')->from('someone@example.com');
		$result = $email->send('①㈱');
		$this->assertTextContains("Content-Type: text/plain; charset=ISO-2022-JP", $result['headers']);
		$this->assertTextNotContains("Content-Type: text/plain; charset=ISO-2022-JP-MS", $result['headers']); // not charset=iso-2022-jp-ms
		$this->assertTextNotContains(mb_convert_encoding('①㈱', 'ISO-2022-JP-MS'), $result['message']);
	}

/**
 * Tests that the body is encoded using the configured charset (Japanese irregular encoding, but sometime use this)
 *
 * @return void
 */
	public function testBodyEncodingIso2022JpMs() {
		$this->skipIf(!function_exists('mb_convert_encoding'));
		$email = new Email(array(
			'charset' => 'iso-2022-jp-ms',
			'headerCharset' => 'iso-2022-jp-ms',
			'transport' => 'Debug'
		));
		$email->subject('あれ？もしかしての前と');
		$headers = $email->getHeaders(array('subject'));
		$expected = "?ISO-2022-JP?B?GyRCJCIkbCEpJGIkNyQrJDckRiROQTAkSBsoQg==?=";
		$this->assertContains($expected, $headers['Subject']);

		$email->to('someone@example.com')->from('someone@example.com');
		$result = $email->send('①㈱');
		$this->assertTextContains("Content-Type: text/plain; charset=ISO-2022-JP", $result['headers']);
		$this->assertTextNotContains("Content-Type: text/plain; charset=iso-2022-jp-ms", $result['headers']); // not charset=iso-2022-jp-ms
		$this->assertContains(mb_convert_encoding('①㈱', 'ISO-2022-JP-MS'), $result['message']);
	}

	protected function _checkContentTransferEncoding($message, $charset) {
		$boundary = '--' . $this->Email->getBoundary();
		$result['text'] = false;
		$result['html'] = false;
		$length = count($message);
		for ($i = 0; $i < $length; ++$i) {
			if ($message[$i] === $boundary) {
				$flag = false;
				$type = '';
				while (!preg_match('/^$/', $message[$i])) {
					if (preg_match('/^Content-Type: text\/plain/', $message[$i])) {
						$type = 'text';
					}
					if (preg_match('/^Content-Type: text\/html/', $message[$i])) {
						$type = 'html';
					}
					if ($message[$i] === 'Content-Transfer-Encoding: ' . $charset) {
						$flag = true;
					}
					++$i;
				}
				$result[$type] = $flag;
			}
		}
		return $result['text'] && $result['html'];
	}

/**
 * Test Email::_encode function
 *
 * @return void
 */
	public function testEncode() {
		$this->skipIf(!function_exists('mb_convert_encoding'));

		$this->Email->headerCharset = 'ISO-2022-JP';
		$result = $this->Email->encode('日本語');
		$expected = '=?ISO-2022-JP?B?GyRCRnxLXDhsGyhC?=';
		$this->assertSame($expected, $result);

		$this->Email->headerCharset = 'ISO-2022-JP';
		$result = $this->Email->encode('長い長い長いSubjectの場合はfoldingするのが正しいんだけどいったいどうなるんだろう？');
		$expected = "=?ISO-2022-JP?B?GyRCRDkkJEQ5JCREOSQkGyhCU3ViamVjdBskQiROPmw5ZyRPGyhCZm9s?=\r\n" .
			" =?ISO-2022-JP?B?ZGluZxskQiQ5JGskTiQsQDUkNyQkJHMkQCQxJEkkJCRDJD8kJCRJGyhC?=\r\n" .
			" =?ISO-2022-JP?B?GyRCJCYkSiRrJHMkQCRtJCYhKRsoQg==?=";
		$this->assertSame($expected, $result);
	}

/**
 * Tests charset setter/getter
 *
 * @return void
 */
	public function testCharset() {
		$this->Email->charset('UTF-8');
		$this->assertSame($this->Email->charset(), 'UTF-8');

		$this->Email->charset('ISO-2022-JP');
		$this->assertSame($this->Email->charset(), 'ISO-2022-JP');

		$charset = $this->Email->charset('Shift_JIS');
		$this->assertSame($charset, 'Shift_JIS');
	}

/**
 * Tests headerCharset setter/getter
 *
 * @return void
 */
	public function testHeaderCharset() {
		$this->Email->headerCharset('UTF-8');
		$this->assertSame($this->Email->headerCharset(), 'UTF-8');

		$this->Email->headerCharset('ISO-2022-JP');
		$this->assertSame($this->Email->headerCharset(), 'ISO-2022-JP');

		$charset = $this->Email->headerCharset('Shift_JIS');
		$this->assertSame($charset, 'Shift_JIS');
	}

/**
 * Tests for compatible check.
 *          charset property and       charset() method.
 *    headerCharset property and headerCharset() method.
 *
 * @return void
 */
	public function testCharsetsCompatible() {
		$this->skipIf(!function_exists('mb_convert_encoding'));

		$checkHeaders = array(
			'from' => true,
			'to' => true,
			'cc' => true,
			'subject' => true,
		);

		// Header Charset : null (used by default UTF-8)
		//   Body Charset : ISO-2022-JP
		$oldStyleEmail = $this->_getEmailByOldStyleCharset('iso-2022-jp', null);
		$oldStyleHeaders = $oldStyleEmail->getHeaders($checkHeaders);

		$newStyleEmail = $this->_getEmailByNewStyleCharset('iso-2022-jp', null);
		$newStyleHeaders = $newStyleEmail->getHeaders($checkHeaders);

		$this->assertSame($oldStyleHeaders['From'], $newStyleHeaders['From']);
		$this->assertSame($oldStyleHeaders['To'], $newStyleHeaders['To']);
		$this->assertSame($oldStyleHeaders['Cc'], $newStyleHeaders['Cc']);
		$this->assertSame($oldStyleHeaders['Subject'], $newStyleHeaders['Subject']);

		// Header Charset : UTF-8
		//   Boby Charset : ISO-2022-JP
		$oldStyleEmail = $this->_getEmailByOldStyleCharset('iso-2022-jp', 'utf-8');
		$oldStyleHeaders = $oldStyleEmail->getHeaders($checkHeaders);

		$newStyleEmail = $this->_getEmailByNewStyleCharset('iso-2022-jp', 'utf-8');
		$newStyleHeaders = $newStyleEmail->getHeaders($checkHeaders);

		$this->assertSame($oldStyleHeaders['From'], $newStyleHeaders['From']);
		$this->assertSame($oldStyleHeaders['To'], $newStyleHeaders['To']);
		$this->assertSame($oldStyleHeaders['Cc'], $newStyleHeaders['Cc']);
		$this->assertSame($oldStyleHeaders['Subject'], $newStyleHeaders['Subject']);

		// Header Charset : ISO-2022-JP
		//   Boby Charset : UTF-8
		$oldStyleEmail = $this->_getEmailByOldStyleCharset('utf-8', 'iso-2022-jp');
		$oldStyleHeaders = $oldStyleEmail->getHeaders($checkHeaders);

		$newStyleEmail = $this->_getEmailByNewStyleCharset('utf-8', 'iso-2022-jp');
		$newStyleHeaders = $newStyleEmail->getHeaders($checkHeaders);

		$this->assertSame($oldStyleHeaders['From'], $newStyleHeaders['From']);
		$this->assertSame($oldStyleHeaders['To'], $newStyleHeaders['To']);
		$this->assertSame($oldStyleHeaders['Cc'], $newStyleHeaders['Cc']);
		$this->assertSame($oldStyleHeaders['Subject'], $newStyleHeaders['Subject']);
	}

/**
 * @param mixed $charset
 * @param mixed $headerCharset
 * @return Email
 */
	protected function _getEmailByOldStyleCharset($charset, $headerCharset) {
		$email = new Email(array('transport' => 'Debug'));

		if (!empty($charset)) {
			$email->charset = $charset;
		}
		if (!empty($headerCharset)) {
			$email->headerCharset = $headerCharset;
		}

		$email->from('someone@example.com', 'どこかの誰か');
		$email->to('someperson@example.jp', 'どこかのどなたか');
		$email->cc('miku@example.net', 'ミク');
		$email->subject('テストメール');
		$email->send('テストメールの本文');

		return $email;
	}

/**
 * @param mixed $charset
 * @param mixed $headerCharset
 * @return Email
 */
	protected function _getEmailByNewStyleCharset($charset, $headerCharset) {
		$email = new Email(array('transport' => 'Debug'));

		if (!empty($charset)) {
			$email->charset($charset);
		}
		if (!empty($headerCharset)) {
			$email->headerCharset($headerCharset);
		}

		$email->from('someone@example.com', 'どこかの誰か');
		$email->to('someperson@example.jp', 'どこかのどなたか');
		$email->cc('miku@example.net', 'ミク');
		$email->subject('テストメール');
		$email->send('テストメールの本文');

		return $email;
	}

/**
 * testWrapLongLine()
 *
 * @return void
 */
	public function testWrapLongLine() {
		$message = '<a href="http://cakephp.org">' . str_repeat('x', Email::LINE_LENGTH_MUST) . "</a>";

		$this->Email->reset();
		$this->Email->transport('Debug');
		$this->Email->from('cake@cakephp.org');
		$this->Email->to('cake@cakephp.org');
		$this->Email->subject('Wordwrap Test');
		$this->Email->config(array('empty'));
		$result = $this->Email->send($message);
		$expected = "<a\r\n" . 'href="http://cakephp.org">' . str_repeat('x', Email::LINE_LENGTH_MUST - 26) . "\r\n" .
			str_repeat('x', 26) . "\r\n</a>\r\n\r\n";
		$this->assertEquals($expected, $result['message']);
		$this->assertLineLengths($result['message']);

		$str1 = "a ";
		$str2 = " b";
		$length = strlen($str1) + strlen($str2);
		$message = $str1 . str_repeat('x', Email::LINE_LENGTH_MUST - $length - 1) . $str2;

		$result = $this->Email->send($message);
		$expected = "{$message}\r\n\r\n";
		$this->assertEquals($expected, $result['message']);
		$this->assertLineLengths($result['message']);

		$message = $str1 . str_repeat('x', Email::LINE_LENGTH_MUST - $length) . $str2;

		$result = $this->Email->send($message);
		$expected = "{$message}\r\n\r\n";
		$this->assertEquals($expected, $result['message']);
		$this->assertLineLengths($result['message']);

		$message = $str1 . str_repeat('x', Email::LINE_LENGTH_MUST - $length + 1) . $str2;

		$result = $this->Email->send($message);
		$expected = $str1 . str_repeat('x', Email::LINE_LENGTH_MUST - $length + 1) . sprintf("\r\n%s\r\n\r\n", trim($str2));
		$this->assertEquals($expected, $result['message']);
		$this->assertLineLengths($result['message']);
	}

/**
 * testWrapWithTagsAcrossLines()
 *
 * @return void
 */
	public function testWrapWithTagsAcrossLines() {
		$str = <<<HTML
<table>
<th align="right" valign="top"
        style="font-weight: bold">The tag is across multiple lines</th>
</table>
HTML;
		$message = $str . str_repeat('x', Email::LINE_LENGTH_MUST + 1);

		$this->Email->reset();
		$this->Email->transport('Debug');
		$this->Email->from('cake@cakephp.org');
		$this->Email->to('cake@cakephp.org');
		$this->Email->subject('Wordwrap Test');
		$this->Email->config(array('empty'));
		$result = $this->Email->send($message);
		$message = str_replace("\r\n", "\n", substr($message, 0, -9));
		$message = str_replace("\n", "\r\n", $message);
		$expected = "{$message}\r\nxxxxxxxxx\r\n\r\n";
		$this->assertEquals($expected, $result['message']);
		$this->assertLineLengths($result['message']);
	}

/**
 * CakeEmailTest::testWrapIncludeLessThanSign()
 *
 * @return void
 */
	public function testWrapIncludeLessThanSign() {
		$str = 'foo<bar';
		$length = strlen($str);
		$message = $str . str_repeat('x', Email::LINE_LENGTH_MUST - $length + 1);

		$this->Email->reset();
		$this->Email->transport('Debug');
		$this->Email->from('cake@cakephp.org');
		$this->Email->to('cake@cakephp.org');
		$this->Email->subject('Wordwrap Test');
		$this->Email->config(array('empty'));
		$result = $this->Email->send($message);
		$message = substr($message, 0, -1);
		$expected = "{$message}\r\nx\r\n\r\n";
		$this->assertEquals($expected, $result['message']);
		$this->assertLineLengths($result['message']);
	}

/**
 * CakeEmailTest::testWrapForJapaneseEncoding()
 *
 * @return void
 */
	public function testWrapForJapaneseEncoding() {
		$this->skipIf(!function_exists('mb_convert_encoding'));

		$message = mb_convert_encoding('受け付けました', 'iso-2022-jp', 'UTF-8');

		$this->Email->reset();
		$this->Email->transport('Debug');
		$this->Email->from('cake@cakephp.org');
		$this->Email->to('cake@cakephp.org');
		$this->Email->subject('Wordwrap Test');
		$this->Email->config(array('empty'));
		$this->Email->charset('iso-2022-jp');
		$this->Email->headerCharset('iso-2022-jp');
		$result = $this->Email->send($message);
		$expected = "{$message}\r\n\r\n";
		$this->assertEquals($expected, $result['message']);
	}

/**
 * testZeroOnlyLinesNotBeingEmptied()
 *
 * @return void
 */
	public function testZeroOnlyLinesNotBeingEmptied() {
		$message = "Lorem\r\n0\r\n0\r\nipsum";

		$this->Email->reset();
		$this->Email->transport('Debug');
		$this->Email->from('cake@cakephp.org');
		$this->Email->to('cake@cakephp.org');
		$this->Email->subject('Wordwrap Test');
		$this->Email->config(array('empty'));
		$result = $this->Email->send($message);
		$expected = "{$message}\r\n\r\n";
		$this->assertEquals($expected, $result['message']);
	}

/**
 * Test that really long lines don't cause errors.
 *
 * @return void
 */
	public function testReallyLongLine() {
		$this->Email->reset();
		$this->Email->config(array('empty'));
		$this->Email->transport('Debug');
		$this->Email->from('cake@cakephp.org');
		$this->Email->to('cake@cakephp.org');
		$this->Email->subject('Wordwrap Test');
		$this->Email->emailFormat('html');
		$this->Email->template('long_line', null);
		$result = $this->Email->send();
		$this->assertContains('<a>', $result['message'], 'First bits are included');
		$this->assertContains('x', $result['message'], 'Last byte are included');
	}

/**
 * CakeEmailTest::assertLineLengths()
 *
 * @param string $message
 * @return void
 */
	public function assertLineLengths($message) {
		$lines = explode("\r\n", $message);
		foreach ($lines as $line) {
			$this->assertTrue(strlen($line) <= Email::LINE_LENGTH_MUST,
				'Line length exceeds the max. limit of Email::LINE_LENGTH_MUST');
		}
	}

}
