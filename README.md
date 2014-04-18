# OAuth2 client

A simple OAuth2 client working with Zend\Http

## Exemple with Google+ API

    $googleClient = new OAuth2\Client('YOUR_API_KEY', 'YOUR_API_SECRET');
    $redirectUri  = 'http://domain/redirect-uri';

    if (isset($_GET['code'])) {

        $params = array(
            'code'         => $_GET['code'],
            'redirect_uri' => $redirectUri,
        );

        $response = $googleClient->getAccessToken(
            'https://accounts.google.com/o/oauth2/token',
            OAuth2\Client::GRANT_TYPE_AUTH_CODE,
            $params
        );

        $token = json_decode($response);
        $googleClient->setAccessToken($token->access_token)

        $response = $googleClient->fetch('https://www.googleapis.com/plus/v1/people/me');

        var_dump(json_decode($response->getBody()));

    } else {

        $scopes = array(
            'https://www.googleapis.com/auth/plus.login',
            'https://www.googleapis.com/auth/plus.me'
        );

        $params = array(
            'state'        => uniqid('', true),
            'redirect_uri' => $redirectUri,
            'scope'        => implode(' ', $scopes)
        );

        $authUrl = $googleClient->getAuthenticationUrl('https://accounts.google.com/o/oauth2/auth', $params);

        header('Location: ' . $authUrl);
        exit;

    }

## License

Released under the MIT License

## Contact

Pablo Prieto, [pabloprieto.net](http://pabloprieto.net/)

