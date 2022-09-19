<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
 
class TemplateController extends Controller
{
    public function CreateEmbeddedRequestUrl(\Illuminate\Http\Request $request)
    {
        if ($_ENV["API_KEY"] == "") {
            throw new Exception("Api key should not be empty, please update them in the .env");
        }

        $sendTemplate = [
            "roles" => array(
                [
                    "roleIndex" => 1,
                    "signerOrder" => 1,
                    "signerName" => $request['name'],
                    "signerEmail" => $request['email'],
                ]),
                "ShowToolbar" => 'true',

                // TODO: you can provide redirect URL of your choice
                "RedirectUrl" => 'http://localhost:4200/embedDocument/completed'
            ];

        $createEmbeddedRequestAPIUrl = "https://api.boldsign.com/v1/template/createEmbeddedRequestUrl?templateId=".$request['templateId'];
        $sendResponse = \Httpful\Request::post($createEmbeddedRequestAPIUrl)
                        ->sendsJson()
                        ->addHeader('X-API-Key', $_ENV["API_KEY"])
                        ->body($sendTemplate)
                        ->expectsjson()
                        ->send();

        return json_encode($sendResponse->body->sendUrl);
    }

    public function GetEmbedSigningLink(\Illuminate\Http\Request $request)
    {
        if ($_ENV["API_KEY"] == "") {
            throw new Exception("Api key should not be empty, please update them in the .env");
        }

        $sendTemplateDetails = [
            "roles" =>
            array(
                [
                    "roleIndex" => 1,
                    "signerName" => $request['name'],
                    "signerEmail" => $request['email'],
                ]
            )
        ];

        $documentId = $this->SendTemplateById($sendTemplateDetails, $request['templateId']);
        return $this->GetSignLink($documentId, $request['email']);
    }

    public function SendTemplate(\Illuminate\Http\Request $request)
    {
        if ($_ENV["API_KEY"] == "") {
            throw new Exception("Api key should not be empty, please update them in the .env");
        }

        $sendTemplateDetails = [
            "roles" => array(
                [
                    "roleIndex" => 1,
                    "signerOrder" => 1,
                    "signerName" => $request['name'],
                    "signerEmail" => $request['email'],
                ]),
                "ShowToolbar" => 'true',

                // TODO: you can provide redirect URL of your choice
                "RedirectUrl" => 'http://localhost:4200/embedDocument/completed',
            ];
 
        $documentId = $this->SendTemplateById($sendTemplateDetails, $request['templateId']);
        return json_encode($documentId );
    }

    private function SendTemplateById($sendTemplateDetails, $templateId)
    {
        $sendTemplateUrl = "https://api.boldsign.com/v1/template/send?templateId=" . $templateId;
        $sendResponse = \Httpful\Request::post($sendTemplateUrl)
            ->sendsJson()
            ->addHeader('X-API-Key', $_ENV["API_KEY"])
            ->body($sendTemplateDetails)
            ->expectsjson()
            ->send();

        return $sendResponse->body->documentId;
    }

    private function GetSignLink($documentId, $email)
    {
        $queryString = http_build_query([
            'documentId' => $documentId,
            'signerEmail' => $email,

            // TODO: you can provide redirect URL of your choice
            'RedirectUrl' => "http://localhost:4200/embedDocument/completed"
        ]);

        $res = \Httpful\Request::get("https://api.boldsign.com/v1/document/getEmbeddedSignLink?" . $queryString)
            ->addHeader('X-API-Key', $_ENV["API_KEY"])
            ->expectsjson()
            ->send();

        return json_encode($res->body->signLink);
    }
}