<?php

include_once "EmailGlobalSender.class.php";
include_once "Database.class.php";

/**
 * Description of Mailer
 *
 * @author sefimov
 */

class Mailer {
	/** @var array массив данных */
	private $values = array();

	private $save_to_db = 0;
	
	/** @var array адресаты кому письмище */
	private $addressies = array();
	/** @var string путь в библиотеки */
	private $lib_path = __DIR__;

	public function __construct() {
        $this->db = new Database('mysql:host=localhost;dbname=pkbeeline', 'pkbeeline', 'pkbeeline');
	}
    
    public function saveToDb(){
		$this->values["phone"] = $this->PhoneNumberFormat($_POST["phone"]);

		if (!empty($this->values['phone']) && $this->save_to_db == 1)
		try {
        	$stmt = $this->db->getConnection()->prepare('insert into T_LOG(name, phone) values(:name, :phone)');
			$stmt->bindValue('name', $this->values["name"]);
			//$stmt->bindValue('surname', $this->values["surname"]);
			$stmt->bindValue('phone', $this->values["phone"]);
			//$stmt->bindValue('email', $this->values["email"]);
			$stmt->execute();

			$this->db->getConnection()->commit();

			//var_dump($stmt->errorInfo()); exit;
		}
		catch(exception $e){var_dump('yes'); exit;}
    }

    private function getFromDB() {
        $sql = "
          select name, phone 
          from T_LOG 
          where DT between 
          subtime(now(), '1 0:0:0.000000') and
          now()";
        $conn = $this->db->getConnection();
        $string = "<table border='1'>";
        $string .= "<tr>";
        $string .= "<th>Name</th>";
        $string .= "<th>Phone</th>";
        $string .= "</tr>";
        foreach ($conn->query($sql) as $row) {
            $name = $row['name'];
            $phone =  $row['phone'];

            $string .= "<tr>";
            $string .= "<td>" . $name . "</td>";
            $string .= "<td>" . $phone . "</td>";
            $string .= "</tr>";

        }
        $string .= "</table>";

        return $string;
    }

	/**
	 * Инициализирует список адресатов
	 *
	 * @param string $is_test тест или нет, разные списки адресатов
	 */
	public function SetAddresee($is_test){
		if($is_test){
            //$this->send_email_flag = 1;

            //$this->addressies[] = "<a.pshenichnikov@be-interactive.ru>";
            //$this->addressies[] = "<puzanius@gmail.com>";
            $this->addressies[] = "<m.volchkov@be-interactive.ru>";
            //$this->addressies[] = "<s.efimov@be-interactive.ru>";
			//$this->addressies[] = "<kuzmichev@my.com>";
            //$this->addressies[] = "<elena.goldman@mpsa.com>";
		}
		else{
//			$this->addressies[] = "<mgts-box@mts.ru>";
//			$this->addressies[] = "<M.A.Davydova@mgts.ru>";
//			$this->addressies[] = "<Y.M.Bondareva@mgts.ru>";
//			$this->addressies[] = "<G.A.Shulyupova@mgts.ru>";
		}
	}

	/**
	 * Валидация и приведение POST-а к нормальному тексту
	 */
	public function PostValidation(){
        //	$json = json_decode(file_get_contents('php://input'),TRUE);
	    //  var_dump($json); exit;
	    //  if (is_array($json)){
	    //      $this->values["name"] = @$json['name'];
	    //      $this->values["phone"] = @$json['phone'];
	    // }
	    
//	    var_dump($_POST['name']); exit;

		//TODO, валидация будет только есть или нет телефона. Есть - шлём письмо
        $this->values["name"] = $_POST["name"];
		//$this->values["surname"] = $_POST["surname"];
        $this->values["phone"] = $_POST["phone"];
		//$this->values["email"] = $_POST["email"];

		//шлём письмище
		if (!(empty($this->values["phone"]))) {
			//$this->send_email_flag = 1;
			$this->save_to_db = 1;
			$this->values["phone"] = $this->PhoneNumberFormat($this->values["phone"]);			
		}
	}

	/**
	 * Функция посылает письма по массиву адресатов
	 */
	public function SendEMail(){
        //echo "before";
        //echo "after 0";
		//отправляем письмо об удачной регистрации
		$mailSender = new EmailGlobalSender('VMET_RO');
        //echo "after 1";

        $from = "\"Weborama\" <info@weborama.com.ru>";
		//$string = "<p>".implode("</p><p>", $this->values)."</p>";
        $string = $this->getFromDB();
		$string = quoted_printable_encode($string);
		$email_string = file_get_contents($this->lib_path.'/mail.mht');
		$email_string = preg_replace("/\{emailtext\}/", $string, $email_string);

        //echo "after 2";
		foreach ($this->addressies as $to) {
            //echo "after 3";
			$mail = $mailSender->newMail($from, $to, "Получена заявка PKBEELINE", NULL);
			$mail->setMultipartEmailBody($email_string);
			//echo "send";
            $mail->send();
            //echo "after send";
		}

		return 1;
	}

	/**
	 * меняем номер телефона на нужный
	 *
	 * @param string $phone_number телефонный номер вида +7(926)123-45-66
	 */
	private function PhoneNumberFormat($phone_number = "") {
		if(empty($phone_number))
			return $phone_number;

		$phone_number = preg_replace("/\D/", "", $phone_number);
		$phone_number = preg_replace("/^7/", "8", $phone_number);

		return $phone_number;

	}
}
