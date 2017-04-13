<?php

namespace App\Http\Controllers;


use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use \SforcePartnerClient;

class ForcecomExportController extends Controller {
    //
    public function export(Request $request) {

        $client  = $this->init_client();

        $exporter = app()->make('SalesforceExportService');

        $users_to_export = $this->getUsersToExport();

        $user_dtos = $exporter->processUsers($users_to_export);

        $success = $exporter->upsert_accounts($client, $user_dtos);

    }

    function init_client() {
        ini_set("soap.wsdl_cache_enabled", "0");

        $wsdl = base_path() . '/vendor/forcecom/soapclient/partner.wsdl.xml';
        $userName = Config::get('salesforce.username');
        $password = Config::get('salesforce.password') . Config::get('salesforce.token');

        // Process of logging on and getting a salesforce.com session
        $client = new SforcePartnerClient();
        $client->createConnection($wsdl);
        $loginResult = $client->login($userName, $password);

        // TODO check login result
        return $client;
    }

    function getUsersToExport() {
        return User::all();
    }


}
