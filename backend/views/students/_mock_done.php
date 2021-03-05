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
				<?= Html::img('https://www.blackrobot.mx/oxford/logo.png', ['width' => '100px']) ?>

			</a>
		</th>
	</tr>
	<tr>
		<td style="padding:60px; text-align: justify; background-color: #fff; font-family: 'Karla'; font-size: 19px;">
			<h2 style="font-family:'Karla';font-size:25px;font-weight:700;color:#cda534;">
				All students have done their exams
			</h2>
			<br>
				Hello <span style="font-weight:bold;color:#056039;"><?= $instituto->nombre ?></span>!
			<br>
			<br>
				Here are the results from the mock exam that you recently took with us. The levels that you see here are what we suggest each student should continue with for the next step of The Oxford TCC process (the certificate test).
			<br>
				If you have any doubts or queries throughout the entire process, please do not hesitate to contact us. Congratulations and keep up the good work!
		</td>
	</tr>
</table>
