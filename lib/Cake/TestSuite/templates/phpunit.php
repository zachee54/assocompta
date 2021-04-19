<?php
/**
 * Missing PHPUnit error page.
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
 * @package       Cake.TestSuite.templates
 * @since         CakePHP(tm) v 1.2.0.4433
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace lib\Cake\TestSuite\templates;

?>
<?php include dirname(__FILE__) . DS . 'header.php'; ?>
<div id="content">
	<h2>PHPUnit is not installed!</h2>
	<p>You must install PHPUnit to use the CakePHP(tm) Test Suite.</p>
	<p>PHPUnit can be installed with Composer, or downloaded as a phar archive.</p>
	<p>Once PHPUnit is installed make sure its located on PHP's <code>include_path</code> by checking your php.ini</p>
	<p>For full instructions on how to <a href="http://www.phpunit.de/manual/current/en/installation.html" target="_blank">install PHPUnit, see the PHPUnit installation guide</a>.</p>
	<p><a href="https://github.com/sebastianbergmann/phpunit" target="_blank">Download PHPUnit</a></p>
</div>
<?php
include dirname(__FILE__) . DS . 'footer.php';
