<?php

namespace App\Services;

class SalesforceExportService {


    function processUsers($users) {
        $result = array();
        foreach($users as $user) {
            $result[] = (object) array(
                'type' => 'Contact',
                'fields' => array('FirstName' => $user->name, 'LastName' => $user->name , 'laravel_id__c' => $user->id)
            );
        }
        return $result;
    }

    function upsert_accounts($client, $sObjects) {

        try {
            $results = $client->upsert("laravel_id__c", $sObjects);

            $this->writeLog($results);

            return $results;
        }
        catch (\Exception $e)
        {
            return false;
        }
    }

    function writeLog() {
        // TODO write logs
        echo "writeLog";
    }

}