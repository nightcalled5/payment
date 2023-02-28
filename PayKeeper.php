<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
  // обработка отправки формы
  $username = $_POST['username'];
  $total = $_POST['total'];

  $payment_parameters = http_build_query(array(
    "clientid" => $username,
    "sum" => $total,
  ));

  $options = array(
    "http" => array(
      "method" => "POST",
      "header" => "Content-type: application/x-www-form-urlencoded",
      "content" => $payment_parameters
    )
  );

  $context = stream_context_create($options);
  $result = file_get_contents("https://glonass38.server.paykeeper.ru/order/inline/", FALSE, $context);
  echo $result;

} else {
  // отображение формы
?>
<form class="my-wialonCSS" method="POST">
<input type="hidden" name="total" id="total_input">
<label for="username">Имя пользователя:</label>
<input type="text" id="username">
<label for="vehicle_count">Кол-во единиц:</label>
<input type="text" id="vehicle_count"  oninput="calculateTotal()"  name="vehicle_count">
<label for="payment_count">Кол-во месяцев:</label>
<input type="text" id="payment_count"  oninput="calculateTotal()"  name="payment_count">
<input type="hidden" name="username" value="<?php echo $username; ?>">
<input type="hidden" name="count" value="<?php echo $count; ?>">
<input type="submit" type="submit" name="submit" value="Оплатить"/>
<div id="log"></div>
<p><span id="total"></span></p>
</form>

<script>
function calculateTotal() {
  var count = parseInt(document.getElementById("vehicle_count").value);
  var month = parseInt(document.getElementById("payment_count").value);
  var total = count * month * 0.1; // вычисляем значение total
  document.getElementById("total").innerHTML = "Итоговая к оплате: " + total + " рублей"; // записываем значение total в поле формы
  document.getElementById("total_input").value = total; // записываем значение total в скрытое поле формы
}
</script>

<?php
}
?>
