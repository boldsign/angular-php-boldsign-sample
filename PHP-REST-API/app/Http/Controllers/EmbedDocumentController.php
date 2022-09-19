<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
 
class EmbedDocumentController extends Controller
{
    public function CreateEmbedDocument(\Illuminate\Http\Request $request)
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
                ],
                [
                    'name' => 'ShowToolbar',
                    'contents' => 'true'
                ],
                [
                    'name' => 'RedirectUrl',

                    // TODO: you can provide redirect URL of your choice
                    'contents' =>'http://localhost:4200/embedDocument/completed'
                ],
                [
                    'name' => 'SendViewOption',
                    'contents' =>'FillingPage'
                ]
            ]
        ];

        $createEmbeddedRequestAPIUrl = "https://api.boldsign.com/v1/document/createEmbeddedRequestUrl/";
        $headers = ['X-API-Key' => $_ENV["API_KEY"]];
        $request = new \GuzzleHttp\Psr7\Request('POST', $createEmbeddedRequestAPIUrl, $headers);
        $client = new \GuzzleHttp\Client(['verify' => false]);
        $res = $client->sendAsync($request, $options)->wait();
        return $res->getBody()->getContents();
    }
}