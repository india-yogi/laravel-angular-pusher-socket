<?php
namespace App\Traits;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use DB;

trait HelperTrait {
    // Set Connection with new tenant DB
    public static function setConnectionWithTenantDB($strUsername,$strPassword,$strDbName,$strHost,$strPort){
        if( env('APP_ENV') == 'local-db-separation' ){
            $strUsername = Str::length($strUsername) > 100 ? self::pwdDecrypt($strUsername) : $strUsername;
			$strPassword = Str::length($strPassword) > 100 ? self::pwdDecrypt($strPassword) : $strPassword;

			$aConnections = [
				'mysql'
			];

			$aCurrentDbConfig = Config::get('database');
			foreach($aConnections as $strConnection){
				DB::disconnect($strConnection);
				$aCurrentDbConfig['connections'][$strConnection]['username'] = $strUsername;
				$aCurrentDbConfig['connections'][$strConnection]['password'] = $strPassword;
				$aCurrentDbConfig['connections'][$strConnection]['database'] = $strDbName;
				$aCurrentDbConfig['connections'][$strConnection]['host'] = $strHost;
				$aCurrentDbConfig['connections'][$strConnection]['port'] = $strPort;
			}

			DB::disconnect($strDbName);

			$options = $aCurrentDbConfig['connections'][$strDbName]['options'] ?? [];

			$aCurrentDbConfig['connections'][$strDbName] = [
				'driver' => 'mysql',
            	'url' => env('DATABASE_URL'),
				'username' => $strUsername,
				'password' => $strPassword,
				'database' => $strDbName,
				'host' => $strHost,
				'port' => $strPort,
				'unix_socket' => env('DB_ESP_SUPERADMIN_SOCKET', ''),
				'charset' => env('DB_CHARSET', 'utf8mb4'),
				'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
				'prefix' => env('DB_PREFIX', ''),
				'strict' => env('DB_STRICT_MODE', true),
				'engine' => env('DB_ENGINE', null),
				'prefix_indexes' => true,
				'options'		=> $options					
			];
			Config::set('database',$aCurrentDbConfig);
		}
    }

	// get encrypted value
	public static function pwdEncrypt($password){
		$newEncrypter = new \Illuminate\Encryption\Encrypter( config( 'app.password_salt' ), config( 'app.cipher' ) );
		return $newEncrypter->encrypt( $password );
	}

	// get decrypted value
	public static function pwdDecrypt($encrypted){
		$newEncrypter = new \Illuminate\Encryption\Encrypter( config( 'app.password_salt' ), config( 'app.cipher' ) );
		return $decrypted = $newEncrypter->decrypt( $encrypted );	
	}
}