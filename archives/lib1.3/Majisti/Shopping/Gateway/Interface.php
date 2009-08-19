<?php

/**
 * TODO: doc
 * 
 * @author Steven Rosato
 */
interface Majisti_Shopping_Gateway_Interface
{
	public function send( $params = array() );
	public function onSuccess( $params = array() );
	public function onFail( $params = array() );
}