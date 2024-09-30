<?php
App::uses('AppModel', 'Model');
/**
 * Message Model
 *
 * @property Conversation $Conversation
 * @property User $User
 */
class Message extends AppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */
	public $useTable = 'message';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'conversation_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'user_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'content' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'created' => array(
			'datetime' => array(
				'rule' => array('datetime'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'modified' => array(
            'datetime' => array(
                'rule' => array('datetime'),
                'allowEmpty' => true,
            ),
        ),
        'created_ip' => array(
            'ip' => array(
                'rule' => array('ip'),
                'allowEmpty' => false,
            ),
        ),
        'modified_ip' => array(
            'ip' => array(
                'rule' => array('ip'),
                'allowEmpty' => true,
            ),
        ),

	);
/**
 * beforeSave callback
 *
 * @param array $options
 * @return bool
 */
	public function beforeSave($options = array()) {
        if (empty($this->data['Message']['id'])) {
            $ipAddress = env('HTTP_CLIENT_IP') ? env('HTTP_CLIENT_IP') : env('REMOTE_ADDR');
            $this->data['Message']['created_ip'] = $ipAddress;
            $this->data['Message']['modified_ip'] = $ipAddress;
        }

        return true;
	}

	// The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Conversation' => array(
			'className' => 'Conversation',
			'foreignKey' => 'conversation_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
