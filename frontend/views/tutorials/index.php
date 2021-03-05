<?php
use yii\helpers\Html;
?>

<div id="tutorials">
    <div class="container">
        <h1>TUTORIALS</h1>
        <div id="videos">
            <div class="video">
                <iframe width="100%" height="auto" src="https://www.youtube.com/embed/mQzp6dVQE7c" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
            <div class="video">
                <iframe width="100%" height="auto" src="https://www.youtube.com/embed/056uh-FGVqg" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
            <div class="video">
                <iframe width="100%" height="auto" src="https://www.youtube.com/embed/WpRL9XHKbFE" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
            <div class="video">
                <iframe width="100%" height="auto" src="https://www.youtube.com/embed/Ynvf1C0z1as" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
            <div class="clear"></div>
        </div>
    </div>
</div>

<?= $this->render('_profile', [
    'model' => $model,
    'institutoForm' => $institutoForm,
    'passwordForm' => $passwordForm,
    'paises'=>$paises,
]) ?>
