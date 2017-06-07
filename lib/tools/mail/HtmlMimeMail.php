<?php

class HtmlMimeMail {
    var $headers;
    var $multipart;
    var $mime;
    var $html;
    var $parts = array();
    var $server = "mail.ias.su";
    var $port = 25;

    function configure_server($server, $port=25) {
        if(!empty($server)) {
            $this->server = $server;
        }
        $this->port = $port;
    }

    function html_mime_mail($headers="") {
        $this->headers=$headers;
    }

    function add_html($html="") {
        $this->html.=$html;
    }

    function build_html($orig_boundary, $kod,$format) {
        $this->multipart.="--$orig_boundary\n";
        if ($kod=='w' || $kod=='win' || $kod=='windows-1251') {
            $kod='windows-1251';
        } else {
            $kod='utf-8';
        }
        if ($format == 'txt')
            $this->multipart.="Content-Type: text/plain; charset=$kod\n";
        else
            $this->multipart.="Content-Type: text/html; charset=$kod\n";

        $this->multipart.="Content-Transfer-Encoding: Quot-Printed\n\n";
        $this->multipart.="$this->html\n\n";
    }

    function add_attachment($path="", $name = "", $c_type="application/octet-stream", $c_loc="") {
        if (!file_exists($path.$name)) {
            print "File $path.$name dosn't exist.";
            return;
        }
        $fp=fopen($path.$name,"r");
        if (!$fp) {
            print "File $path.$name coudn't be read.";
            return;
        }
        $file=fread($fp, filesize($path.$name));
        fclose($fp);
        $this->parts[]=array("body"=>$file, "name"=>$name,"c_type"=>$c_type, "c_loc"=>$c_loc);
    }

    function add_attachment_data($data = "", $name = "", $c_type="application/octet-stream", $c_loc="") {
        $this->parts[]=array("body"=>$data, "name"=>$name,"c_type"=>$c_type, "c_loc"=>$c_loc);
        return 1;
    }

    function build_part($i) {
        $message_part="";
        $message_part.="Content-Type: ".$this->parts[$i]["c_type"];
        if ($this->parts[$i]["name"]!="")
            $message_part.="; name = \"".$this->parts[$i]["name"]."\"\n";
        else
            $message_part.="\n";
        $message_part.="Content-Transfer-Encoding: base64\n";
        if(empty($this->parts[$i]['c_loc']))
            $message_part.="Content-Disposition: attachment; filename = \"".$this->parts[$i]["name"]."\"\n\n";
        else
            $message_part.="Content-Location: {$this->parts[$i]['c_loc']}\n\n";

        $message_part.=chunk_split(base64_encode($this->parts[$i]["body"]))."\n";
        return $message_part;
    }

    function build_message($kod,$format='html') {
        $boundary="=_".sha3(uniqid(time()));
        $this->headers.="MIME-Version: 1.0\n";
        $this->headers.="Content-Type: multipart/mixed; boundary=\"$boundary\"\n";
        $this->multipart="";
        $this->multipart.="This is a MIME encoded message.\n\n";
        $this->build_html($boundary,$kod,$format);
        if ($format == 'html') {
            for ($i=(count($this->parts)-1); $i>=0; $i--)
                $this->multipart.="--$boundary\n".$this->build_part($i);
        }

        $this->mime = "$this->multipart--$boundary--\n";
    }

    function build_recepients($recipients) {
        $receivers = "";
        if(is_array($recipients)) {
            foreach ($recipients as $email) {
                if(!empty($receivers)) {
                    $receivers .= ", ";
                }
                $receivers .= $email;
            }
        }
        else {
            $receivers = $recipients;
        }
        return $receivers;
    }

    function build_emails_array($to, $cc, $bcc) {
        $emails = array();

        if(!empty($to)) {
            if(!is_array($to)) {
                $to = array($to);
            }
        } else
            $to = array();

        if(!empty($cc)) {
            if(!is_array($cc)) {
                $cc = array($cc);
            }
        } else
            $cc = array();

        if(!empty($bcc)) {
            if(!is_array($bcc)) {
                $bcc = array($bcc);
            }
        } else
            $bcc = array();

        $emails = array_merge($emails, $to);
        $emails = array_merge($emails, $cc);
        $emails = array_merge($emails, $bcc);
        return $emails;
    }

    function send($server, $to, $cc, $bcc, $from, $subject="", $headers="") {
        $this->configure_server($server);
        $this->send_ex($to, $cc, $bcc, $from, $subject, $headers);
    }

    function send_ex($to, $cc, $bcc, $from, $subject="", $headers="") {

        $receivers = "";
        $to_str = $this->build_recepients($to);
        if(!empty($to_str))
            $receivers = "To: $to_str\n";

        $to_str = $this->build_recepients($cc);
        if(!empty($to_str))
            $receivers .= "Cc: $to_str\n";

        $to_str = $this->build_recepients($bcc);
        if(!empty($to_str))
            $receivers .= "Bcc: $to_str\n";

        $emails = $this->build_emails_array($to, $cc, $bcc);

        //grezz: добавил дабы русский текст в заголовке был читабелен
        $subject = '=?Windows-1251?B?'.base64_encode($subject).'?=';

        $headers=$receivers."From: $from\nSubject: $subject\nX-Mailer: IAS-mail\n$headers";
        $fp = fsockopen($this->server, $this->port, $errno, $errstr, 30);
        if (!$fp) {
            die("Server $this->server. Connection failed: $errno, $errstr");
        }
        fputs($fp,"HELO $this->server\n");
        fputs($fp,"MAIL FROM: $from\n");
        foreach ($emails as $email) {
            fputs($fp,"RCPT TO: $email\n");
        }
        fputs($fp,"DATA\n");
        fputs($fp,$this->headers);
        if (strlen($headers)) {
            fputs($fp,"$headers\n");
        }
        fputs($fp,$this->mime);
        fputs($fp,"\n.\nQUIT\n");
        $resp = "";
        while(!feof($fp)) {
            $resp.=fgets($fp,1024);
        }
//	echo $resp;
        fclose($fp);
        return $resp;
    }

}