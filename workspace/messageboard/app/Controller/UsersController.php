<?php
App::uses('AppController', 'Controller');
/**
 * Users Controller
 *
 * @property User $User
 * @property PaginatorComponent $Paginator
 * @property SessionComponent $Session
 * @property FlashComponent $Flash
 */
class UsersController extends AppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Session', 'Flash');

	public function beforeFilter() {
		parent::beforeFilter(); 
		$this->Auth->allow('register', 'thankyou');
	}
	
	
/**
 * index method
 *
 * @return void
 */
	public function login() {
		if ($this->request->is('post')) {
			if ($this->Auth->login()) {
				$this->User->id = $this->Auth->user('id');
				$currentTime = date('Y-m-d H:i:s');
				
				if ($this->User->saveField('last_logged_in', $currentTime)) {
					$user = $this->User->read(null, $this->User->id);
					$this->Auth->login($user['User']); 
				} else {
					$this->Flash->error(__('An error occurred while updating last logged in.'));
				}

				return $this->redirect($this->Auth->redirect('/conversations'));
			} else {
				$this->Flash->error(__('Invalid email or password, try again'));
			}
		}
	}
	
	public function logout(){
		$this->Auth->logout();
		$this->redirect('/users');
	}

	public function index() {
		$this->User->recursive = 0;
		$this->set('users', $this->Paginator->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function calculateAge($birthday) {
		$birthdayDate = new DateTime($birthday);
		$today = new DateTime();
		$age = $today->diff($birthdayDate)->y;
		return $age;
	}

	public function view($id = null) {
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
	
		$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
		$user = $this->User->find('first', $options);
	
		$age = null; // Set age to null by default
	
		if (!empty($user['User']['birthday'])) {
			$age = $this->calculateAge($user['User']['birthday']);
		}
	
		$this->set(compact('user', 'age'));
	}
	
	

/**
 * register method
 *
 * @return void
 */
	public function register() {
		if ($this->request->is('post')) {
			$this->User->create();

			$this->request->data['User']['password'] = AuthComponent::password($this->request->data['User']['password']);
			$this->request->data['User']['confirm_password'] = AuthComponent::password($this->request->data['User']['confirm_password']);
			$this->request->data['User']['joined_date'] = date('Y-m-d H:i:s');
			if ($this->User->save($this->request->data)) {
				$this->Session->write('User.registered', true);
				return $this->redirect(array('action' => 'thankyou'));
			} else {
				$this->Flash->error(__('An error occurred. Please, try again.'));
				debug($this->User->validationErrors);
			}
		}

		$conversations = $this->User->Conversation->find('list');
		$this->set(compact('conversations'));
	}

	public function thankyou() {
		if (!$this->Session->check('User.registered')) {
			return $this->redirect(array('action' => 'index')); 
		}
		$this->Session->delete('User.registered');
	}
	
/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->User->exists($id) || !($user = $this->User->findById($id))) {
			throw new NotFoundException(__('Invalid user'));
		}

		if ($this->request->is(['post', 'put'])) {
			$this->User->id = $id;

			if (!empty($this->request->data['User']['old_password']) && 
				AuthComponent::password($this->request->data['User']['old_password']) !== $user['User']['password']) {
				$this->Flash->error(__('Old password is incorrect.'));
				$this->request->data = $user; 
				return;
			}

			$this->handleProfilePictureUpload($user);

			$fieldsToSave = ['name', 'birthday', 'gender', 'profile_picture', 'hobby'];
			
			if (!empty($this->request->data['User']['email'])) {
				$fieldsToSave[] = 'email';
			}
			
			if (!empty($this->request->data['User']['new_password'])) {
				if ($this->request->data['User']['new_password'] === $this->request->data['User']['confirm_password']) {
					$this->request->data['User']['password'] = AuthComponent::password($this->request->data['User']['new_password']);
					$fieldsToSave[] = 'password';
				} else {
					$this->Flash->error(__('New passwords do not match. Please try again.'));
					return; 
				}
			}

			if ($this->User->save($this->request->data, ['fieldList' => $fieldsToSave])) {
				$this->Flash->success(__('The user has been saved.'));
				return $this->redirect(['action' => 'view', $id]);
			} else {
				$this->Flash->error(__('The user could not be saved. Please, try again.'));
			}
		}

		$this->request->data = $user; 
		$this->set(compact('user')); 
	}

	private function handleProfilePictureUpload($user) {
		if (!empty($this->request->data['User']['profile_picture']['tmp_name'])) {
			$uploadPath = WWW_ROOT . 'uploads' . DS . 'profile_pictures' . DS;
			$uploadFile = $uploadPath . basename($this->request->data['User']['profile_picture']['name']);

			if (move_uploaded_file($this->request->data['User']['profile_picture']['tmp_name'], $uploadFile)) {
				$this->request->data['User']['profile_picture'] = 'uploads/profile_pictures/' . basename($this->request->data['User']['profile_picture']['name']);
			} else {
				$this->Flash->error(__('There was an error uploading the profile picture. Please try again.'));
				unset($this->request->data['User']['profile_picture']);
			}
		} else {
			unset($this->request->data['User']['profile_picture']);
		}
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->User->delete($id)) {
			$this->Flash->success(__('The user has been deleted.'));
		} else {
			$this->Flash->error(__('The user could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}
}
