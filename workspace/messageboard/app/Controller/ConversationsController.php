<?php
App::uses('AppController', 'Controller');
/**
 * Conversations Controller
 *
 * @property Conversation $Conversation
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 * @property FlashComponent $Flash
 */
class ConversationsController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Session', 'Flash', 'RequestHandler');

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$currentUserId = $this->Auth->user('id');

		// GET AND PAGINATE CONVERSATIONS
		$this->Paginator->settings = array(
			'joins' => array(
				array(
					'table' => 'message',
					'alias' => 'Message',
					'type' => 'LEFT',
					'conditions' => array(
						'Message.conversation_id = Conversation.id',
						'Message.created = (SELECT MAX(created) FROM message WHERE conversation_id = Conversation.id)' 
					)
				)
			),
			'conditions' => array(
				'OR' => array(
					'Conversation.sender_id' => $currentUserId,
					'Conversation.receiver_id' => $currentUserId
				)
			),
			'order' => array('Message.created' => 'DESC'), 
			'limit' => 5,
			'page' => $this->request->query('page') ?: 1
		);
		$conversations = $this->Paginator->paginate('Conversation');

		$userConversations = $this->Conversation->UserConversation->find('list', array(
			'conditions' => array(
				'UserConversation.user_id' => $currentUserId,
				'UserConversation.is_deleted' => false
			),
			'fields' => array('UserConversation.conversation_id')
		));
		$conversations = array_filter($conversations, function($conversation) use ($userConversations) {
			return in_array($conversation['Conversation']['id'], $userConversations);
		});
		foreach ($conversations as &$conversation) {
			$lastMessage = $this->Conversation->Message->find('first', array(
				'conditions' => array('Message.conversation_id' => $conversation['Conversation']['id']),
				'order' => array('Message.created' => 'DESC')
			));
			if (!empty($lastMessage)) {
				$conversation['last_message'] = $lastMessage['Message']['content'];
				$conversation['last_created'] = (new DateTime($lastMessage['Message']['created']))->format('M d, Y h:i A');
				$conversation['last_message_user_id'] = $lastMessage['Message']['user_id'];
			} else {
				$conversation['last_message'] = __('No messages yet.');
				$conversation['last_created'] = null;
			}
		}
		
		// GET USERS
		$usersData = $this->Conversation->User->find('all', array(
			'fields' => array('User.id', 'User.name', 'User.profile_picture')
		));
		$users = array();
		foreach ($usersData as $user) {
			$users[$user['User']['id']] = array(
				'name' => $user['User']['name'],
				'profile_picture' => $user['User']['profile_picture']
			);
		}

		$this->set(compact('conversations', 'users', 'currentUserId'));
	}


