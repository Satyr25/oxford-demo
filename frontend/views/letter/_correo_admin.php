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
				A new institute signed up in Oxford TCC
			</h2>
			<br>
				<span style="font-weight:bold;color:#056039;"><?= $instituto ?></span> just signed up in Oxford TCC.
		</td>
	</tr>
</table>
