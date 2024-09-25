<?php
App::uses('AppController', 'Controller');

/**
 * Messages Controller
 *
 * @property Message $Message
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 * @property FlashComponent $Flash
 */
class MessagesController extends AppController {

    public $components = array('Paginator', 'Session', 'Flash');

    public function index() {
        $this->Message->recursive = 0;
        $this->set('messages', $this->Paginator->paginate());
    }

    public function view($id = null) {
        if (!$this->Message->exists($id)) {
            throw new NotFoundException(__('Invalid message'));
        }
        $options = array('conditions' => array('Message.' . $this->Message->primaryKey => $id));
        $this->set('message', $this->Message->find('first', $options));
    }

    public function add($conversationId) {
        if ($this->request->is('post')) {
            $this->Message->create();
            
            $this->request->data['Message']['conversation_id'] = $conversationId;
            $this->request->data['Message']['user_id'] = $this->Auth->user('id'); 
    
            if (!empty($this->request->data['Message']['content'])) {
                $this->request->data['Message']['sent_date'] = date('Y-m-d H:i:s');
    
                if ($this->Message->save($this->request->data)) {
                    // $this->Flash->success(__('Message sent.'));
                    return $this->redirect(array('controller' => 'Conversations', 'action' => 'view', $conversationId)); 
                } else {
                    $this->Flash->error(__('The message could not be saved. Please, try again.'));
                }
            } else {
                $this->Flash->error(__('Content cannot be empty.'));
            }
        }
    }

    public function edit($id = null) {
        if (!$this->Message->exists($id)) {
            throw new NotFoundException(__('Invalid message'));
        }
        if ($this->request->is(array('post', 'put'))) {
            if ($this->Message->save($this->request->data)) {
                $this->Flash->success(__('The message has been saved.'));
                return $this->redirect(array('action' => 'index'));
            } else {
                $this->Flash->error(__('The message could not be saved. Please, try again.'));
            }
        } else {
            $options = array('conditions' => array('Message.' . $this->Message->primaryKey => $id));
            $this->request->data = $this->Message->find('first', $options);
        }
        $conversations = $this->Message->Conversation->find('list');
        $users = $this->Message->User->find('list');
        $this->set(compact('conversations', 'users'));
    }

    public function delete($id = null, $conversationId) {
        if (!$this->Message->exists($id)) {
            throw new NotFoundException(__('Invalid message'));
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Message->delete($id)) {
            // $this->Flash->success(__('The message has been deleted.'));
        } else {
            $this->Flash->error(__('The message could not be deleted. Please, try again.'));
        }
        return $this->redirect(array('controller' => 'Conversations', 'action' => 'view', $conversationId)); 
    }
}
