<?php

/*
$mailSender = new EmailGlobalSender('test');
$mail = $mailSender->newMail('test@ias.su', 'a.grezov@gmail.com', 'test #37', 'this is test #38');
$mail->addAttachment('test.txttest.txttest.txttest.txttest.txttest.txttest.txttest.txt', 'test.txt');
$mail->send();
*/


class EmailGlobalSender {

    protected $conn;
    protected $service;

    public function __construct($service) {
        $this->service = $service;

        $dsn = 'oci:dbname=DBACT2;charset=UTF8';
        $username = 'email_global';
        $password = 'email_global';
        $this->conn = new PDO($dsn, $username, $password);
    }

    public function getEmailId() {
        $email_id = 0;
        $stmt = $this->conn->prepare('
            declare
                l_email_id number;
            begin
                l_email_id := email_process.get_new_email_id();
                :email_id := l_email_id;
            end;
        ');
        $stmt->bindParam(":email_id", $email_id, PDO::PARAM_INT, 10);
        if (!$stmt->execute())
            throw new Exception($this->conn->errorCode()." --> ".serialize($this->conn->errorInfo()));
        if ($email_id == 0)
            throw new Exception("error in get new email id");

        return $email_id;
    }

    public function newMail($from, $to, $subject, $body, $headers = null, $template_data = null) {
        return new EmailGlobalSenderMail($this, $from, $to, $subject, $body, $headers, $template_data);
    }

    public function linkAttachment($email_id, $attachment_id) {
        $stmt = $this->conn->prepare('
            begin email_process.link_attachment(:email_id, :attachment_id); end;
        ');
        $bodyB = fopen('data://text/plain;base64,'.base64_encode($body), 'r');
        $stmt->bindParam(':email_id', $email_id);
        $stmt->bindParam(':attachment_id', $attachment_id);
        $this->conn->beginTransaction();
        if (!$stmt->execute())
            throw new Exception($this->conn->errorCode()." --> ".serialize($this->conn->errorInfo()));
        $this->conn->commit();
    }

    public function addAttachment($email_id, $body, $filename, $mime) {
        $att_id = 0;
        $stmt = $this->conn->prepare('
            declare
                l_att_id number;
                l_blob_data blob;
            begin
                l_att_id := email_process.add_attachment(:email_id, empty_blob(), :filename, :mime_type);
                :att_id := l_att_id;
                update t_message_blob_data set body = empty_blob() where attachment_id = l_att_id returning body into :body;
            end;
        ');
        $bodyB = fopen('data://text/plain;base64,'.base64_encode($body), 'r');	//     ____
        $stmt->bindParam(':att_id', $att_id, PDO::PARAM_STR, 10);				//    /____\
        $stmt->bindParam(':email_id', $email_id);								//	 //    \\
        $stmt->bindParam(':body', $bodyB, PDO::PARAM_LOB);						//	||      ||  <-- THIS IS "BUBEN"
        $stmt->bindParam(':filename', $filename);								//   \\____//
        $stmt->bindParam(':mime_type', $mime);									//    \____/
        $this->conn->beginTransaction();
        if (!$stmt->execute())
            throw new Exception($this->conn->errorCode()." --> ".serialize($this->conn->errorInfo()));
        $this->conn->commit();
        return $att_id;
    }

    public function send($mail) {
        $stmt = $this->conn->prepare('
            declare
                l_email_id number;
            begin
                l_email_id := email_process.send_email(:service, :email_from, :email_to, :subject, :email_body, :email_id, :headers, :template_data);
            end;
        ');

        $from = $mail->getFrom();
        $to = $mail->getTo();
        $subject = $mail->getSubject();
        $body = $mail->getBody();
        $email_id = $mail->getEmailId();
        $headers = $mail->getHeaders();
        $template_date = $mail->getTemplateData();

        $stmt->bindParam(':service',		$this->service);
        $stmt->bindParam(':email_from',		$from);
        $stmt->bindParam(':email_to',		$to);
        $stmt->bindParam(':subject',		$subject);
        $stmt->bindParam(':email_body',		$body);
        $stmt->bindParam(':email_id',		$email_id);
        $stmt->bindParam(':headers',		$headers);
        $stmt->bindParam(':template_data',	$template_date);

        if (!$stmt->execute())
            throw new Exception($this->conn->errorCode() . " --> " . serialize($this->conn->errorInfo()));
    }
}


class EmailGlobalSenderMail {

    protected $sender;
    protected $from;
    protected $to;
    protected $subject;
    protected $body;
    protected $headers;
    protected $email_id;
    protected $template_data;

    function getFrom()		{ return $this->from; }
    function getTo()		{ return $this->to; }
    function getSubject()	{ return $this->subject; }
    function getBody()		{ return $this->body; }
    function getHeaders()	{ return $this->headers; }
    function getEmailId()	{ return $this->email_id; }
    function getTemplateData()	{ return $this->template_data; }

    function __construct(EmailGlobalSender $sender, $from, $to, $subject, $body, $headers = null, $template_data = null) {
        $this->sender	= $sender;
        $this->from		= $from;
        $this->to		= $to;
        $this->subject	= $subject;
        $this->body		= $body;
        $this->headers	= $headers;
        $this->template_data = $template_data;
        $this->email_id = $sender->getEmailId();
    }

    function setMultipartEmailBody($body) {
        return $this->sender->addAttachment($this->email_id, $body, null, null);
    }

    function addAttachment($body, $filename, $mime = null) {
        return $this->sender->addAttachment($this->email_id, $body, $filename, $mime);
    }

    function linkAttachment($attachment_id) {
        $this->sender->linkAttachment($this->email_id, $attachment_id);
    }

    function send() {
        $this->sender->send($this);
    }

}

?>
