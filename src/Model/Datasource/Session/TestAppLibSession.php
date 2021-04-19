<?php
/**
 * Test suite app/Model/Datasource/Session session handler
 */
namespace lib\Cake\Test\test_app\Model\Datasource\Session;



class TestAppLibSession implements SessionHandlerInterface {

	public function open() {
		return true;
	}

	public function close() {
		return true;
	}

	public function read($id) {
		return '';
	}

	public function write($id, $data) {
		return true;
	}

	public function destroy($id) {
		return true;
	}

	public function gc($expires = null) {
		return true;
	}

}
