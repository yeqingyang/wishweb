<?php

class SessionController extends ControllerBase
{
    public function initialize()
    {
        $this->tag->setTitle('Sign Up/Sign In');
        parent::initialize();
    }

    public function indexAction()
    {
        if (!$this->request->isPost()) {
            $this->tag->setDefault('email', 'lpz8120903@163.com');
            $this->tag->setDefault('password', '111111');
        }
    }

    public function registerAction()
    {
        $request = $this->request;
        if ($request->isPost()) {

            $name = $request->getPost('name', array('string', 'striptags'));
            $username = $request->getPost('username', 'alphanum');
            $email = $request->getPost('email', 'email');
            $password = $request->getPost('password');
            $repeatPassword = $this->request->getPost('repeatPassword');

            if ($password != $repeatPassword) {
                $this->flash->error('Passwords are diferent');
                return false;
            }

            $user = new User();
			$user->init();
            $user->uname = $username;
            $user->password = md5($password);
            $user->email = $email;
            $user->create_time = new Phalcon\Db\RawValue('now()');
            $user->status = 1;

//			$register = new Register();
//			$register->rand_code = "1";
//			$register->identifier = "lpz";
//			$register->phoneNumber = "18810465137";
//
//            if ($register->save() == false) {
//                foreach ($register->getMessages() as $message) {
//                    $this->flash->error((string) $message);
//                }
//            }

            if ($user->save() == false) {
                foreach ($user->getMessages() as $message) {
                    $this->flash->error((string) $message);
                }
            } else {
                $this->tag->setDefault('email', '');
                $this->tag->setDefault('password', '');
                $this->flash->success('Thanks for sign-up, please log-in to start generating invoices');
                return $this->forward('session/index');
            }

        }
    }

    /**
     * Register authenticated user into session data
     *
     * @param Users $user
     */
    private function _registerSession($user)
    {
        $this->session->set('auth', array(
            'id' => $user->uid,
            'name' => $user->uname
        ));
        $this->session->set('uid',$user->uid);
    }

    /**
     * This actions receive the input from the login form
     *
     */
    public function startAction()
    {
        if ($this->request->isPost()) {
            $email = $this->request->getPost('email', 'email');

            $password = $this->request->getPost('password');
            $password = md5($password);

            $user = User::findFirst("email='$email' AND password='$password'");
            if ($user != false) {
                $this->_registerSession($user);
                $this->flash->success('Welcome ' . $user->uname);
                return $this->forward('user/index');
            }

            $username = $this->request->getPost('email', 'alphanum');
            $user = User::findFirst("uname='$username' AND password='$password'");
            if ($user != false) {
                $this->_registerSession($user);
				$date=new DateTime();
				$user->last_login_time=$date->getTimeStamp();
				$user->save();
                $this->flash->success('Welcome ' . $user->uname);
                return $this->forward('user/index');
            }

            $this->flash->error('Wrong email/password');
        }

        return $this->forward('session/index');
    }

    /**
     * Finishes the active session redirecting to the index
     *
     * @return unknown
     */
    public function endAction()
    {
        $this->session->remove('auth');
        $this->flash->success('Goodbye!');
        return $this->forward('index/index');
    }
}
