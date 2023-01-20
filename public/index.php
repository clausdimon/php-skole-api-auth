<?php
require __DIR__."/../bootstrap.php";

use Src\Controllers\MeasuringController;
use Src\Controllers\AlarmController;
use Src\Controllers\DeviceController;
use Src\Controllers\MeasurmentConfigController;
use Firebase\JWT\JWT;
use Firebase\JWT\JWK;

// send some CORS headers so the API can be called from anywhere
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS, GET, POST, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers. Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

// Switch to handle all http requests, and gets you to the right endpoint
switch ($uri[1])
{
    case 'alarm':
        $alarmId = null;
        $measuringUnit_id = null;

        if ($uri[3] === 'alarm')
        {
            $alarmId = $uri[2];
        } elseif ($uri[3] === 'measurement')
        {
            $measuringUnit_id = $uri[2];
        }

        if (! authenticate())
        {
            header("HTTP/1.1 401 Unauthorized");
            exit('Unauthorized');
        }

        $requestMethod = $_SERVER["REQUEST_METHOD"];

        $alarmController = new AlarmController($dbConnection, $requestMethod, $alarmId, $measuringUnit_id);
        $alarmController->processRequest();
        break;
    case 'measuring':
        $measurment_id = null;
        $measuringUnit_id = null;

        if ($uri[3] === 'single')
        {
            $measurment_id = $uri[2];
        } elseif ($uri[3] === 'type')
        {
            $measuringUnit_id = $uri[2];
        }

        if (! authenticate())
        {
            header('HTTP/1.1 401 Unauthorized');
            exit('Unauthorized');
        }
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        $measuringController = new MeasuringController($dbConnection, $requestMethod, $measurment_id, $measuringUnit_id);
        $measuringController->processRequest();
        break;
    case 'device':
        $device_id = null;
        $location_id = null;

        if ($uri[3] === 'device')
        {
            $device_id = $uri[2];
        } elseif ($uri[3] === 'location')
        {
            $location_id = $uri[2];
        }

        if (! authenticate())
        {
            header('HTTP/1.1 401 Unauthorized');
            exit('Unauthorized');
        }
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        $deviceController = new DeviceController($dbConnection, $requestMethod, $device_id, $location_id);
        $deviceController->processRequest();
        break;
    case 'measurementconfig':
        $measurementconfig_id = null;
        $measuringUnit_id = null;
        $device_id = null;

        if ($uri[3] === 'config')
        {
            $measurementconfig_id = $uri[2];
        } elseif ($uri[3] === 'type')
        {
            $measuringUnit_id = $uri[2];
        } elseif ($uri[3] === 'device')
        {
            $device_id = $uri[2];
        }

        if (! authenticate())
        {
            header('HTTP/1.1 401 Unauthorized');
            exit('Unauthorized');
        }
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        $measurementconfigController = new MeasurmentConfigController($dbConnection, $requestMethod, $measurementconfig_id, $measuringUnit_id, $device_id);
        $measurementconfigController->processRequest();
        break;
    default:
        header("HTTP/1.1 404 Not Found");
        exit();
}


// END OF FRONT CONTROLLER
// OAuth authentication functions follow
/**
 * Checks to see if you are Authorized and are allowed to enter the API Calls
 *
 * @return bool
 */
function authenticate()
{
    // extract the token from the headers
    if (! isset($_SERVER['HTTP_AUTHORIZATION']))
    {
        return false;
    }

    $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
    preg_match('/Bearer\s(\S+)/', $authHeader, $matches);

    if (! isset($matches[1]))
    {
        return false;
    }

    $token = $matches[1];
    $tokenParts = explode('.', $token);

    /*
    if ($methodName == 'charge')
    {
        return authenticateRemotely($token);
    } else
    {

    }
    */
    return authenticateLocally($token, $tokenParts);

}

