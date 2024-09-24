<?php
App::uses('AppController', 'Controller');
/**
 * UserConversations Controller
 *
 * @property UserConversation $UserConversation
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 * @property FlashComponent $Flash
 */
class UserConversationsController extends AppController {

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
		$this->UserConversation->recursive = 0;
		$this->set('userConversations', $this->Paginator->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->UserConversation->exists($id)) {
			throw new NotFoundException(__('Invalid user conversation'));
		}
		$options = array('conditions' => array('UserConversation.' . $this->UserConversation->primaryKey => $id));
		$this->set('userConversation', $this->UserConversation->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->UserConversation->create();
			if ($this->UserConversation->save($this->request->data)) {
				$this->Flash->success(__('The user conversation has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The user conversation could not be saved. Please, try again.'));
			}
		}
		$users = $this->UserConversation->User->find('list');
		$conversations = $this->UserConversation->Conversation->find('list');
		$this->set(compact('users', 'conversations'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->UserConversation->exists($id)) {
			throw new NotFoundException(__('Invalid user conversation'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->UserConversation->save($this->request->data)) {
				$this->Flash->success(__('The user conversation has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Flash->error(__('The user conversation could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('UserConversation.' . $this->UserConversation->primaryKey => $id));
			$this->request->data = $this->UserConversation->find('first', $options);
		}
		$users = $this->UserConversation->User->find('list');
		$conversations = $this->UserConversation->Conversation->find('list');
		$this->set(compact('users', 'conversations'));
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		if (!$this->UserConversation->exists($id)) {
			throw new NotFoundException(__('Invalid user conversation'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->UserConversation->delete($id)) {
			$this->Flash->success(__('The user conversation has been deleted.'));
		} else {
			$this->Flash->error(__('The user conversation could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
