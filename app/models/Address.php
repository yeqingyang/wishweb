<?php

class Address extends \Phalcon\Mvc\Model
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
    public $city;

    /**
     *
     * @var integer
     */
    public $district;

    /**
     *
     * @var integer
     */
    public $zone;

    /**
     *
     * @var integer
     */
    public $building;

    /**
     *
     * @var integer
     */
    public $unit;

    /**
     *
     * @var integer
     */
    public $room;

    /**
     *
     * @var string
     */
    public $remark;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource('t_address');
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'uid' => 'uid', 
            'city' => 'city', 
            'district' => 'district', 
            'zone' => 'zone', 
            'building' => 'building', 
            'unit' => 'unit', 
            'room' => 'room', 
            'remark' => 'remark'
        );
    }

	public function toArray($column=null){
	//	return "City:$this->city \nDistrict:$this->district \nZone:$this->zone \nBuilding:$this->building \nUnit:$this->unit \nRoom:$this->room \nRemark:$this->remark\n";
		$column=array();
		$column['city']=AddressDef::$CITY[$this->city];
		$column['district']=AddressDef::$DISTRICT[$this->city][$this->district];
		$column['zone']=$this->zone;
		$column['building']=$this->building;
		$column['unit']=$this->unit;
		$column['room']=$this->room;
		$column['remark']=$this->remark;
		return $column;
	}
}
