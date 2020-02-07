<?php

namespace App\Models;

use App\Token;

use PDO;

/**
 * Example user model
 *
 * PHP version 7.0
 */
class User extends \Core\Model{
	/**
     * Error messages
	 *
	 *@var array
     *
     */
    public $errorsLogin = [];
    public $errorsEmail = [];
    public $errorsPassword = [];
    public $errorsCaptcha = [];
	
	/**
     * Class constructor
	 *
	 * @param array $data  Initial property values
     *
     * @return void
     */
    public function __construct($data = []){
        foreach($data as $key => $value){
			$this->$key = $value;
		}
    }

    /**
     * Save the user model with the current property values
     *
     * @return boolean
     */
    public function save(){
		
		$this->success = true;
		
		$this->validate();
		
		if( $this->success ){
			
			$db = static::getDB();
			
			$hash = password_hash($this->password1, PASSWORD_DEFAULT);
			
			$db->beginTransaction();
			
				$sql = 'INSERT INTO users (login, email, password)
					VALUES (:login, :email, :password)';
					
				$query = $db->prepare($sql);
				
				$query->bindValue(':login', $this->login, PDO::PARAM_STR);
				$query->bindValue(':email', $this->email, PDO::PARAM_STR);
				$query->bindValue(':password', $hash, PDO::PARAM_STR);
				
				$query->execute();
				
				$userWithID = static::findByLogin($this->login);
				DefaultOptions::addAllDefaultOptions($userWithID->id, $db);
			
			return $db->commit();
		}
		
		return false;
    }
	
	/**
     * Validate current property values, adding validation error messages to the errors array property
     *
     * @return void
     */
    public function validate($passwordVerification = true){
		
        /**
		 * Login
		 */
		if ($this->login == ''){
			$this->errorsLogin[] = "Login jest wymagany. ";
			$this->success = false;
		}
		if ((strlen($this->login) < 3) || (strlen($this->login) > 20)){
			$this->errorsLogin[] = "Login musi posiadać od 3 do 20 znaków! ";
			$this->success = false;
		}
		if (ctype_alnum($this->login) == false){
			$this->errorsLogin[] = "Login może składać się tylko z liter i cyfr (bez polskich znaków). ";
			$this->success = false;
		}
		if ( isset($this->id) ){
			if(static::loginExists($this->login, $this->id)){
				$this->errorsLogin[] = "Podany login jest już zajęty. ";
				$this->success = false;
			}
		}else{
			if(static::loginExists($this->login)){
				$this->errorsLogin[] = "Podany login jest już zajęty. ";
				$this->success = false;
			}
		}
		
		/**
		 * Email
		 */
		$emailB = filter_var($this->email, FILTER_SANITIZE_EMAIL);
		
		if ((filter_var($emailB, FILTER_VALIDATE_EMAIL) == false) || ($emailB != $this->email)){
			$this->errorsEmail[] = "Podaj poprawny adres e-mail! ";
			$this->success = false;
		}	
		if ( isset($this->id) ){
			if(static::emailExists($this->email, $this->id)){
				$this->errorsEmail[] = "Istnieje już konto przypisane do podanego adresu e-mail. ";
				$this->success = false;
			}
		}else{
			if(static::emailExists($this->email)){
				$this->errorsEmail[] = "Istnieje już konto przypisane do podanego adresu e-mail. ";
				$this->success = false;
			}
		}
		
		/**
		 * Password
		 */	
		if($passwordVerification){
			
			if ((strlen($this->password1) < 8) || (strlen($this->password1) > 30)){
				$this->errorsPassword[] = "Hasło musi posiadać od 8 do 30 znaków! ";
				$this->success = false;
			}	
			if (preg_match('/.*[a-z]+.*/i', $this->password1) == 0){
				$this->errorsPassword[] = "Hasło musi zawierać przynajmniej jedną literę. ";
				$this->success = false;
			}	
			if (preg_match('/.*\d+.*/i', $this->password1) == 0){
				$this->errorsPassword[] = "Hasło musi zawierać przynajmniej jedną cyfrę. ";
				$this->success = false;
			}	
			if ($this->password1 != $this->password2){
				$this->errorsPassword[] = "Podane hasła nie są identyczne! ";
				$this->success = false;
			}
		}

		/**
		 * Captcha
		 */
		if( isset($_POST['g-recaptcha-response']) ){
			$secret = \App\Config::CAPTCHA_SECRET;
			
			$check = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
			
			$reply = json_decode($check);
			
			if ($reply->success == false){
				$this->errorsCaptcha[] = "Potwierdź, że nie jesteś robotem! ";
				$this->success = false;
			}
		}		

    }
	
	/**
     * See if an email already exists in database
	 *
	 * @param string email address to search for
	 * @param string $ignore_id return false anyway if the record has this id
     *
     * @return boolean TRUE if exists, FALSE otherwise
     */
	public static function emailExists($email, $ignored_id = null){	
		
		$user = static::findByEmail($email);
		
		if($user){
			if($user->id != $ignored_id){
				return true;
			}
		}
		
		return false;
    }
	
