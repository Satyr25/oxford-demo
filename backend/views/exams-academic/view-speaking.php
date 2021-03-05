<?php

use backend\assets\PhotoSwipeAsset;
use yii\helpers\Html;
use yii\helpers\Url;

PhotoSwipeAsset::register($this);
?>

<div class="main">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="title">Speaking</h2>
            </div>
        </div>
        <div class="row main-select">
            <div class="col-md-6">
                <div class="form-group">
                    <?= Html::dropDownList('level', null, $levels, ['class' => 'form-control', 'id' => 'select-level']) ?>
                </div>
            </div>
            <div class="col-md-6">
                <button type="button" class="btn-oxford boton-peque" id="start-speaking">Start</button>
            </div>
        </div>
    </div>
</div>
<?php foreach ($levels as $level): ?>
<div class="gallery <?= $level ?> hidden">
    <?php foreach (array_slice(scandir("../web/images/speaking/{$level}"), 2) as $file):
        list($width, $height) = getimagesize("../web/images/speaking/{$level}/{$file}");
    ?>
        <?= Html::a('', Url::to("@web/images/speaking/{$level}/{$file}", true), ['data' => ['width' => $width, 'height' => $height]]) ?>
    <?php endforeach; ?>
</div>
<?php endforeach; ?>
<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="pswp__bg"></div>
    <div class="pswp__scroll-wrap">
    <div class="pswp__container">
        <div class="pswp__item"></div>
        <div class="pswp__item"></div>
        <div class="pswp__item"></div>
    </div>
    <div class="pswp__ui pswp__ui--hidden">
            <div class="pswp__top-bar">
                <div class="pswp__counter"></div>
                <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>
                <button class="pswp__button pswp__button--share" title="Share"></button>
                <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>
                <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>
                <div class="pswp__preloader">
                    <div class="pswp__preloader__icn">
                      <div class="pswp__preloader__cut">
                        <div class="pswp__preloader__donut"></div>
                      </div>
                    </div>
                </div>
            </div>
            <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
                <div class="pswp__share-tooltip"></div>
            </div>
            <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)">
            </button>
            <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)">
            </button>
            <div class="pswp__caption">
                <div class="pswp__caption__center"></div>
            </div>
        </div>
    </div>
</div>