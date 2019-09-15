<?php

namespace App;

use App\Models\User;
use App\Models\RememberedLogin;

/**
 * Authentication
 *
 */
class Auth{
	/**
	 * Log user in
	 *
	 * @param User $user the user model
	 * @param boolean $rememberMe remember the login if true
	 *
	 * @return void
	 */
	public static function login($user, $rememberMe){
		session_regenerate_id(true);
		$_SESSION['userID'] = $user->id;
		
		if($rememberMe){
			if($user->rememberLogin()){
				setcookie('rememberMe', $user->rememberToken, $user->expiryTimestamp, '/');
			}
		}
	}
	
	/**
	 * Log user out
	 *
	 * @return void
	 */
	public static function logout(){
		// Unset all of the session variables.
		$_SESSION = array();

		// If it's desired to kill the session, also delete the session cookie.
		// Note: This will destroy the session, and not just the session data!
		if(ini_get("session.use_cookies")) {
			
			$params = session_get_cookie_params();
			setcookie(
				session_name(),
				'',
				time() - 42000,
				$params["path"],
				$params["domain"],
				$params["secure"],
				$params["httponly"]
			);
		}

		// Finally, destroy the session.
		session_destroy();
		
		static::forgetLogin();
	}
	
	/**
	 * Remember the originally-requested page in the session
	 *
	 * @return void
	 */
	public static function rememberRequestedPage(){
		$_SESSION['return_to'] = $_SERVER['REQUEST_URI'];
	}
	
	/**
	 * Get the originally-requested page to return to after logging in
	 *
	 * @return void
	 */
	public static function getReturnToPage(){
		return $_SESSION['return_to'] ?? '/';
	}
	
	/**
	 * Get the current logged-in user
	 *
	 * @return mixed the user model or null if not logged in
	 */
	public static function getUser(){
		if(isset($_SESSION['userID'])){
			return User::findByID($_SESSION['userID']);
		}
		else{
			return static::loginFromRememberedCookie();
		}
	}
	
	/**
	 * Login user from a remembered login cookie
	 *
	 * @return mixed the user model or null
	 */
	protected static function loginFromRememberedCookie(){
		$cookie = $_COOKIE['rememberMe'] ?? false;
		
		if($cookie){
			$rememberedLogin = RememberedLogin::findByToken($cookie);
			
			if($rememberedLogin && ! $rememberedLogin->hasExpired()){
				$user = $rememberedLogin->getUser();
				static::login($user, false);
				
				return $user;
			}
		}
	}
	
	/**
	 * Forget the remembered login
	 *
	 * @return void
	 */
	protected static function forgetLogin(){
		$cookie = $_COOKIE['rememberMe'] ?? false;
		
		if($cookie){
			
			$rememberedLogin = RememberedLogin::findByToken($cookie);
			
			if($rememberedLogin){
				
				$rememberedLogin->delete();
			}
			
			setcookie('rememberMe', '', time() - 3600);
		}
	}
}