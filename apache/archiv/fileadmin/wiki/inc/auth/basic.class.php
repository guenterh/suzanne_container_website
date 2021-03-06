<?php
/**
 * auth/basic.class.php
 *
 * foundation authorisation class
 * all auth classes should inherit from this class
 *
 * @author    Chris Smith <chris@jalakai.co.uk>
 */

class auth_basic {

  var $success = true;


  /**
   * Posible things an auth backend module may be able to
   * do. The things a backend can do need to be set to true
   * in the constructor.
   */
  var $cando = array (
    'addUser'     => false, // can Users be created?
    'delUser'     => false, // can Users be deleted?
    'modLogin'    => false, // can login names be changed?
    'modPass'     => false, // can passwords be changed?
    'modName'     => false, // can real names be changed?
    'modMail'     => false, // can emails be changed?
    'modGroups'   => false, // can groups be changed?
    'getUsers'    => false, // can a (filtered) list of users be retrieved?
    'getUserCount'=> false, // can the number of users be retrieved?
    'getGroups'   => false, // can a list of available groups be retrieved?
    'external'    => false, // does the module do external auth checking?
    'logoff'      => false, // has the module some special logoff method?
  );


  /**
   * Constructor.
   *
   * Carry out sanity checks to ensure the object is
   * able to operate. Set capabilities in $this->cando
   * array here
   *
   * Set $this->success to false if checks fail
   *
   * @author  Christopher Smith <chris@jalakai.co.uk>
   */
  function auth_basic() {
     // the base class constructor does nothing, derived class
    // constructors do the real work
  }

  /**
   * Capability check. [ DO NOT OVERRIDE ]
   *
   * Checks the capabilities set in the $this->cando array and
   * some pseudo capabilities (shortcutting access to multiple
   * ones)
   *
   * ususal capabilities start with lowercase letter
   * shortcut capabilities start with uppercase letter
   *
   * @author  Andreas Gohr <andi@splitbrain.org>
   * @return  bool
   */
  function canDo($cap) {
    switch($cap){
      case 'Profile':
        // can at least one of the user's properties be changed?
        return ( $this->cando['modPass']  ||
                 $this->cando['modName']  ||
                 $this->cando['modMail'] );
        break;
      case 'UserMod':
        // can at least anything be changed?
        return ( $this->cando['modPass']   ||
                 $this->cando['modName']   ||
                 $this->cando['modMail']   ||
                 $this->cando['modLogin']  ||
                 $this->cando['modGroups'] ||
                 $this->cando['modMail'] );
        break;
      default:
        // print a helping message for developers
        if(!isset($this->cando[$cap])){
          msg("Check for unknown capability '$cap' - Do you use an outdated Plugin?",-1);
        }
        return $this->cando[$cap];
    }
  }

  /**
   * Log off the current user [ OPTIONAL ]
   *
   * Is run in addition to the ususal logoff method. Should
   * only be needed when trustExternal is implemented.
   *
   * @see     auth_logoff()
   * @author  Andreas Gohr
   */
  function logOff(){
  }

  /**
   * Do all authentication [ OPTIONAL ]
   *
   * Set $this->cando['external'] = true when implemented
   *
   * If this function is implemented it will be used to
   * authenticate a user - all other DokuWiki internals
   * will not be used for authenticating, thus
   * implementing the functions below becomes optional.
   *
   * The function can be used to authenticate against third
   * party cookies or Apache auth mechanisms and replaces
   * the auth_login() function
   *
   * The function will be called with or without a set
   * username. If the Username is given it was called
   * from the login form and the given credentials might
   * need to be checked. If no username was given it
   * the function needs to check if the user is logged in
   * by other means (cookie, environment).
   *
   * The function needs to set some globals needed by
   * DokuWiki like auth_login() does.
   *
   * @see auth_login()
   * @author  Andreas Gohr <andi@splitbrain.org>
   *
   * @param   string  $user    Username
   * @param   string  $pass    Cleartext Password
   * @param   bool    $sticky  Cookie should not expire
   * @return  bool             true on successful auth
   */
  function trustExternal($user,$pass,$sticky=false){
#    // some example:
#
#    global $USERINFO;
#    global $conf;
#    $sticky ? $sticky = true : $sticky = false; //sanity check
#
#    // do the checking here
#
#    // set the globals if authed
#    $USERINFO['name'] = 'FIXME';
#    $USERINFO['mail'] = 'FIXME';
#    $USERINFO['grps'] = array('FIXME');
#    $_SERVER['REMOTE_USER'] = $user;
#    $_SESSION[$conf['title']]['auth']['user'] = $user;
#    $_SESSION[$conf['title']]['auth']['pass'] = $pass;
#    $_SESSION[$conf['title']]['auth']['info'] = $USERINFO;
#    return true;
  }

