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
	public $components = array('Paginator', 'Session', 'Flash');

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$currentUserId = $this->Auth->user('id');
		$conversations = $this->Conversation->find('all', array(
			'conditions' => array(
				'OR' => array(
					'Conversation.sender_id' => $currentUserId,
					'Conversation.receiver_id' => $currentUserId
				)
			)
		));

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

		foreach ($conversations as &$conversation) {
			$lastMessage = $this->Conversation->Message->find('first', array(
				'conditions' => array('Message.conversation_id' => $conversation['Conversation']['id']),
				'order' => array('Message.sent_date' => 'DESC')
			));
			if (!empty($lastMessage)) {
				$conversation['last_message'] = $lastMessage['Message']['content'];
				$conversation['last_sent_date'] = (new DateTime($lastMessage['Message']['sent_date']))->format('M d, Y h:i A');
				$conversation['last_message_user_id'] = $lastMessage['Message']['user_id'];
			} else {
				$conversation['last_message'] = __('No messages yet.');
				$conversation['last_sent_date'] = null;
			}
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
		
		$loggedInUserId = $this->Auth->user('id');

		$otherUserId = ($conversation['Conversation']['sender_id'] == $loggedInUserId) ? 
			$conversation['Conversation']['receiver_id'] : 
			$conversation['Conversation']['sender_id'];

		$otherUser = $this->Conversation->User->findById($otherUserId);

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

		$otherConversations = $this->Conversation->find('all', array(
			'conditions' => array(
				'OR' => array(
					'Conversation.sender_id' => $loggedInUserId,
					'Conversation.receiver_id' => $loggedInUserId
				),
			),
			'order' => array('Conversation.id' => 'DESC')
		));

		foreach ($otherConversations as &$conv) {
			$latestMessage = $this->Conversation->Message->find('first', [
				'conditions' => ['Message.conversation_id' => $conv['Conversation']['id']],
				'order' => ['Message.sent_date' => 'DESC']
			]);
			$conv['latestMessage'] = $latestMessage;
		}

		$this->Paginator->settings = array(
			'conditions' => array('Message.conversation_id' => $id),
			'order' => array('Message.sent_date' => 'ASC'),
			'limit' => 5,
			'page' => $this->request->query('page') ?: 1 
		);

		$messages = $this->Paginator->paginate('Message');

		$this->set(compact('conversation', 'loggedInUserId', 'otherUser', 'users', 'messages', 'otherConversations'));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		$loggedInUserId = $this->Auth->user('id');
		$users = $this->Conversation->User->find('list', [
			'conditions' => ['User.id !=' => $loggedInUserId],
			'fields' => ['User.id', 'User.name'], 
			'order' => ['User.name' => 'ASC']
		]);
		
		$this->set(compact('users'));

		if ($this->request->is('post')) {
			$data = $this->request->data['Conversation'];
			
			$existingConversation = $this->Conversation->find('first', [
				'conditions' => [
					'OR' => [
						['Conversation.sender_id' => $loggedInUserId, 'Conversation.receiver_id' => $data['receiver_id']],
						['Conversation.sender_id' => $data['receiver_id'], 'Conversation.receiver_id' => $loggedInUserId]
					]
				]
			]);

			$conversationId = $existingConversation ? $existingConversation['Conversation']['id'] : $this->createConversation($loggedInUserId, $data['receiver_id']);
			
			if ($conversationId) {
				$messageData = [
					'conversation_id' => $conversationId,
					'user_id' => $loggedInUserId,
					'content' => $data['message'],
					'sent_date' => date('Y-m-d H:i:s'),
				];

				if ($this->Conversation->Message->save($messageData)) {
					$this->Flash->success(__('The message has been sent.'));
					return $this->redirect(['action' => 'index']);
				} else {
					$this->Flash->error(__('The message could not be saved. Please try again.'));
				}
			}
		}
	}

	private function createConversation($loggedInUserId, $receiverId) {
		$this->Conversation->create();
		if ($this->Conversation->save(['sender_id' => $loggedInUserId, 'receiver_id' => $receiverId])) {
			$conversationId = $this->Conversation->id;
			$this->saveUserConversations($conversationId, [$loggedInUserId, $receiverId]);
			return $conversationId;
		}
		$this->Flash->error(__('The conversation could not be saved. Please try again.'));
		return null;
	}

	private function saveUserConversations($conversationId, $userIds) {
		foreach ($userIds as $userId) {
			$this->Conversation->UserConversation->create();
			if (!$this->Conversation->UserConversation->save(['user_id' => $userId, 'conversation_id' => $conversationId])) {
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
		if ($this->Conversation->delete($id)) {
			$this->Flash->success(__('The conversation has been deleted.'));
		} else {
			$this->Flash->error(__('The conversation could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