	/**
     * Find a user model by email address
	 *
	 * @param string email address to search for
     *
     * @return boolean TRUE if exists, FALSE otherwise
     */
	public static function findByEmail($email){	
		$db = static::getDB();
		
		$sql = 'SELECT * FROM users WHERE email = :email';
			
		$query = $db->prepare($sql);
		
		$query->bindValue(':email', $email, PDO::PARAM_STR);
		
		$query->setFetchMode(PDO::FETCH_CLASS, get_called_class());
		
		$query->execute();
		
		return $query->fetch();
    }
	
	/**
     * See if a login already exists in database
	 *
	 * @param string login to search for
     *
     * @return boolean TRUE if exists, FALSE otherwise
     */
	public static function loginExists($login, $ignored_id = null){
		
		$user = static::findByLogin($login);
		
		if($user){
			if($user->id != $ignored_id){
				return true;
			}
		}
		
		return false;
		//return static::findByLogin($login) !== false;
    }
	
	/**
     * Find a user model by login
	 *
	 * @param string login to search for
     *
     * @return mixed User object if found, false otherwise
     */
	public static function findByLogin($login){	
		$db = static::getDB();
		
		$sql = 'SELECT * FROM users WHERE login = :login';
			
		$query = $db->prepare($sql);
		
		$query->bindValue(':login', $login, PDO::PARAM_STR);
		
		$query->setFetchMode(PDO::FETCH_CLASS, get_called_class());
		
		$query->execute();
		
		return $query->fetch();
    }
	
	/**
     * Aunthenticate user by login and password
	 *
	 * @param string $login login
	 * @param string $password password
     *
     * @return mixed the user object or false if authentication fails
     */
	public static function authenticate($login, $password){	
		
		$user = static::findByLogin($login);
		
		if($user){
			if(password_verify($password, $user->password)){
				return $user;
			}
		}
		
		return false;
    }
	
	/**
     * Find a user model by id
	 *
	 * @param string $id
     *
     * @return mixed User object if found, false otherwise
     */
	public static function findByID($id){	
		$db = static::getDB();
		
		$sql = 'SELECT * FROM users WHERE id = :id';
			
		$query = $db->prepare($sql);
		
		$query->bindValue(':id', $id, PDO::PARAM_INT);
		
		$query->setFetchMode(PDO::FETCH_CLASS, get_called_class());
		
		$query->execute();
		
		return $query->fetch();
    }
	
	/**
     * Remember the login by inserting a new unique token into the database
     *
     * @return boolean true if success, false otherwise
     */
	public function rememberLogin(){	
		
		$token = new Token();
		$hashedToken = $token->getHash();
		$this->rememberToken = $token->getValue();
		
		$this->expiryTimestamp = time() + 60 * 60 * 24 * 7; // 7 days from now
		
		$db = static::getDB();
		
		$sql = 'INSERT INTO remembered_logins (token_hash, user_id, expires_at) VALUES (:token_hash, :user_id, :expires_at)';
			
		$query = $db->prepare($sql);
		
		$query->bindValue(':token_hash', $hashedToken, PDO::PARAM_STR);
		$query->bindValue(':user_id', $this->id, PDO::PARAM_INT);
		$query->bindValue(':expires_at', date('Y-m-d H:i:s', $this->expiryTimestamp), PDO::PARAM_STR);
		
		return $query->execute();
    }
	
	/**
     * Update the user's profile
     *
     * @return user model
     */
	public function updateProfile(){
		
		$this->success = true;
		if($this->password1 == "")
			$this->password1 = null;
		if($this->password2 == "")
			$this->password2 = null;

		$oldData = static::findByID($this->id);
		
		if( !password_verify($this->passwordOld, $oldData->password) ){
			
			$this->success = false;
			$this->errorPasswordOld = "Podane hasło jest nieprawidłowe.";
			return $this;
			
		}else if( isset($this->password1) && $this->password1 == $oldData->password ){
			
			$this->success = false;
			$this->errorsPassword[] = "Nowe hasło musi być inne, niż aktualnie używane.";
			return $this;
		}
		
		$this->validate( isset($this->password1) );
		
		if( $this->success ){
			$db = static::getDB();
		
			$sql = 'UPDATE users
					SET login = :login,
					email = :email';
					
			if(isset($this->password1)){
				$sql .= ', password = :password_hash';
			}
			
			$sql .= ' WHERE id = :id';
				
			$query = $db->prepare($sql);
			
			$query->bindValue(':login', $this->login, PDO::PARAM_STR);
			$query->bindValue(':email', $this->email, PDO::PARAM_STR);
			$query->bindValue(':id', $this->id, PDO::PARAM_INT);
			
			if(isset($this->password1)){
				$password_hash = password_hash($this->password1, PASSWORD_DEFAULT);
				$query->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
			}
			
			$query->execute();
		}
		
		return $this;
    }
}
