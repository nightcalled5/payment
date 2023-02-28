<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Wialon Playground - Do payment</title>
    <script type="text/javascript" src="//code.jquery.com/jquery-latest.min.js"></script>
    <script type="text/javascript" src="//hst-api.wialon.com/wsdk/script/wialon.js"></script>
</head>
<body>
<style>
<style>
  select#res {
    padding: 5px;
    border-radius: 5px;
    border: 1px solid gray;
    margin: 10px 0;
  }

  input#pay_btn {
    padding: 5px 10px;
    border-radius: 5px;
    background-color: dodgerblue;
    color: white;
    border: none;
    margin-left: 10px;
  }

  #log {
    margin: 10px 0;
    padding: 10px;
    border-radius: 5px;
    background-color: lightgray;
  }
</style>
</style>

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

</script>
</body>
</html>
