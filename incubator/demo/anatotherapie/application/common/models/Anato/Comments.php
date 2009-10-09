<?php

/**
 * @desc This model contains the Anato comments list
 * 
 * @author Steven Rosato
 */
class Anato_Comments extends Anato_Util_ArrayObject
{
	/**
	 * @desc Constructs the model by appending
	 * pre-defined comments.
	 */
	public function __construct()
	{
		$iterable = Anato_Util_Content::getXmlContent('comments')->comments->data->toArray();

		/* support only one comment */
		if( !is_array(reset($iterable)) ) {
			$this->offsetSet($iterable['id'], new Anato_Comment(
				$iterable['id'], 
				$iterable['summary'], 
				$iterable['comment'], 
				$iterable['signature'],
				$iterable['attachment'] === '1'
			));
		} else {
			/* fetch all comments */
			foreach ($iterable as $data) {
				$this->offsetSet($data['id'], new Anato_Comment(
					$data['id'],
					$data['summary'], 
					$data['comment'], 
					$data['signature'],
					$data['attachment'] ===  '1'
				));
			}	
		}
		
	}
	
	public function getById($id)
	{
		return $this->{$id};
	}
}