<?php

    ini_set('error_reporting', E_ALL);
    ini_set('display_errrs', 0);
    
    define('API_GENERATOR_DIR', dirname(__DIR__).'/../api/apigenerator');
    require_once(dirname(__FILE__).'/../../api/lib/autoload.php');

    try {
	    $context = new ApiContext(agContext::ENV_PROD);

	    $subject = 'Запрос от Unisender ' . date('Y-m-d H:i:s');
	    $subject = mb_convert_encoding($subject, 'cp1251');

	    $from = 'info@be-interactive.ru';

	    $mail = new HtmlMimeMail();

	    $body = "---------------------\r\n";
	    $body .= date_format(new DateTime(), 'Y-m-d H:i:s') . "\r\n";
	    $body .= print_r($_POST, 1) . "\r\n";
	    $body .= print_r($_GET, 1);
	    $body .= "Post data:\r\n";
	    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		    $fh = fopen('php://input', 'r');
		    $postData = '';
		    while ($line = fgets($fh)) {
			    $body .= $line;
			    $postData .= $line;
		    }
	    }

	    //exaple
        /*$postData  = '{"auth":"9cba62a05e71a4456958c65a72b4be9d","events_by_user":[{"login":"be-interactive","events":[{"event_name":"email_status","event_time":"2017-06-19 10:38:22","event_data":{"email":"a.russkih@be-interactive.ru","status":"ok_delivered","status_group":"success_pending_group","email_id":11411542313}}]}]}';
        */
        
        $postData = json_decode($postData, true);

        //save to simpleregisterlog
        if (isset($postData['events_by_user'][0]['events'])){
            if (is_array($postData['events_by_user'][0]['events']))
            foreach ($postData['events_by_user'][0]['events'] as $row){
            try {
                $conn = $context->getDb();
                $tmp = $row;
                $sql = 'insert into `T_LOG_MAIL` (
                    `id_service`,
                    `mail_event_name`,
                    `mail_event_time`,
                    `mail_email`,
                    `mail_status`,
                    `mail_status_group`,
                    `mail_email_id`
                )
                values (
                    21,
                    :mail_event_name,
                    :mail_event_time,
                    :mail_email,
                    :mail_status,
                    :mail_status_group,
                    :mail_email_id
                )';

                /*
                var_dump(
                $tmp,
                $tmp['event_name'],
                $tmp['event_time'],
                $tmp['event_data']['email'],
                $tmp['event_data']['status'],
                $tmp['event_data']['status_group'],
                $tmp['event_data']['email_id']
                );exit;*/
                
                
                $stmt = $conn->prepare($sql);
                $stmt->bindValue(':mail_event_name', $tmp['event_name']);
                $stmt->bindValue(':mail_event_time', $tmp['event_time']);
                $stmt->bindValue(':mail_email', $tmp['event_data']['email']);
                $stmt->bindValue(':mail_status', $tmp['event_data']['status']);
                $stmt->bindValue(':mail_status_group', $tmp['event_data']['status_group']);
                $stmt->bindValue(':mail_email_id', $tmp['event_data']['email_id']);        

                if (!$stmt->execute())
                    print_r($stmt->errorInfo());
                else
                    echo "Insert data success\n";
            }
            catch(exception $e){}
            }
        }

        //send mail
	    $body .= "\r\n---------------------\r\n";

	    $body = mb_convert_encoding($body, 'cp1251');

	    $mail->send(
		    null, 
		    'a.pshenichnikov@be-interactive.ru;a.russkih@be-interactive.ru',
		    null, 
		    null, 
		    $from, 
		    $subject, 
		    $body
	    );

	    $f = fopen('mail.log', 'a');
	    fputs($f, $body);
	    fclose($f);
	
	    //echo $_GET['value'].  "\r\n";
	    echo "Send email success\r\n";
    } catch (Exception $ex) {
	    echo $ex->getMessage();
    }
