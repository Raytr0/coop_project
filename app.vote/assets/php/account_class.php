<?php
class Account
{
    /**
     * @var int $user_id
     */
    protected $user_id;

    /**
     * School that user goes to.
     * 
     * @var int $school_id
     */
    protected $school_id;

    /**
     * Type of user. Ex: Admin = 0, user = 1.
     * 
     * @var int $type_id
     */
    protected $type_id;

    /**
     * @var string $username
     */
    protected $username;

    /**
     * @var string $email
     */
    protected $email;

    /**
     * @var string $first_name
     */
    protected $first_name;

    /**
     * @var string $last_name
     */
    protected $last_name;

    /**
     * Wether the users email is verified.
     * 
     * @var bool $verified
     */
    protected $verified;

    /**
     * Wether the user is authenticated.
     * 
     * @var bool $authenticated
     */
    protected $authenticated;

    /**
     * Holds messages of why something is not valid.
     * 
     * @var array $errors
     */
    protected $errors;  

    /* Public class methods */ 

    /**
     * Constructor. Initializes the token's attributes to null.
     */
    public function __construct()
    {
        $this -> user_id = null;
        $this -> school_id = null;
        $this -> type_id = null;
        $this -> username = null;
        $this -> email = null;
        $this -> first_name = null;
        $this -> last_name = null;
        $this -> verified = null;
        $this -> authenticated = null;
        $this -> errors = [];  
    }

