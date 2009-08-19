<?php

namespace MyProject;

abstract class AbstractAnimal
{
	public function __construct()
	{
		$this->talk();	
	}
	
	abstract public function talk();
}