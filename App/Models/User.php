<?php

namespace App\Models;

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
    public $errors = [];
	
	/**
     * Class constructor
	 *
	 *@param array $data  Initial property values
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
     * @return void
     */
    public function save(){
		
		$this->validate();
		
		if(empty($this->errors)){
			
			$db = static::getDB();
			
			$hash = password_hash($this->password1, PASSWORD_DEFAULT);
			
			$sql = 'INSERT INTO users (login, email, password)
				VALUES (:login, :email, :password)';
				
			$query = $db->prepare($sql);
			
			$query->bindValue(':login', $this->login, PDO::PARAM_STR);
			$query->bindValue(':email', $this->email, PDO::PARAM_STR);
			$query->bindValue(':password', $hash, PDO::PARAM_STR);
			
			return $query->execute();
		}
		
		return false;
    }
	
	/**
     * Validate current property values, adding validation error messages to the errors array property
     *
     * @return void
     */
    public function validate(){
		
        /**
		 * Login
		 */
		if ($this->login == ''){
			$this->errors[] = "Login jest wymagany.";
		}
		if ((strlen($this->login) < 3) || (strlen($this->login) > 20)){
			$this->errors[] = "Login musi posiadać od 3 do 20 znaków!";
		}
		if (ctype_alnum($this->login) == false){
			$this->errors[] = "Login może składać się tylko z liter i cyfr (bez polskich znaków)";
		}
		if(static::loginExists($this->login)){
			$this->errors[] = "Podany login jest już zajęty.";
		}
		
		/**
		 * Email
		 */
		$emailB = filter_var($this->email, FILTER_SANITIZE_EMAIL);
		
		if ((filter_var($emailB, FILTER_VALIDATE_EMAIL) == false) || ($emailB != $this->email)){
			$this->errors[] = "Podaj poprawny adres e-mail!";
		}	
		if(static::emailExists($this->email)){
			$this->errors[] = "Istnieje już konto przypisane do podanego adresu e-mail.";
		}
		
		/**
		 * Password
		 */	
		if ((strlen($this->password1) < 8) || (strlen($this->password1) > 30)){
			$this->errors[] = "Hasło musi posiadać od 8 do 30 znaków!";
		}	
		if (preg_match('/.*[a-z]+.*/i', $this->password1) == 0){
			$this->errors[] = "Hasło musi zawierać przynajmniej jedną literę.";
		}	
		if (preg_match('/.*\d+.*/i', $this->password1) == 0){
			$this->errors[] = "Hasło musi zawierać przynajmniej jedną cyfrę.";
		}	
		if ($this->password1 != $this->password2){
			$this->errors[] = "Podane hasła nie są identyczne!";
		}	

    }
	
	/**
     * See if an email already exists in database
	 *
	 * @param string email address to search for
     *
     * @return boolean TRUE if exists, FALSE otherwise
     */
	public static function emailExists($email){	
		$db = static::getDB();
		
		$sql = 'SELECT * FROM users WHERE email = :email';
			
		$query = $db->prepare($sql);
		
		$query->bindValue(':email', $email, PDO::PARAM_STR);
		
		$query->execute();
		
		return $query->fetch() !== false;
    }
	
	/**
     * See if a login already exists in database
	 *
	 * @param string login to search for
     *
     * @return boolean TRUE if exists, FALSE otherwise
     */
	public static function loginExists($login){	
		
		return static::findByLogin($login) !== false;
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
}
