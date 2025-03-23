<?php

namespace App\Services;

use Mailjet\Client;
use Mailjet\Resources;

class MailService
{
    /**
     * @param string $to_email
     * @param string $to_name
     * @param string $subject
     * @param string $email_body
     * @return void
     */
    public function send(string $to_email, string $to_name,string $subject, string $template,$vars=null):void
    {
        //le contenu de l'email
        $content = file_get_contents(dirname(__DIR__).'/Mail/'.$template);
        if ($vars){
           foreach ($vars as $key => $value){
               $content = str_replace('{'.$key.'}',$value,$content);
           }
        }

        $mj = new Client($_ENV['MJ_APIKEY_PUBLIC'],$_ENV['MJ_APIKEY_PRIVATE'],true,['version' => 'v3.1']);

        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "streetlascars@gmail.com",
                        'Name' => "fruityShop"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 6833992,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' =>[
                      'content'=>$content
                    ]
                ]
            ]
        ];

        //poster l'email
        $mj->post(Resources::$Email, ['body' => $body]);
        //$response->success() && dd($response->getData());
    }
}