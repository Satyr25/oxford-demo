<?php
use yii\helpers\Url;
$this->title = 'Speaking';
?>

<div class="new-speaking">
    <div class="container">
        <div class="row title-page">
            <div class="col-md-12">
                <span class="title">Score Speaking</span>
            </div>
        </div>
        <div class="row institute-display">
            <div class="col-md-8">
                <span><?= $institute->nombre ?></span>
                <span> - </span>
                <span><?= $ciclo->nombre ?></span>
                <a class="clear-link" href="<?= Url::to(['score-exam/clear-institute-speaking']) ?>">
                    CLEAR INSTITUTE <span class="glyphicon glyphicon-trash"></span>
                </a>
            </div>
            <div class="col-md-4 timer speaking text-right">
                <input type="hidden" class="spent-seconds" value="0">
                <p class="timer-text hidden"><span class="minutes">00</span>:<span class="seconds">00</span></p>
                <button type="button" class="start-timer btn-oxford boton-peque" data-timer="speaking">Start Timer</button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6" id='student-1'>
                <?= $this->render('_student-speaking-table', [
                    'instituteStudents' => $instituteStudents,
                    'studentNumber' => 1,
                    'speakingModel' => $speakingModel
                ]) ?>
            </div>
            <div class="col-md-6" id='student-2'>
                <?= $this->render('_student-speaking-table', [
                    'instituteStudents' => $instituteStudents,
                    'studentNumber' => 2,
                    'speakingModel' => $speakingModel
                ]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <button type="button" class="pull-right btn-oxford boton-peque" id="submit-speaking-forms">Continue</button>
            </div>
        </div>
    </div>
</div>
<div class="background-observations hidden"></div>

<div id="score-confirmation" class="mfp-hide white-popup-block">
    <div class="lds-dual-ring oculto"></div>
    <div class="contenido">
        <div id="message"></div>
        <div id="student-grades-speaking">
            <div id="student-1" class="student">
                <div class="student-name"></div>
                <div class="student-grade"></div>
            </div>
            <div id="student-2" class="student">
                <div class="student-name"></div>
                <div class="student-grade"></div>
            </div>
            <div class="clear"></div>
        </div>
        <div id="buttons">
            <a id="review-speaking" href="javascript:;" class="btn-oxford boton-peque">Review</a>
            <a id="end-speaking" href="javascript:;" class="btn-oxford boton-peque">Continue</a>
        </div>
    </div>
</div>
