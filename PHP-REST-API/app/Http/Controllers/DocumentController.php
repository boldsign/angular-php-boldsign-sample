<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
 
class DocumentController extends Controller
{
    public function GetDocumentProperties(\Illuminate\Http\Request $request)
    {
        if ($_ENV["API_KEY"] == "") {
            throw new Exception("Api key should not be empty, please update them in the .env");
        }

        $getDocumentPropertiesUrl = "https://api.boldsign.com/v1/document/properties?documentId=".$request->query('documentId');
        return \Httpful\Request::get($getDocumentPropertiesUrl)->addHeader('X-API-Key', $_ENV["API_KEY"])->expectsjson()->send();
    }

    public function SendDocument(\Illuminate\Http\Request $request)
    {
        if ($_ENV["API_KEY"] == "") {
            throw new Exception("Api key should not be empty, please update them in the .env");
        }

        $options = [
            'multipart' => [
                [
                    'name' => 'Title',
                    'contents' => 'API Sample'
                ],
                [
                    'name' => 'Message',
                    'contents' => 'This is document message sent from API SDK'
                ],
                [
                    'name' => 'Files',
                    'contents' => file_get_contents($request["Files"]),
                    'filename' => 'Test',
                    'headers'  => [
                        'Content-Type' => 'application/pdf'
                    ]
                ],
                [
                    'name' => 'Signers',
                    'contents' => $request["Signers"]
                ]
            ]
        ];

        $sendAPIUrl = "https://api.boldsign.com/v1/document/send/";
        $headers = ['X-API-Key' => $_ENV["API_KEY"]];
        $request = new \GuzzleHttp\Psr7\Request('POST', $sendAPIUrl, $headers);
        $client = new \GuzzleHttp\Client(['verify' => false]);
        $res = $client->sendAsync($request, $options)->wait();
        return json_encode(json_decode($res->getBody()->getContents())->documentId);
    }
}