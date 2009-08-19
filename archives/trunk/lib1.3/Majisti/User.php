<?php

/**
 * @desc This singleton class stores anything related to a user through the session.
 * The user always have it's default data which is anything related to what was fetched
 * upon login using Zend_Auth. It has a namespace which contains everything related to it's profile
 * and a namespace for its roles.
 *
 * @author Steven Rosato
 */
class Majisti_User
{
	protected static $_instance;

	/** @var $_data Zend_Session_Namespace */
	protected $_data;

	/**
	 * @desc Constructs the data namespace
	 */
	protected function __construct()
	{
		$this->_data = new Zend_Session_Namespace('Majisti_User_Data');
	}

	/**
	 * @desc Set the default date ratated to authentification results.
	 * 
	 * @param stdClass|array $data
	 */
	public function setDefaultData($data)
	{
		$this->_data->default = $data;
	}

	/**
	 * @desc The only way to access the user's data.
	 * 
	 * The namespaces roles and profile are accecible.
	 * 
	 * If $name = role The user's highest role will be returned.
	 * 
	 * If $name doesn't match any namespace, the default namespace is
	 * assumed.
	 *
	 * @param String $name
	 * @return String|stdClass depending on the scope selected.
	 */
	public function __get($name)
	{
		switch($name) {
			case 'profile':
				return $this->_profile();
			case 'role':
				return $this->_role();
			case 'roles':
				return $this->_roles();
			default:
				if( isset($this->_data->default) ) {
					return $this->_data->default->$name;
				}
				break;
		}

		return null;
	}
	
	public function __set($name, $value)
	{
		if( $name != 'profile' && $name != 'roles' && $name != 'role' ) {
			$this->_data->default->$name = $value;
		}
	}

	/**
	 * @desc Sets the default data for this user
	 *
	 * @param array $data
	 */
	public function setProfileData(array $data)
	{
		$stdClass = new stdClass();

		foreach ($data as $key => $value) {
			if( $key == NULL ) {
				throw new Majisti_User_Exception('All values in array passed as parameter must contain its key pair');
			}
			$stdClass->$key = $value;
		}

		$this->_data->profile = $stdClass;
	}

	/**
	 * @desc Returns the profile scope
	 *
	 * @return StdClass
	 */
	protected function _profile()
	{
		return $this->_data->profile;
	}
	
	/**
	 * @desc Clears all of the user's data
	 */
	public function clear()
	{
		$this->_data->unsetAll();
	}

	/**
	 * @desc Add the user's roles to this object. This function will
	 * sort the passed roles by index and apply the 'highest' role
	 * on the first index. The highest role can then be retrieved with
	 * $this->role(), while all other roles will be retrievable with
	 * $this->roles().
	 *
	 * Note that the function isAdmin() will check for
	 * a value named 'admin' by default in the roles array to return
	 * whether the user is an admin if the boolean was never applied.
	 * To change this default behaviour the setIsAdmin() should be used,
	 * or the checkForAdminInRoles() with a different valueName could be called.
	 *
	 * @see setIsAdmin(), checkForAdminInRoles()
	 *
	 * @param array $roles The roles to add
	 */
	public function setRoles(array $roles)
	{
		$count = count($roles);

		if( $count ) {
			ksort($roles);
			$this->_data->highestRole = reset($roles);
			$this->_data->roles = $roles;
		}
	}

	/**
	 * @desc Return whether the user is having the administrator role
	 * within all its current roles.
	 *
	 * @param String $valueName The rolename that identify a user as an admin
	 * @return bool True if one of the user's role is to administrate
	 */
	public function checkForAdminInRoles($valueName = 'admin')
	{
		$found = false;
		if( isset($this->_data->roles) ) {
			reset($this->_data->roles);
			while( ($role = current($this->_data->roles)) && !$found ) {
				if( strcasecmp($role, $valueName) === 0 ) {
					$found = $this->_data->isAdmin = true;
				}
				next($this->_data->roles);
			}
		}
		return $found;
	}

	/**
	 * @desc Sets whether this user is an administrator.
	 *
	 * @param boolean $isAdmin
	 */
	public function setIsAdmin($isAdmin)
	{
		$this->_data->isAdmin = $isAdmin;
	}

	/**
	 * @desc Returns the highest role of this user
	 * @return string The highest role
	 */
	protected function _role()
	{
		return $this->_data->highestRole;
	}

	/**
	 * @return Array All the user's roles
	 */
	protected function _roles()
	{
		return $this->_data->roles;
	}

	/**
	 * @desc Returns whether the user is an administrator or not according
	 * to its roles.
	 *
	 * @return bool True if the user is an administrator
	 */
	public function isAdmin()
	{
		if( !isset($this->_data->isAdmin) ) {
			return $this->checkForAdminInRoles();
		}
		return $this->_data->isAdmin;
	}

	/**
	 * @desc Retrieve this class single instance
	 *
	 * @return Majisti_User
	 */
	public static function getInstance()
	{
		if( self::$_instance == null ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
}