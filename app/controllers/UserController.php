<?php
 
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;

class UserController extends ControllerBase
{

    /**
     * Index action
     */
    public function indexAction()
    {
		$this->tag->setTitle('User');
		parent::initialize();
        $this->persistent->parameters = null;


		$auth = $this->session->get('auth');
		$uid = $auth['id'];
        $user = User::findFirstByuid($uid);
		$this->view->user = $user->toArray();
		$address = Address::findFirstByuid($uid);
		if(!empty($address)){
			$this->view->address = $address->toArray();
		}
    }

    /**
     * Searches for user
     */
    public function searchAction()
    {

        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, "User", $_POST);
            $this->persistent->parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = array();
        }
        $parameters["order"] = "uid";

        $user = User::find($parameters);
        if (count($user) == 0) {
            $this->flash->notice("The search did not find any user");

            return $this->dispatcher->forward(array(
                "controller" => "user",
                "action" => "index"
            ));
        }

        $paginator = new Paginator(array(
            "data" => $user,
            "limit"=> 10,
            "page" => $numberPage
        ));

        $this->view->page = $paginator->getPaginate();
    }

    /**
     * Displays the creation form
     */
    public function newAction()
    {

    }

    /**
     * Edits a user
     *
     * @param string $uid
     */
    public function editAction($uid)
    {

        if (!$this->request->isPost()) {

            $user = User::findFirstByuid($uid);
            if (!$user) {
                $this->flash->error("user was not found");

                return $this->dispatcher->forward(array(
                    "controller" => "user",
                    "action" => "index"
                ));
            }

            $this->view->uid = $user->uid;

            $this->tag->setDefault("uname", $user->uname);
            $this->tag->setDefault("email", $user->email);
            $this->tag->setDefault("status", $user->status);
            $this->tag->setDefault("birthday", date("Y-m-d H:i:s",$user->birthday));
            
        }
    }

    /**
     * Creates a new user
     */
    public function createAction()
    {

        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "user",
                "action" => "index"
            ));
        }

        $user = new User();

        $user->usetime = $this->request->getPost("usetime");
        $user->uname = $this->request->getPost("uname");
        $user->email = $this->request->getPost("email", "email");
        $user->status = $this->request->getPost("status");
        $user->create_time = $this->request->getPost("create_time");
        $user->dtime = $this->request->getPost("dtime");
        $user->birthday = $this->request->getPost("birthday");
        $user->gold_num = $this->request->getPost("gold_num");
        $user->reward_point = $this->request->getPost("reward_point");
        $user->last_login_time = $this->request->getPost("last_login_time");
        $user->online_accum_time = $this->request->getPost("online_accum_time");
        $user->password = md5($this->request->getPost("password"));
        

        if (!$user->save()) {
            foreach ($user->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "user",
                "action" => "new"
            ));
        }

        $this->flash->success("user was created successfully");

        return $this->dispatcher->forward(array(
            "controller" => "user",
            "action" => "index"
        ));

    }

    /**
     * Saves a user edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "user",
                "action" => "index"
            ));
        }
		$uid = $this->session->get("uid");

        $user = User::findFirstByuid($uid);
        if (!$user) {
            $this->flash->error("user does not exist " . $uid);

            return $this->dispatcher->forward(array(
                "controller" => "user",
                "action" => "index"
            ));
        }

        $user->uname = $this->request->getPost("uname");
        $user->email = $this->request->getPost("email", "email");
        $user->birthday = strtotime($this->request->getPost("birthday"));
        $user->password = md5($this->request->getPost("password"));
        

        if (!$user->save()) {

            foreach ($user->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "user",
                "action" => "edit",
                "params" => array($user->uid)
            ));
        }

        $this->flash->success("user was updated successfully");

        return $this->dispatcher->forward(array(
            "controller" => "user",
            "action" => "index"
        ));

    }

    /**
     * Deletes a user
     *
     * @param string $uid
     */
    public function deleteAction($uid)
    {

        $user = User::findFirstByuid($uid);
        if (!$user) {
            $this->flash->error("user was not found");

            return $this->dispatcher->forward(array(
                "controller" => "user",
                "action" => "index"
            ));
        }

        if (!$user->delete()) {

            foreach ($user->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "user",
                "action" => "search"
            ));
        }

        $this->flash->success("user was deleted successfully");

        return $this->dispatcher->forward(array(
            "controller" => "user",
            "action" => "index"
        ));
    }

}
