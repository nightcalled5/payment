<head>
    <meta charset="utf-8" />
    <title>Wialon Playground - Do payment</title>
    <script type="text/javascript" src="//code.jquery.com/jquery-latest.min.js"></script>
    <script type="text/javascript" src="//hst-api.wialon.com/wsdk/script/wialon.js"></script>
</head>

<form class="my-wialonCSS" method="POST">
<input type="hidden" name="total" id="total_input">
<label for="username">Имя пользователя:</label>
<input type="text" id="username">
<label for="payment_vehicles">Кол-во единиц:</label>
<input type="text" id="payment_vehicles"  oninput="calculateTotal()"  name="payment_vehicles">
<label for="payment_count">Кол-во месяцев:</label>
<input type="text" id="payment_count"  oninput="calculateTotal()"  name="payment_count">
<input type="submit" type="submit" name="submit" value="Оплатить"/>
<div id="log"></div>
<p><span id="total"></span></p>
</form>

<script>
function calculateTotal() {
  var count = parseInt(document.getElementById("payment_count").value);
  var month = parseInt(document.getElementById("payment_month").value);
  var total = count * month * 500; // вычисляем значение total
  document.getElementById("total").innerHTML = "Итоговая сумма: " + total + " рублей"; // записываем значение total в поле формы
  document.getElementById("total_input").value = total; // записываем значение total в скрытое поле формы
}

</script>


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

<script type="text/javascript">
function msg(text) {
  $("#log").prepend(text + "<br />");
}

function init() {
  var sess = wialon.core.Session.getInstance();
  var flags = wialon.item.Item.dataFlag.base | wialon.item.Item.dataFlag.billingProps;

  sess.loadLibrary("resourceAccounts");
  sess.updateDataFlags(
    [
      {
        type: "type",
        data: "avl_resource",
        flags: flags,
        mode: 0,
      },
    ],
    function (code) {
      if (code) {
        msg(wialon.core.Errors.getErrorText(code));
        return;
      }

      var res = sess.getItems("avl_resource");
      if (!res || !res.length) {
        msg("User not found");
        return;
      }

      for (var i = 0; i < res.length; i++) {
        $("#res").append(
          "<option value='" + res[i].getId() + "'>" + res[i].getName() + "</option>"
        );
      }
    }
  );
}

function doPayment() {
  var sess = wialon.core.Session.getInstance();
  var username = $("#username").val();

  var res = sess.getItems("avl_resource").find(function (item) {
    return item.getName() === username;
  });

  if (!res) {
    msg("User not found");
    return;
  }

  var res_id = res.getId();
  var usr = sess.getCurrUser();
  var is_acc = res.getId() == res.getAccountId();
  var is_it_you = res.getCreatorId() == usr.getId();

  if (!is_acc) {
    msg("Can not do payment: '" + res.getName() + "' is not an account");
    return;
  }

  if (is_it_you) {
    msg("Can not do payment for your account");
    return;
  }

  var payment_amount = 30;
  var payment_count = $("#payment_count").val();

  res.doPayment(
    0,
    payment_amount * payment_count,
    "Paid with glonass",
    function (code) {
      if (code) {
        msg(wialon.core.Errors.getErrorText(code));
        return;
      }

      msg("Payment registered successfully");
      $("#username").val("");
      $("#payment_count").val("");
    }
  );
}


// execute when DOM ready
$(document).ready(function () {
	$("#pay_btn").click(doPayment); // bind action to button click 
    wialon.core.Session.getInstance().initSession("https://sdk.wialon.com"); // init session
    // For more info about how to generate token check
    // http://sdk.wialon.com/playground/demo/app_auth_token
	wialon.core.Session.getInstance().loginToken("12345677899123456778991234567789912345677899", "", // try to login
	    function (code) { // login callback
	    	if (code){ msg(wialon.core.Errors.getErrorText(code)); return; } // exit if error code
	    	msg("Авторизация по токену успешна!"); init(); // when login suceed then run init() function
	});
});

