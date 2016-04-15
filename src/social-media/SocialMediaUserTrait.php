<?php

namespace App\libs\LinkedIn;

use Illuminate\Support\Facades\Schema;
use App\User;
use Illuminate\Support\Facades\Auth;
use \Illuminate\Support\Facades\Redirect;

/**
 * Description of SocialMediaUserTrait
 *
 * @author Tuhin
 */
trait SocialMediaUserTrait {

    /**
     *
     * @var array 
     */
    protected $_apiData = array();

    /**
     *
     * @var array 
     */
    protected $_fillable = array();

    /**
     *
     * @var array 
     */
    protected $_login_credential = array();

    /**
     *
     * @var email 
     */
    protected $_emailFieldName;

    /**
     *
     * @var URL 
     */
    protected $_redirectPath;

    /**
     *
     * @var type 
     */
    protected $_social_media;

    /**
     * Error Bag
     * @var array 
     */
    protected $_errors = array();

    /**
     * 
     * @param Array $credentials
     * @return boolean
     */
    public function smLogin($credentials) {
        $userModel = $this->_getUserModel();

        if ($userModel) {
            Auth::login($userModel);
            $this->_redirectPath = \Config::get('laravel-linkedin-sdk.redirect_after_login');

            return TRUE;
        } else {
            $this->_errors['login_failed'] = 'Sorry unable to login via ' . $this->_social_media;

            return FALSE;
        }
    }

    /**
     * Create a new user based on api data. 
     * Here fillable data is process data. like associative array we give in User::create($array);
     * 
     * @param array $fillable 
     * @return boolean
     */
    public function createUser($fillable) {

        try {
            if (empty($fillable['password'])) {
                $fillable['password'] = bcrypt($fillable[$this->_emailFieldName]);
            }
            if ($userModel = User::create($fillable)) {
                Auth::login($userModel);
                $this->_redirectPath = \Config::get('laravel-linkedin-sdk.redirect_after_register');
                return TRUE;
            } else {
                $this->_errors['register_failed'] = 'Sorry unable to register via ' . $this->_social_media;
                return FALSE;
            }
        } catch (\Exception $ex) {

            $this->_errors['exception'] = $ex->getMessage();
            return FALSE;
        }
    }

    /**
     * Find User based on email address if not found then register that user.
     * 
     * @param array $apiData get data from calling api.
     * @return boolean
     */
    public function loginOrCreate(array $apiData, $social_media) {

        try {
            $this->_social_media = $social_media;

            $this->_apiData = $apiData;

            if (!isset(User::$social_media_field_alias[$social_media])) {
                $this->_errors['undefined_SM'] = 'Sorry ' . $social_media . ' does not exists in User::$social_media_field_alias';
                return FALSE;
            }

            $this->_prepareData($apiData, User::$social_media_field_alias[$social_media]);

            $response = $this->smLogin($this->_login_credential);

            if (!$response) {
                $response = $this->createUser($this->_fillable);
            }

            if (!$response) {
                return FALSE;
            }

            return TRUE;
        } catch (\Exception $ex) {
            $this->_errors['exception'] = $ex->getMessage();

            return FALSE;
        }
    }

    /**
     * Redirect user in proper place based on action like login or register
     * 
     * @return URL
     */
    public function getRedirectPath() {
        return $this->_redirectPath;
    }

    /**
     * Prepare data for further use.
     * 
     * @param array $source associative array got from api

     *  @param array $columns api field alias in database.
     *  This is defined in scoail_media_field_alias static variable in User Model
     * 
     * @return array
     */
    protected function _prepareData($source, $columns) {

        $fillableData = array();
        $emailApiFieldName = '';

        //LinkedIn does not return Full Name field. So make it appeding firstName and lastName field

        if (isset($source['firstName'])) {
            $source['full_name'] = $source['firstName'];
        }

        if (isset($source['lastName'])) {
            $source['full_name'] = $source['full_name'] . " " . $source['lastName'];
        }

        //Name of the Country you live e.g Bangladesh

        if (isset($source['location']['name'])) {
            $source['location'] = $source['location']['name'];
        }

        /**
         * Here we check if an linkedin field name filled up with database column name. If so then assign it to array for later use.
         */
        foreach ($columns as $key => $column) {
            if (!empty($column)) {
                $fillableData[$column] = $source[$key];
            }
        }
        $this->_fillable = $fillableData;

        if (isset($columns['emailAddress']) && !empty($columns['emailAddress'])) {

            $emailApiFieldName = 'emailAddress';
        } elseif (isset($columns['email']) && !empty($columns['email'])) {

            $emailApiFieldName = 'email';
        }

        $this->_emailFieldName = $columns[$emailApiFieldName];
        $this->_login_credential[$columns[$emailApiFieldName]] = $source[$emailApiFieldName];

        print_r($this->_fillable);
        return $this->_fillable;
    }

    /**
     * 
     * @return User
     */
    protected function _getUserModel() {

        return User::where($this->_emailFieldName, '=', $this->_fillable[$this->_emailFieldName])->first();
    }

    public function getErrors() {
        return $this->_errors;
    }

    public function syncToDb($source, $columns) {
        return $this->_prepareData($source, $columns);
    }

}
