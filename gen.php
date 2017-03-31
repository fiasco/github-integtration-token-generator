#!/usr/bin/env php
<?php

use Lcobucci\JWT\Builder as JWTBuilder;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Github\HttpClient\Builder;
use Github\Client;

require 'vendor/autoload.php';

define('ISSUER_ID', $argv[1]);
define('INSTALLATION_ID', $argv[2]);
define('PRIVATE_KEY', $argv[3]);

$id = $argv[1];

$time = time();

$jwt = (new JWTBuilder)
		->setIssuer(ISSUER_ID)
		->setIssuedAt($time)
		// Set expiry of 10 minutes (max allowed).
		->setExpiration($time + 600)
		->sign(new Sha256(),  new Key(file_get_contents(PRIVATE_KEY)))
		->getToken();

$builder = new Builder();

$github = new Client($builder, 'machine-man-preview');
$github->authenticate($jwt, null, Client::AUTH_JWT);
$token = $github->api('integrations')
								->createInstallationToken(INSTALLATION_ID);

echo "TOKEN: {$token['token']}\n";
