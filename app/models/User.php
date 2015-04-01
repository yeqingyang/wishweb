<?php

use Phalcon\Mvc\Model\Validator\Email as Email;

class User extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $uid;

    /**
     *
     * @var integer
     */
    public $usetime;

    /**
     *
     * @var string
     */
    public $uname;

    /**
     *
     * @var string
     */
    public $email;

    /**
     *
     * @var integer
     */
    public $status;

    /**
     *
     * @var integer
     */
    public $create_time;

    /**
     *
     * @var integer
     */
    public $dtime;

    /**
     *
     * @var integer
     */
    public $birthday;

    /**
     *
     * @var integer
     */
    public $gold_num;

    /**
     *
     * @var integer
     */
    public $reward_point;

    /**
     *
     * @var integer
     */
    public $last_login_time;

    /**
     *
     * @var integer
     */
    public $online_accum_time;

    /**
     *
     * @var string
     */
    public $password;

    /**
     * Validations and business logic
     */
    public function validation()
    {

        $this->validate(
            new Email(
                array(
                    'field'    => 'email',
                    'required' => true,
                )
            )
        );
        if ($this->validationHasFailed() == true) {
            return false;
        }
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource('t_user');
    }

	/**
	 * Initialize some properties for default
	 */
	public function init(){
		$this->usetime = time();
		$this->status = 1;
		$this->create_time = time();
		$this->dtime = 0;
		$this->birthday = 0;
		$this->gold_num = 0;
		$this->reward_point = 0;
		$this->last_login_time = 0;
		$this->online_accum_time = 0;
	}

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'uid' => 'uid', 
            'usetime' => 'usetime', 
            'uname' => 'uname', 
            'email' => 'email', 
            'status' => 'status', 
            'create_time' => 'create_time', 
            'dtime' => 'dtime', 
            'birthday' => 'birthday', 
            'gold_num' => 'gold_num', 
            'reward_point' => 'reward_point', 
            'last_login_time' => 'last_login_time', 
            'online_accum_time' => 'online_accum_time', 
            'password' => 'password'
        );
    }

	public function toArray($column=null){
		$column=array();
		$column['uid']=$this->uid;
		$column['uname']=$this->uname;
		$column['email']=$this->email;
		$column['status']=$this->status;
		$column['birthday']=date('Y-m-d H:i:s',$this->birthday);
		$column['create_time']=date('Y-m-d H:i:s',$this->create_time);
		$column['last_login_time']=date('Y-m-d H:i:s',$this->last_login_time);
		return $column;
	}

}
