<?php
@session_start();
defined('_JEXEC') or die('Restricted access');

class pm_sn extends PaymentRoot{
    
    function showPaymentForm($params, $pmconfigs){
        include(dirname(__FILE__)."/paymentform.php");
    }


	function showAdminFormParams($params){
	  $array_params = array('MerchantCode', 'muser', 'mpass', 'transaction_end_status', 'transaction_pending_status', 'transaction_failed_status');
	  foreach ($array_params as $key){
	  	if (!isset($params[$key])) $params[$key] = '';
	  } 
	  
	  $jver = new JVersion();
	  if( $jver->RELEASE[0] == 2 ){
		  $orders = &JModel::getInstance('orders', 'JshoppingModel');
	  }else{
		  $orders = JSFactory::getModel('orders', 'JshoppingModel'); 
	  }	
      include(dirname(__FILE__)."/adminparamsform.php");  
	}
	
	function checkTransaction($pmconfigs, $order, $act){
// Security
$sec=$_GET['sec'];
$mdback = md5($sec.'vm');
$mdurl=$_GET['md'];
// Security
$transData = $_SESSION[$sec];

if(isset($_GET['sec']) or isset($_GET['md']) AND $mdback == $mdurl ){	
		$orderId = (int) $_GET["orderid"];
		$api = $pmconfigs['MerchantCode'];
		$amount = $transData['price']; //Tooman
		$au=$transData['au'];


		
$bank_return = $_POST + $_GET ;
$data_string = json_encode(array (
'pin' => $api,
'price' => ceil($amount),
'order_id' => $orderId,
'au' => $au,
'bank_return' =>$bank_return,
));

$ch = curl_init('https://developerapi.net/api/v1/verify');
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
'Content-Type: application/json',
'Content-Length: ' . strlen($data_string))
);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 20);
$result = curl_exec($ch);
curl_close($ch);
$json = json_decode($result,true);

 $res=$json['result'];
                 
	                 switch ($res) {
						    case -1:
						    $msg = "پارامترهای ارسالی برای متد مورد نظر ناقص یا خالی هستند . پارمترهای اجباری باید ارسال گردد";
						    break;
						     case -2:
						    $msg = "دسترسی api برای شما مسدود است";
						    break;
						     case -6:
						    $msg = "عدم توانایی اتصال به گیت وی بانک از سمت وبسرویس";
						    break;

						     case -9:
						    $msg = "خطای ناشناخته";
						    break;

						     case -20:
						    $msg = "پین نامعتبر";
						    break;
						     case -21:
						    $msg = "ip نامعتبر";
						    break;

						     case -22:
						    $msg = "مبلغ وارد شده کمتر از حداقل مجاز میباشد";
						    break;


						    case -23:
						    $msg = "مبلغ وارد شده بیشتر از حداکثر مبلغ مجاز هست";
						    break;
						    
						      case -24:
						    $msg = "مبلغ وارد شده نامعتبر";
						    break;
						    
						      case -26:
						    $msg = "درگاه غیرفعال است";
						    break;
						    
						      case -27:
						    $msg = "آی پی مسدود شده است";
						    break;
						    
						      case -28:
						    $msg = "آدرس کال بک نامعتبر است ، احتمال مغایرت با آدرس ثبت شده";
						    break;
						    
						      case -29:
						    $msg = "آدرس کال بک خالی یا نامعتبر است";
						    break;
						    
						      case -30:
						    $msg = "چنین تراکنشی یافت نشد";
						    break;
						    
						      case -31:
						    $msg = "تراکنش ناموفق است";
						    break;
						    
						      case -32:
						    $msg = "مغایرت مبالغ اعلام شده با مبلغ تراکنش";
						    break;
						 
						    
						      case -35:
						    $msg = "شناسه فاکتور اعلامی order_id نامعتبر است";
						    break;
						    
						      case -36:
						    $msg = "پارامترهای برگشتی بانک bank_return نامعتبر است";
						    break;
						        case -38:
						    $msg = "تراکنش برای چندمین بار وریفای شده است";
						    break;
						    
						      case -39:
						    $msg = "تراکنش در حال انجام است";
						    break;
						    
                            case 1:
						    $msg = "پرداخت با موفقیت انجام گردید.";
						    break;

						    default:
						       $msg = $json['msg'];
						}






                    if($json['result'] == 1){
			$id_get = $orderId;
			$trans_id = $au;
			$mess = "پرداخت با موفقیت انجام شده است. شماره تراکنش: $id_get    شناسه پرداخت: $trans_id";
			$_SESSION['verifySaleOrderId'] = $id_get;
			$_SESSION['verifySaleReferenceId'] = $trans_id;	
			return array(1, $mess);
		}
		else{
			echo $msg;
			$mess = "خطا: " . $msg;
			return array(0, $mess);
		}		
		}
		else{
			$mess = "خطای امنیتی !!!";
			return array(0, $mess);
		}		
	exit;
	

	}
	
	function showEndForm($pmconfigs, $order){
		// Security
@session_start();
$sec = uniqid();
$md = md5($sec.'vm');
// Security
		$amount = round($order->order_total);
		$return = JURI::root(). "index.php?option=com_jshopping&controller=checkout&task=step7&act=return&orderid=".$order->order_id."&js_paymentclass=pm_sn&amount=".$amount."&md=".$md."&sec=".$sec;

		$api = $pmconfigs['MerchantCode'];
		$amount = $amount/10; //Tooman
		$callbackUrl = $return;
		$orderId = $order->order_id;
$data_string = json_encode(array(
'pin'=> $api,
'price'=> ceil($amount),
'callback'=> $callbackUrl ,
'order_id'=> $orderId,
'ip'=> $_SERVER['REMOTE_ADDR'],
'callback_type'=>2
));

$ch = curl_init('https://developerapi.net/api/v1/request');
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
'Content-Type: application/json',
'Content-Length: ' . strlen($data_string))
);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 20);
$result = curl_exec($ch);
curl_close($ch);


