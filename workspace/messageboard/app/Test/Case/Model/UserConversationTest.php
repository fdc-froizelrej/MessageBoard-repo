<?php
App::uses('UserConversation', 'Model');

/**
 * UserConversation Test Case
 */
class UserConversationTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.user_conversation',
		'app.user',
		'app.message',
		'app.conversation'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->UserConversation = ClassRegistry::init('UserConversation');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->UserConversation);

		parent::tearDown();
	}

}
