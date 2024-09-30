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
            $messageData = array(
                'Message' => array(
                    'content' => $this->request->data['Message']['content'],
                    'conversation_id' => $conversationId,
                    'user_id' => $this->Auth->user('id')
                )
            );
    
            if ($this->Message->save($messageData)) {
                $savedMessage = $this->Message->findById($this->Message->id);
                $formattedDate = date('H:i A - F d, Y', strtotime($savedMessage['Message']['created']));

                $response = array(
                    'success' => true,
                    'message' => array(
                        'id' => $savedMessage['Message']['id'],
                        'created' => $formattedDate,
                        'content' => $savedMessage['Message']['content'],
                    )
                );
            } else {
                $response = array('success' => false, 'errors' => $this->Message->validationErrors);
            }
    
            $this->autoRender = false;
            $this->response->type('json');
            echo json_encode($response);
            exit;
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

    public function delete($id = null) {
        if (!$this->Message->exists($id)) {
            throw new NotFoundException(__('Invalid message'));
        }
        $this->request->allowMethod('post', 'delete');
        if ($this->Message->delete($id)) {
        } else {
            $this->Flash->error(__('The message could not be deleted. Please, try again.'));
        }
        return $this->redirect(array('action' => 'index'));
    }

    public function search($conversationId = null) {
        $this->autoRender = false; 
        $query = $this->request->query('query');
    
        $messages = $this->Message->find('all', array(
            'conditions' => array(
                'Message.conversation_id' => $conversationId,
                'Message.content LIKE' => '%' . $query . '%'
            ),
            'order' => array('Message.created' => 'DESC'),
            'recursive' => -1
        ));

        foreach ($messages as &$message) {
            $message['Message']['created'] = date('H:i A - F d, Y', strtotime($message['Message']['created']));
        }
        echo json_encode(array('messages' => $messages));
    }
}
