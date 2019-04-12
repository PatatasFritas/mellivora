<?php

require('../../../include/mellivora.inc.php');

enforce_authentication(CONST_USER_CLASS_MODERATOR);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    validate_xsrf_token($_POST[CONST_XSRF_TOKEN_KEY]);

    if ($_POST['action'] == 'new') {

        require_fields(array('email', 'team_name', 'password', 'country'), $_POST);

        $email = $_POST['email'];
        $team_name = $_POST['team_name'];
        $password = $_POST['password'];
        $country = $_POST['country'];
        $enabled = @$_POST['enabled'];
        $competing = @$_POST['competing'];

        $user_id = db_insert(
            'users',
            array(
                'email'=>$email,
                'passhash'=>make_passhash($password),
                'download_key'=>hash('sha256', generate_random_string(128)),
                'team_name'=>$team_name,
                'added'=>time(),
                'enabled'=>$enabled,
                'competing'=>$competing,
                'user_type'=>0,
                'country_id'=>$country
            )
        );

        if ($user_id) {
            redirect(Config::get('MELLIVORA_CONFIG_SITE_ADMIN_RELPATH') . 'new_users?generic_success=1');
        } else {
            message_error('Could not insert new user.');
        }

    } elseif ($_POST['action'] == 'csv') {

        require_fields(array('users_csv'), $_POST);

        $lines = explode("\n", $_POST['users_csv']);

        $country = $_POST['country'];
        $enabled = @$_POST['enabled'];
        $competing = @$_POST['competing'];

        $i = 0;
        foreach($lines as $line) {
            $i++;
            $p = explode(',', $line);
            if(count($p)<3) {
                message_error('Error in line '.$i.' -> '.$line);
            } else {

                if(!valid_email($p[0])) {
                    message_error('Email not valid in line '.$i.' -> '.$p[0]);
                }

                // Check duplicates
                $user = db_select_one('users', array('id'), array('team_name' => $p[1]));
                if($user) {
                    message_error('Duplicated team name in line '.$i.' -> '.$p[1]);
                }

                $user = db_select_one('users', array('id'), array('email' => $p[0]));
                if($user) {
                    message_error('Duplicated email in line '.$i.' -> '.$p[0]);
                }

                $users[] = $p;
            }
        }

        foreach($users as $user) {
            $email = $user[0];
            $team_name = $user[1];
            $password = $user[2];

             $user_id = db_insert(
                'users',
                array(
                    'email'=>$email,
                    'passhash'=>make_passhash($password),
                    'download_key'=>hash('sha256', generate_random_string(128)),
                    'team_name'=>$team_name,
                    'added'=>time(),
                    'enabled'=>$enabled,
                    'competing'=>$competing,
                    'user_type'=>0,
                    'country_id'=>$country
                )
            );
        }

        redirect(Config::get('MELLIVORA_CONFIG_SITE_ADMIN_RELPATH') . 'new_users?generic_success=1');
    }
}

redirect(Config::get('MELLIVORA_CONFIG_SITE_ADMIN_RELPATH') . 'new_users');

