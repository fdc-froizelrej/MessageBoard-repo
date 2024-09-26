<?php
App::uses('AppModel', 'Model');

/**
 * User Model
 *
 * @property Message $Message
 * @property Conversation $Conversation
 */
class User extends AppModel {

/**
 * Use table
 *
 * @var mixed False or table name
 */
    public $useTable = 'user';

/**
 * Display field
 *
 * @var string
 */
    public $displayField = 'name';

/**
 * Validation rules
 *
 * @var array
 */
    public $validate = array(
        'id' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
            ),
        ),
        'name' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Name cannot be empty.',
                'allowEmpty' => false,
                'required' => true,
            ),
            'length' => array(
                'rule' => array('lengthBetween', 5, 20),
                'message' => 'Name must be between 5 and 20 characters.',
                'allowEmpty' => false,
                'required' => true,
            ),
        ),
        'email' => array(
            'email' => array(
                'rule' => array('email'),
                'message' => 'Please enter a valid email address.',
                'allowEmpty' => false,
                'required' => true,
            ),
            'unique' => array(
                'rule' => 'isUnique',
                'message' => 'This email address is already in use.',
                'allowEmpty' => false,
                'required' => true,
            ),
        ),
        'password' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Please enter your password.',
            ),
            'matchPassword' => array(
                'rule' => 'matchPassword',
                'message' => 'Passwords do not match.',
            ),
        ),
        'confirm_password' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Please confirm your password.',
            ),
        ),
        'old_password' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Old password cannot be empty.',
                'allowEmpty' => true,
                'required' => false,
            ),
            'checkCurrentPassword' => array(
                'rule' => 'checkCurrentPassword',
                'message' => 'Old password is incorrect.',
            ),
        ),
        'new_password' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'New password cannot be empty.',
                'allowEmpty' => true,
                'required' => false,
            ),
            'length' => array(
                'rule' => array('lengthBetween', 6, 20),
                'message' => 'New password must be between 6 and 20 characters.',
                'allowEmpty' => true,
                'required' => false,
            ),
        ),
        'confirm_new_password' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Please confirm your new password.',
                'allowEmpty' => true,
                'required' => false,
            ),
            'matchNewPassword' => array(
                'rule' => 'matchNewPassword',
                'message' => 'Passwords do not match.',
            ),
        ),
        'profile_picture' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Profile picture cannot be empty.',
                'allowEmpty' => true,
                'required' => false,
            ),
            'file' => array(
                'rule' => array('checkFileType', array('image/jpeg', 'image/png', 'image/gif')),
                'message' => 'Please upload a valid image file (JPG, PNG, GIF).',
                'allowEmpty' => true,
                'required' => false,
            ),
        ),
        'birthday' => array(
            'date' => array(
                'rule' => array('date'),
            ),
        ),
        'gender' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
                'message' => 'Gender cannot be empty.',
            ),
            'validValues' => array(
                'rule' => array('inList', array('Male', 'Female')),
                'message' => 'Please select a valid gender.',
            ),
        ),
        'hobby' => array(
            'notBlank' => array(
                'rule' => array('notBlank'),
            ),
        ),
        'joined_date' => array(
            'date' => array(
                'rule' => array('datetime'),
            ),
        ),
        'last_logged_in' => array(
            'date' => array(
                'rule' => array('datetime'),
            ),
        ),
    );

    public function beforeValidate($options = array()) {
        parent::beforeValidate($options);
        
        if (isset($this->data['User']['id'])) {
            // Editing profile
            unset($this->validate['password']);
            unset($this->validate['confirm_password']);
        } else {
            // Registering new user
            unset($this->validate['old_password']);
            unset($this->validate['new_password']);
            unset($this->validate['confirm_new_password']);
        }
    
        return true;
    }
    
    
    public function checkCurrentPassword($data) {
        return $this->Auth->identify(['email' => $this->data['User']['email'], 'password' => $data['old_password']]);
    }
    
    public function matchNewPassword($data) {
        if ($data['confirm_new_password'] === $this->data['User']['new_password']) {
            return true;
        }
        return false;
    }
    
    public function matchPassword($data) {
        if ($this->data['User']['password'] === $this->data['User']['confirm_password']) {
            return true;
        }
        $this->invalidate('confirm_password', 'Passwords do not match.');
        return false;
    }

    public function checkFileType($file, $validTypes) {
        if (!empty($file['profile_picture']['tmp_name'])) {
            $fileType = mime_content_type($file['profile_picture']['tmp_name']);
            return in_array($fileType, $validTypes);
        }
        return true; // Allow empty files
    }

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = array(
        'Message' => array(
            'className' => 'Message',
            'foreignKey' => 'user_id',
            'dependent' => false,
        )
    );

    /**
     * hasAndBelongsToMany associations
     *
     * @var array
     */
    public $hasAndBelongsToMany = array(
        'Conversation' => array(
            'className' => 'Conversation',
            'joinTable' => 'user_conversation',
            'foreignKey' => 'user_id',
            'associationForeignKey' => 'conversation_id',
            'unique' => 'keepExisting',
        )
    );
}