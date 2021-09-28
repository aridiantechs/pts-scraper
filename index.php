
<?php

ini_set('display_errors', 1);
ini_set('max_execution_time', -1);
error_reporting(E_ALL);

include_once('simple_html_dom.php');
require_once (__DIR__ . '/vendor/autoload.php');
use Rct567\DomQuery\DomQuery;


    function getWebPage( $url )
    {
        $user_agent = 'Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';

        $options = array(
    
            CURLOPT_CUSTOMREQUEST  =>"GET",        //set request type post or get
            CURLOPT_POST           =>false,        //set to GET
            CURLOPT_USERAGENT      => $user_agent, //set user agent
            CURLOPT_COOKIEFILE     =>"cookie.txt", //set cookie file
            CURLOPT_COOKIEJAR      =>"cookie.txt", //set cookie jar
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER         => false,    // don't return headers
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_ENCODING       => "",       // handle all encodings
            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT        => 120,      // timeout on response
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
            // CURLOPT_PROXY          => 'zproxy.lum-superproxy.io',
            // CURLOPT_PROXYPORT      => '22225',
            // CURLOPT_PROXYUSERPWD   => 'lum-customer-hl_fa848026-zone-daniel_sahlin_zone:0xwx5ytxlfcc',
            // CURLOPT_HTTPPROXYTUNNEL=> 1,

        );

        $ch      = curl_init( $url );
        curl_setopt_array( $ch, $options );
        $content = curl_exec( $ch );
        $err     = curl_errno( $ch );
        $errmsg  = curl_error( $ch );
        $header  = curl_getinfo( $ch );
        curl_close( $ch );

        $header['errno']   = $err;
        $header['errmsg']  = $errmsg;
        $header['content'] = $content;
        return $header;
    }


    function postReq($number, $token)
    {
        
        $user_agent = 'Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';

        $options = array(
    
            CURLOPT_USERAGENT      => $user_agent, //set user agent
            CURLOPT_COOKIEFILE     =>"cookie.txt", //set cookie file
            CURLOPT_COOKIEJAR      =>"cookie.txt", //set cookie jar
            CURLOPT_RETURNTRANSFER => true,     // return web page
            CURLOPT_HEADER         => false,    // don't return headers
            CURLOPT_FOLLOWLOCATION => true,     // follow redirects
            CURLOPT_ENCODING       => "",       // handle all encodings
            CURLOPT_AUTOREFERER    => true,     // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
            CURLOPT_TIMEOUT        => 120,      // timeout on response
            CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
            // CURLOPT_PROXY          => 'zproxy.lum-superproxy.io',
            // CURLOPT_PROXYPORT      => '22225',
            // CURLOPT_PROXYUSERPWD   => 'lum-customer-hl_fa848026-zone-daniel_sahlin_zone:0xwx5ytxlfcc',
            // CURLOPT_HTTPPROXYTUNNEL=> 1,
        );

        $post = [
            '__RequestVerificationToken' => $token,
            'NbrToSearch' => $number,
        ];

        $ch = curl_init();
        curl_setopt_array( $ch, $options );
        curl_setopt($ch, CURLOPT_URL,"https://nummer.pts.se/NbrSearch");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
                    '__RequestVerificationToken='.$token.'&NbrToSearch=0'.$number);


        // Receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close ($ch);


        return $server_output;

    }

    function createLog($number, $key)
    {
            $myfile = fopen('./logs/log.txt', "a") or die("Unable to open file!");

            $txt = $key . ' - ' . $number;

            fwrite($myfile, $txt);

            fwrite($myfile, "\n");

            fclose($myfile);
    }

    function notFound($number, $file_name)
    {
            $myfile = fopen('./uploads/'.$file_name.'.txt', "a") or die("Unable to open file!");

            $txt = '0' . $number . 'Not found' . "\n" ;

            fwrite($myfile, $txt);

            fclose($myfile);
    }

    if (1)
    {
            // echo 'server output'; 
            
            echo $number = $_POST['number'];
            // die();
            $token  = '';
            $url    = 'https://nummer.pts.se/NbrSearch';
            $result = getWebPage($url);
            $html   = $result['content'];

            echo $html;

            $dom    = new DomQuery($html);

            if(gettype($dom) == 'boolean'){
                
                notFound($number, $file_name);
                createLog($number, $key . ' - Boolean');
                // continue;
            }
            else{

                echo 'Not Boolean';
            }

            $nodes  = $dom->find("input[type=hidden]");

            foreach ($nodes as $node){
                $token = $node->value;
            }

            echo $token ?? 'not found';

            // $dom = str_get_html(postReq((int)$number, $token));

            // echo $token ?? 'not found';
            // die();

            // // Create request log for data
            // createLog($number, $key);

            // // System Sleep

            // if($key > 1 && ($key % 30) == 0)
            //     sleep(2);

            // if(gettype($dom) !== 'boolean'){

            //     foreach($dom->find('.alert-success') as $element){
                    
            //         $txt = trim(str_replace('tillh&#246;r','-',$element->text())). "\n" ;
                    
            //         echo $txt;
                               
            //     }
            // }
            // else
            // {
            //     echo 'Not found';
            // }



    }

