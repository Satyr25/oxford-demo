<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use kartik\depdrop\DepDrop;
?>
<div id="profile" class="<?= $model->email || $model->codigo ? 'visible' : '' ?>">
    <div id="close-profile">
        <a href="javascript:;" class="perfil-switch">X</a>
    </div>
    <div id="profile-title">
        MY ACCOUNT / LOGIN
    </div>
    <div id="login-type">
        <div class="row">
            <div class="col-md-2 col-sm-2 col-xs-2 text-center">
                <a href="https://www.youtube.com/watch?v=056uh-FGVqg" class="help-button"><?php echo Html::img('@web/images/help.png') ?></a>
            </div>
            <div class="col-md-5 col-sm-5 col-xs-5">
                <button type="button" class="select-login-btn selected" data-tipo="INS">Institute</button>
            </div>
            <div class="col-md-5 col-sm-5 col-xs-5">
                <button type="button" class="select-login-btn" data-tipo="STU">Student</button>
            </div>
        </div>
    </div>
    <div id="institute-login" class="login-form">
        <div id='institute-form' class="<?= !$model->email && $model->codigo ? 'oculto' : '' ?>">
            <?php if (Yii::$app->user->isGuest) { ?>
                <?php $form = ActiveForm::begin(); ?>
                    <div class="row">
                        <div class="col-md-12">
                            <?= $form->field($model, 'email')->textInput(['placeholder' => 'Email Address'])->label(false) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <?= $form->field($model, 'password', [
                                'template' => '{label}{input}<span class="show-pass glyphicon glyphicon-eye-open" data-showing="0"></span>{hint}{error}'
                            ])
                                ->passwordInput(['placeholder' => 'Password'])
                                ->label(false)
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <?= Html::submitButton('Login', ['class' => 'btn-oxford', 'name' => 'login-button']) ?>
                            </div>
                        </div>
                    </div>
                <?php ActiveForm::end(); ?>
            <?php } else { ?>
                <p>You are already logged in, you need to log out before logging in as different user.</p>
                <a class="btn-oxford" href="<?php echo Url::to(['site/logout']); ?>">Logout</a>
            <?php } ?>
        </div>
        <div id='student-form' class="<?= ($model->email && !$model->codigo) || (!$model->email && !$model->codigo) ? 'oculto' : '' ?>">
            <?php if (Yii::$app->user->isGuest) { ?>
                <?php $form = ActiveForm::begin(); ?>
                <div class="row">
                        <div class="col-md-12">
                            <?= $form->field($model, 'codigo')->textInput(['placeholder' => 'Code'])->label(false) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <?= $form->field($model, 'password', [
                                'template' => '{label}{input}<span class="show-pass glyphicon glyphicon-eye-open" data-showing="0"></span>{hint}{error}'
                            ])
                                ->passwordInput(['placeholder' => 'Password'])
                                ->label(false)
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <?= Html::submitButton('Login', ['class' => 'btn-oxford', 'name' => 'login-button']) ?>
                            </div>
                        </div>
                    </div>
                <?php ActiveForm::end(); ?>
            <?php } else { ?>
                <p>You are already logged in, you need to log out before logging in as different user.</p>
                <a class="btn-oxford" href="<?php echo Url::to(['site/logout']); ?>">Logout</a>
            <?php } ?>
        </div>
    </div>
    <div id="recover-password">
        <a href="javascript:;" id="recover">FORGOT YOUR ACCOUNT?</a>
        <div id="password-form" style="display:none;">
            <?php $formPassword = ActiveForm::begin(); ?>
            <?= $formPassword->field($passwordForm, 'email')->textInput(['placeholder' => 'Email Address'])->label(false) ?>
            <div class="form-group">
                <?= Html::submitButton('Recover', ['class' => 'btn-oxford', 'name' => 'password-button']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <div id="institute-signup">
            <div class="col-md-2">
                <a href="https://www.youtube.com/watch?v=mQzp6dVQE7c" class="help-button"><?php echo Html::img('@web/images/help.png') ?></a>
            </div>
            <div id="signup-here" class="col-lg-10">
            Not yet an OTCC site member?
            <a href="javascript:;">Sign Up Here</a>
        </div>

        <div class="login-form" id="campos-registro" style="display:none;">
            <?php $form = ActiveForm::begin(['id'=>'direccion-edit-form']); ?>

            <div class="instituto-fields">
                <div class="col-md-12">
                    <?= $form-> field($institutoForm,'nombre')->textInput(['required'=>'true','placeholder' => 'Institute Name *'])->label(false) ?>
                </div>
                <div class="col-md-12">
                    <?= $form-> field($institutoForm,'email')->textInput(['type'=>'email','required'=>'true', 'placeholder' => 'Email'])->label(false) ?>
                </div>
                <div class="col-md-12">
                    <?= $form-> field($institutoForm,'password')->passwordInput(['placeholder' => 'Password'])->label(false) ?>
                </div>
                <div class="col-md-12">
                    <?= $form-> field($institutoForm,'password_confirm')->passwordInput(['placeholder' => 'Confirm Password'])->label(false) ?>
                </div>
                <div class="col-md-12">
                    <?= $form-> field($institutoForm,'telefono')->textInput(['required'=>'true', 'placeholder' => 'Phone No.'])->label(false) ?>
                </div>
                <div class="col-md-12">
                    <?= $form-> field($institutoForm,'calle')->textInput(['placeholder' => 'Street Address'])->label(false) ?>
                </div>
                <div class="col-md-12">
                    <?= $form-> field($institutoForm,'numero_ext')->textInput(['placeholder' => 'Ext. No.'])->label(false) ?>
                </div>
                <div class="col-md-12">
                    <?= $form-> field($institutoForm,'numero_int')->textInput(['placeholder' => 'Int. No.'])->label(false) ?>
                </div>
                <div class="col-md-12">
                    <?= $form-> field($institutoForm,'colonia')->textInput(['placeholder' => 'District'])->label(false) ?>
                </div>
                <div class="col-md-12">
                    <?= $form-> field($institutoForm,'municipio')->textInput(['placeholder' => 'Municipality'])->label(false)?>
                </div>
                <div class="col-md-12">
                    <?= $form-> field($institutoForm,'ciudad')->textInput(['placeholder' => 'City'])->label(false)?>
                </div>
                <div class="col-md-12">
                    <?= $form-> field($institutoForm,'pais')->dropDownList($paises, ['prompt'=>'Country', 'id'=>'paises-drop'])->label(false)?>
                </div>
                <div class="col-md-12">
                    <?php echo $form->field($institutoForm, 'estado')->widget(DepDrop::classname(), [
                        'pluginOptions' => [
                            'depends' => ['paises-drop'],
                            'placeholder' => 'Select...',
                            'url' => Url::to(['/home/subpais'])
                        ]
                    ])->label(false); ?>
                </div>
                <div class="col-md-12">
                    <?= $form-> field($institutoForm,'codigo_postal')->textInput(['placeholder' => 'Zip Code'])->label(false)?>
                </div>
                <div class="col-md-12">
                    <?= $form-> field($institutoForm,'nombre_contacto')->textInput(['required'=>'true', 'placeholder' => 'Contact Name'])->label(false)?>
                </div>
                <div class="col-md-12">
                    <?= $form-> field($institutoForm,'email_contacto')->textInput(['type'=>'email','required'=>'true', 'placeholder' => 'Contact Email'])->label(false)?>
                </div>

                <div class="form-group col-md-12" id="div-guardar">
                    <?= Html::submitButton('Signup', ['class' => 'btn-oxford', 'name' => 'signup-button']) ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
