<?php

if (!defined('WHMCS')) die('This file cannot be accessed directly');
use Illuminate\Database\Capsule\Manager as Database;

add_hook('ClientAreaFooterOutput', 1, function ($vars) {
    require_once __DIR__.'/Helpers/UserComHelper.php';

    $data = Database::table('tbladdonmodules')
        ->select('*')
        ->where('module', '=', 'usercom')
        ->get();

    $settings = [];
    foreach ($data as $configuration) {
        $settings[$configuration->setting] = $configuration->value;
    }

    if(empty($settings['domain']) || empty($settings['apiKey'])) {
        return '';
    }
    $clientData = $vars['client'];

    $apiData = [];
    $apiData['apiKey'] = $settings['apiKey'];


    if ($vars['loggedin']) {
        $apiData['name'] = $clientData->firstName.' '.$clientData->lastName;
        $apiData['user_id'] = $clientData->id;
        $apiData['email'] = $clientData->email;
        $apiData['city'] = $clientData->city;
        $apiData['state'] = $clientData->state;
        $apiData['post_code'] = $clientData->postcode;
        $apiData['status'] = $clientData->status;
        $apiData['country'] = $clientData->country;
        $apiData['phone_number'] = $clientData->phonenumber;
        $apiData['address1'] = $clientData->address1;
        $apiData['address2'] = $clientData->address2;

        $services = Database::table('tblhosting')->select('name')->leftJoin('tblproducts', 'tblhosting.packageid', '=', 'tblproducts.id')->where('userid', $clientData->id)->get();
        if($services && is_array($services)) {
            $apiData['whmcs_plan_tag'] = implode(',', array_map(function($service) {return $service->name;}, $services));
        }

        $promos =  Database::table('tblhosting as h')->select('p.code', 'p.id')->leftJoin('tblpromotions as p', 'h.promoid', '=', 'p.id')
            ->where('h.userid', $clientData->id)->where('h.promoid', '!=', 0)->get();

        if($promos && is_array($promos)) {
            $apiData['whmcs_promo_tag'] = implode(',', array_map(function($promo) {return sprintf('#%d %s', $promo->id, $promo->code);}, $promos));
        }

        $apiData['tags'] = implode(',', array_unique($apiData['tags']));

        if($clientData['companyname']) {
            $apiData['company'] = [
                'name' => $clientData['companyname']
            ];
        }
    }

    $dataEncoded = json_encode($apiData);
    $domain = UserComHelper::parseDomainUrl($settings['domain']);

    return <<<SCRIPT
  <script data-cfasync="false">
    window.civchat = {$dataEncoded};
  </script>
  <script data-cfasync="false" src="https://{$domain}.user.com/widget.js"></script>
SCRIPT;
});
