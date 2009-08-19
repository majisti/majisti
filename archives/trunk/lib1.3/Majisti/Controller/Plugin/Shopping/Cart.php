<?php

/**
 * Initialize the Shopping cart from config
 *
 * @author Yanick Rochon
 */
class Majisti_Controller_Plugin_Shopping_Cart extends Majisti_Controller_Plugin_Abstract {

	/**
	 * Initialize shopping cart
	 *
	 * @param Zend_Controller_Request_Abstract $request
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request) {

		// ensure that this is called only if a factory can be set
		if ( !Majisti_Shopping_Cart::hasFactory() ) {
			$config = $this->getConfig();

			if ( isset($config->enabled) && !$config->enabled ) return;

			if ( $config->options ) {
				$options = $config->options->toArray();
			} else {
				$options = array();
			}

			$factory = Majisti_Shopping_Cart_Factory::create($config->factory, $options);

			Majisti_Shopping_Cart::setFactory($factory);
		}
	}

}