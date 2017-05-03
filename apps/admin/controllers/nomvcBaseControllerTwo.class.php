<?php

class nomvcBaseControllerTwo extends nomvcBaseController{
    protected $baseUrl;

    protected function checkIsRoot(){
        $roles = $this->context->getUser()->getAttribute('roles');

        foreach ($roles as $role){
            if ($role['role'] == 'root')
                return true;
        }

        return false;
    }

    protected function init()
    {
        $this->dbHelper = $this->context->getDbHelper();

        $this->baseUrl = '/admin';
        
        //$this->url_file = '/files/';
        //$this->path_file = DIRNAME(__FILE__).'/../../../web/files/';

        $user = $this->context->getUser();
        $this->setDBContextParameter('id_service', $user->getAttribute('id_service'));

        $services = $user->getAttribute('id_services');
        if (is_array($services) && !$this->checkIsRoot()) {
            $service_list_str = '^(';

            foreach ($services as $key => $service) {
                $service_list_str .= $service;

                if (isset($services[$key + 1])) {
                    $service_list_str .= '|';
                }
            }

            $service_list_str .= ')$';

            //var_dump($service_list_str); //exit;
            $this->setDBContextParameter('id_services', $service_list_str);
        }
    }

    public function redirect($url = null) {
		if (is_null($url)) {
			header('Location: '.$_SERVER['HTTP_REFERER']);
		} else {
			header('Location: https://'.$_SERVER['HTTP_HOST'].$url);
		}
		exit();
	}
	

    public function sendEmail($to = array(), $subject, $message) {
        $mail = new HtmlMimeMail();
        $mail->send_ex($to, null, null, 'emg@ias.su', $subject, $message);
        return true;

    }

    protected function setDBContextParameter($var, $val) {
        $conn = $this->context->getDb();
        $stmt = $conn->prepare('call setParameter(:parameter, :value); end;');        
        $stmt->bindValue('parameter', $var);
        $stmt->bindValue('value', $val);
        $stmt->execute();
        
    }

    public function run(){
        parent::run();
        
    }

 

    public static function crypto_rand_secure($min = 0, $max = 9) {
        $range = $max - $min;
        if ($range == 0) return $min; // not so random...
        $log = log($range, 2);
        $bytes = (int) ($log / 8) + 1; // length in bytes
        $bits = (int) $log + 1; // length in bits
        $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes, $s)));
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ($rnd >= $range);
        return $min + $rnd;
    }

    public function generateFileName($ext){
        return sha3($this->crypto_rand_secure(11111111111111, 99999999999999)).$this->getExtensionFromType($ext);
    }

    public function base64_to_image($base64_string, $output_file) {
        $ifp = fopen($output_file, "wb");
        fwrite($ifp, base64_decode($base64_string));
        fclose($ifp);
        return($output_file);
    }

    public function getExtensionFromType($type, $default = ''){
        static $extensions = array(
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'video/mp4' => 'mp4',
            'video/mpeg' => 'mp4',
            'video/quicktime' => 'mov',

            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'text/plain' => 'txt',

            'application/vnd.oasis.opendocument.text',
            'application/vnd.ms-excel',
            'application/pdf',
            'text/html',
            'text/rtf',
            'text/csv',
            'image/vnd.adobe.photoshop',
            'application/zip',
            'application/vnd.ms-office'
        );

        return !$type ? $default : (isset($extensions[$type]) ? '.'.$extensions[$type] : $default);
    }


    protected function updateMemberSessionData(){
        $user = $this->context->getUser();

        try {
            $this->dbHelper->addQuery(get_class($this).'/select_member_info', 'select * from T_MEMBER where id_member = :id_member');
            $userInfo = $this->dbHelper->selectRow(get_class($this).'/select_member_info', array('id_member' => $user->getAttribute('id_member')));

            if ($userInfo)
                foreach ($userInfo as $key => $val) {
                    $user->setAttribute(strtolower($key), $val);
                }

            $user->setAttribute('member_photo_default', $this->getMemberPhotoDefault($userInfo['id_member']));
        }
        catch(exception $e){}
    }

    protected function getData(){
        $data = [];
        $data['user'] = $this->context->getUser();

        $loginForm = new LoginForm($this->context, array('method' => 'post', 'action' => '/login'));
        $loginForm->init();
        $data['login_form'] = $loginForm;

        $restoreForm = new RestoreForm($this->context, array('method' => 'post', 'action' => '/restore'));
        $restoreForm->init();
        $data['restore_form'] = $restoreForm;

        $data['context'] = $this->context;
//        
//        $data['member_photo_default'] = $this->context->getUser()->getAttribute('member_photo_default');
//        
        //var_dump($data['member_photo_default']['file_bin']); exit;

        return $data;
    }

    protected function getFormData($formId){
        $data = [];

        if (isset($_POST[$formId])) {
            $data = $_POST[$formId];
        }

        return $data;
    }   

    function cut_paragraph($string, $your_desired_width = 100)
    {
        //$string = strip_tags($string);
        $string = substr($string, 0, $your_desired_width);
        $string = rtrim($string, "!,.-");
        $string = substr($string, 0, strrpos($string, ' '));

        return $string;
    }
}
