<?php
use yii\helpers\Html;
 ?>
<style>
@import url('https://fonts.googleapis.com/css?family=Karla:400,700');
</style>

<table style="width: 600px;">
	<tr>
		<th style="background-image:url('https://www.blackrobot.mx/oxford/london.jpg');background-size: 100% auto;padding-top:110px;padding-bottom:110px;">
			<a>
				<?= Html::img('https://www.blackrobot.mx/oxford/logo.png', ['width'=>'100px'])?>

			</a>
		</th>
	</tr>
	<tr>
		<td style="padding:60px; text-align: center; background-color: #fff; font-family: 'Karla'; font-size: 19px;">
			<h2 style="font-family:'Karla';font-size:25px;font-weight:700;color:#cda534;">
				Welcome!
			</h2>
			<br>
				Your account has been approved.<br><br>
                Please enter <a href="https://www.oxfordtcc.co.uk/" style="font-weight:bold;text-decoration:none;color:#000;">www.oxfordtccv2.co.uk</a> and log in with
                this email address and the password you previously chose.
		</td>
	</tr>
</table>