    /** 
     * Logs user in. Returns wether he was successfully logged in our
     * not.
     * 
     * @param string $username_email
     * @param string $password
     * @param bool $remember_me
     * @return bool 
     */ 
    public function login(string $username_email, string $password, bool $remember_me): bool
    {
        // Checks if user is already logged in 
        if ($this -> authenticated) 
        {
            return true;
        }

        global $db;

        // Gets users info
        //note that you don't have to bother specifying username or email, as the user may enter either  
        $get_user = $db -> prepare("
            SELECT * FROM users WHERE username=:username or email=:email LIMIT 1;
        ");

        $get_user -> execute([
            ":username" => $username_email,
            ":email" => $username_email,
        ]);

        $user_info = $get_user -> fetch(PDO::FETCH_ASSOC);
        
        // Checks if correct password was given
        //no need to get the salt
        if (password_verify($password, $user_info["password"]))
        {
            // Initializes Account's attributes
            $this -> initAccount(
                $user_info["user_id"],
                $user_info["school_id"],
                $user_info["type_id"],
                $user_info["username"],
                $user_info["email"],
                $user_info["first_name"],
                $user_info["last_name"],
                $user_info["verified"]
            );

            // Authenticates user
            $this -> authenticated = true;

            // Starts a session for the user
            $_SESSION["user_id"] = $this -> user_id;

            // Checks if user clicked remember me
            if ($remember_me == true)
            {
                // Creates timestamp for when the token will expire
                $expires = time() + (60 * 60 * 24 * 60);

                // Creates token
                $token = new Token();

                $token -> createToken($this -> user_id, $this -> username, 0, $expires);

                // Sets authentication cookie
                setcookie("auth_token", $token -> getTokenInfo("token"), $expires, "/", "localhost", false, true);
            }

            // User successfully logged in
            return true;
        }
        
        // User did not successfully log in
        return false;
    }

    
    /** 
     * Logs user out.
     */
    public function logout()
    {
        // Destroys session
        session_destroy();
        session_start();

        // Checks if an authentication cookie is set
        if (isset($_COOKIE["auth_token"]))
        {
            $token = new Token();

            // Checks if the token is valid
            if ($token -> validateToken($_COOKIE["auth_token"], 0))
            {
                // Removes the token from the database
                $token -> removeToken();

                // Deletes the authentication cookie
                setcookie("auth_token", "", 1);
            }
        }

        // Unauthenticated user
        $this -> authenticated = false;
    }

    /** 
     * Sends a reset password email to a user. Returns wether or not
     * it was successful.
     * 
     * @param string $email
     * @return bool
     */
    public function sendPasswordUpdateEmail(string $email): bool
    {
        global $db;

        // Gets user
        $get_user = $db -> prepare("
            SELECT * FROM users WHERE email=:email LIMIT 1;
        ");

        $get_user -> execute([
            ":email" => $email,
        ]);

        $user_info = $get_user -> fetch(PDO::FETCH_ASSOC);

        // Checks if there is a user with the givin email
        if (!empty($user_info))
        {
            // Initializes users account
            $this -> initAccount(
                $user_info["user_id"],
                $user_info["school_id"],
                $user_info["type_id"],
                $user_info["username"],
                $user_info["email"],
                $user_info["first_name"],
                $user_info["last_name"],
                $user_info["verified"]
            );

            $token = new Token();

            // Creates token
            $auth_token = $token -> createToken($this -> user_id, $this -> username, 1, time() + 60 * 15);
            
            $message = "
                Hi there " . $this -> username . ",<br>

                We've received a request to reset your password. If you didn't make the request, just
                ignore this email. Otherwise, you can reset your password using this link.
                <br>
                <a href=\"http://localhost:8000/pages/reset.php?token=${auth_token}\">
                    Click here to reset your password
                </a>
                <br><br>

                Thanks,
                The School Vote Team
            ";

            return $this -> sendEmail("Password Reset", $message);
        }

        return false;
    }

    /** 
     * Updates password for an Account. Returns whether or not 
     * it was successful.
     * 
     * @param string $auth_token
     * @param string $password
     * @param string $confirm_password
     * @return bool
     */
    public function updatePassword(string $auth_token, string $password, string $confirm_password): bool
    {
        global $db;

        // Checks if passwords given are valid
        if ($this -> isPasswdValid($password, $confirm_password))
        {
            $token = new Token();

            // Validates token givin
            if ($token -> validateToken($auth_token, 1))
            {
                // Defines options for hashing algo
                $options = [
                    'memory_cost' => 512,
                    'time_cost'   => 4,
                    'threads'     => 3,
                ];

                // Creates unique 64 character salt  
                //$salt = bin2hex(random_bytes(32));

                // Hashes password using argon2id
                //you need to compile php with argon2 support, and since we didn't here and I am not paid enough: default blowfish time
                $password = password_hash(/*$salt . */$password, PASSWORD_BCRYPT, $options);

                // Updates password and salt in the database
                $update_password = $db -> prepare("
                    UPDATE users SET password=:password WHERE user_id=:user_id;
                ");

                $update_password -> execute([
                    ":password" => $password,
                    ":user_id" => $token -> getTokenInfo("user_id"),
                ]);
                
                // Password was successfully changed
                return true;
            }

            else
            {
                $this -> errors["token"] = "Invalid reset token";
            }
        }

        // Password was not changed
        return false;
    }

    /** 
     * Sends an account confirmation email to a user. Returns wether or not
     * it was successful.
     * 
     * @return bool
     */
    public function sendAccountConfirmationEmail(): bool
    {
        // Checks if there the user is authenticated 
        if ($this -> authenticated)
        {
            $token = new Token();

            // Creates token
            $auth_token = $token -> createToken(
                $this -> user_id, $this -> username, 2, time() + 60 * 15
            );
            
            $message = "
                Hi there " . $this -> username . ",<br>
                Thanks for registering for school vote. If you didn't register for an account, 
                just ignore this email. Otherwise, you can confirm your email using this link.
                <br>
                <a href=\"http://localhost:8000/pages/confirm-email.php?token=${auth_token}\">
                    Click here to confirm your email
                </a>
                <br><br>

                Thanks,
                The School Vote Team
            ";

            // Set email to verified so that it can send the email 
            $this -> verified = true;

            // Sends email
            $result = $this -> sendEmail("Confirm Email", $message);

            // Sets email back to not verified 
            $this -> verified = false;

            return $result;
        }

        return false;
    }

    /** 
     * Confirms users account. Returns wether or not 
     * it was successful.
     * 
     * @param string $auth_token
     * @return bool
     */
    public function confirmAccount(string $auth_token): bool
    {
        // If account is already verified returns true
        if ($this -> verified)
        {
            return true;
        }

        global $db;

        $token = new Token();

        // Validates token givin
        if ($token -> validateToken($auth_token, 2))
        {
            // Updates database information
            $update_verified = $db -> prepare("
                UPDATE users SET verified=:verified WHERE user_id=:user_id;
            ");

            $update_verified -> execute([
                ":verified" => 1,
                ":user_id" => $token -> getTokenInfo("user_id"),
            ]);

            $this -> verified = true;
        }

        // Returns verified value
        return $this -> verified;
    }

    /** 
     * Gets the currently signed in user. Returns user_id of signed 
     * in user or returns -1 if the user is not.
     * 
     * @return int
     */
    public function currentAccount(): int
    {
        // Checks if user is already signed in 
        if ($this -> authenticated)
        {
            return $this -> user_id;
        }
        
        global $db;

        // Checks if season is set
        if (isset($_SESSION["user_id"]))
        {            
            // Authenticates user
            $this -> authenticated = true;

            // Sets user id
            $user_id = $_SESSION["user_id"];
        }

        // Checks if authentication cookie is set
        else if (isset($_COOKIE["auth_token"]))
        {
            $token = new Token();

            // Validates token
            if ($token -> validateToken($_COOKIE["auth_token"], 0))
            {
                // Authenticates user
                $this -> authenticated = true;

                // Sets user id 
                $user_id = $token -> getTokenInfo("user_id");
            }
        }

        // Checks if user was authenticated
        if ($this -> authenticated)
        {
            // Gets users information
            $get_user = $db -> prepare("
                SELECT * FROM users WHERE user_id=:user_id LIMIT 1;
            ");

            $get_user -> execute([
                ":user_id" => $user_id,
            ]);

            $user_info = $get_user -> fetch(PDO::FETCH_ASSOC);

            // Initializes users account
            $this -> initAccount(
                $user_info["user_id"],
                $user_info["school_id"],
                $user_info["type_id"],
                $user_info["username"],
                $user_info["email"],
                $user_info["first_name"],
                $user_info["last_name"],
                $user_info["verified"]
            );

            // Starts a session for the user
            $_SESSION["user_id"] = $this -> user_id;

            // Returns user_id
            return $user_id;
        }
        
        // No user was signed in so it returned -1
        return -1;
    }

    /** 
     * Sends email to user. Returns wether or not 
     * it was successful.
     * 
     * @param string $subject
     * @param string $message
     */
    public function sendEmail(string $subject, string $message): bool
    {
        // Checks if email address is not verified
        if (!$this -> verified)
        {
            return false;
        }

        // New PHPMailer instance
        $mail = new PHPMailer();

        // Sets SMTP settings
        $mail -> IsSMTP();
        $mail -> SMTPAuth = true;
        $mail -> Host = "ssl://smtp.gmail.com:465";
        
        // Sets password and username of email account
        $mail -> Username = "school.vote.digitera@gmail.com";  
        $mail -> Password = "digitera2019";            
        
        // Email content
        $mail -> Subject = $subject;
        $mail -> AltBody = "To view the message, please use an HTML compatible email viewer!";
        $mail -> MsgHTML($message);
        
        // Email settings 
        $mail -> SetFrom($mail -> Username, 'School Vote');
        $mail -> AddReplyTo("no-reply@gmail.com");
        $mail -> AddAddress($this -> email, $this -> username);
        
        // Debug Settings
        //$mail -> SMTPDebug = 4;

        // Sends email
        return $mail -> Send();
    }

    /** 
     * Returns a specified attribute. If no attribute is 
     * specified it returns all attributes.
     * 
     * @param string|null $attribute
     * @return mixed 
     */
    public function getAccountInfo(string $attribute = null)
    {
        // Checks if an attribute was specified
        if ($attribute == null)
        {
            // Returns all attributes
            return [
                "user_id" => $this -> user_id,
                "school_id" => $this -> school_id,
                "type_id" => $this -> type_id,
                "username" => $this -> username,
                "email" => $this -> email,
                "first_name" => $this -> first_name,
                "last_name" => $this -> last_name,
                "verified" => $this -> verified,
                "authenticated" => $this -> authenticated,
                "errors" => $this -> errors,
            ];
        }

        // Checks if the attribute is in the class
        else if (isset($this -> $attribute))
        {
            // Returns attribute specified
            return $this -> $attribute;
        }

        // Invalid attribute
        return null;
    }

    /** 
     * Returns a path to user's profile picture.
     * 
     * @return string|null
     */
    public function getProfilePicture(): string
    {
        global $db;

        // Gets profile picture
        $get_profile_picture = $db -> prepare("
            SELECT profile_picture FROM users WHERE user_id=:user_id
        "); 

        $get_profile_picture -> execute([
            ":user_id" => $this -> user_id,
        ]);

        // Invalid attribute
        return $get_profile_picture -> fetch(PDO::FETCH_ASSOC)["profile_picture"];
    }

    /** 
     * Creates account for voter. Returns wether or not 
     * it was successful.
     * 
     * @param int $school_id
     * @param string $username
     * @param string $password
     * @param string $confirm_password
     * @param string $email
     * @param string $first_name
     * @param string $last_name
     * @param int $type_id
     * @return bool
     */
    public function createAccount(int $school_id, string $username, string $password, string $confirm_password, string $email, string $first_name, string $last_name, int $type_id): bool 
    {
        $success = true;

        // Validates username
        if (!$this -> isUsernameValid($username))
        {
            $success = false;
        }

        // Validates password
        if (!$this -> isPasswdValid($password, $confirm_password))
        {
            $success = false;
        }

        // Validates email
        if (!$this -> isEmailValid($email))
        {
            $success = false;
        }

        // Validates school_id
        if (!$this -> isSchoolValid($school_id))
        {
            $success = false;
        }

        if ($success)
        {
            // Logs out any users
            $this -> logout();

            global $db;

            // Defines options for hashing algo
            $options = [
                'memory_cost' => 512,
                'time_cost'   => 4,
                'threads'     => 3,
            ];

            /***** Salts may be deprecated, no need to use them *****/

            // Creates unique 64 character salt  
            //$salt = bin2hex(random_bytes(32));

            // Hashes password using argon2id
            //refer above as to why we use bcrypt
            $password = password_hash(/*$salt . */$_POST["password"], PASSWORD_BCRYPT, $options);

            // Inserts user information into database
            $insert_user = $db -> prepare("
                INSERT INTO users 
                (school_id, type_id, username, password, email, 
                first_name, last_name, verified) 
                VALUES 
                (:school_id, :type_id, :username, :password, :email, 
                :first_name, :last_name, :verified);
            ");

            $insert_user -> execute([
                ":school_id" => $school_id, 
                ":type_id" => $type_id,
                ":username" => $username,
                ":password" => $password,
                ":email" => $email,
                ":first_name" => ucfirst($first_name),
                ":last_name" => ucfirst($last_name),
                ":verified" => 0,
            ]);

            // Gets user information of newly created user
            $get_user = $db -> prepare("
                SELECT * FROM users WHERE username=:username LIMIT 1;
            ");

            $get_user -> execute([
                ":username" => $_POST["username"],
            ]);

            $user_info = $get_user -> fetch(PDO::FETCH_ASSOC);

            // Initializes users account
            $this -> initAccount(
                $user_info["user_id"],
                $user_info["school_id"],
                $user_info["type_id"],
                $user_info["username"],
                $user_info["email"],
                $user_info["first_name"],
                $user_info["last_name"],
                $user_info["verified"]
            );

            // Authenticates user
            $this -> authenticated = true;

            // Sends email confirmation email 
            $this -> sendAccountConfirmationEmail();

            // Starts a session for the user
            $_SESSION["user_id"] = $this -> user_id;

            return true;
        }

        return false;
    }

    /* Protected class methods */ 

    /** 
     * Returns wether a givin username is valid or not.
     * 
     * @param string $username
     * @return bool
     */
    protected function isUsernameValid(string $username): bool
    {
        global $db;

        $valid = true;

        // Gets givin username from database
        $get_username = $db -> prepare("
            SELECT username FROM users WHERE username=:username;;
        ");

        $get_username -> execute([
            ":username" => $username,
        ]);

        // Checks if username is taken 
        if (!empty($get_username -> fetchAll()))
        {
            $this -> errors["username"] = "Username taken";
            $valid = false;
        }

        return $valid;
    }

    /** 
     * Returns wether a givin email is valid or not.
     * 
     * @param string $email
     * @return bool
     */
    protected function isEmailValid(string $email): bool
    {
        global $db;

        $valid = true;

        // Gets givin email from database
        $get_email = $db -> prepare("
            SELECT email FROM users WHERE email=:email;;
        ");

        $get_email -> execute([
            ":email" => $email,
        ]);

        // Returns wether the email account is taken or not
        if (!empty($get_email -> fetchAll()))
        {
            $this -> errors["email"] = "Email taken";
            $valid = false;
        }

        return $valid;
    }

    /** 
     * Returns wether a givin password is valid or not.
     * 
     * @param string $password
     * @param string $confirm_password
     * @return bool
     */
    protected function isPasswdValid(string $password, string $confirm_password): bool
    {
        $valid = true;

        // Checks if the two passwords are the same
        if ($password != $confirm_password)
        {
            $this -> errors["password"] = "Password do not match";
            $valid = false;
        }

        $valid_password = "/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.{8,})/";

        // Checks if the user's password is secure enough
        if(!preg_match($valid_password, $_POST["password"]))
        {
            $this -> errors["password"] = "Password must contain 8 characters, 1 digit, and 1 capital character";
            $valid = false;
        }

        return $valid;
    }

    /** 
     * Returns wether a givin school_id is valid or not.
     * 
     * @param int $school_id
     * @return bool
     */
    protected function isSchoolValid(int $school_id)
    {
        global $db;

        $valid = true;

        // Gets givin school from database
        $get_data = $db -> prepare("
            SELECT school_id FROM schools WHERE school_id=:school_id;;
        ");

        $get_data -> execute([
            ":school_id" => $school_id,
        ]);

        // Checks wether the givin school is in the database or not
        if (empty($get_data -> fetchAll()))
        {            
            $this -> errors["school"] = "Invalid school";
            $valid = false;
        }

        return $valid;
    }

    /** 
     * Initializes users account
     *  
     * @param int $user_id
     * @param int $school_id
     * @param int $type_id
     * @param string $username
     * @param string $email
     * @param string $first_name
     * @param string $last_name
     * @param bool $verified
     */
    protected function initAccount(int $user_id, int $school_id, int $type_id, string $username, string $email, string $first_name, string $last_name, bool $verified)
    {
        $this -> user_id = $user_id;
        $this -> school_id = $school_id;
        $this -> type_id = $type_id;
        $this -> username = $username;
        $this -> email = $email;
        $this -> first_name = $first_name;
        $this -> last_name = $last_name;
        $this -> verified = $verified;
    }
}
?>