/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Conversation->exists($id)) {
			throw new NotFoundException(__('Invalid conversation'));
		}

		$options = array('conditions' => array('Conversation.' . $this->Conversation->primaryKey => $id));
		$conversation = $this->Conversation->find('first', $options);

		// GET LOGGED IN USER
		$loggedInUserId = $this->Auth->user('id');
		if (!in_array($loggedInUserId, array($conversation['Conversation']['sender_id'], $conversation['Conversation']['receiver_id']))) {
			$this->Flash->error(__('You do not have permission to view this conversation.'));
			return $this->redirect(array('action' => 'index'));
		}

		// GET OTHER USER
		$otherUserId = ($conversation['Conversation']['sender_id'] == $loggedInUserId) ? $conversation['Conversation']['receiver_id'] : $conversation['Conversation']['sender_id'];
		$otherUser = $this->Conversation->User->findById($otherUserId);

		// GET MESSAGES
		$limit = 5;
		$page = $this->request->query('page') ?: 1;
		$this->Paginator->settings = array(
			'conditions' => array('Message.conversation_id' => $id),
			'order' => array('Message.created' => 'DESC'),
			'limit' => $limit,
        	'offset' => ($page - 1) * $limit
		);
		
		$messages = $this->Paginator->paginate('Message');
		$hasMore = count($messages) === $limit && $this->Conversation->Message->find('count', array('conditions' => array('Message.conversation_id' => $id))) > $limit;

		$data = array(
			'conversation' => array(
				'id' => $conversation['Conversation']['id'],
				'sender_id' => $conversation['Conversation']['sender_id'],
				'receiver_id' => $conversation['Conversation']['receiver_id'],
			),
			'messages' => array_map(function($message) {
				return array(
					'id' => $message['Message']['id'],
					'content' => $message['Message']['content'],
					'created' => date('H:i A - F d, Y', strtotime($message['Message']['created'])),
					'user_id' => $message['Message']['user_id'],
				);
			}, $messages),
			'loggedInUserId' => $loggedInUserId,
			'otherUser' => array(
				'id' => $otherUser['User']['id'],
				'name' => $otherUser['User']['name'],
				'profile_picture' => $otherUser['User']['profile_picture'],
			),
			'hasMore' => $hasMore,
		);

		if ($this->request->is('ajax')) {
			$this->autoRender = false; 
			$this->response->type('json'); 

			echo json_encode($data); 
			return; 
		}

		$this->set(compact('conversation', 'messages', 'loggedInUserId', 'otherUser'));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		$loggedInUserId = $this->Auth->user('id');
		$users = $this->Conversation->User->find('list', array(
			'conditions' => array('User.id !=' => $loggedInUserId),
			'fields' => array('User.id', 'User.name'), 
			'order' => array('User.name' => 'ASC')
		));
		
		$this->set(compact('users'));

		if ($this->request->is('post')) {
			$data = $this->request->data['Conversation'];
			
			$existingConversation = $this->Conversation->find('first', array(
				'conditions' => array(
					'OR' => array(
						array('Conversation.sender_id' => $loggedInUserId, 'Conversation.receiver_id' => $data['receiver_id']),
						array('Conversation.sender_id' => $data['receiver_id'], 'Conversation.receiver_id' => $loggedInUserId)
					)
				)
			));

			$conversationId = $existingConversation ? $existingConversation['Conversation']['id'] : $this->createConversation($loggedInUserId, $data['receiver_id']);
			
			if ($conversationId) {
				$messageData = array(
					'conversation_id' => $conversationId,
					'user_id' => $loggedInUserId,
					'content' => $data['message'],
					'created' => date('Y-m-d H:i:s'),
				);

				if ($this->Conversation->Message->save($messageData)) {
					return $this->redirect(array('action' => 'index'));
				} else {
					$this->Flash->error(__('The message could not be saved. Please try again.'));
				}
			}
		}
	}

	private function createConversation($loggedInUserId, $receiverId) {
		$this->Conversation->create();
		if ($this->Conversation->save(array('sender_id' => $loggedInUserId, 'receiver_id' => $receiverId))) {
			$conversationId = $this->Conversation->id;
			$this->saveUserConversations($conversationId, array($loggedInUserId, $receiverId));
			return $conversationId;
		}
		$this->Flash->error(__('The conversation could not be saved. Please try again.'));
		return null;
	}

	private function saveUserConversations($conversationId, $userIds) {
		foreach ($userIds as $userId) {
			$this->Conversation->UserConversation->create();
			if (!$this->Conversation->UserConversation->save(array('user_id' => $userId, 'conversation_id' => $conversationId))) {
				$this->Flash->error(__('The UserConversation entry could not be saved. Please try again.'));
			}
		}
	}


/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->Conversation->exists($id)) {
			throw new NotFoundException(__('Invalid conversation'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->Conversation->save($this->request->data)) {
				$this->Flash->success(__('The conversation has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The conversation could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Conversation.' . $this->Conversation->primaryKey => $id));
			$this->request->data = $this->Conversation->find('first', $options);
		}
		$users = $this->Conversation->User->find('list');
		$this->set(compact('users'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		if (!$this->Conversation->exists($id)) {
			throw new NotFoundException(__('Invalid conversation'));
		}
		$this->request->allowMethod('post', 'delete');

		if ($this->Conversation->Message->deleteAll(array('Message.conversation_id' => $id), false)) {
			if ($this->Conversation->delete($id)) {
				// $this->Flash->success(__('The conversation has been deleted.'));
			} else {
				$this->Flash->error(__('The conversation could not be deleted. Please, try again.'));
			}
		} else {
			$this->Flash->error(__('The messages could not be deleted. Please, try again.'));
		}
		
		return $this->redirect(array('action' => 'index'));
	}
}
