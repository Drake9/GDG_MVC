<?php

namespace App;

/**
 * Application configuration
 *
 * PHP version 7.0
 */
class Config
{

    /**
     * Database host
     * @var string
     */
    const DB_HOST = 'localhost';

    /**
     * Database name
     * @var string
     */
    const DB_NAME = 'grosz_do_grosza';

    /**
     * Database user
     * @var string
     */
    const DB_USER = 'root';

    /**
     * Database password
     * @var string
     */
    const DB_PASSWORD = '';

    /**
     * Show or hide error messages on screen
     * @var boolean
     */
    const SHOW_ERRORS = true;
	
	/**
     * Secret key for hashing
     * @var string
     */
    const SECRET_KEY = "VW41X1rUCxSbSm4Fr0Wcf4m68y7xuQaD";
	
	/**
     * Captcha keys
     * @var string
     */
    const CAPTCHA_SITEKEY = "6LcgpawUAAAAAD_adlwN85JezYl7N6E9Yrba8WBY";
    const CAPTCHA_SECRET = "6LcgpawUAAAAAOlz9oO49z-n1Quh6e51vb6qdTTj";
}
