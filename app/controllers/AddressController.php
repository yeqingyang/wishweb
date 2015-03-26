<?php
 
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;

class AddressController extends ControllerBase
{

    /**
     * Index action
     */
    public function indexAction()
    {
        $this->persistent->parameters = null;
    }

    /**
     * Searches for address
     */
    public function searchAction()
    {

        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, "Address", $_POST);
            $this->persistent->parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = array();
        }
        $parameters["order"] = "uid";

        $address = Address::find($parameters);
        if (count($address) == 0) {
            $this->flash->notice("The search did not find any address");

            return $this->dispatcher->forward(array(
                "controller" => "address",
                "action" => "index"
            ));
        }

        $paginator = new Paginator(array(
            "data" => $address,
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

		$this->view->CITY = AddressDef::$CITY;
		$this->view->DISTRICT = AddressDef::$DISTRICT;
    }

    /**
     * Edits a addres
     *
     * @param string $uid
     */
    public function editAction($uid)
    {

        if (!$this->request->isPost()) {

            $addres = Address::findFirstByuid($uid);
            if (!$addres) {
                $this->flash->error("addres was not found");

                return $this->dispatcher->forward(array(
                    "controller" => "address",
                    "action" => "index"
                ));
            }

			$this->view->CITY = AddressDef::$CITY;
			$this->view->DISTRICT = AddressDef::$DISTRICT;
            $this->view->uid = $addres->uid;

            $this->tag->setDefault("city", $addres->city);
            $this->tag->setDefault("district", $addres->district);
            $this->tag->setDefault("zone", $addres->zone);
            $this->tag->setDefault("building", $addres->building);
            $this->tag->setDefault("unit", $addres->unit);
            $this->tag->setDefault("room", $addres->room);
            $this->tag->setDefault("remark", $addres->remark);
            
        }
    }

    /**
     * Creates a new addres
     */
    public function createAction()
    {

        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "address",
                "action" => "index"
            ));
        }
		$uid=$this->session->get('uid');
        $addres = new Address();

        $addres->uid = $uid;
        $addres->city = $this->request->getPost("city");
        $addres->district = $this->request->getPost("district");
        $addres->zone = $this->request->getPost("zone");
        $addres->building = $this->request->getPost("building");
        $addres->unit = $this->request->getPost("unit");
        $addres->room = $this->request->getPost("room");
        $addres->remark = $this->request->getPost("remark");
        

        if (!$addres->save()) {
            foreach ($addres->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "address",
                "action" => "new"
            ));
        }

        $this->flash->success("addres was created successfully");

        return $this->dispatcher->forward(array(
            "controller" => "user",
            "action" => "index"
        ));

    }

    /**
     * Saves a addres edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            return $this->dispatcher->forward(array(
                "controller" => "address",
                "action" => "index"
            ));
        }

        $uid = $this->session->get("uid");

        $addres = Address::findFirstByuid($uid);
        if (!$addres) {
            $this->flash->error("addres does not exist " . $uid);

            return $this->dispatcher->forward(array(
                "controller" => "address",
                "action" => "index"
            ));
        }

        $addres->city = $this->request->getPost("city");
        $addres->district = $this->request->getPost("district");
        $addres->zone = $this->request->getPost("zone");
        $addres->building = $this->request->getPost("building");
        $addres->unit = $this->request->getPost("unit");
        $addres->room = $this->request->getPost("room");
        $addres->remark = $this->request->getPost("remark");
        

        if (!$addres->save()) {

            foreach ($addres->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "address",
                "action" => "edit",
                "params" => array($addres->uid)
            ));
        }

        $this->flash->success("addres was updated successfully");

        return $this->dispatcher->forward(array(
            "controller" => "user",
            "action" => "index"
        ));

    }

    /**
     * Deletes a addres
     *
     * @param string $uid
     */
    public function deleteAction($uid)
    {

        $addres = Address::findFirstByuid($uid);
        if (!$addres) {
            $this->flash->error("addres was not found");

            return $this->dispatcher->forward(array(
                "controller" => "address",
                "action" => "index"
            ));
        }

        if (!$addres->delete()) {

            foreach ($addres->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                "controller" => "address",
                "action" => "search"
            ));
        }

        $this->flash->success("addres was deleted successfully");

        return $this->dispatcher->forward(array(
            "controller" => "address",
            "action" => "index"
        ));
    }
	
	public function getDistrictAction($city){

		$this->view->setRenderLevel(View::LEVEL_NO_RENDER);

        if (!$this->request->isGet() || !$this->request->isAjax()) {
			$this->flash->error("invalid action!");
        }
		//Getting a response instance
		$response = new \Phalcon\Http\Response();
		//Set status code
		//$response->setStatusCode(200, "Not Found");
		$feed = AddressDef::$DISTRICT[$city];
		echo "$feed";
		//Set the content of the response
		$response->setContent(json_encode($feed,JSON_UNESCAPED_UNICODE));
		//Send response to the client
		$response->send();

	}

}