function authenticateRemotely($token)
{
    $metadataUrl = getenv('OKTA_ISSUER') . '/.well-known/oauth-authorization-server';
    $metadata = http($metadataUrl);
    $introspectionUrl = $metadata['introspection_endpoint'];

    $params = [
        'token' => $token,
        'client_id' => getenv('OKTA_SERVICE_APP_ID'),
        'client_secret' => getenv('OKTA_SERVICE_APP_SECRET')
    ];

    $result = http($introspectionUrl, $params);

    if (! $result['active'])
    {
        return false;
    }

    return true;
}

/**
 * @param  string $token Is the part of the authentication, that is being checked to see if your access key used is valid.
 * @param array $tokenParts Is a array of the token, used to check each individual thing for matches.
 * @return bool If returning false, you are Unauthorized. If returning true, you're allowed access.
 */
function authenticateLocally($token, $tokenParts)
{
    //echo json_decode(base64UrlDecode($tokenParts[0])) . "\n";
    //$tokenParts = explode('.', $token);
    $decodedToken['header'] = json_decode(base64UrlDecode($tokenParts[0]), true);
    //echo $decodedToken['header'] . "\n";
    $decodedToken['payload'] = json_decode(base64UrlDecode($tokenParts[1]), true);
    $decodedToken['signatureProvided'] = base64UrlDecode($tokenParts[2]);

    // Get the JSON Web Keys form the server that signed the token
    // (ideally they should be cached to avoid
    // calls to OKta on each API request)...
    $metadataUrl = getenv('OKTA_ISSUER') . '/.well-known/oauth-authorization-server';
    $metadata = http($metadataUrl);
    $jwksUri = $metadata['jwks_uri'];
    $keys = http($jwksUri);

    //echo $keys . "\n";
    // Find the public key matching the kid from the input token
    $publicKey = false;
    foreach ($keys['keys'] as $key)
    {
        //echo $key['kid'] . "\n";
        //echo $decodedToken['header']['kid'] . "\n";
        if ($key['kid'] == $decodedToken['header']['kid'])
        {
            $publicKey = JWK::parseKey($key);
            break;
        }
    }

    if (!$publicKey)
    {
        echo "Couldn't find public key\n";
        return false;
    }

    // Check the signing algorithm
    if ($decodedToken['header']['alg'] != 'RS256')
    {
        echo "Bad algorithm\n";
        return false;
    }

    try {
        $result = JWT::decode($token, $publicKey, array('RS256'));
    }catch (\Firebase\JWT\ExpiredException $e) {
        echo 'Caught exception: ', $e->getMessage(), "\n";
    }


    if (! $result)
    {
        echo "Error decoding JWT\n";
        return false;
    }

    // Basic JWT validation passed, now check the claims

    // Verify the Issuer matches Okta's issuer
    if ($decodedToken['payload']['iss'] != getenv('OKTA_ISSUER'))
    {
        echo "Issuer did not match\n";
        return false;
    }

    // Verify the audience matches the expected audience for this API
    if ($decodedToken['payload']['aud'] != getenv('OKTA_AUDIENCE'))
    {
        echo "Audience did not match\n";
        return false;
    }

    // Verify the token was issued to the expected client_id
    if ($decodedToken['payload']['cid'] != getenv('OKTA_CLIENT_ID'))
    {
        echo "Client ID did not match\n";
        return false;
    }

    return true;
}

/**
 * makes the curl request, used for checking the Authentication.
 * @param $url
 * @param $params
 * @return mixed
 */
function http($url, $params = null)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    if ($params)
    {
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    }
    return json_decode(curl_exec($ch), true);
}

/**
 *  used to make the tokenparts into a string
 * @param $input
 * @return false|string
 */
function base64UrlDecode($input)
{
    $remainder = strlen($input) % 4;
    if ($remainder)
    {
        $padlen = 4 - $remainder;
        $input .= str_repeat('=', $padlen);
    }
    return base64_decode(strtr($input, '-_', '+/'));
}

function encodeLength($length)
{
    if ($length <= 0x7F)
    {
        return chr($length);
    }
    $temp = ltrim(pack('N', $length), chr(0));
    return pack('Ca*', 0x80 | strlen($temp), $temp);
}

function base64UrlEncode($text)
{
    return str_replace(
        ['+', '/', '='],
        ['-', '_', ''],
        base64_encode($text)
    );
}
