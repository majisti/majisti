<?php

/**
 * @desc This is an anato comment. A comment
 * has an id, asummary, a comment, a signature
 * and maybe an attachment.
 * 
 * @author Steven Rosato
 */
class Anato_Comment
{
	/** @var String */
	private $_id;
	
	/** @var String */
	private $_summary;
	
	/** @var String */
	private $_comment;
	
	/** @var String */
	private $_signature;
	
	/** @var String */
	private $_hasAttachment;
	
	/**
	 * @desc Constructs the comment
	 *
	 * @param String $comment The comment
	 * @param String $signature The signature
	 */
	public function __construct($id, $summary, $comment, $signature, $hasAttachment = null)
	{
		$this->_id				= $id;
		$this->_summary			= $summary;
		$this->_comment 		= $comment;
		$this->_signature 		= $signature;
		$this->_hasAttachment	= $hasAttachment;
	}
	
	/**
	 * @return String the id
	 */
	public function getId()
	{
		return $this->_id;
	}
	
	/**
	 * @return String the summary
	 */
	public function getSummary()
	{
		return $this->_summary;
	}
	
	/**
	 * @return String the comment
	 */
	public function getComment()
	{
		return $this->_comment;
	}
	
	/**
	 * @return String the signature
	 */
	public function getSignature()
	{
		return $this->_signature;
	}
	
	/**
	 * @return bool True if the comment has an attachment
	 */
	public function hasAttachment()
	{
		return $this->_hasAttachment;
	}
	
	/**
	 * @see getComment()
	 *
	 * @return String
	 */
	public function toString()
	{
		return $this->getComment();
	}
	
	/**
	 * @see toString()
	 *
	 * @return String
	 */
	public function __toString()
	{
		return $this->toString();	
	}
}
