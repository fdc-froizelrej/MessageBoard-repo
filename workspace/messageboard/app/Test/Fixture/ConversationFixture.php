<?php
/**
 * Conversation Fixture
 */
class ConversationFixture extends CakeTestFixture {

/**
 * Table name
 *
 * @var string
 */
	public $table = 'conversation';

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false, 'key' => 'primary'),
		'sender_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'receiver_id' => array('type' => 'integer', 'null' => false, 'default' => null, 'unsigned' => false),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
		),
		'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_0900_ai_ci', 'engine' => 'InnoDB')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'sender_id' => 1,
			'receiver_id' => 1
		),
	);

}
