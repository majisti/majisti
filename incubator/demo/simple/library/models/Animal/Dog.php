<?php

namespace MyProject\Animal;

class Dog extends \MyProject\AbstractAnimal
{
	public function talk()
	{
		print 'wouf';
	}
}