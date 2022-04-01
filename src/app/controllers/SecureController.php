<head>
<link rel="stylesheet" href=".././../public/node_modules/bootstrap/dist/css/bootstrap.min.css">
</head>
<?php

use Phalcon\Mvc\Controller;
use Phalcon\Acl\Adapter\Memory;
use Phalcon\Security\JWT\Builder;
use Phalcon\Security\JWT\Signer\Hmac;
use Phalcon\Security\JWT\Token\Parser;
use Phalcon\Security\JWT\Validator;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class SecureController extends Controller
{
    public function indexAction()
    {
    }
 public function createTokenAction($role,$name,$email)
 {



    $key = "example_key";
    $payload = array(
        "iss" => "http://example.org",
        "aud" => "https://target.phalcon.io",
        "iat" => 1356999524,
        "nbf" => 1357000000,
        "role" => $role,
        "name" => $name,
        "email" => $email,
        "fsf" => "https://phalcon.io"
    );
    
    /**
     * IMPORTANT:
     * You must specify supported algorithms for your application. See
     * https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40
     * for a list of spec-compliant algorithms.
     */
      $jwt = JWT::encode($payload, $key, 'HS256');

    
      echo $jwt."<br>" ;
      echo "<p class='text-danger'>Please copy this token for future use</p>";
      $lang= $this->request->get('lang');
      $role= $this->request->get('bearer');

     
    header('Refresh: 10; URL=http://localhost:8080/?bearer='.$role.'&lang='.$lang);
 
    die ;



    // Defaults to 'sha512'
    // $signer  = new Hmac();
    
    // // Builder object
    // $builder = new Builder($signer);
    
    // $now        = new DateTimeImmutable();
    // $issued     = $now->getTimestamp();
    // $notBefore  = $now->modify('-1 minute')->getTimestamp();
    // $expires    = $now->modify('+1 day')->getTimestamp();
    // $passphrase = 'QcMpZ&b&mo3TPsPk668J6QH8JA$&U&m2';
    
    // // Setup
    // $builder
    //     ->setAudience('https://target.phalcon.io')  // aud
    //     ->setContentType('application/json')        // cty - header
    //     ->setExpirationTime($expires)               // exp 
    //     ->setId('abcd123456789')                    // JTI id 
    //     ->setIssuedAt($issued)                      // iat 
    //     ->setIssuer('https://phalcon.io')           // iss 
    //     ->setNotBefore($notBefore)                  // nbf
    //     ->setSubject($role)                        // sub
    //     ->setPassphrase($passphrase)                // password 
    // ;
    
    // // Phalcon\Security\JWT\Token\Token object
    // $tokenObject = $builder->getToken();
    
    // // The token
   
    // $token =  $tokenObject->getToken();
  
 }

    public function BuildAclAction()
    {
        $aclFile = APP_PATH . '/security/acl.cache';

        if (true !== is_file($aclFile)) {

            // The ACL does not exist - build it
            $acl = new Memory();
             

            $var = new Role();
            $res = json_decode(json_encode($var->find())); 

            foreach ($res as $key => $value) {
                $acl->addRole($value->jobProfile);
            }
         
            // ... Defining roles
            
         

            //adding components 

    
          

            // $acl->addComponent(
            //     'orders',
            //     [
            //         '*'
            //     ]
            // );
         

            //giving access
            $var = new Access();
            $res = json_decode(json_encode($var->find()));  
     
            foreach ($res as $key => $value) {

                $acl->addComponent(
                    $value->controller,
                    [
                        $value->action
                    ]
                );
                $acl->allow($value->jobProfile, $value->controller, $value->action);
            }
         


            // Store serialized list into plain file
            file_put_contents(
                $aclFile,
                serialize($acl)
            );
        }
    }
}
