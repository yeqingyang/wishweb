<?php
 
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;

class FriendController extends ControllerBase
{
	public function onConstruct(){
		$this->tag->setTitle('Friend');
		//parent::initialize();
        $this->persistent->parameters = null;
		$ret = $this->getAllAction();
		if(!empty($ret)){
        	$this->view->page1 = $ret->getPaginate();
		}else{
			$this->view->page1 = array();
		}
		$ret = $this->getInvitesAction();
		if(!empty($ret)){
        	$this->view->page2 = $ret->getPaginate();
		}else{
			$this->view->page2 = array();
		}
		$ret = $this->getSendsAction();
		if(!empty($ret)){
        	$this->view->page3 = $ret->getPaginate();
		}else{
			$this->view->page3 = array();
		}
	}

    /**
     * Index action
     */
    public function indexAction()
    {
//		$this->tag->setTitle('Friend');
//		parent::initialize();
        $this->persistent->parameters = null;
    }

	public function getAllAction(){
		$numberPage = 1;
        $numberPage = $this->request->getQuery("page1", "int");
		$auth = $this->session->get('auth');
		$uid = $auth['id'];
		$wheres = "(uid=$uid or fuid=$uid) and friend_type=2";
		$query = $this->modelsManager->createQuery("select User.uid,User.uname as name from User join Friend on Friend.fuid=User.uid where Friend.uid=$uid and Friend.friend_type=2");
		$friend = $query->execute();
		//$friend = Friend::find(array($wheres,"order"=>"uid"));
        if (count($friend) == 0) {
			return array();
        }
        $paginator = new Paginator(array(
            "data" => $friend,
            "limit"=> 10,
            "page" => $numberPage
        ));
		return $paginator;
	}
	public function getInvitesAction(){
		
		$numberPage = 1;
        $numberPage = $this->request->getQuery("page2", "int");
		$auth = $this->session->get('auth');
		$uid = $auth['id'];
		$wheres = "fuid=$uid and friend_type=1";
		$friend = Friend::find(array($wheres,"order"=>"uid"));
        if (count($friend) == 0) {
			return array();
        }
        $paginator = new Paginator(array(
            "data" => $friend,
            "limit"=> 10,
            "page" => $numberPage
        ));
		return $paginator;
	}
	public function getSendsAction(){
		
		$numberPage = 1;
        $numberPage = $this->request->getQuery("page3", "int");
		$auth = $this->session->get('auth');
		$uid = $auth['id'];
		$wheres = "uid=$uid and friend_type=1";
		$friend = Friend::find(array($wheres,"order"=>"fuid"));
        if (count($friend) == 0) {
			return array();
        }
        $paginator = new Paginator(array(
            "data" => $friend,
            "limit"=> 10,
            "page" => $numberPage
        ));
		return $paginator;
	}

    /**
     * Searches for user
     */
    public function searchAction()
    {

        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, "Friend", $_POST);
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

	public function acceptAction($fuid){
		$auth = $this->session->get('auth');
		$uid = $auth['id'];
		$wheres = "fuid=$uid and uid=$fuid";
		$friend = Friend::findFirst(array($wheres));
		$friend->friend_type=2;
        if (!$friend->save()) {

            foreach ($friend->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "friend",
                "action" => "index",
            ));
        }
		$afriend=new Friend();
        $afriend->uid = $uid;
        $afriend->fuid = $fuid;
        $afriend->friend_type = 2;
        $afriend->status = 1;
        

        if (!$afriend->save()) {
            foreach ($afriend->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "friend",
                "action" => "new"
            ));
        }

        $this->flash->success("user was updated successfully");

        return $this->dispatcher->forward(array(
            "controller" => "friend",
            "action" => "index"
        ));
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

            $this->tag->setDefault("uid", $user->uid);
            $this->tag->setDefault("usetime", $user->usetime);
            $this->tag->setDefault("uname", $user->uname);
            $this->tag->setDefault("email", $user->email);
            $this->tag->setDefault("status", $user->status);
            $this->tag->setDefault("create_time", $user->create_time);
            $this->tag->setDefault("dtime", $user->dtime);
            $this->tag->setDefault("birthday", $user->birthday);
            $this->tag->setDefault("gold_num", $user->gold_num);
            $this->tag->setDefault("reward_point", $user->reward_point);
            $this->tag->setDefault("last_login_time", $user->last_login_time);
            $this->tag->setDefault("online_accum_time", $user->online_accum_time);
            $this->tag->setDefault("password", $user->password);
            
        }
    }

    /**
     * Creates a new user
     */
    public function createAction($fuid)
    {

		$auth = $this->session->get('auth');
		$uid = $auth['id'];

		if($uid==$fuid){
			$this->flash->error("You can't add youself.");
			return 1;
		}
        $friend = new Friend();

        $friend->uid = $uid;
        $friend->fuid = $fuid;
        $friend->friend_type = 1;
        $friend->status = 1;
        

        if (!$friend->save()) {
            foreach ($friend->getMessages() as $message) {
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

        $uid = $this->request->getPost("uid");

        $user = User::findFirstByuid($uid);
        if (!$user) {
            $this->flash->error("user does not exist " . $uid);

            return $this->dispatcher->forward(array(
                "controller" => "user",
                "action" => "index"
            ));
        }

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
