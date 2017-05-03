<?

class SberbankClient {
    private $ch;
    private $login = 'parkovka-api';
    private $passwd = 'parkovka';
    
    private $returnUrl = 'http://morton.act4.ias.su/api/check_transaction.php';
        
    private $url_register = 'https://3dsec.sberbank.ru/payment/rest/register.do'; //Регистрация заказа
    private $url_registerPreAuth = 'https://3dsec.sberbank.ru/payment/rest/registerPreAuth.do'; //Регистрация заказа с предавторизацией 
    private $url_deposit = 'https://3dsec.sberbank.ru/payment/rest/deposit.do'; //Запрос завершения оплаты заказа 
    private $url_reverse = 'https://3dsec.sberbank.ru/payment/rest/reverse.do'; //Запрос отмены оплаты заказа
    
    private $url_refund = 'https://3dsec.sberbank.ru/payment/rest/refund.do'; //Запрос возврата средств оплаты заказа
    
    private $url_getOrderStatus = 'https://3dsec.sberbank.ru/payment/rest/getOrderStatus.do'; //Получение статуса заказа 
    
    private $url_getOrderStatusExtended = 'https://3dsec.sberbank.ru/payment/rest/getOrderStatusExtended.do'; //Получение статуса заказа
    
    private $url_verifyEnrollment = 'https://3dsec.sberbank.ru/payment/rest/verifyEnrollment.do'; //Запрос проверки вовлеченности карты в 3DS 
    
    private $url_paymentOrderBinding = 'https://3dsec.sberbank.ru/payment/rest/paymentOrderBinding.do'; //Запрос проведения оплаты по связкам
    
    private $url_unBindCard = 'https://3dsec.sberbank.ru/payment/rest/unBindCard.do'; //Запрос деактивации связки
    
    private $url_bindCard = 'https://3dsec.sberbank.ru/payment/rest/bindCard.do'; //Запрос активации связки
    
    private $url_extendBinding = '3dsec.sberbank.ru/payment/rest/extendBinding.do'; //Запрос изменения срока действия связки https
    
    private $url_getBindings = 'https://3dsec.sberbank.ru/payment/rest/getBindings.do'; //Запрос списка возможных связок для мерчанта
    
    private $url_getLastOrdersForMerchants = 'https://3dsec.sberbank.ru/payment/rest/getLastOrdersForMerchants.do'; //Запрос статистики по платежам за период 

    //private $currency = 810;

    private $language = 'ru';
    
    public function __construct($login = false, $passwd = false){ 
        $this->ch = curl_init();
        
        $this->login = $login?$login:$this->login;
        $this->passwd = $passwd?$passwd:$this->passwd;

        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);  
        curl_setopt($this->ch, CURLOPT_HEADER, 0);  
          
    }
    
    public function registerOrder($data){
        $data['userName'] = $this->login;
        $data['password'] = $this->passwd;
        $data['returnUrl'] = $this->returnUrl;
        $data['currency'] = $this->currency;
        $data['language'] = $this->language;
        
        curl_setopt($this->ch, CURLOPT_URL, $this->url_register);
        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data);
        
        $resp = [];
        if ($resp = curl_exec($this->ch)){
            $resp = json_decode($resp, true);
        }
        
        return $resp;
    }
}

    $client = new SberbankClient();
    
    $data['orderNumber'] = 10;
    $data['amount'] = 100;
    
    $data['clientId'] = 1;
    
    $resp = $client->registerOrder($data);
    
    if (isset($resp['orderId'])){
        var_dump($resp['orderId'], $resp['formUrl']); exit;
    }
    elseif(isset($resp['errorCode'])){
        var_dump($resp['errorCode'], $resp['errorMessage']); exit;    
    }
    
    var_dump($resp); exit;  
?>
