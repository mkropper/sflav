<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use \SforceEnterpriseClient;

use App\Providers\SalesforceExportProvider;
use Mockery\CountValidator\Exception;


class ForcecomController extends Controller
{
    //
    public function testapi(Request $request) {

        // TODO seperate template for error ?


        $username   = Config::get('salesforce.username');
        $password   = Config::get('salesforce.password');
        $token      = Config::get('salesforce.token');

        $mySforceConnection = "";
        try {

            $mySforceConnection = new SforceEnterpriseClient();
            $mySforceConnection->createConnection(base_path() . '/vendor/forcecom/soapclient/enterprise.wsdl.xml');

            // session

            if ($request->session()->has('enterpriseSessionId')) {
                $location   = $request->session()->get('enterpriseLocation');
                $sessionId  = $request->session()->get('enterpriseSessionId');

                $mySforceConnection->setEndpoint($location);
                $mySforceConnection->setSessionHeader($sessionId);

                $sessioninfo ="Used session ID for enterprise<br/><br/>";

            } else {
                $mySforceConnection->login($username , $password . $token);

                $request->session()->put('enterpriseLocation', $mySforceConnection->getLocation());
                $request->session()->put('enterpriseSessionId', $mySforceConnection->getSessionId());

                $sessioninfo = "Logged in with enterprise<br/><br/>";
            }
            print_r($mySforceConnection->getUserInfo());


            // query

            $query = "SELECT Id, FirstName, LastName, Phone, OwnerId, AccountId from Contact";

            $response = $mySforceConnection->query($query);

            // create

            $create_records = array(
                (object) array(
                    'FirstName' => 'John',
                    'LastName' => 'Smith',
                    'Phone' => '(510) 555-5555',
                    'BirthDate' => '1957-01-25',
                    'OwnerId' => '0050Y000000HI8WQAW',
                ),
                (object) array(
                    'FirstName' => 'Mary',
                    'LastName' => 'Jones',
                    'Phone' => '(510) 486-9969',
                    'BirthDate' => '1977-01-25',
                    'OwnerId' => '0050Y000000HI8WQAW',
                ),
            );

            $create_response = $mySforceConnection->create($create_records, 'Contact');

            $ids = array();
            foreach ($create_response as $i => $result) {
                array_push($ids, $result->id);
            }

            // retrieving

            $retrieve_response = $mySforceConnection->retrieve('Id, FirstName, LastName, Phone, OwnerId', 'Contact', $ids);

            // update

            $records = array(
                (object) array(
                    'Id'    => $ids[0],
                    'Phone' => '(415) 555-5555',
                    'OwnerId' => '0050Y000000HI8WQAW'
                ),
                (object) array(
                    'Id'    => $ids[1],
                    'Phone' => '(415) 486-9969',
                    'OwnerId' => '0050Y000000HI8WQAW'
                ),
                (object) array(
                    'Id'    => '0030Y00000HzGd8QAF',
                    'FirstName' => 'Frinz',
                    'OwnerId' => '0050Y000000HI8WQAW'
                ),
            );

            $update_response = $mySforceConnection->update($records, 'Contact');

            // check update

            $check_response = $mySforceConnection->retrieve('Id, FirstName, LastName, Phone, OwnerId', 'Contact', $ids);


            // remove numbers

            $records = array(
                (object) array(
                    'Id'            => $ids[0],
                    'fieldsToNull'  => 'Phone',
                ),
                (object) array(
                    'Id'            => $ids[1],
                    'fieldsToNull'  => 'Phone',
                ),
            );

            $remove_response = $mySforceConnection->update($records, 'Contact');

            // check again

            $check2_response = $mySforceConnection->retrieve('Id, FirstName, LastName, Phone, OwnerId', 'Contact', $ids);

            // delete

           $delete_response = $mySforceConnection->delete($ids);


            return view('salesforce', array(
                'sessioninfo' => $sessioninfo,
                'query' => $query,
                'response' => $response,
                'create_response' => $create_response,
                'create_records' => $create_records,
                'retrieve_response' => $retrieve_response,
                'update_response' => $update_response,
                'check_response' => $check_response,
                'check2_response' => $check2_response,
                'remove_response' => $remove_response,
                'delete_response' => $delete_response,
            ));


        }
        catch(\SoapFault $e) {

            $msg ="Exception ".$e->getMessage()."<br/><br/>\n";
            $msg .="Last Request:<br/><br/>\n";
            $msg .=$mySforceConnection->getLastRequestHeaders();
            $msg .="<br/><br/>\n";
            $msg .=$mySforceConnection->getLastRequest();
            $msg .="<br/><br/>\n";
            $msg .="Last Response:<br/><br/>\n";
            $msg .=$mySforceConnection->getLastResponseHeaders();
            $msg .="<br/><br/>\n";
            $msg .=$mySforceConnection->getLastResponse();

            return view('error', array('msg' => $msg));
        }



    }
}
