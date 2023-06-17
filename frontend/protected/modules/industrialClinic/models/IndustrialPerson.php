<?php

/**
 * Description of IndustrialPerson
 *
 * @package industrialClinic.models
 *
 */
class IndustrialPerson extends Person 
{
	public static function model($className=__CLASS__){
		return parent::model($className);
	}

	public function getBirthDate()
	{
		if($this->date_birth) {
			return date('F d, Y', strtotime($this->date_birth));
		}

		return null;
	}

	public function getCivilStatus()
	{
		if($this->civil_status) {
			return ucfirst($this->civil_status);
		}

		return null;
	}
}