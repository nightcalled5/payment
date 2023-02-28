<?php 
 $payment_parameters = http_build_query(array( "clientid"=>$client_login,
                "orderid"=>$orderid,
                "sum"=>$order_sum,
                "client_phone"=>$optional_phone));
  $options = array("http"=>array(
                "method"=>"POST",
                "header"=>
                "Content-type: application/x-www-form-urlencoded",
                "content"=>$payment_parameters
                   ));
  $context = stream_context_create($options);
 
  echo file_get_contents("https://demo.paykeeper.ru/order/inline/",FALSE, $context);
?>
