<?php

if (!defined('WHMCS')) die('This file cannot be accessed directly');

function UserCom_config() {
    return [
        'name'          => 'User.com Integration',
        'description'   => 'Allows you to connect to the User.com',
        'version'       => '1.0',
        'author'        => '<img src="https://user.com/static/img/front-3/navbar/user_logo.svg" alt="User.com" style="padding: 20px 10px;" />',
        'fields'        => [
            'domain' => [
                'FriendlyName' => 'Your User.com domain',
                'Type' => 'text'
            ],
            'apiKey' => [
                'FriendlyName' => 'Your User.com API Key',
                'Type' => 'text'
            ]
        ]
    ];
}

function UserCom_output() {
    $view = new \Smarty();
    $view->setCompileDir(ROOTDIR.'/templates_c');
    $view->display(__DIR__.'/templates/admin.tpl');
}