  /**
   * Check user+password [ MUST BE OVERRIDDEN ]
   *
   * Checks if the given user exists and the given
   * plaintext password is correct
   *
   * @author  Andreas Gohr <andi@splitbrain.org>
   * @return  bool
   */
  function checkPass($user,$pass){
    msg("no valid authorisation system in use", -1);
    return false;
  }

  /**
   * Return user info [ MUST BE OVERRIDDEN ]
   *
   * Returns info about the given user needs to contain
   * at least these fields:
   *
   * name string  full name of the user
   * mail string  email addres of the user
   * grps array   list of groups the user is in
   *
   * @author  Andreas Gohr <andi@splitbrain.org>
   * @return  array containing user data or false
   */
  function getUserData($user) {
    msg("no valid authorisation system in use", -1);
    return false;
  }

  /**
   * Create a new User [implement only where required/possible]
   *
   * Returns false if the user already exists, null when an error
   * occured and true if everything went well.
   *
   * The new user HAS TO be added to the default group by this
   * function!
   *
   * Set addUser capability when implemented
   *
   * @author  Andreas Gohr <andi@splitbrain.org>
   */
  function createUser($user,$pass,$name,$mail,$grps=null){
    msg("authorisation method does not allow creation of new users", -1);
    return null;
  }

  /**
   * Modify user data [implement only where required/possible]
   *
   * Set the mod* capabilities according to the implemented features
   *
   * @author  Chris Smith <chris@jalakai.co.uk>
   * @param   $user      nick of the user to be changed
   * @param   $changes   array of field/value pairs to be changed (password will be clear text)
   * @return  bool
   */
  function modifyUser($user, $changes) {
    msg("authorisation method does not allow modifying of user data", -1);
    return false;
  }

  /**
   * Delete one or more users [implement only where required/possible]
   *
   * Set delUser capability when implemented
   *
   * @author  Chris Smith <chris@jalakai.co.uk>
   * @param   array  $users
   * @return  int    number of users deleted
   */
  function deleteUsers($users) {
    msg("authorisation method does not allow deleting of users", -1);
    return false;
  }

  /**
   * Return a count of the number of user which meet $filter criteria
   * [should be implemented whenever retrieveUsers is implemented]
   *
   * Set getUserCount capability when implemented
   *
   * @author  Chris Smith <chris@jalakai.co.uk>
   */
  function getUserCount($filter=array()) {
    msg("authorisation method does not provide user counts", -1);
    return 0;
  }

  /**
   * Bulk retrieval of user data [implement only where required/possible]
   *
   * Set getUsers capability when implemented
   *
   * @author  Chris Smith <chris@jalakai.co.uk>
   * @param   start     index of first user to be returned
   * @param   limit     max number of users to be returned
   * @param   filter    array of field/pattern pairs, null for no filter
   * @return  array of userinfo (refer getUserData for internal userinfo details)
   */
  function retrieveUsers($start=0,$limit=-1,$filter=null) {
    msg("authorisation method does not support mass retrieval of user data", -1);
    return array();
  }

  /**
   * Define a group [implement only where required/possible]
   *
   * Set addGroup capability when implemented
   *
   * @author  Chris Smith <chris@jalakai.co.uk>
   * @return  bool
   */
  function addGroup($group) {
    msg("authorisation method does not support independent group creation", -1);
    return false;
  }

  /**
   * Retrieve groups [implement only where required/possible]
   *
   * Set getGroups capability when implemented
   *
   * @author  Chris Smith <chris@jalakai.co.uk>
   * @return  array
   */
  function retrieveGroups($start=0,$limit=0) {
    msg("authorisation method does not support group list retrieval", -1);
    return array();
  }

}
//Setup VIM: ex: et ts=2 enc=utf-8 :
