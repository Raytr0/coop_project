<?php
class TokenError extends Exception {}

class Token
{
    /**
     * @var string $token
     */
    protected $token;

    /**
     * User the token belongs to.
     * 
     * @var int $user_id
     */
    protected $user_id;

    /**
     * Type of token. Tokens with token type greater than 0 
     * can only be used once.
     * 
     * @var int $token_type
     */
    protected $token_type;

    /**
     * Timestamp for when the token expires.
     * 
     * @var int $expires
     */
    protected $expires;

    /**
     * Wether the token is valid or not.
     * 
     * @var bool $valid
     */
    protected $valid;

    /** 
     * Constructor. Initializes the token's attributes to null.
     */
    public function __construct()
    {
        $this -> token = null;
        $this -> user_id = null;
        $this -> token_type = null;
        $this -> expires = null;
        $this -> valid = null;
    }

    /** 
     * Validates token. Returns wether or not the token
     * was valid.
     * 
     * @param string $token
     * @param int $token_type
     * @return bool
     */
    public function validateToken(string $token, string $token_type): bool
    {
        // Checks if the token is already validated
        if ($this -> valid == true)
        {
            // Token is valid
            return true;
        } 

        global $db;

        // Breaks the token apart
        list($selector, $validator) = explode("-", $token);

        // Hashs the validator
        $validator = hash("sha256", $validator);

        // Gets the token from the database
        $get_token = $db -> prepare("
            SELECT * FROM auth_tokens WHERE selector=:selector AND token_type=:token_type LIMIT 1;
        ");

        $get_token -> execute([
            ":selector" => $selector,
            ":token_type" => $token_type,
        ]);

        $token_info = $get_token -> fetch(PDO::FETCH_ASSOC);

        // Checks if the token was found in the database
        if (empty($token_info))
        {
            // No token was found
            $this -> valid = false;

            return false;
        }

        else
        {
            // Token was found in the databse. Initialize the token's attributes. 
            $this -> token = $token;
            $this -> user_id = $token_info["user_id"];
            $this -> token_type = $token_info["token_type"];
            $this -> expires = strtotime($token_info["expires"]);
            $this -> valid = true;

            // Checks if the validator givin is valid and if the token is not expired 
            if (hash_equals($validator, $token_info["validator"]) && time() < $this -> expires)
            {
                // Removes token if token type is greater than 0
                if (0 < $this -> token_type) $this -> removeToken();

                // Token is valid
                return true;
            }

            else
            {
                // Removes token
                $this -> removeToken();

                // Token is not valid
                return false;
            }
        }
    }

    /** 
     * Creates token. Returns the token created.
     * 
     * @param int $user_id
     * @param string $username
     * @param int $token_type
     * @param int $expires
     * @return string
     */
    public function createToken(int $user_id, string $username, int $token_type, int $expires): string
    {
        // Checks if there is already a valid token 
        if ($this -> valid == true)
        {
            // Returns the valid token 
            return $this -> token;
        } 

        global $db;

        // Removes any token of token_type that the user might have
        $this -> removeToken();

        // Creates random authentication validator
        $validator = bin2hex(random_bytes(16) . $username);

        // Creates random selector for the validator
        $selector = hash("sha256", bin2hex(random_bytes(32)));

        // Saves token in database
        $save_token = $db -> prepare("
            INSERT INTO auth_tokens 
            (user_id, token_type, selector, validator, expires)
            VALUES
            (:user_id, :token_type, :selector, :validator, :expires)
        ");

        $save_token -> execute([
            ":user_id" => $user_id,
            ":token_type" => $token_type,
            ":selector" => $selector,
            ":validator" => hash("sha256", $validator),
            ":expires" => date("Y-m-d H:i:s", $expires),
        ]);

        // Initialize the token's attributes. 
        $this -> token = "${selector}-${validator}";
        $this -> user_id = $user_id;
        $this -> token_type = $token_type;
        $this -> expires = $expires;
        $this -> valid = true; 

        // Return created token
        return $this -> token;
    }

    /** 
     * Removes the token. Returns wether it was successful 
     * or not.
     */
    public function removeToken()
    {
        global $db;

        // Checks if token is invalid
        if ($this -> valid == false)
        {
            // Did not remove token as it was not valid 
            return false;
        }

        // Removes token
        $remove_token = $db -> prepare("
            DELETE FROM auth_tokens WHERE user_id=:user_id AND token_type=:token_type;
        ");

        $remove_token -> execute([
            ":user_id" => $this -> user_id,
            ":token_type" => $this -> token_type,
        ]);

        // Token is not valid anymore
        $this -> valid = false;

        // Successfully removed token
        return true;
    }

    /** 
     * Returns a specified attribute. If no attribute is 
     * specified it returns all attributes.
     * 
     * @param string|null $attribute
     * @return string
     */
    public function getTokenInfo($attribute = null): string
    {
        // Checks if an attribute was specified
        if ($attribute == null)
        {
            // Returns all attributes
            return [
                "token" => $this -> token,
                "user_id" => $this -> user_id,
                "token_type" => $this -> token_type,
                "expires" => $this -> expires,
                "valid" => $this -> valid,
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
}
?>