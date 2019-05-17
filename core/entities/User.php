<?php

    namespace thebuggenie\core\entities;

    use Ramsey\Uuid\Uuid;
    use thebuggenie\core\entities\common\IdentifiableEventContainer;
    use thebuggenie\core\entities\tables\Notifications;
    use thebuggenie\core\entities\tables\UserIssues;
    use thebuggenie\core\entities\tables\Users;
    use thebuggenie\core\framework;

    /**
     * User class
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://opensource.org/licenses/MPL-2.0 Mozilla Public License 2.0 (MPL 2.0)
     * @package thebuggenie
     * @subpackage core
     */

    /**
     * User class
     *
     * @package thebuggenie
     * @subpackage core
     *
     * @Table(name="\thebuggenie\core\entities\tables\Users")
     */
    class User extends IdentifiableEventContainer
    {

        protected static $_num_users = null;

        /**
         * All users
         *
         * @var array
         */
        protected static $_users = null;

        /**
         * Unique username (login name)
         *
         * @var string
         * @Column(type="string", length=50)
         */
        protected $_username = '';

        /**
         * Hashed password
         *
         * @var string
         * @Column(type="string", length=100)
         */
        protected $_password = '';

        /**
         * User real name
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_realname = '';

        /**
         * User short name (buddyname)
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_buddyname = '';

        /**
         * User email
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_email = '';

        /**
         * Is email private?
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_private_email = true;

        /**
         * The user state
         *
         * @var \thebuggenie\core\entities\Userstate
         * @Column(type="integer", length=10)
         * @Relates(class="\thebuggenie\core\entities\Userstate")
         */
        protected $_userstate = null;

        /**
         * Whether the user has a custom userstate set
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_customstate = false;

        /**
         * User homepage
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_homepage = '';

        /**
         * Users language
         *
         * @var string
         * @Column(type="string", length=20)
         */
        protected $_language = '';

        /**
         * Array of team ids where the current user is a member
         *
         * @var array
         * @Relates(class="\thebuggenie\core\entities\Team", collection=true, manytomany=true, joinclass="\thebuggenie\core\entities\tables\TeamMembers")
         */
        protected $teams = null;

        protected $_teams = null;

        /**
         * Array of client ids where the current user is a member
         *
         * @var array
         * @Relates(class="\thebuggenie\core\entities\Client", collection=true, manytomany=true, joinclass="\thebuggenie\core\entities\tables\ClientMembers")
         */
        protected $clients = null;

        /**
         * The users avatar
         *
         * @var string
         * @Column(type="string", length=200)
         */
        protected $_avatar = null;

        /**
         * Whether to use the users gravatar or not
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_use_gravatar = true;

        /**
         * Array of scopes this user is a member of
         *
         * @var array
         * @Relates(class="\thebuggenie\core\entities\Scope", collection=true, manytomany=true, joinclass="\thebuggenie\core\entities\tables\UserScopes")
         */
        protected $_scopes = null;

        /**
         * Array of unconfirmed scopes this user is a member of
         *
         * @var array
         */
        protected $_unconfirmed_scopes = null;

        /**
         * Array of confirmed scopes this user is a member of
         *
         * @var array
         */
        protected $_confirmed_scopes = null;

        /**
         * Array of issues to watch
         *
         * @var array
         * @Relates(class="\thebuggenie\core\entities\Issue", collection=true, manytomany=true, joinclass="\thebuggenie\core\entities\tables\UserIssues")
         */
        protected $_starredissues = null;

        /**
         * Array of issues assigned to the user
         *
         * @var array
         */
        protected $userassigned = null;

        /**
         * Array of issues assigned to the users team(s)
         *
         * @var array
         */
        protected $teamassigned = array();

        /**
         * The users group
         *
         * @var Group
         */
        protected $_group_id = null;

        /**
         * Whether the user is confirmed in this scope or not
         *
         * @var boolean
         */
        protected $_scope_confirmed = null;

        /**
         * A list of the users associated projects, if any
         *
         * @var array
         */
        protected $_associated_projects = null;

        /**
         * Timestamp of when the user was last seen
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_lastseen = 0;

        /**
         * The timezone this user is in
         *
         * @var \DateTimeZone
         * @Column(type="string", length=100)
         */
        protected $_timezone = null;

        /**
         * This users upload quota (MB)
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_quota;

        /**
         * When this user joined
         *
         * @var integer
         * @Column(type="integer", length=10)
         */
        protected $_joined = 0;

        /**
         * This users friends
         *
         * @var array An array of \thebuggenie\core\entities\User objects
         */
        protected $_friends = null;

        /**
         * Whether the user is enabled
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_enabled = false;

        /**
         * Whether the user is autogenerated via openid
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_openid_locked = false;

        /**
         * Whether the user is activated
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_activated = false;

        /**
         * Whether the user is deleted
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_deleted = false;

        /**
         * The users preferred formatting syntax for issues
         *
         * @var integer
         * @Column(type="integer", length=3, default=2)
         */
        protected $_preferred_issues_syntax = framework\Settings::SYNTAX_MD;

        /**
         * The users preferred formatting syntax for articles
         *
         * @var integer
         * @Column(type="integer", length=3, default=1)
         */
        protected $_preferred_wiki_syntax = framework\Settings::SYNTAX_MW;

        /**
         * The users preferred formatting syntax for comments
         *
         * @var integer
         * @Column(type="integer", length=3, default=2)
         */
        protected $_preferred_comments_syntax = framework\Settings::SYNTAX_MD;

        /**
         * Whether the user wants to default to markdown in wiki pages
         *
         * @var boolean
         * @Column(type="boolean")
         */
        protected $_prefer_wiki_markdown = false;

        /**
         * List of user's notification settings
         *
         * @var \thebuggenie\core\entities\NotificationSetting[]
         * @Relates(class="\thebuggenie\core\entities\NotificationSetting", collection=true, foreign_column="user_id")
         */
        protected $_notification_settings = [];

        /**
         * List of user's notifications
         *
         * @var \thebuggenie\core\entities\Notification[]
         * @Relates(class="\thebuggenie\core\entities\Notification", collection=true, foreign_column="user_id", orderby="created_at")
         */
        protected $_notifications = null;

        /**
         * List of user's dashboards
         *
         * @var \thebuggenie\core\entities\Dashboard[]
         * @Relates(class="\thebuggenie\core\entities\Dashboard", collection=true, foreign_column="user_id", orderby="name")
         */
        protected $_dashboards = null;

        /**
         * List of user's application-specific passwords
         *
         * @var \thebuggenie\core\entities\ApplicationPassword[]
         * @Relates(class="\thebuggenie\core\entities\ApplicationPassword", collection=true, foreign_column="user_id", orderby="created_at")
         */
        protected $_application_passwords = null;

        /**
         * List of user's session tokens
         *
         * @var \thebuggenie\core\entities\UserSession[]
         * @Relates(class="\thebuggenie\core\entities\UserSession", collection=true, foreign_column="user_id", orderby="created_at")
         */
        protected $_user_sessions = null;

        protected $_unread_notifications_count = null;

        protected $_read_notifications_count = null;

        protected $_filter_first_notification = null;

        protected $_permissions_cache = [];

        /**
         * Retrieve a user by username
         *
         * @param string $username
         *
         * @return \thebuggenie\core\entities\User
         */
        public static function getByUsername($username)
        {
            return self::getB2DBTable()->getByUsername($username);
        }

        /**
         * Retrieve a user by its email address
         *
         * @param string $email
         *   Email address of the user.
         * @param bool $createNew
         *   Whether to create the user if it does not exist.
         *   Defaults to `true`.
         * @return \thebuggenie\core\entities\User|null
         *   User instance or null, if the user was not found.
         */
        public static function getByEmail($email, $createNew = true)
        {
            $user = self::getB2DBTable()->getByEmail($email);

            if (!$user instanceof User && $createNew && !framework\Settings::isUsingExternalAuthenticationBackend())
            {
                $user = new User();
                $user->setPassword(self::createPassword());
                $user->setUsername($email);
                $user->setEmail($email);
                $user->setActivated();
                $user->setEnabled();
                $user->setValidated();
                $user->save();
            }

            return $user;
        }

        /**
         * Retrieve all users
         *
         * @return array
         */
        public static function getAll()
        {
            if (self::$_users === null)
            {
                self::$_users = self::getB2DBTable()->getAll();
            }

            return self::$_users;
        }

        public static function isUsernameAvailable($username)
        {
            return static::getB2DBTable()->isUsernameAvailable($username);
        }

        public static function doesIDExist($id)
        {
            return (bool) static::getB2DBTable()->doesIDExist($id);
        }

        /**
         * Load user fixtures for a specified scope
         *
         * @param Scope $scope
         * @param Group $admin_group
         * @param Group $user_group
         * @param Group $guest_group
         */
        public static function loadFixtures(Scope $scope, Group $admin_group, Group $user_group, Group $guest_group)
        {
            $adminuser = new User();
            $adminuser->setUsername('administrator');
            $adminuser->setRealname('Administrator');
            $adminuser->setBuddyname('Admin');
            $adminuser->setGroup($admin_group);
            $adminuser->setPassword('admin');
            $adminuser->setActivated();
            $adminuser->setEnabled();
            $adminuser->setAvatar('admin');
            $adminuser->save();

            $guestuser = new User();
            $guestuser->setUsername('guest');
            $guestuser->setRealname('Guest user');
            $guestuser->setBuddyname('Guest user');
            $guestuser->setGroup($guest_group);
            $guestuser->setPassword('password'); // Settings not active yet
            $guestuser->setActivated();
            $guestuser->setEnabled();
            $guestuser->save();

            framework\Settings::saveSetting('defaultuserid', $guestuser->getID(), 'core', $scope->getID());

            return array($guestuser->getID(), $adminuser->getID());
        }

        /**
         * Take a raw password and convert it to the hashed format
         *
         * @param string $password
         *
         * @return hashed password
         */
        public static function hashPassword($password, $salt)
        {
            return crypt($password, '$2a$07$'.$salt.'$');
        }

        /**
         * Returns the logged in user, or default user if not logged in
         *
         * @param \thebuggenie\core\framework\Request $request
         * @param \thebuggenie\core\framework\Action $action
         * @param bool $auto
         *
         * @return \thebuggenie\core\entities\User
         * @throws framework\exceptions\ElevatedLoginException
         */
        public static function identify(framework\Request $request, framework\Action $action, $auto = false)
        {
            $authentication_method = framework\Context::getRouting()->getCurrentRouteAuthenticationMethod($action);
            $authentication_backend = framework\Settings::getAuthenticationBackend();
            $user = null;
            framework\Logging::log("Using auth method {$authentication_method}", 'auth', framework\Logging::LEVEL_INFO);

            switch ($authentication_method)
            {
                case framework\Action::AUTHENTICATION_METHOD_ELEVATED:
                case framework\Action::AUTHENTICATION_METHOD_CORE:
                    framework\Logging::log('Authenticating with backend: '.framework\Settings::getAuthenticationBackendIdentifier(), 'auth', framework\Logging::LEVEL_INFO);

                    // If automatic, check if we have a session that exists already
                    if ($auto)
                    {
                        if ($authentication_backend->getAuthenticationMethod() == framework\AuthenticationBackend::AUTHENTICATION_TYPE_TOKEN)
                        {
                            $user = $authentication_backend->autoVerifyToken($request->getCookie('username'), $request->getCookie('session_token'));
                            if ($user instanceof User && $authentication_method == framework\Action::AUTHENTICATION_METHOD_ELEVATED)
                            {
                                $user = $authentication_backend->autoVerifyToken($request->getCookie('username'), $request->getCookie('elevated_session_token'), true);
                            }
                        }
                        else
                        {
                            $user = $authentication_backend->autoVerifyLogin($request->getCookie('username'), $request->getCookie('password'));
                            if ($user instanceof User && $authentication_method == framework\Action::AUTHENTICATION_METHOD_ELEVATED)
                            {
                                $user = $authentication_backend->autoVerifyLogin($request->getCookie('username'), $request->getCookie('elevated_password'), true);
                            }
                        }
                        // Try autologin if we did not do an auto login using cookies
                        if(!$request->getCookie('username') && !$user) {
                            $user = $authentication_backend->doAutoLogin($request);
                        }
                    }
                    else
                    {
                        // If we don't have login details, try logging in with provided parameters
                        $user = $authentication_backend->doExplicitLogin($request);
                    }

                    break;
                case framework\Action::AUTHENTICATION_METHOD_DUMMY:
                    $user = self::getB2DBTable()->getByUserID(framework\Settings::getDefaultUserID());
                    break;
                case framework\Action::AUTHENTICATION_METHOD_CLI:
                    $user = self::getB2DBTable()->getByUsername(framework\Context::getCurrentCLIusername());
                    break;
                case framework\Action::AUTHENTICATION_METHOD_RSS_KEY:
                    $user = self::getB2DBTable()->getByRssKey($request['rsskey']);
                    break;
                case framework\Action::AUTHENTICATION_METHOD_APPLICATION_PASSWORD:

                    $authorization_header = $request->getAuthorizationHeader();
                    if (!$authorization_header || strlen($authorization_header) < 7) {
                        throw new \Exception('Cannot read authorization headers');
                    }

                    $authorization_header = substr($authorization_header, 7);
                    $header_details = explode('.', $authorization_header);

                    if (count($header_details) != 2) {
                        throw new \Exception('Incorrect data in authorization header');
                    }

                    $username = $header_details[0];
                    $token = $header_details[1];

                    framework\Logging::log('Fetching user by username', 'auth', framework\Logging::LEVEL_INFO);
                    $user = self::getB2DBTable()->getByUsername($username);

                    if ($user instanceof User)
                    {
                        if (!$user->authenticateApplicationPassword($token)) $user = null;
                    }

                    break;
                case framework\Action::AUTHENTICATION_METHOD_BASIC:

                    $username = $_SERVER['PHP_AUTH_USER'];
                    framework\Logging::log("Fetching user by username", 'auth', framework\Logging::LEVEL_INFO);
                    $user = self::getB2DBTable()->getByUsername($username);

                    if ($user instanceof User)
                    {
                        if (!$user->hasPassword($_SERVER['PHP_AUTH_PW'])) $user = null;
                    }

                    break;
            }

            if (!$user instanceof User && !framework\Settings::isLoginRequired())
            {
                $user = framework\Settings::getDefaultUser();
            }

            if ($user instanceof User)
            {
                if (!$user->isActivated())
                {
                    throw new \Exception('This account has not been activated yet');
                }
                elseif (!$user->isEnabled())
                {
                    throw new \Exception('This account has been suspended');
                }
                elseif(!$user->isConfirmedMemberOfScope(framework\Context::getScope()))
                {
                    if (!framework\Settings::isRegistrationAllowed())
                    {
                        throw new \Exception('This account does not have access to this scope');
                    }
                }
            }
            elseif (framework\Settings::isLoginRequired())
            {
                throw new \Exception('Login required');
            }
            else
            {
                throw new \Exception('No such login');
            }

            return $user;
        }

        /**
         * Create and return a temporary password
         *
         * @return string
         */
        public static function createPassword($len = 16)
        {
            $pass = '';
            $lchar = 0;
            $char = 0;
            for($i = 0; $i < $len; $i++)
            {
                while($char == $lchar)
                {
                    $char = mt_rand(48, 109);
                    if($char > 57) $char += 7;
                    if($char > 90) $char += 6;
                }
                $pass .= chr($char);
                $lchar = $char;
            }
            return $pass;
        }

        public static function getUsersCount()
        {
            if (self::$_num_users === null)
            {
                self::$_num_users = tables\UserScopes::getTable()->countUsers();
            }

            return self::$_num_users;
        }

        /**
         * Pre-save function to check for conflicting usernames and to make
         * sure some properties are set
         *
         * @param boolean $is_new Whether this is a new user object
         */
        protected function _preSave($is_new)
        {
            parent::_preSave($is_new);
            if (!framework\Context::isInstallmode() && !framework\Context::isUpgrademode())
            {
                $compare_user = self::getByUsername($this->getUsername());
                if ($compare_user instanceof User && $compare_user->getID() && $compare_user->getID() != $this->getID())
                {
                    throw new \Exception(framework\Context::getI18n()->__('This username already exists'));
                }
            }
            if ($is_new)
            {
                // In case the postsave event isn't processed we automatically enable the user
                // since we can't be sure that an activation email has been sent out
                $this->setEnabled();
                $this->setActivated();
            }
            if (!$this->_realname)
            {
                $this->_realname = $this->_username;
            }
            if (!$this->_buddyname)
            {
                $this->_buddyname = $this->_username;
            }
            if (is_object($this->_timezone))
            {
                $this->_timezone = $this->_timezone->getName();
            }
            if ($is_new && $this->_joined === 0)
            {
                $this->_joined = NOW;
            }
            if ($is_new && $this->_group_id === null)
            {
                $this->setGroup(framework\Settings::getDefaultGroup());
            }
            if ($this->_deleted)
            {
                try
                {
                    if ($this->getGroup() instanceof \thebuggenie\core\entities\Group)
                    {
                        $this->getGroup()->removeMember($this);
                    }
                }
                catch (\Exception $e) {}

                $this->_group_id = null;
                $this->_buddyname = $this->_username;
                $this->_username = '';
                if (!$is_new)
                {
                    tables\TeamMembers::getTable()->clearTeamsByUserID($this->getID());
                    tables\ClientMembers::getTable()->clearClientsByUserID($this->getID());
                    tables\UserScopes::getTable()->clearUserScopes($this->getID());
                }
            }
        }

        /**
         * Performs post-save actions on user objects
         *
         * This includes firing off events for modules to listen to (e.g. so
         * activation emails can be sent out), and setting up a default
         * dashboard for the new user.
         *
         * @param boolean $is_new Whether this is a new object or not (automatically passed to the function from B2DB)
         */
        protected function _postSave($is_new)
        {
            if ($is_new)
            {
                // Set up a default dashboard for the user
                $dashboard = new \thebuggenie\core\entities\Dashboard();
                                $dashboard->setUser($this);
                                $dashboard->save();

                $scope = \thebuggenie\core\entities\Scope::getB2DBTable()->selectById((int) framework\Settings::getDefaultScopeID());
                $this->addScope($scope, false);
                $this->confirmScope($scope->getID());
                if (!framework\Context::getScope()->isDefault())
                {
                    $scope = framework\Context::getScope();
                    $this->addScope($scope, false);
                    $this->confirmScope($scope->getID());
                }

                $event = \thebuggenie\core\framework\Event::createNew('core', 'self::_postSave', $this);
                $event->trigger();
            }

            if ($this->_group_id !== null)
            {
                tables\UserScopes::getTable()->updateUserScopeGroup($this->getID(), framework\Context::getScope()->getID(), $this->_group_id);
            }

        }

        /**
         * Returns whether the current user is a guest or not
         *
         * @return boolean
         */
        public static function isThisGuest()
        {
            if (framework\Context::getUser() instanceof User)
            {
                return framework\Context::getUser()->isGuest();
            }
            else
            {
                return true;
            }
        }

        /**
         * Class constructor
         *
         * @param \b2db\Row $row
         */
        public function _construct(\b2db\Row $row, $foreign_key = null)
        {
            framework\Logging::log("User with id {$this->getID()} set up successfully");
        }

        /**
         * Retrieve the users real name
         *
         * @return string
         */
        public function getName()
        {
            if ($this->isDeleted())
            {
                return framework\Context::getI18n()->__('No such user');
            }
            switch (framework\Settings::getUserDisplaynameFormat())
            {
                case framework\Settings::USER_DISPLAYNAME_FORMAT_REALNAME:
                    return ($this->_realname) ? $this->_realname : $this->_username;

                case framework\Settings::USER_DISPLAYNAME_FORMAT_BUDDY:
                default:
                    return ($this->_buddyname) ? $this->_buddyname : (($this->_realname) ? $this->_realname : $this->_username);
            }
        }

        /**
         * Retrieve the users id
         *
         * @return integer
         */
        public function getID()
        {
            return $this->_id;
        }

        /**
         * Retrieve this users realname and username combined
         *
         * @return string "Real Name (username)"
         */
        public function getNameWithUsername()
        {
            if ($this->isDeleted())
            {
                return __('No such user');
            }
            if ($this->isOpenIdLocked())
            {
                return $this->_buddyname;
            }
            switch (framework\Settings::getUserDisplaynameFormat())
            {
                case framework\Settings::USER_DISPLAYNAME_FORMAT_REALNAME:
                    return ($this->_realname) ? $this->_realname . ' (@' . $this->_username . ')' : '@' . $this->_username;

                case framework\Settings::USER_DISPLAYNAME_FORMAT_BUDDY:
                default:
                    return ($this->_buddyname) ? $this->_buddyname . ' (@' . $this->_username . ')' : (($this->_realname) ? $this->_realname . ' (@' . $this->_username . ')' : '@' . $this->_username);
            }
        }

        public function __toString()
        {
            return $this->getNameWithUsername();
        }

        /**
         * Whether this user is authenticated or just an authenticated guest
         *
         * @return boolean
         */
        public function isAuthenticated()
        {
            return (bool) ($this->getID() == framework\Context::getUser()->getID() && ($this->getID() != framework\Settings::getDefaultUserID() || !framework\Settings::isDefaultUserGuest()));
        }

        /**
         * Set users "last seen" property to NOW
         */
        public function updateLastSeen()
        {
            $this->_lastseen = NOW;
        }

        /**
         * Return timestamp for when this user was last online
         *
         * @return integer
         */
        public function getLastSeen()
        {
            return $this->_lastseen;
        }

        /**
         * Marks this user with the Online user state
         */
        public function setOnline()
        {
            $this->_userstate = framework\Settings::getOnlineState();
            $this->_customstate = false;
        }

        /**
         * Marks this user with the Offline user state
         */
        public function setOffline()
        {
            $this->_userstate = framework\Settings::getOfflineState();
            $this->_customstate = true;
            $this->save();
        }

        /**
         * Retrieve the timestamp for when this user joined
         *
         * @return integer
         */
        public function getJoinedDate()
        {
            return $this->_joined;
        }

        /**
         * Populates team array when needed
         */
        protected function _populateTeams()
        {
            if ($this->teams === null)
            {
                $this->_teams = array('assigned' => array(), 'ondemand' => array());
                $this->_b2dbLazyLoad('teams');
                framework\Logging::log('Populating user teams');
                if (count($this->teams))
                {
                    foreach ($this->teams as $team)
                    {
                        if (!$team->getScope() instanceof Scope || $team->getScope()->getID() != framework\Context::getScope()->getID()) continue;
                        $key = ($team->isOndemand()) ? 'ondemand' : 'assigned';
                        $this->_teams[$key][$team->getID()] = $team;
                    }
                }
                framework\Logging::log('...done (Populating user teams)');
            }
        }

        /**
         * Checks if the user is a member of the given team
         *
         * @param \thebuggenie\core\entities\Team $team
         *
         * @return boolean
         */
        public function isMemberOfTeam(\thebuggenie\core\entities\Team $team)
        {
            $this->_populateTeams();
            return (array_key_exists($team->getID(), $this->_teams['assigned']) || array_key_exists($team->getID(), $this->_teams['ondemand']));
        }

        /**
         * Populates client array when needed
         *
         */
        protected function _populateClients()
        {
            if ($this->clients === null)
            {
                $this->_b2dbLazyLoad('clients');
            }
        }

        /**
         * Checks if the user is a member of the given client
         *
         * @param \thebuggenie\core\entities\Client $client
         *
         * @return boolean
         */
        public function isMemberOfClient(\thebuggenie\core\entities\Client $client)
        {
            $this->_populateClients();
            return array_key_exists($client->getID(), $this->clients);
        }

        /**
         * Return all this user's clients
         *
         * @return Client[]
         */
        public function getClients()
        {
            $this->_populateClients();
            return $this->clients;
        }

        /**
         * Checks whether or not the user is logged in
         *
         * @return boolean
         */
        public function isLoggedIn()
        {
            return ($this->_id != 0) ? true : false;
        }

        /**
         * Checks whether or not the current user is a "regular" or "guest" user
         *
         * @return boolean
         */
        public function isGuest()
        {
            return (bool) (!$this->isLoggedIn() || ($this->getID() == framework\Settings::getDefaultUserID() && framework\Settings::isDefaultUserGuest()));
        }

        /**
         * Returns an array of issue ids which are directly assigned to the current user
         *
         * @return Issue[]
         */
        public function getUserAssignedIssues()
        {
            if ($this->userassigned === null)
            {
                $this->userassigned = array();
                if ($issues = tables\Issues::getTable()->getOpenIssuesByUserAssigned($this->getID()))
                {
                    foreach ($issues as $issue)
                    {
                        $this->userassigned[$issue->getID()] = $issue;
                    }
                    ksort($this->userassigned, SORT_NUMERIC);
                }
            }
            return $this->userassigned;
        }

        /**
         * Returns an array of issue ids assigned to the given team
         *
         * @param integer $team_id The team id
         *
         * @return Issue[]
         */
        public function getUserTeamAssignedIssues($team_id)
        {
            if (!array_key_exists($team_id, $this->teamassigned))
            {
                $this->teamassigned[$team_id] = array();
                if ($issues = tables\Issues::getTable()->getOpenIssuesByTeamAssigned($team_id))
                {
                    foreach ($issues as $issue)
                    {
                        $this->teamassigned[$team_id][$issue->getID()] = $issue;
                    }
                }
                ksort($this->teamassigned[$team_id], SORT_NUMERIC);
            }
            return $this->teamassigned[$team_id];
        }

        /**
         * Populate the array of starred issues
         */
        protected function _populateStarredIssues()
        {
            if ($this->_starredissues === null)
            {
                $this->_starredissues = UserIssues::getTable()->getUserStarredIssues($this->getID());
                ksort($this->_starredissues, SORT_NUMERIC);
            }
        }

        /**
         * Returns whether or not an issue is starred
         *
         * @param integer $issue_id The issue ID to check
         *
         * @return boolean
         */
        public function isIssueStarred($issue_id)
        {
            $this->_populateStarredIssues();
            return array_key_exists($issue_id, $this->_starredissues);
        }

        /**
         * Adds an issue to the list of issues "starred" by this user
         *
         * @param integer $issue_id ID of issue to add
         * @return boolean
         */
        public function addStarredIssue($issue_id)
        {
            $this->_populateStarredIssues();
            if ($this->isLoggedIn() && !$this->isGuest())
            {
                if (array_key_exists($issue_id, $this->_starredissues))
                    return true;

                tables\UserIssues::getTable()->addStarredIssue($this->getID(), $issue_id);
                $issue = tables\Issues::getTable()->selectById($issue_id);
                $this->_starredissues[$issue->getID()] = $issue;
                ksort($this->_starredissues);
                return true;
            }

            return false;
        }

        /**
         * Removes an issue from the list of flagged issues
         *
         * @param integer $issue_id ID of issue to remove
         */
        public function removeStarredIssue($issue_id)
        {
            tables\UserIssues::getTable()->removeStarredIssue($this->getID(), $issue_id);
            if (is_array($this->_starredissues) && array_key_exists($issue_id, $this->_starredissues))
            {
                unset($this->_starredissues[$issue_id]);
            }
            return true;
        }

        /**
         * Sets up the internal friends array
         */
        protected function _setupFriends()
        {
            if ($this->_friends === null)
            {
                $userids = tables\Buddies::getTable()->getFriendsByUserID($this->getID());
                $friends = array();
                foreach ($userids as $friend)
                {
                    try
                    {
                        $friend = \thebuggenie\core\entities\User::getB2DBTable()->selectById((int) $friend);
                        $friends[$friend->getID()] = $friend;
                    }
                    catch (\Exception $e)
                    {
                        $this->removeFriend($friend);
                    }
                }

                $this->_friends = $friends;
            }
        }

        /**
         * Adds a friend to the buddy list
         *
         * @param \thebuggenie\core\entities\User $user Friend to add
         *
         * @return boolean
         */
        public function addFriend($user)
        {
            if (!($this->isFriend($user)) && !$user->isDeleted())
            {
                tables\Buddies::getTable()->addFriend($this->getID(), $user->getID());
                if ($this->_friends !== null)
                {
                    $this->_friends[$user->getID()] = $user;
                }
                return true;
            }
            else
            {
                return false;
            }
        }

        /**
         * Get all this users friends
         *
         * @return User An array of users[]
         */
        public function getFriends()
        {
            $this->_setupFriends();

            return $this->_friends;
        }

        /**
         * Removes a user from the list of buddies
         *
         * @param \thebuggenie\core\entities\User $user User to remove
         */
        public function removeFriend($user)
        {
            $user_id = ($user instanceof User) ? $user->getID() : $user;
            tables\Buddies::getTable()->removeFriendByUserID($this->getID(), $user_id);
            if (is_array($this->_friends))
            {
                unset($this->_friends[$user_id]);
            }
        }

        /**
         * Check if the given user is a friend of this user
         *
         * @param \thebuggenie\core\entities\User $user The user to check
         *
         * @return boolean
         */
        public function isFriend($user)
        {
            $this->_setupFriends();
            if (empty($this->_friends)) return false;
            return array_key_exists($user->getID(), $this->_friends);
        }

        /**
         * Change the password to a new password
         *
         * @param string $newpassword
         */
        public function changePassword($newpassword)
        {
            if (!$newpassword)
            {
                throw new \Exception("Cannot set empty password");
            }
            $this->_password = password_hash($newpassword, PASSWORD_DEFAULT);
        }

        /**
         * Alias for changePassword
         *
         * @param string $newpassword
         *
         * @see self::changePassword
         */
        public function setPassword($newpassword)
        {
            return $this->changePassword($newpassword);
        }

        /**
         * Set the user state to this state
         *
         * @param Userstate $state The userstate to set
         */
        public function setState(Userstate $state)
        {
            $this->_userstate = $state;
            $this->_customstate = true;
        }

        /**
         * Whether this user is currently active on the site
         *
         * @return boolean
         */
        public function isActive()
        {
            return (bool) ($this->_lastseen > (NOW - (60 * 10)));
        }

        /**
         * Whether this user is currently inactive (but not logged out) on the site
         *
         * @return boolean
         */
        public function isAway()
        {
            return (bool) (($this->_lastseen < (NOW - (60 * 10))) && ($this->_lastseen > (NOW - (60 * 30))));
        }

        /**
         * Whether this user is currently offline (timed out or explicitly logged out)
         *
         * @return boolean
         */
        public function isOffline()
        {
            return (!$this->getState() instanceof UserState) ? false : !$this->getState()->isOnline();
        }

        /**
         * Get the current user state
         *
         * @return \thebuggenie\core\entities\Userstate
         */
        public function getState()
        {
            $active = $this->isActive();
            $away = $this->isAway();
            if (($active || $away) && $this->_customstate)
            {
                $this->_b2dbLazyLoad('_userstate');
                if ($this->_userstate instanceof Userstate)
                {
                    return $this->_userstate;
                }
            }


            if ($active)
                return framework\Settings::getOnlineState();

            if ($away)
                return framework\Settings::getAwayState();

            else
                return framework\Settings::getOfflineState();
        }

        /**
         * Whether this user is enabled or not
         *
         * @return boolean
         */
        public function isEnabled()
        {
            return $this->_enabled;
        }

        /**
         * Set whether this user is activated or not
         *
         * @param boolean $val [optional]
         */
        public function setActivated($val = true)
        {
            $this->_activated = (boolean) $val;
        }

        /**
         * Whether this user is activated or not
         *
         * @return boolean
         */
        public function isActivated()
        {
            return $this->_activated;
        }

        /**
         * Whether this user is deleted or not
         *
         * @return boolean
         */
        public function isDeleted()
        {
            return $this->_deleted;
        }

        public function markAsDeleted()
        {
            $this->_deleted = true;
        }

        /**
         * Returns an array of teams which the current user is a member of
         *
         * @return Team[]
         */
        public function getTeams()
        {
            $this->_populateTeams();
            $teams = $this->_teams['assigned'];

            if (framework\Context::isProjectContext())
            {
                $project = framework\Context::getCurrentProject();
            }
            else if (framework\Context::getRequest()->hasParameter('issue_id'))
            {
                $issue = Issue::getB2DBTable()->selectById(framework\Context::getRequest()->getParameter('issue_id'));

                if ($issue instanceof Issue && $issue->getProject() instanceof Project) $project = $issue->getProject();
            }

            if (isset($project))
            {
                $project_assigned_teams = $project->getAssignedTeams();

                foreach ($teams as $team_id => $team)
                {
                    if (! array_key_exists($team_id, $project_assigned_teams)) unset($teams[$team_id]);
                }
            }

            return $teams;
        }

        public function hasTeams()
        {
            $this->_populateTeams();
            return count($this->_teams['assigned']);
        }

        /**
         * Returns an array of teams which the current user is a member of
         *
         * @return Team[]
         */
        public function getOndemandTeams()
        {
            $this->_populateTeams();
            return $this->_teams['ondemand'];
        }

        /**
         * Clear this users teams
         */
        public function clearTeams()
        {
            \thebuggenie\core\entities\tables\TeamMembers::getTable()->clearTeamsByUserID($this->getID());
        }

        /**
         * Clear this users clients
         */
        public function clearClients()
        {
            \thebuggenie\core\entities\tables\ClientMembers::getTable()->clearClientsByUserID($this->getID());
        }

        /**
         * Add this user to a team
         *
         * @param \thebuggenie\core\entities\Team $team
         */
        public function addToTeam(\thebuggenie\core\entities\Team $team)
        {
            $team->addMember($this);
            $this->_teams = null;
            $this->teams = null;
        }

        /**
         * Add this user to a client
         *
         * @param \thebuggenie\core\entities\Client $client
         */
        public function addToClient(\thebuggenie\core\entities\Client $client)
        {
            $client->addMember($this);
            $this->clients = null;
        }

        /**
         * Set whether or not the email address is hidden for normal users
         *
         * @param boolean $val
         */
        public function setEmailPrivate($val)
        {
            $this->_private_email = (bool) $val;
        }

        /**
         * Returns whether or not the email address is private
         *
         * @return boolean
         */
        public function isEmailPrivate()
        {
            return $this->_private_email;
        }

        /**
         * Returns whether or not the email address is public
         *
         * @return boolean
         */
        public function isEmailPublic()
        {
            return !$this->_private_email;
        }

        /**
         * Returns whether the user is confirmed in this scope or not
         *
         * @return boolean
         */
        public function getScopeConfirmed()
        {
            if ($this->_scope_confirmed === null)
            {
                $this->_scope_confirmed = tables\UserScopes::getTable()->getUserConfirmedByScope($this->getID(), framework\Context::getScope()->getID());
            }
            return (bool) $this->_scope_confirmed;
        }

        public function setScopeConfirmed($value = true)
        {
            $this->_scope_confirmed = $value;
        }

        public function isScopeConfirmed()
        {
            return $this->getScopeConfirmed();
        }

        /**
         * Returns the user group
         *
         * @return Group
         */
        public function getGroup()
        {
            if (!is_object($this->_group_id))
            {
                try
                {
                    if (!is_numeric($this->_group_id))
                    {
                        $this->_group_id = tables\UserScopes::getTable()->getUserGroupIdByScope($this->getID(), framework\Context::getScope()->getID());
                    }
                    if (!is_numeric($this->_group_id))
                    {
                        $this->_group_id = framework\Settings::getDefaultGroup();
                    }
                    else
                    {
                        $this->_group_id = tables\Groups::getTable()->selectById($this->_group_id);
                    }
                }
                catch (\Exception $e) {}
            }
            return $this->_group_id;
        }

        /**
         * Return this users group ID if any
         *
         * @return integer
         */
        public function getGroupID()
        {
            if (is_object($this->getGroup()))
            {
                return $this->getGroup()->getID();
            }

            return null;
        }

        /**
         * Set this users group
         *
         * @param Group|integer $group
         */
        public function setGroup($group)
        {
            $this->_group_id = $group;
        }

        /**
         * Set the username
         *
         * @param string $username
         */
        public function setUsername($username)
        {
            $this->_username = $username;
        }

        /**
         * Return this users' username
         *
         * @return string
         */
        public function getUsername()
        {
            return $this->_username;
        }

        /**
         * Returns a hash of the user password
         *
         * @return string
         */
        public function getHashPassword()
        {
            return $this->_password;
        }

        /**
         * Returns a hash of the user password
         *
         * @see self::getHashPassword
         * @deprecated
         * @return string
         */
        public function getPassword()
        {
            return $this->getHashPassword();
        }

        /**
         * Return whether or not the users password is this
         *
         * @param string $password Unhashed password
         *
         * @return boolean
         */
        public function hasPassword($password)
        {
            return password_verify($password, $this->_password);
        }

        /**
         * Return whether or not the users password hash matches the provided hash value
         *
         * @param string $password_hash Hashed password
         *
         * @return boolean
         */
        public function hasPasswordHash($password_hash)
        {
            return hash_equals($password_hash, $this->_password);
        }

        /**
         * Returns the real name (full name) of the user
         *
         * @return string
         */
        public function getRealname()
        {
            return $this->_realname;
        }

        /**
         * Returns the buddy name (friendly name) of the user
         *
         * @return string
         */
        public function getBuddyname()
        {
            return $this->_buddyname;
        }

        /**
         * Return the users nickname (buddyname)
         *
         * @uses self::getBuddyname()
         *
         * @return string
         */
        public function getNickname()
        {
            return $this->getBuddyname();
        }

        /**
         * Returns the realname or, if not available, the buddyname.
         *
         * @return string
         */
        public function getDisplayName()
        {
            return ($this->getRealname() == '') ? $this->getBuddyname() : $this->getRealname();
        }

        /**
         * Returns the email of the user
         *
         * @return string
         */
        public function getEmail()
        {
            return $this->_email;
        }

        /**
         * Returns the users homepage
         *
         * @return string
         */
        public function getHomepage()
        {
            return $this->_homepage;
        }

        /**
         * Set this users homepage
         *
         * @param string $homepage
         */
        public function setHomepage($homepage)
        {
            $this->_homepage = $homepage;
        }

        /**
         * Set the avatar image
         *
         * @param string $avatar
         */
        public function setAvatar($avatar)
        {
            $this->_avatar = $avatar;
        }

        /**
         * Returns the avatar of the user
         *
         * @return string
         */
        public function getAvatar()
        {
            return ($this->_avatar != '') ? $this->_avatar : 'user';
        }

        /**
         * Return the users avatar url
         *
         * @param boolean $small [optional] Whether to get the URL for the small avatar (default small)
         *
         * @return string an URL to put in an <img> tag
         */
        public function getAvatarURL($small = true)
        {
            $event = \thebuggenie\core\framework\Event::createNew('core', 'self::getAvatarURL', $this)->trigger();
            $url = $event->getReturnValue();

            if ($url === null)
            {
                if ($this->usesGravatar() && $this->getEmail())
                {
                    $url = (framework\Context::getScope()->isSecure()) ? 'https://secure.gravatar.com/avatar/' : 'http://www.gravatar.com/avatar/';
                    $url .= md5(trim($this->getEmail())) . '.png?d=wavatar&amp;s=';

                    $size_event = \thebuggenie\core\framework\Event::createNew('core', 'self::getGravatarSize', $this)->trigger(compact('small'));
                    $size = $size_event->getReturnValue();

                    if ($size === null)
                    {
                        if (is_bool($small))
                        {
                            $url .= ($small === true) ? 28 : 48;
                        }
                        else if (is_numeric($small))
                        {
                            $url .= $small;
                        }
                    }
                    else
                    {
                        $url .= $size;
                    }
                }
                else
                {
                    $url = framework\Context::getWebroot() . 'avatars/' . $this->getAvatar();
                    if ($small) $url .= '_small';
                    $url .= '.png';
                }
            }
            return $url;
        }

        /**
         * Return whether the user uses gravatar for avatars
         *
         * @return boolean
         */
        public function usesGravatar()
        {
            if (!framework\Settings::isGravatarsEnabled()) return false;
            if ($this->isGuest()) return false;
            return (bool) $this->_use_gravatar;
        }

        public function disableTutorial($key)
        {
            framework\Settings::saveUserSetting($this->getID(), 'disable_tutorial_'.$key, true);
        }

        protected function _isTutorialEnabled($key)
        {
            if ($this->isGuest()) return false;
            return !(bool) framework\Settings::getUserSetting($this->getID(), 'disable_tutorial_'.$key);
        }

        public function enableTutorial($key)
        {
            framework\Settings::deleteUserSetting($this->getID(), 'disable_tutorial_'.$key);
        }

        public function isViewissueTutorialEnabled()
        {
            return $this->_isTutorialEnabled('viewissue');
        }

        public function isPlanningTutorialEnabled()
        {
            return $this->_isTutorialEnabled('planning');
        }

        public function isKeyboardNavigationEnabled()
        {
            $val = framework\Settings::get(framework\Settings::SETTING_USER_KEYBOARD_NAVIGATION, 'core', framework\Context::getScope(), $this->getID());
            return ($val !== null) ? $val : true;
        }

        public function isDesktopNotificationsNewTabEnabled()
        {
            $val = framework\Settings::get(framework\Settings::SETTING_USER_DESKTOP_NOTIFICATIONS_NEW_TAB, 'core', framework\Context::getScope(), $this->getID());
            return ($val !== null) ? $val : false;
        }

        public function setKeyboardNavigationEnabled($value = true)
        {
            if (!$value) framework\Settings::saveSetting(framework\Settings::SETTING_USER_KEYBOARD_NAVIGATION, false, 'core', null, $this->getID());
            else framework\Settings::deleteSetting(framework\Settings::SETTING_USER_KEYBOARD_NAVIGATION, 'core', null, $this->getID());
        }

        public function setDesktopNotificationsNewTabEnabled($value = true)
        {
            if ($value) framework\Settings::saveSetting(framework\Settings::SETTING_USER_DESKTOP_NOTIFICATIONS_NEW_TAB, true, 'core', null, $this->getID());
            else framework\Settings::deleteSetting(framework\Settings::SETTING_USER_DESKTOP_NOTIFICATIONS_NEW_TAB, 'core', null, $this->getID());
        }

        public function setCommentSortOrder($value)
        {
            framework\Settings::saveSetting(framework\Settings::SETTING_USER_COMMENT_ORDER, $value, 'core', null, $this->getID());
        }

        public function getCommentSortOrder()
        {
            $val = framework\Settings::get(framework\Settings::SETTING_USER_COMMENT_ORDER, 'core', framework\Context::getScope(), $this->getID());
            return ($val !== null) ? $val : 'asc';
        }

        public function getActivationKey()
        {
            return $this->_getOrGenerateActivationKey();
        }

        public function regenerateActivationKey()
        {
            $value = md5(uniqid().rand(100, 100000));
            framework\Settings::saveUserSetting($this->getID(), framework\Settings::SETTING_USER_ACTIVATION_KEY, $value);

            return $value;
        }

        protected function _getOrGenerateActivationKey()
        {
            $value = framework\Settings::getUserSetting($this->getID(), framework\Settings::SETTING_USER_ACTIVATION_KEY);
            if (!$value)
            {
                $value = $this->regenerateActivationKey();
            }

            return $value;
        }

        /**
         * Set the users email address
         *
         * @param string $email A valid email address
         */
        public function setEmail($email)
        {
            $this->_email = $email;
        }

        /**
         * Set the users realname
         *
         * @param string $realname
         */
        public function setRealname($realname)
        {
            $this->_realname = $realname;
        }

        /**
         * Set the users buddyname
         *
         * @param string $buddyname
         */
        public function setBuddyname($buddyname)
        {
            $this->_buddyname = $buddyname;
        }

        /**
         * Set whether the user uses gravatar
         *
         * @param boolean $val
         */
        public function setUsesGravatar($val)
        {
            $this->_use_gravatar = (bool) $val;
        }

        /**
         * Set whether this user is enabled or not
         *
         * @param boolean $val [optional]
         */
        public function setEnabled($val = true)
        {
            $this->_enabled = $val;
        }

        /**
         * Set whether this user is validated or not
         *
         * @param boolean $val [optional]
         */
        public function setValidated($val = true)
        {
            $this->_activated = $val;
        }

        /**
         * Set the user's joined date
         *
         * @param integer $val [optional]
         */
        public function setJoined($val = null)
        {
            if ($val === null)
            {
                $val = NOW;
            }
            $this->_joined = $val;
        }

        /**
         * Find one user based on details
         *
         * @param string $details Any user detail (email, username, realname or buddyname)
         *
         * @return \thebuggenie\core\entities\User
         */
        public static function findUser($details)
        {
            $users = self::getB2DBTable()->getByDetails($details);
            if (is_array($users) && count($users) == 1)
                return array_shift($users);

            return null;
        }

        /**
         * Find users based on details
         *
         * @param string $details Any user detail (email, username, realname or buddyname)
         * @param integer $limit [optional] an optional limit on the number of results
         *
         * @return array
         */
        public static function findUsers($details, $limit = null)
        {
            return self::getB2DBTable()->getByDetails($details, $limit);
        }

        /**
         * Checks if user has permission to access the specified resource.
         *
         * A resource is specified using combination of module name, permission
         * type, and target ID.
         *
         * All permission types are tied-in to a specific module. Interpretation
         * of what a permission type entitles depends on the module itself.
         *
         * Target ID provides context for narrowing down the check to a specific
         * object. Target ID is not applicable for all module + permission type
         * combinations, and its interpretation is left up to the caller.
         *
         * Target ID set to 0 is treated as "all relevant objects", i.e. like a
         * global check.
         *
         * A common use of target ID is to narrow down permission check to a
         * specific project. For example, the module "core" has a permission
         * called "canseeproject". Setting the target ID to same value as
         * project ID would effectively narrow down the check to that specific
         * project. On the other hand, if you specified target ID as 0 in this
         * case, this would denote permission check to see if user has a global
         * access to any project.
         *
         * @param string $permission_type Type of permission to check. Available values depend on module specified.
         * @param mixed $target_id [optional] Target (object) ID, if applicable. Should be non-negative integer or string. Default is 0.
         * @param string $module_name [optional] Module to which the $permission_type is applicable. Default is 'core'.
         *
         * @return mixed If permission matching the specified criteria has been found in database (cache, to be more precise), returns permission value (true or false). If no matching permission has been found, returns null. Receiving null means the caller needs to apply a default rule (allow or deny), which depends on caller implementation.
         */
        public function hasPermission($permission_type, $target_id = 0, $module_name = 'core')
        {
            // Parts of code seem to expected to be able to pass-in target_id as
            // null. Assume this means target_id 0.
            if ($target_id === null)
            {
                $target_id = 0;
            }

            framework\Logging::log('Checking permission '.$permission_type.', target ID '.$target_id.', module '.$module_name);

            // We store cached results locally in User instance for improving performance.
            if (array_key_exists($module_name . '_' . $permission_type . '_' . $target_id, $this->_permissions_cache)) {
                $cached_value = $this->_permissions_cache[$module_name . '_' . $permission_type . '_' . $target_id];
                framework\Logging::log('Permission check has already been done and cached, using the cached value: ' . ($cached_value === null ? 'null' : $cached_value));
                return $cached_value;
            }

            // Obtain group, team, and role memberships for the user.
            $user_id = $this->getID();
            $group_id = (int) $this->getGroupID();
            $teams = $this->getTeams();
            $team_ids = array();
            foreach ($teams as $team)
            {
                $team_ids[] = $team->getID();
            }

            framework\Logging::log('Checking permission for user ID: '.$user_id.', group ID '.$group_id.',team IDs ' . implode(',', $team_ids));

            $retval = framework\Context::permissionCheck($module_name, $permission_type, $target_id, $user_id, $group_id, $team_ids);
            if ($retval === null)
            {
                framework\Logging::log('... Done checking permission '.$permission_type.', target id'.$target_id.', module '.$module_name.', no matching rules found.');
            }
            else
            {
                framework\Logging::log('... Done checking permission '.$permission_type.', target id'.$target_id.', module '.$module_name.', permission granted: '. (($retval) ? 'true' : 'false'));
            }

            // Cache the check for specified module/permission type/target ID combo in User object.
            $this->_permissions_cache[$module_name . '_' . $permission_type . '_' . $target_id] = $retval;

            return $retval;
        }

        /**
         * Whether this user can access the specified module
         *
         * @param string $module The module key
         *
         * @return boolean
         */
        public function hasModuleAccess($module)
        {
            return framework\Context::getModule($module)->hasAccess($this->getID());
        }

        /**
         * Whether this user can access the specified page
         *
         * @param string $page The page key
         *
         * @return boolean
         */
        public function hasPageAccess($page, $target_id = null)
        {
            if ($target_id === null)
            {
                $retval = $this->hasPermission("page_{$page}_access", 0, "core");
                return $retval;
            }
            else
            {
                $retval = $this->hasPermission("page_{$page}_access", $target_id, "core");
                return ($retval === null) ? $this->hasPermission("page_{$page}_access", 0, "core") : $retval;
            }
        }

        /**
         * Check whether the user can access the specified project page
         *
         * @param string $page The page key
         * @param Project $project
         *
         * @return boolean
         */
        public function hasProjectPageAccess($page, Project $project)
        {
            $retval = $this->hasPageAccess($page, $project->getID());
            $retval = ($retval === null) ? $this->hasPageAccess('project_allpages', $project->getID()) : $retval;

            if ($retval === null)
            {
                if ($project->getOwner() instanceof User && $project->getOwner()->getID() == $this->getID()) return true;
                if ($project->getLeader() instanceof User && $project->getLeader()->getID() == $this->getID()) return true;
            }

            return ($retval !== null) ? $retval : framework\Settings::isPermissive();
        }

        public function getTimezoneIdentifier()
        {
            return (is_object($this->_timezone)) ? $this->_timezone->getName() : $this->_timezone;
        }

        /**
         * Get this users timezone
         *
         * @return \DateTimeZone
         */
        public function getTimezone()
        {
            if (!is_object($this->_timezone))
            {
                if ($this->_timezone == 'sys' || $this->_timezone == null)
                {
                    $this->_timezone = framework\Settings::getServerTimezone();
                }
                else
                {
                    $this->_timezone = new \DateTimeZone($this->_timezone);
                }
            }
            return $this->_timezone;
        }

        /**
         * Set this users timezone
         *
         * @param string $timezone
         */
        public function setTimezone($timezone)
        {
            $this->_timezone = $timezone;
        }

        public function getPreferredWikiSyntax($real_value = false)
        {
            if ($real_value)
                return $this->_preferred_wiki_syntax;

            return framework\Settings::getSyntaxClass($this->_preferred_wiki_syntax);
        }

        public function setPreferredWikiSyntax($preferred_syntax)
        {
            $this->_preferred_wiki_syntax = $preferred_syntax;
        }

        public function getPreferredIssuesSyntax($real_value = false)
        {
            if ($real_value)
                return $this->_preferred_issues_syntax;

            return framework\Settings::getSyntaxClass($this->_preferred_issues_syntax);
        }

        public function setPreferredIssuesSyntax($preferred_syntax)
        {
            $this->_preferred_issues_syntax = $preferred_syntax;
        }

        public function getPreferredCommentsSyntax($real_value = false)
        {
            if ($real_value)
                return $this->_preferred_comments_syntax;

            return framework\Settings::getSyntaxClass($this->_preferred_comments_syntax);
        }

        public function setPreferredCommentsSyntax($preferred_syntax)
        {
            $this->_preferred_comments_syntax = $preferred_syntax;
        }

        protected function _dualPermissionsCheck($permission_1, $permission_1_target, $permission_2, $permission_2_target, $fallback)
        {
            $retval = $this->hasPermission($permission_1, $permission_1_target);
            $retval = ($retval !== null) ? $retval : $this->hasPermission($permission_2, $permission_2_target);

            return (bool) ($retval !== null) ? $retval : $fallback;
        }

        /**
         * Return if the user can report new issues
         *
         * @param integer|Project $project_id [optional] A project id
         * @return boolean
         */
        public function canReportIssues($project_id = null)
        {
            $retval = null;
            if ($project_id !== null)
            {
                if (is_numeric($project_id)) $project_id = tables\Projects::getTable()->selectById($project_id);
                if ($project_id->isArchived()) return false;

                $project_id = ($project_id instanceof \thebuggenie\core\entities\Project) ? $project_id->getID() : $project_id;
                $retval = $this->hasPermission('cancreateissues', $project_id);
                $retval = ($retval !== null) ? $retval : $this->hasPermission('cancreateandeditissues', $project_id);
            }

            return ($retval !== null) ? $retval : framework\Settings::isPermissive();
        }

        /**
         * Return if the user can search for issues
         *
         * @return boolean
         */
        public function canSearchForIssues()
        {
            return (bool) $this->_dualPermissionsCheck('canfindissues', 0, 'canfindissuesandsavesearches', 0, framework\Settings::isPermissive());
        }

        /**
         * Return if the user can edit the main menu
         *
         * @return boolean
         */
        public function canEditMainMenu($target_type = null,$target_context = 0)
        {
            if ($target_type === null || $target_type == 'main_menu')
            {
                $retval = $this->hasPermission('caneditmainmenu', $target_context, 'core');
            }
            else if ($target_type == 'wiki')
            {
                $retval = $this->hasPermission('editwikimenu', $target_context, 'publish');
            }
            else
            {
                $retval = false;
            }
            return ($retval !== null) ? $retval : false;
        }

        /**
         * Return if the user can see comments
         *
         * @return boolean
         */
        public function canViewComments()
        {
            return $this->_dualPermissionsCheck('canviewcomments', 0, 'canpostseeandeditallcomments', 0, framework\Settings::isPermissive());
        }

        /**
         * Return if the user can post comments
         *
         * @return boolean
         */
        public function canPostComments()
        {
            return $this->_dualPermissionsCheck('canpostcomments', 0, 'canpostseeandeditallcomments', 0, framework\Settings::isPermissive());
        }

        /**
         * Return if the user can see non public comments
         *
         * @return boolean
         */
        public function canSeeNonPublicComments()
        {
            return $this->_dualPermissionsCheck('canseenonpubliccomments', 0, 'canpostseeandeditallcomments', 0, framework\Settings::isPermissive());
        }

        /**
         * Return if the user can create public saved searches
         *
         * @return boolean
         */
        public function canCreatePublicSearches()
        {
            return $this->_dualPermissionsCheck('cancreatepublicsearches', 0, 'canfindissuesandsavesearches', 0, framework\Settings::isPermissive());
        }

        /**
         * Return whether the user can access a saved search
         *
         * @param B2DBrow $savedsearch
         *
         * @return boolean
         */
        public function canAccessSavedSearch(\thebuggenie\core\entities\SavedSearch $savedsearch)
        {
            return (bool) ($savedsearch->isPublic() || $savedsearch->getUserID() == $this->getID());
        }

        /**
         * Return if the user can access configuration pages
         *
         * @param integer $section [optional] a section, or the configuration frontpage
         *
         * @return boolean
         */
        public function canAccessConfigurationPage($section = null)
        {
            $retval = $this->hasPermission('canviewconfig', $section);
            $retval = ($retval !== null) ? $retval : $this->hasPermission('cansaveconfig', $section);

            foreach (range(0, 19) as $target_id)
            {
                if ($retval !== null) break;

                $retval = ($retval !== null) ? $retval : $this->hasPermission('canviewconfig', $target_id);
                $retval = ($retval !== null) ? $retval : $this->hasPermission('cansaveconfig', $target_id);
            }

            return (bool) ($retval !== null) ? $retval : false;
        }

        /**
         * Return if the user can save configuration in a section
         *
         * @return boolean
         */
        public function canSaveConfiguration($section, $module = 'core')
        {
            $retval = $this->hasPermission('cansaveconfig', $section, $module);
            $retval = ($retval !== null) ? $retval : $this->hasPermission('cansaveconfig', 0, $module);

            return (bool) ($retval !== null) ? $retval : false;
        }

        /**
         * Return if the user can manage a project
         *
         * @param \thebuggenie\core\entities\Project $project
         *
         * @return boolean
         */
        public function canManageProject(\thebuggenie\core\entities\Project $project)
        {
            if ($project->getOwner() instanceof User && $project->getOwner()->getID() == $this->getID()) return true;

            return (bool) $this->hasPermission('canmanageproject', $project->getID()) || $this->canSaveConfiguration(framework\Settings::CONFIGURATION_SECTION_PROJECTS);
        }

        /**
         * Return if the user can manage releases for a project
         *
         * @param \thebuggenie\core\entities\Project $project
         *
         * @return boolean
         */
        public function canManageProjectReleases(\thebuggenie\core\entities\Project $project)
        {
            if ($project->isArchived()) return false;
            if ($this->canSaveConfiguration(framework\Settings::CONFIGURATION_SECTION_PROJECTS)) return true;
            if ($project->getOwner() instanceof User && $project->getOwner()->getID() == $this->getID()) return true;

            return $this->_dualPermissionsCheck('canmanageprojectreleases', $project->getID(), 'canmanageproject', $project->getID(), false);
        }

        public function canEditMilestones(\thebuggenie\core\entities\Project $project)
        {
            if ($project->isArchived()) return false;
            if ($project->getOwner() instanceof User && $project->getOwner()->getID() == $this->getID()) return true;

            return $this->_dualPermissionsCheck('canaddscrumsprints', $project->getID(), 'canmanageproject', $project->getID(), false);
        }

        /**
         * Return if the user can edit project details and settings
         *
         * @param \thebuggenie\core\entities\Project $project
         *
         * @return boolean
         */
        public function canEditProjectDetails(\thebuggenie\core\entities\Project $project)
        {
            if ($project->isArchived()) return false;
            if ($this->canSaveConfiguration(framework\Settings::CONFIGURATION_SECTION_PROJECTS)) return true;
            if ($project->getOwner() instanceof User && $project->getOwner()->getID() == $this->getID()) return true;

            return $this->_dualPermissionsCheck('caneditprojectdetails', $project->getID(), 'canmanageproject', $project->getID(), false);
        }

        /**
         * Return if the user can assign scrum user stories
         *
         * @param \thebuggenie\core\entities\Project $project
         *
         * @return boolean
         */
        public function canAssignScrumUserStories(\thebuggenie\core\entities\Project $project)
        {
            if ($project->isArchived()) return false;
            if ($this->canSaveConfiguration(framework\Settings::CONFIGURATION_SECTION_PROJECTS)) return true;
            if ($project->getOwner() instanceof User && $project->getOwner()->getID() == $this->getID()) return true;

            $retval = $this->hasPermission('caneditissueassigned_to', $project->getID());
            $retval = ($retval !== null) ? $retval : $this->hasPermission('caneditissue', $project->getID());
            $retval = ($retval !== null) ? $retval : $this->hasPermission('caneditissueassigned_to', 0);
            $retval = ($retval !== null) ? $retval : $this->hasPermission('caneditissue', 0);

            return (bool) ($retval !== null) ? $retval : false;
        }

        /**
         * Return if the user can change its own password
         *
         * @return boolean
         */
        public function canChangePassword()
        {
            return $this->_dualPermissionsCheck('canchangepassword', 0, 'page_account_access', 0, true);
        }

        /**
         * Return a list of the users latest log items
         *
         * @param integer $number Limit to a number of changes
         *
         * @return LogItem[]
         */
        public function getLatestActions($number = 10)
        {
            $items = tables\LogItems::getTable()->getByUserID($this->getID(), $number);
            return $items;
        }

        /**
         * Clears the associated projects cache (useful only when you know that you've changed assignees this same request
         *
         * @return null
         */
        public function clearAssociatedProjectsCache()
        {
            $this->_associated_projects = null;
        }

        /**
         * Get all the projects a user is associated with
         *
         * @return Project[]
         */
        public function getAssociatedProjects()
        {
            if ($this->_associated_projects === null)
            {
                $this->_associated_projects = array();

                $projects = tables\ProjectAssignedUsers::getTable()->getProjectsByUserID($this->getID());
                $lo_projects = tables\Projects::getTable()->getByUserID($this->getID());

                $project_ids = array_merge(array_keys($projects), array_keys($lo_projects));

                foreach ($this->getTeams() as $team)
                {
                    $project_ids = array_merge($project_ids, array_keys($team->getAssociatedProjects()));
                }

                $project_ids = array_unique($project_ids);

                foreach ($project_ids as $project_id)
                {
                    try
                    {
                        $this->_associated_projects[$project_id] = tables\Projects::getTable()->selectById($project_id);
                    }
                    catch (\Exception $e) { }
                }
            }

            return $this->_associated_projects;
        }

        /**
         * Return an array of issues that has changes pending
         *
         * @return array
         */
        public function getIssuesPendingChanges()
        {
            return \thebuggenie\core\entities\common\Changeable::getChangedItems('\thebuggenie\core\entities\Issue');
        }

        public function setLanguage($language)
        {
            $this->_language = $language;
        }

        public function getLanguage()
        {
            return ($this->_language != '') ? $this->_language : framework\Settings::getLanguage();
        }

        /**
         * Return an array of issues posted by this user
         *
         * @param int $limit number of issues to be retrieved
         *
         * @return array
         */
        public function getIssues($limit = null)
        {
            return tables\Issues::getTable()->getIssuesPostedByUser($this->getID(), $limit);
        }

        public function isOpenIdLocked()
        {
            return (bool) $this->_openid_locked;
        }

        public function setOpenIdLocked($value = true)
        {
            $this->_openid_locked = (bool) $value;
        }

        /**
         * Return the users associated scopes
         *
         * @return Scope[]
         */
        public function getScopes()
        {
            $this->_b2dbLazyLoad('_scopes');
            return $this->_scopes;
        }

        protected function _populateScopeDetails()
        {
            if ($this->_unconfirmed_scopes === null || $this->_confirmed_scopes === null)
            {
                $this->_unconfirmed_scopes = array();
                $this->_confirmed_scopes = array();
                if ($this->_scopes === null) $this->_scopes = array();

                if ($this->getID() == framework\Settings::getDefaultUserID() && framework\Settings::isDefaultUserGuest()) {
                    $this->_confirmed_scopes[framework\Context::getScope()->getID()] = framework\Context::getScope();
                } else {
                    $scopes = tables\UserScopes::getTable()->getScopeDetailsByUser($this->getID());
                    foreach ($scopes as $scope_id => $details)
                    {
                        if (!$details['confirmed'])
                        {
                            $this->_unconfirmed_scopes[$scope_id] = $details['scope'];
                        }
                        else
                        {
                            $this->_confirmed_scopes[$scope_id] = $details['scope'];
                        }
                        if (!array_key_exists($scope_id, $this->_scopes)) $this->_scopes[$scope_id] = $details['scope'];
                    }
                }
            }
        }

        /**
         * Get users unconfirmed scope memberships
         *
         * @return Scope[]
         */
        public function getUnconfirmedScopes()
        {
            $this->_populateScopeDetails();
            return $this->_unconfirmed_scopes;
        }

        /**
         * Get users confirmed scope memberships
         *
         * @return Scope[]
         */
        public function getConfirmedScopes()
        {
            $this->_populateScopeDetails();
            return $this->_confirmed_scopes;
        }

        public function clearScopes()
        {
            tables\UserScopes::getTable()->clearUserScopes($this->getID());
            $this->_scopes = null;
            $this->_unconfirmed_scopes = null;
            $this->_confirmed_scopes = null;
        }

        public function addScope(Scope $scope, $notify = true)
        {
            if (!$this->isMemberOfScope($scope))
            {
                tables\UserScopes::getTable()->addUserToScope($this->getID(), $scope->getID());
                if ($notify)
                {
                    \thebuggenie\core\framework\Event::createNew('core', 'self::addScope', $this, array('scope' => $scope))->trigger();
                }
                $this->_scopes = null;
                $this->_unconfirmed_scopes = null;
                $this->_confirmed_scopes = null;
            }
        }

        public function removeScope($scope)
        {
            $scope_id = ($scope instanceof Scope) ? $scope->getID() : $scope;
            tables\UserScopes::getTable()->removeUserFromScope($this->getID(), $scope_id);
            $this->_scopes = null;
            $this->_unconfirmed_scopes = null;
            $this->_confirmed_scopes = null;
        }

        public function confirmScope($scope)
        {
            $scope_id = ($scope instanceof Scope) ? $scope->getID() : $scope;
            tables\UserScopes::getTable()->confirmUserInScope($this->getID(), $scope_id);
            $this->_scopes = null;
            $this->_unconfirmed_scopes = null;
            $this->_confirmed_scopes = null;
        }

        public function isConfirmedMemberOfScope(Scope $scope)
        {
            return array_key_exists($scope->getID(), $this->getConfirmedScopes());
        }

        public function isMemberOfScope(Scope $scope)
        {
            return array_key_exists($scope->getID(), $this->getScopes());
        }

        protected function _populateNotifications($first_notification_id = null, $last_notification_id = null)
        {
            $filter_first_notification = ! is_null($first_notification_id) && is_numeric($first_notification_id);
            if ($filter_first_notification)
            {
                $this->_notifications = null;
                $this->_filter_first_notification = true;
            }
            else if (! $filter_first_notification && $this->_filter_first_notification)
            {
                $this->_notifications = null;
                $this->_filter_first_notification = false;
            }
            if (!is_array($this->_notifications))
            {
                $this->_notifications = Notifications::getTable()->getByUserIDAndGroupableMinutes($this->getID(), $this->getNotificationSetting(framework\Settings::SETTINGS_USER_NOTIFY_GROUPED_NOTIFICATIONS, false, 'core')->getValue());
                $notifications = array('unread' => array(), 'read' => array(), 'all' => array());
                $db_notifcations = $this->_notifications;
                foreach ($db_notifcations as $notification)
                {
                    if ($filter_first_notification && $notification->getID() <= $first_notification_id) break;
                    if (! $filter_first_notification && ! is_null($last_notification_id) && $notification->getID() >= $last_notification_id) continue;
                    if ($notification->getTriggeredByUser()->getID() == $this->getID()) continue;

                    array_push($notifications['all'], $notification);
                    if ($notification->isRead())
                    {
                        array_push($notifications['read'], $notification);
                    }
                    else
                    {
                        array_push($notifications['unread'], $notification);
                    }
                }
                $notifications = array_reverse($notifications);
                $this->_notifications = $notifications;
                $this->_unread_notifications_count = count($notifications['unread']);
                $this->_read_notifications_count = count($notifications['read']);
            }
        }

        /**
         * Returns an array of notifications for this user
         *
         * @return \thebuggenie\core\entities\Notification[]
         */
        public function getNotifications($first_notification_id = null, $last_notification_id = null)
        {
            $this->_populateNotifications($first_notification_id, $last_notification_id);
            return $this->_notifications['all'];
        }

        /**
         * Returns an array of unread notifications for this user
         *
         * @return \thebuggenie\core\entities\Notification[]
         */
        public function getUnreadNotifications()
        {
            $this->_populateNotifications();
            return $this->_notifications['unread'];
        }

        public function getReadNotifications()
        {
            $this->_populateNotifications();
            return $this->_notifications['read'];
        }

        protected function _populateNotificationsCounts()
        {
            if ($this->_unread_notifications_count === null)
            {
                list ($this->_unread_notifications_count, $this->_read_notifications_count) = tables\Notifications::getTable()->getCountsByUserIDAndGroupableMinutes($this->getID(), $this->getNotificationSetting(framework\Settings::SETTINGS_USER_NOTIFY_GROUPED_NOTIFICATIONS, false, 'core')->getValue());
            }
        }

        public function getNumberOfUnreadNotifications()
        {
            $this->_populateNotificationsCounts();
            return $this->_unread_notifications_count;
        }

        public function getNumberOfReadNotifications()
        {
            $this->_populateNotificationsCounts();
            return $this->_read_notifications_count;
        }

        public function getNumberOfNotifications()
        {
            $this->_populateNotificationsCounts();
            return $this->_read_notifications_count + $this->_unread_notifications_count;
        }

        public function markAllNotificationsRead()
        {
            tables\Notifications::getTable()->markUserNotificationsReadByTypesAndIdAndGroupableMinutes(array(), null, $this->getID(), $this->getNotificationSetting(framework\Settings::SETTINGS_USER_NOTIFY_GROUPED_NOTIFICATIONS, false, 'core')->getValue());
        }

        public function markNotificationsRead($type, $id)
        {
            $grouped_notifications_minutes = $this->getNotificationSetting(framework\Settings::SETTINGS_USER_NOTIFY_GROUPED_NOTIFICATIONS, false, 'core')->getValue();
            if ($type == 'issue')
            {
                tables\Notifications::getTable()->markUserNotificationsReadByTypesAndIdAndGroupableMinutes(array(Notification::TYPE_ISSUE_CREATED, Notification::TYPE_ISSUE_UPDATED, Notification::TYPE_ISSUE_MENTIONED), $id, $this->getID(), $grouped_notifications_minutes);
                $comment_ids = tables\Comments::getTable()->getCommentIDs($id, Comment::TYPE_ISSUE);
                if (count($comment_ids))
                {
                    tables\Notifications::getTable()->markUserNotificationsReadByTypesAndIdAndGroupableMinutes(array(Notification::TYPE_ISSUE_COMMENTED, Notification::TYPE_COMMENT_MENTIONED), $comment_ids, $this->getID(), $grouped_notifications_minutes);
                }
            }
            if ($type == 'article')
            {
                tables\Notifications::getTable()->markUserNotificationsReadByTypesAndIdAndGroupableMinutes(array(Notification::TYPE_ARTICLE_CREATED, Notification::TYPE_ARTICLE_UPDATED, Notification::TYPE_ARTICLE_MENTIONED), $id, $this->getID(), $grouped_notifications_minutes);
                $comment_ids = tables\Comments::getTable()->getCommentIDs($id, Comment::TYPE_ARTICLE);
                if (count($comment_ids))
                {
                    tables\Notifications::getTable()->markUserNotificationsReadByTypesAndIdAndGroupableMinutes(array(Notification::TYPE_ARTICLE_COMMENTED, Notification::TYPE_COMMENT_MENTIONED), $comment_ids, $this->getID(), $grouped_notifications_minutes);
                }
            }
            $this->_notifications = null;
            $this->_unread_notifications_count = null;
            $this->_read_notifications_count = null;
        }

        public function regenerateRssKey()
        {
            $key = Uuid::uuid4()->toString();
            framework\Settings::saveUserSetting($this->getID(), framework\Settings::USER_RSS_KEY, $key);

            return $key;
        }

        protected function _getOrGenerateRssKey()
        {
            static $key;

            $key = ($key === null) ? framework\Settings::getUserSetting($this->getID(), framework\Settings::USER_RSS_KEY) : $key;

            if ($key === null)
            {
                $key = $this->regenerateRssKey();
            }

            return $key;
        }

        public function getRssKey()
        {
            return $this->_getOrGenerateRssKey();
        }

        /**
         * Returns an array of user dashboards
         *
         * @return \thebuggenie\core\entities\Dashboard[]
         */
        public function getDashboards()
        {
            $this->_b2dbLazyLoad('_dashboards');
            return $this->_dashboards;
        }

        /**
         * Get the users default dashboard, create one if none exists
         *
         * @return \thebuggenie\core\entities\Dashboard
         */
        public function getDefaultDashboard()
        {
            foreach ($this->getDashboards() as $dashboard)
            {
                if ($dashboard->getIsDefault()) return $dashboard;
            }

            $dashboard = new \thebuggenie\core\entities\Dashboard();
            $dashboard->setUser($this);
            $dashboard->setIsDefault(true);
            $dashboard->save();
            $this->_dashboards[] = $dashboard;

            return $dashboard;
        }

        /**
         * Returns an array of user sessions
         *
         * @return \thebuggenie\core\entities\UserSession[]
         */
        public function getUserSessions()
        {
            $this->_b2dbLazyLoad('_user_sessions');
            return $this->_user_sessions;
        }

        /**
         * @return UserSession
         *
         * @throws \Exception
         */
        public function createUserSession()
        {
            $userSession = new UserSession();
            $userSession->setUser($this);
            $userSession->save();

            $this->_user_sessions = null;

            return $userSession;
        }

        public function verifyUserSession($token, $is_elevated = false)
        {
            $userSessions = $this->getUserSessions();
            framework\Logging::log('Cycling user sessions for given user. Count: '.count($userSessions), 'auth', framework\Logging::LEVEL_INFO);

            foreach ($userSessions as $userSession)
            {
                if ($userSession->getExpiresAt() < time())
                {
                    $userSession->delete();
                    continue;
                }

                if ($userSession->getToken() == $token && $is_elevated == $userSession->isElevated())
                {
                    framework\Logging::log('Verified user session', 'auth', framework\Logging::LEVEL_INFO);
                    return true;
                }
            }

            framework\Logging::log('Could not verify user session', 'auth', framework\Logging::LEVEL_INFO);
            return false;
        }

        /**
         * Returns an array of application passwords
         *
         * @return \thebuggenie\core\entities\ApplicationPassword[]
         */
        public function getApplicationPasswords()
        {
            $this->_b2dbLazyLoad('_application_passwords');
            return $this->_application_passwords;
        }

        /**
         * Authenticates a request via application password.
         * The given token is created by requesting authentication via an API endpoint,
         * which also marks the password as "used" and thus usable here.
         *
         * @param string $token
         * @return boolean
         */
        public function authenticateApplicationPassword($token)
        {
            $applicationPasswords = $this->getApplicationPasswords();
            framework\Logging::log('Cycling application passwords for given user. Count: '.count($applicationPasswords), 'auth', framework\Logging::LEVEL_INFO);

            foreach ($applicationPasswords as $password)
            {
                if (password_verify($token, $password->getHashPassword()))
                {
                    framework\Logging::log('Token hash matches.', 'auth', framework\Logging::LEVEL_INFO);
                    $password->useOnce();
                    $password->save();
                    return true;
                }
            }
            framework\Logging::log('No token hash matched.', 'auth', framework\Logging::LEVEL_INFO);
            return false;
        }

        /**
         * Get a notification setting for a specific module
         *
         * @param string $setting The setting to retrieve
         * @param string $module The module if not 'core'
         *
         * @return NotificationSetting
         */
        public function getNotificationSetting($setting, $default_value = null, $module = 'core')
        {
            if (!array_key_exists($module, $this->_notification_settings))
            {
                $this->_notification_settings[$module] = [];
            }

            if (!array_key_exists($setting, $this->_notification_settings[$module]))
            {
                $notificationsetting = NotificationSetting::getB2DBTable()->getByModuleAndNameAndUserId($module, $setting, $this->getID());
                if (!$notificationsetting instanceof NotificationSetting)
                {
                    $notificationsetting = new \thebuggenie\core\entities\NotificationSetting();
                    $notificationsetting->setUser($this);
                    $notificationsetting->setName($setting);
                    $notificationsetting->setModuleName($module);
                    $notificationsetting->setValue($default_value);
                }

                $this->_notification_settings[$module][$setting] = $notificationsetting;
            }

            return $this->_notification_settings[$module][$setting];
        }

        /**
         * Set notification setting for a specific setting / module
         *
         * @param string $setting The setting name
         * @param mixed $value The value to set
         * @param string $module [optional] The module if not 'core'
         *
         * @return NotificationSetting
         */
        public function setNotificationSetting($setting, $value, $module = 'core')
        {
            $setting_object = $this->getNotificationSetting($setting, null, $module);
            $setting_object->setValue($value);
            $setting_object->save();
            return $setting_object;
        }

        public function toJSON($detailed = true)
        {
            $returnJSON = array(
                'id' => $this->getID(),
                'name' => $this->getName(),
                'username' => $this->getUsername(),
                'type' => 'user' // This is for distinguishing of assignees & similar "ambiguous" values in JSON.
            );

            if($detailed) {
                $returnJSON['display_name'] = $this->getDisplayName();
                $returnJSON['realname'] = $this->getRealname();
                $returnJSON['buddyname'] = $this->getBuddyname();

                // Only return email if it is public or we are looking at the currently logged-in user
                if($this->isEmailPublic() || framework\Context::getUser()->getID() == $this->getID()) {
                    $returnJSON['email'] = $this->getEmail();
                }
                $returnJSON['avatar'] = $this->getAvatar();
                $returnJSON['avatar_url'] = $this->getAvatarURL(false);
                $returnJSON['avatar_url_small'] = $this->getAvatarURL(true);
                $returnJSON['url_homepage'] = $this->getHomepage();

                $returnJSON['date_joined'] = $this->getJoinedDate();
                $returnJSON['last_seen'] = $this->getLastSeen();

                $returnJSON['timezone'] = $this->getTimezoneIdentifier();
                $returnJSON['language'] = $this->getLanguage();

                $returnJSON['state'] = $this->getState()->toJSON();

                /*
                 * TODO...
                 */

//                 $this->getClients();
//                 $this->getDashboards();
//                 $this->getDefaultDashboard();
//                 $this->getFriends();
//                 $this->getGroup();
//                 $this->getTeams();

                /*
                 * TODO: Return these?
                 */
//                 $this->isActivated();
//                 $this->isDeleted();
//                 $this->isEnabled();
            }

            return $returnJSON;
        }

        /**
         * @param Notification $notification
         */
        public function markNotificationGroupedNotificationsRead(\thebuggenie\core\entities\Notification $notification)
        {
            if ($notification->getNotificationType() != \thebuggenie\core\entities\Notification::TYPE_ISSUE_UPDATED) return;

            tables\Notifications::getTable()->markUserNotificationsReadByTypesAndIdAndGroupableMinutes(array(\thebuggenie\core\entities\Notification::TYPE_ISSUE_UPDATED), $notification->getTargetID(), $this->getID(), $this->getNotificationSetting(framework\Settings::SETTINGS_USER_NOTIFY_GROUPED_NOTIFICATIONS, false, 'core')->getValue(), (int) $notification->isRead(), false);
        }

    }
