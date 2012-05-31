<?php namespace OneAuth\OAuth2\Provider;

/**
 * Ported from FuelPHP \OAuth2 package
 *
 * @package    FuelPHP/OAuth2
 * @category   Provider
 * @author     Phil Sturgeon
 * @copyright  (c) 2012 HappyNinjas Ltd
 * @license    http://philsturgeon.co.uk/code/dbad-license
 */

use OneAuth\OAuth2\Provider as OAuth2_Provider,
	OneAuth\OAuth2\Token\Access as Token_Access;

class Paypal extends OAuth2_Provider
{
	/**
	 * @var  string  default scope (useful if a scope is required for user info)
	 */
	protected $scope = array('https://identity.x.com/xidentity/resources/profile/me');

	/**
	 * @var  string  the method to use when requesting tokens
	 */
	protected $method = 'POST';

	public function url_authorize()
	{
		return 'https://identity.x.com/xidentity/resources/authorize';
	}

	public function url_access_token()
	{
		return 'https://identity.x.com/xidentity/oauthtokenservice';
	}

	public function get_user_info(Token_Access $token)
	{
		$url = 'https://identity.x.com/xidentity/resources/profile/me?' . http_build_query(array(
			'oauth_token' => $token->access_token
		));

		$user = json_decode(file_get_contents($url));
		$user = $user->identity;

		return array(
			'uid'         => $user['userId'],
			'nickname'    => \Str::slug($user['fullName'], '-'),
			'name'        => $user['fullName'],
			'first_name'  => $user['firstName'],
			'last_name'   => $user['lastName'],
			'email'       => $user['emails'][0],
			'location'    => $user->addresses[0],
			'image'       => null,
			'description' => null,
			'urls'        => array(
				'paypal' => null
			)
		);
	}
}