<?php
class UsersController extends AppController {
    public $uses = ['User'];
	public function beforeFilter() {
        parent::beforeFilter();

        // always restrict your whitelists to a per-controller basis
        $this->Auth->allow("ajaxLogin");
    }

    public function login() {
        if ($this->request->is('post')) {
            $user = $this->User->find('first', array(
                'conditions' => array(
                    'username' => $this->request->data['User']['username'],
                    'password' => $this->request->data['User']['password']
                )
            ));
            
            $didLogin = $this->Auth->login($user['User']);
            // $didLogin = $this->Auth->login();
            
            if ($didLogin) {
                return $this->redirect($this->Auth->redirectUrl());
            }
            
            $this->Flash->error(__('Invalid username or password, try again'));
        }
    }

    public function ajaxLogin () {

        $user = $this->User->find('first', array(
            'conditions' => array(
                'username' => $this->request->data['username'],
                'password' => $this->request->data['password']
            )
        ));

        $didLogin = $this->Auth->login($user['User']);
        
        echo json_encode(array(
            "status" => "success",
            "user" => $this->Auth->user()
        ));
        die();
    }

    public function logout() {
        return $this->redirect($this->Auth->logout());
    }
    
    public function index() {
        $this->User->recursive = 0;
        $this->set('users', $this->paginate());
    }

    public function view($id = null) {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        $this->set('user', $this->User->findById($id));
    }

    public function add() {
        if ($this->request->is('post')) {
            $this->User->create();
            if ($this->User->save($this->request->data)) {
                $this->Flash->success(__('The user has been saved'));
                return $this->redirect(array('action' => 'index'));
            }
            $this->Flash->error(
                __('The user could not be saved. Please, try again.')
            );
        }
    }

    public function edit($id = null) {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->User->save($this->request->data)) {
                $this->Flash->success(__('The user has been saved'));
                return $this->redirect(array('action' => 'index'));
            }
            $this->Flash->error(
                __('The user could not be saved. Please, try again.')
            );
        } else {
            $this->request->data = $this->User->findById($id);
            unset($this->request->data['User']['password']);
        }
    }

    public function delete($id = null) {
        // Prior to 2.5 use
        // $this->request->onlyAllow('post');

        $this->request->allowMethod('post');

        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        if ($this->User->delete()) {
            $this->Flash->success(__('User deleted'));
            return $this->redirect(array('action' => 'index'));
        }
        $this->Flash->error(__('User was not deleted'));
        return $this->redirect(array('action' => 'index'));
    }

}