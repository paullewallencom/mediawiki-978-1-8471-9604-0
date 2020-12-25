<?php
/**
 * Authentication plugin interface. Instantiate a subclass of AuthPlugin
 * and set $wgAuth to it to authenticate against some external tool.
 *
 * The default behavior is not to do anything, and use the local user
 * database for all authentication. A subclass can require that all
 * accounts authenticate externally, or use it only as a fallback; also
 * you can transparently create internal wiki accounts the first time
 * someone logs in who can be authenticated externally.
 *
 * This interface is new, and might change a bit before 1.4.0 final is
 * done...
 *
 */
$wgExtensionCredits['parserhook'][] = array (
	'name' => 'AuthJoomla',
	'author' => 'Nori',
	'url' => 'http://joomla.mu-fan.com',
);
 
require_once ( 'AuthPlugin.php' );
class AuthJoomla extends AuthPlugin
{
 
    /**
     * Enter description here...
     *
     * @return Object Database
     */
    private function connectToDB()
    {
        $db = & Database :: newFromParams(
        $GLOBALS['wgAuthJoomla_MySQL_Host'],
        $GLOBALS['wgAuthJoomla_MySQL_Username'],
        $GLOBALS['wgAuthJoomla_MySQL_Password'],
        $GLOBALS['wgAuthJoomla_MySQL_Database']);
 
        $this->userTable = $GLOBALS['wgAuthJoomla_TablePrefix'].'users';
        wfDebug("AuthJoomla::connectToDB() : DB failed to open\n");
        return $db;
    }
 
    /**
     * Check whether there exists a user account with the given name.
     * The name will be normalized to MediaWiki's requirements, so
     * you might need to munge it (for instance, for lowercase initial
     * letters).
     *
     * @param $username String: username.
     * @return bool
     * @public
     */
    function userExists( $username ) {
        # Override this!
        return true;
    }
 
    /**
     * Check if a username+password pair is a valid login.
     * The name will be normalized to MediaWiki's requirements, so
     * you might need to munge it (for instance, for lowercase initial
     * letters).
     *
     * @param $username String: username.
     * @param $password String: user password.
     * @return bool
     * @public
     */
    function authenticate( $username, $password )
    {
        $db = $this->connectToDB();
        $hash_password = $db->selectRow($this->userTable,array ( 'password' ), array ( 'username' => $username ), __METHOD__ );
        $parts	= explode( ':', $hash_password->password );
        $crypt	= $parts[0];
        $salt	= @$parts[1];
        if ( is_file($GLOBALS['wgAuthJoomla_Path'].'/libraries/joomla/user/helper.php'))
        {
            require_once $GLOBALS['wgAuthJoomla_Path'].'/libraries/joomla/user/helper.php';
            $testcrypt = JUserHelper::getCryptedPassword($password, $salt);
        }
        if ($crypt == $testcrypt) {
            return true;
        }
 
        return false;
    }
 
    /**
     * Set the domain this plugin is supposed to use when authenticating.
     *
     * @param $domain String: authentication domain.
     * @public
     */
    function setDomain( $domain ) {
 
        $this->domain = $domain;
    }
 
    /**
     * Check to see if the specific domain is a valid domain.
     *
     * @param $domain String: authentication domain.
     * @return bool
     * @public
     */
    function validDomain( $domain ) {
        # Override this!
        return true;
    }
 
    /**
     * When a user logs in, optionally fill in preferences and such.
     * For instance, you might pull the email address or real name from the
     * external user database.
     *
     * The User object is passed by reference so it can be modified; don't
     * forget the & on your function declaration.
     *
     * @param User $user
     * @public
     */
    function updateUser( &$user )
    {
        $db = $this->connectToDB();
        $juser = $db->selectRow($this->userTable,array ( '*' ), array ( 'username' => $user->mName ), __METHOD__ );
        $user->setRealName($juser->name);
        $user->setEmail($juser->email);
        $user->mEmailAuthenticated = wfTimestampNow();
        $user->saveSettings();
        //exit;
        # Override this and do something
        return true;
    }
    function disallowPrefsEditByUser() {
        return array (
			'wpRealName' => true,
			'wpUserEmail' => true,
			'wpNick' => true
        );
    }
 
    /**
     * Return true if the wiki should create a new local account automatically
     * when asked to login a user who doesn't exist locally but does in the
     * external auth database.
     *
     * If you don't automatically create accounts, you must still create
     * accounts in some way. It's not possible to authenticate without
     * a local account.
     *
     * This is just a question, and shouldn't perform any actions.
     *
     * @return bool
     * @public
     */
    function autoCreate() {
        return true;
    }
 
    /**
     * Can users change their passwords?
     *
     * @return bool
     */
    function allowPasswordChange() {
        return false;
    }
 
    /**
     * Set the given password in the authentication database.
     * As a special case, the password may be set to null to request
     * locking the password to an unusable value, with the expectation
     * that it will be set later through a mail reset or other method.
     *
     * Return true if successful.
     *
     * @param $user User object.
     * @param $password String: password.
     * @return bool
     * @public
     */
    function setPassword( $user, $password ) {
        return true;
    }
 
    /**
     * Update user information in the external authentication database.
     * Return true if successful.
     *
     * @param $user User object.
     * @return bool
     * @public
     */
    function updateExternalDB( $user ) {
        $db = $this->connectToDB();
        $juser = $db->selectRow($this->userTable,array ( '*' ), array ( 'username' => $user->mName ), __METHOD__ );
        $user->setRealName($juser->name);
        $user->setEmail($juser->email);
        $user->mEmailAuthenticated = wfTimestampNow();
        $user->saveSettings();
        return true;
    }
 
    /**
     * Check to see if external accounts can be created.
     * Return true if external accounts can be created.
     * @return bool
     * @public
     */
    function canCreateAccounts() {
        return false;
    }
 
    /**
     * Add a user to the external authentication database.
     * Return true if successful.
     *
     * @param User $user - only the name should be assumed valid at this point
     * @param string $password
     * @param string $email
     * @param string $realname
     * @return bool
     * @public
     */
    function addUser( $user, $password, $email='', $realname='' ) {
        return false;
    }
 
 
    /**
     * Return true to prevent logins that don't authenticate here from being
     * checked against the local database's password fields.
     *
     * This is just a question, and shouldn't perform any actions.
     *
     * @return bool
     * @public
     */
    function strict() {
        return true;
    }
 
    /**
     * When creating a user account, optionally fill in preferences and such.
     * For instance, you might pull the email address or real name from the
     * external user database.
     *
     * The User object is passed by reference so it can be modified; don't
     * forget the & on your function declaration.
     *
     * @param $user User object.
     * @param $autocreate bool True if user is being autocreated on login
     * @public
     */
    function initUser( $user, $autocreate=false ) {
        # Override this to do something.
    }
 
    /**
     * If you want to munge the case of an account name before the final
     * check, now is your chance.
     */
    function getCanonicalName( $username ) {
        return $username;
    }
}

