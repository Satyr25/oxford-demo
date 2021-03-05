<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\Pjax;

$this->title = "Summary";
?>

<div class="container">
    <h2>Summary</h2>
    <?php Pjax::begin() ?>
        <?php $form = ActiveForm::begin([
            'method' => 'get',
            'action' => Url::to(['summary/index']),
            'options' => [
                'data' => [
                    'pjax' => ''
                ]
            ]
        ]) ?>
            <div class="row">
                <div class="col-md-3">
                    <?= $form->field($searchModel, 'examType')->dropDownList($examTypes, ['prompt' => 'Select'])?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($searchModel, 'schoolYear')->dropDownList($schoolYears, ['prompt' => 'Select'])?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <?= $form->field($searchModel, 'program')->inline(true)->checkboxList($programs, ['prompt' => 'Select']) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <?= $form->field($searchModel, 'instituteStatus')->inline(true)->checkboxList(['1' => 'Activo', '2' => 'Cancelado'], ['prompt' => 'Select']) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-center">
                    <button type="submit" class="btn-oxford boton-peque list-inline">Filter</button>
                    <a class="btn-oxford boton-peque list-inline"
                    href="<?= Url::to(
                        [
                            'summary/export',
                            "InstituteSummarySearch[program]" => $searchModel->program,
                            "InstituteSummarySearch[examType]" => $searchModel->examType,
                            "InstituteSummarySearch[schoolYear]" => $searchModel->schoolYear,
                            "InstituteSummarySearch[instituteStatus]" => $searchModel->instituteStatus,
                        ]
                    ) ?>"
                    data-pjax="0">
                        Export
                    </a>
                </div>
            </div>
        <?php ActiveForm::end() ?>
        <table class="table summary-table">
            <tbody>
                <tr class="row">
                    <td class="col md-3">
                        <a href="<?= Url::to(
                            [
                                'summary/details',
                                "InstituteSummarySearch[program]" => $searchModel->program,
                                "InstituteSummarySearch[examType]" => $searchModel->examType,
                                "InstituteSummarySearch[schoolYear]" => $searchModel->schoolYear,
                                "InstituteSummarySearch[instituteStatus]" => $searchModel->instituteStatus,
                            ]
                        ) ?>"
                        data-pjax="0">
                            Total de colegios
                        </a>
                    </td>
                    <td class="col md-3"><?= $totalInstitutes ?></td>
                    <td class="col md-3">Total de alumnos</td>
                    <td class="col md-3"><?= $totalStudents ?></td>
                </tr>
                <tr class="row">
                <td class="col md-3">
                    <a href="<?= Url::to(
                        [
                            'summary/details',
                            "InstituteSummarySearch[examsStarted]" => 'started',
                            "InstituteSummarySearch[program]" => $searchModel->program,
                            "InstituteSummarySearch[examType]" => $searchModel->examType,
                            "InstituteSummarySearch[schoolYear]" => $searchModel->schoolYear,
                            "InstituteSummarySearch[instituteStatus]" => $searchModel->instituteStatus,
                        ]
                    ) ?>"
                    data-pjax="0">
                        Total de colegios realizando
                    </a>
                </td>
                <td class="col md-3"><?= $startedInstitutes ?></td>
                <td class="col md-3">Total de alumnos realizando</td>
                <td class="col md-3"><?= $startedStudents ?></td>
            </tr>
            <tr class="row">
                <td class="col md-3">
                    <a href="
                    <?= Url::to(
                        [
                            'summary/details',
                            "InstituteSummarySearch[examsStarted]" => 'finished',
                            "InstituteSummarySearch[program]" => $searchModel->program,
                            "InstituteSummarySearch[examType]" => $searchModel->examType,
                            "InstituteSummarySearch[schoolYear]" => $searchModel->schoolYear,
                            "InstituteSummarySearch[instituteStatus]" => $searchModel->instituteStatus,
                        ]
                    ) ?>"
                    data-pjax="0">
                        Total de colegios terminados
                    </a>
                </td>
                <td class="col md-3"><?= $finishedInstitutes ?></td>
                <td class="col md-3">Total de alumnos terminados</td>
                <td class="col md-3"><?= $finishedStudents ?></td>
            </tr>
            <tr class="row">
                <td class="col md-3">
                    <a href="<?= Url::to(
                        [
                            'summary/details',
                            "InstituteSummarySearch[examsStarted]" => 'not-started',
                            "InstituteSummarySearch[program]" => $searchModel->program,
                            "InstituteSummarySearch[examType]" => $searchModel->examType,
                            "InstituteSummarySearch[schoolYear]" => $searchModel->schoolYear,
                            "InstituteSummarySearch[instituteStatus]" => $searchModel->instituteStatus,
                        ]
                    ) ?>"
                    data-pjax="0">
                        Total de colegios no realizando
                    </a>
                </td>
                <td class="col md-3"><?= $notStartedInstitutes ?></td>
                <td class="col md-3">Total de alumnos no realizando</td>
                <td class="col md-3"><?= $notStartedStudents ?></td>
            </tr>
            <tr class="row">
                <td class="col md-3">
                    <a href="<?= Url::to(
                        [
                            'summary/details',
                            "InstituteSummarySearch[examsStarted]" => 'remaining',
                            "InstituteSummarySearch[program]" => $searchModel->program,
                            "InstituteSummarySearch[examType]" => $searchModel->examType,
                            "InstituteSummarySearch[schoolYear]" => $searchModel->schoolYear,
                            "InstituteSummarySearch[instituteStatus]" => $searchModel->instituteStatus,
                        ]
                    ) ?>"
                    data-pjax="0">
                        Total de colegios restantes
                    </a>
                </td>
                <td class="col md-3"><?= $totalInstitutes - ($startedInstitutes + $finishedInstitutes + $notStartedInstitutes) ?></td>
                <td class="col md-3">Total de alumnos restantes</td>
                <td class="col md-3"><?= $totalStudents - ($startedStudents + $finishedStudents + $notStartedStudents) ?></td>
            </tr>
            </tbody>
        </table>
    <?php Pjax::end() ?>
</div>