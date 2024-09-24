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

		// Fetch conversations with the most recent message
		$conversations = $this->Conversation->find('all', [
			'conditions' => [
				'OR' => [
					'Conversation.sender_id' => $currentUserId,
					'Conversation.receiver_id' => $currentUserId
				]
			],
			'contain' => ['Message' => [
				'order' => ['Message.sent_date' => 'DESC'],
				'limit' => 1 // Limit to the most recent message
			]]
		]);

		// Fetch user data for displaying in the view
		$usersData = $this->Conversation->User->find('all', [
			'fields' => ['User.id', 'User.name', 'User.profile_picture']
		]);

		$users = [];
		foreach ($usersData as $user) {
			$users[$user['User']['id']] = [
				'name' => $user['User']['name'],
				'profile_picture' => $user['User']['profile_picture']
			];
		}

		// Prepare last message data for the view
		foreach ($conversations as &$conversation) {
			if (!empty($conversation['Message'])) {
				$lastMessage = $conversation['Message'][0]; // Get the last message
				$conversation['last_message'] = $lastMessage['content']; // Message text
				// Format the last sent date using DateTime
				$conversation['last_sent_date'] = !empty($lastMessage['sent_date']) ? 
					(new DateTime($lastMessage['sent_date']))->format('M d, Y h:i A') : null; // Format as needed
			} else {
				$conversation['last_message'] = __('No messages yet');
				$conversation['last_sent_date'] = null; // No messages
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

		// Get the conversation details
		$options = array('conditions' => array('Conversation.' . $this->Conversation->primaryKey => $id));
		$conversation = $this->Conversation->find('first', $options);

		// Get the logged-in user's ID
		$loggedInUserId = $this->Auth->user('id');

		// Fetch user data for the other participant in the conversation
		$otherUserId = ($conversation['Conversation']['sender_id'] == $loggedInUserId) ? 
			$conversation['Conversation']['receiver_id'] : 
			$conversation['Conversation']['sender_id'];

		// Fetch other user data
		$otherUser = $this->Conversation->User->findById($otherUserId);

		// Fetch all users for displaying profile pictures
		$usersData = $this->Conversation->User->find('all', [
			'fields' => ['User.id', 'User.name', 'User.profile_picture']
		]);

		$users = [];
		foreach ($usersData as $user) {
			$users[$user['User']['id']] = [
				'name' => $user['User']['name'],
				'profile_picture' => $user['User']['profile_picture']
			];
		}

		// Set the conversation, logged-in user ID, other user, and all users for the view
		$this->set(compact('conversation', 'loggedInUserId', 'otherUser', 'users'));
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
			$data = $this->request->data;

			$conversationData = [
				'sender_id' => $loggedInUserId,
				'receiver_id' => $data['Conversation']['receiver_id']
			];

			$this->Conversation->create();
			if ($this->Conversation->save($conversationData)) {
				$conversationId = $this->Conversation->id;

				$messageData = [
					'conversation_id' => $conversationId,
					'user_id' => $loggedInUserId,
					'content' => $data['Conversation']['message'],
					'sent_date' => date('Y-m-d H:i:s', strtotime('+8 hours')),
				];

				$this->Conversation->Message->create();
				if ($this->Conversation->Message->save($messageData)) {
					$userConversationData = [
						[
							'user_id' => $loggedInUserId,
							'conversation_id' => $conversationId,
						],
						[
							'user_id' => $data['Conversation']['receiver_id'],
							'conversation_id' => $conversationId,
						]
					];

					foreach ($userConversationData as $ucData) {
						$this->Conversation->UserConversation->create();
						if (!$this->Conversation->UserConversation->save($ucData)) {
							$this->Flash->error(__('The UserConversation entry could not be saved. Please try again.'));
						}
					}

					$this->Flash->success(__('The conversation has been saved.'));
					return $this->redirect(['action' => 'index']);
				} else {
					$this->Flash->error(__('The message could not be saved. Please try again.'));
				}
			} else {
				$errors = $this->Conversation->validationErrors;
				$this->Flash->error(__('The conversation could not be saved. Errors: ' . json_encode($errors)));
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
