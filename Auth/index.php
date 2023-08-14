<?php
    //set up the parameters for the Auth process
    //Client ID
    $ClientId = 'wprQYMZBqqx-dgszFUfQG';

    //Redirect URL
    $redirect_uri = 'http://localhost:3000/oauth-callback';

    //User authorization URL
    $authorizeURL = 'https://id-sandbox.cashtoken.africa/oauth/authorize';

    //Access token Issuance URL
    $token = 'https://id-sandbox.cashtoken.africa/oauth/token';

    function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    //code verifier
    $codeVerifier = base64UrlEncode(random_bytes(32));
    
    //Generate a code challenge method
    $hash = hash('sha256', $codeVerifier);
    $codeChallenge = base64UrlEncode(pack('H*', $hash));

    //start a session
    session_start();

    //if the user is logged in
    if(!isset($_GET['action'])) {
        if(!empty($_SESSION['user_id'])) {
            echo '<h3>Logged In</h3>';
            echo '<p>User Id: '.$_SESSION['user_id'].'</p>';
            echo '<p>Email: '.$_SESSION['email'].'</p>';
            echo '<p>First Name: '.$_SESSION['first_name'].'</p>';
            echo '<p>Last Name: '.$_SESSION['last_name'].'</p>';
            echo '<p><a href="/../../index.php?action=logout">Logout</a></p>';

            echo '<h3>User info</h3';
            echo '<pre>';
            $ch = curl_init('https://id-sandbox.cashtoken.africa/oauth/userinfo');
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer '.$_SESSION['access_token']
            ]);
            curl_exec($ch);
            echo '</pre>';
        }
    }

    //check if the user wants to log in
    if(isset($_GET['action']) && $_GET['action'] == 'login') {
        //Generate a hash for the state and store
        $_SESSION['state'] = bin2hex(random_bytes(16));

        //set the structure for authorization
        $parameters = array(
            'response_type' => 'code',
            'redirect_uri' => $redirect_uri,
            'client_id' => $ClientId,
            'scope' => 'openid email profile',
            'state' => $_SESSION['state'],
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256'
        );

        //Redirect the user to the authorization page
        header('Location: '.$authorizeURL.'?'.http_build_query($parameters));
        die();
    }

    //After the authorization
    //Check that code and scope are the same
    if(isset($_GET['code'])) {
        if(!isset($_GET['state']) || $_SESSION['state'] != $_GET['state']) {
            header('Location: '.$redirect_uri.'?error=invalid_state');
            die();
        }

        //Exchange the authorization code for an access token
        $ch = curl_init($token);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'grant_type' => 'authorization_code',
            'client_id' => $ClientId,
            'redirect_uri' => $redirect_uri,
            'code' => $_GET['code']
        ]));
        $response = json_decode(curl_exec($ch, true));

        //split the jwt into various parts
        $jwt = explode('.', $response['id_token']);

        //extract the middle part, base64 decode, then json_decode it
        $userinfo = json_decode(base64_decode($jwt[1]), true);

        //extract the sub and email to determine the user signed in
        $_SESSION['user_id'] = $userinfo['sub'];
        $_SESSION['email'] = $userinfo['email'];
        $_SESSION['first_name'] = $userinfo['first_name'];
        $_SESSION['last_name'] = $userinfo['last_name'];

        //store the access token and id token for use later
        $_SESSION['access_token'] = $response['access_token'];
        $_SESSION['id_token'] = $response['id_token'];

        header('Location: '. $redirect_uri);
        die();
    }

  


?>
