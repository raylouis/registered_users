<?php

/**
 * This file is part of the "Registered Users" plugin for Wolf CMS.
 * Licensed under an MIT style license. For full details see license.txt.
 *
 * @author Martijn van der Kleijn <martijn.niji@gmail.com>
 * @copyright Martijn van der Kleijn, 2009-2013
 * 
 * Original author:
 * 
 * @author Andrew Waters <andrew@band-x.org>
 * @copyright Andrew Waters, 2009
 *
 */

/*
 * Contains the following functions for the Front End :
 *
 * ru_register_page()           Use this on the page you want to have for registrations eg mysite.com/register
 * ru_login_page()		Use this on the page you want to have for logging in eg mysite.com/login
 * ru_confirm_page()		This is the page a user clicks through to validate their account
 * ru_auth_required_page()	Users who are not authorised to view the requested page will be redirected here
 * ru_reset_page()		Will allow a user to have an email sent to them with a lnk to reset their password
 * ru_logout()			A page to logout a user and return them to the hompage
 */

Plugin::setInfos(array(
    'id'          => 'registered_users',
    'title'       => 'Registered Users',
    'description' => 'Allows you to manage new user registrations on your site.',
    'version'     => '1.0-dev',
    'author'      => 'Martijn van der Kleijn',
    'require_wolf_version' => '0.7.7'
));

// Only when the plugin is enabled
if (Plugin::isEnabled('registered_users')) {

    Plugin::addController('registered_users', 'Registered Users', 'admin_edit', true);

    Observer::observe('view_page_edit_plugins',	'registered_users_access_page_checkbox');
    Observer::observe('page_add_after_save',	'registered_users_add_page_permissions');
    Observer::observe('page_edit_after_save',	'registered_users_edit_page_permissions');
    Observer::observe('page_delete',		'registered_users_delete_page_permissions');
    Observer::observe('page_found',		'registered_users_page_found');

    Behavior::add('login_page', '');

    include('classes/RegisteredUser.php');
    include('classes/RUCommon.php');
    include('observers/RUObservers.php');

    
    // @todo Switch this stupid stuff to use routes
    function ru_login_page() {
        $registered_users_class = new RegisteredUser();
        $loginpage = $registered_users_class->login_page();
        echo $loginpage;
    }

    function ru_register_page() {
        $registered_users_class = new RegisteredUser();
        $registerpage = $registered_users_class->registration_page();
        echo $registerpage;
    }

    function ru_confirm_page() {
        $registered_users_class = new RegisteredUser();
        $confirmation_page = $registered_users_class->confirm();
        echo $confirmation_page;
    }

    function ru_auth_required_page() {
        $registered_users_class = new RegisteredUser();
        $auth_required_page = $registered_users_class->auth_required_page();
        echo $auth_required_page;
    }

    function ru_reset_page() {
        $registered_users_class = new RegisteredUser();
        $reset_page = $registered_users_class->password_reset();
        echo $reset_page;
    }

    function ru_logout() {
        // Allow plugins to handle logout events
            Observer::notify('logout_requested');

            $username = AuthUser::getUserName();
            AuthUser::logout();
            Observer::notify('admin_after_logout', $username);
            redirect(get_url());
    }
}
