<?php 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
  // обработка отправки формы
$total = $_POST['total'];
$username = $_POST['username'];

  $payment_parameters = http_build_query(array(
    "clientid" => $username,
    "sum" => $total,
  ));
  $options = array("http"=>array(
                "method"=>"POST",
                "header"=>
                "Content-type: application/x-www-form-urlencoded",
                "content"=>$payment_parameters
                   ));
  $context = stream_context_create($options);
 
  echo file_get_contents("https://demo.paykeeper.ru/order/inline/",FALSE, $context);
  # Вместо demo.paykeeper.ru нужно указать адрес вашего сервера paykeeper

echo '<script>alert("Запрос выполнен успешно!");window.close();</script>';

}
?>
