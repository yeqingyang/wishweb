<?php

class Friend extends \Phalcon\Mvc\Model
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
    public $fuid;

    /**
     *
     * @var integer
     */
    public $friend_type;

    /**
     *
     * @var integer
     */
    public $status;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource('t_friend');
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'uid' => 'uid', 
            'fuid' => 'fuid', 
            'friend_type' => 'friend_type', 
            'status' => 'status'
        );
    }

}