$json = json_decode($result,true);

	 $res=$json['result'];
                 
	                 switch ($res) {
						    case -1:
						    $msg = "پارامترهای ارسالی برای متد مورد نظر ناقص یا خالی هستند . پارمترهای اجباری باید ارسال گردد";
						    break;
						     case -2:
						    $msg = "دسترسی api برای شما مسدود است";
						    break;
						     case -6:
						    $msg = "عدم توانایی اتصال به گیت وی بانک از سمت وبسرویس";
						    break;

						     case -9:
						    $msg = "خطای ناشناخته";
						    break;

						     case -20:
						    $msg = "پین نامعتبر";
						    break;
						     case -21:
						    $msg = "ip نامعتبر";
						    break;

						     case -22:
						    $msg = "مبلغ وارد شده کمتر از حداقل مجاز میباشد";
						    break;


						    case -23:
						    $msg = "مبلغ وارد شده بیشتر از حداکثر مبلغ مجاز هست";
						    break;
						    
						      case -24:
						    $msg = "مبلغ وارد شده نامعتبر";
						    break;
						    
						      case -26:
						    $msg = "درگاه غیرفعال است";
						    break;
						    
						      case -27:
						    $msg = "آی پی مسدود شده است";
						    break;
						    
						      case -28:
						    $msg = "آدرس کال بک نامعتبر است ، احتمال مغایرت با آدرس ثبت شده";
						    break;
						    
						      case -29:
						    $msg = "آدرس کال بک خالی یا نامعتبر است";
						    break;
						    
						      case -30:
						    $msg = "چنین تراکنشی یافت نشد";
						    break;
						    
						      case -31:
						    $msg = "تراکنش ناموفق است";
						    break;
						    
						      case -32:
						    $msg = "مغایرت مبالغ اعلام شده با مبلغ تراکنش";
						    break;
						 
						    
						      case -35:
						    $msg = "شناسه فاکتور اعلامی order_id نامعتبر است";
						    break;
						    
						      case -36:
						    $msg = "پارامترهای برگشتی بانک bank_return نامعتبر است";
						    break;
						        case -38:
						    $msg = "تراکنش برای چندمین بار وریفای شده است";
						    break;
						    
						      case -39:
						    $msg = "تراکنش در حال انجام است";
						    break;
						    
                            case 1:
						    $msg = "پرداخت با موفقیت انجام گردید.";
						    break;

						    default:
						       $msg = $json['msg'];
						}

if(!empty($json['result']) AND $json['result'] == 1)
{
        // Set Session
$_SESSION[$sec] = [
	'price'=>$amount ,
	'order_id'=>$invoice_id ,
	'au'=>$json['au'] ,
];
		  echo ('<div style="display:none">'.$json['form'].'</div>Please wait ... <script language="javascript">document.payment.submit(); </script>');
	  }else{
		  echo $msg;
		  $mess = "خطا: " . $msg;
			return array(0, $mess);
	  }
		
	}
    
    function getUrlParams($pmconfigs){                        
        $params = array(); 
        $params['order_id'] = JRequest::getInt("orderid");
        $params['hash'] = "";
        $params['checkHash'] = 0;
        $params['checkReturnParams'] = $pmconfigs['checkdatareturn'];
		return $params;
    }
    
}
?>