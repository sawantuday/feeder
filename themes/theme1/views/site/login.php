<?php $this->pageTitle=Yii::app()->name . ' - Login';?>
<link rel="stylesheet" href="<?php echo Yii::app()->theme->baseUrl?>/css/bootstrap.min.css" />
<link rel="stylesheet" href="<?php echo Yii::app()->theme->baseUrl?>/css/bootstrap-responsive.min.css" />
<link rel="stylesheet" href="<?php echo Yii::app()->theme->baseUrl?>/css/matrix-login.css" />

        <div id="loginbox">
            <?php $form=$this->beginWidget('CActiveForm', array(
				'id'=>'login-form',
				'enableClientValidation'=>true,
				'clientOptions'=>array(
					'validateOnSubmit'=>true,
				),
			)); ?>
			<div class="control-group normal_text"><h3>Feeder</h3></div>
                <div class="control-group">
                        <div class="main_input_box">
                            <span class="add-on bg_lg"><i class="icon-user"></i></span>
                            <?php echo $form->textField($model,'username', array('placeholder'=>'Email')); ?>
                        </div>
                        <?php echo $form->error($model,'username'); ?>
                </div>
                <div class="control-group">
                        <div class="main_input_box">
                            <span class="add-on bg_ly"><i class="icon-lock"></i></span>
                            <?php echo $form->passwordField($model,'password', array('placeholder'=>'Password')); ?>
                        </div>
                        <?php echo $form->error($model,'password'); ?>
                </div>
                <div class="control-group">
                        <div class="main_input_box">
                            <?php echo $form->checkBox($model,'rememberMe'); ?>
                            <?php echo $form->label($model,'rememberMe'); ?>
                        </div>
                </div>
                <div class="form-actions">
                    <span class="pull-left"><a href="#" class="flip-link btn btn-info" id="to-recover">Lost password?</a></span>
                    <span class="pull-right">
                   		<?php echo CHtml::submitButton('Login', array('class'=>'btn btn-success')); ?>
                   	</span>
                </div>
            <?php $this->endWidget(); ?>
            <form id="recoverform" action="#" class="form-vertical">
				<p class="normal_text">Enter your e-mail address below and we will send you instructions how to recover a password.</p>
				
                    <div class="controls">
                        <div class="main_input_box">
                            <span class="add-on bg_lo"><i class="icon-envelope"></i></span><input type="text" placeholder="E-mail address" />
                        </div>
                    </div>
               
                <div class="form-actions">
                    <span class="pull-left"><a href="#" class="flip-link btn btn-success" id="to-login">&laquo; Back to login</a></span>
                    <span class="pull-right"><a class="btn btn-info"/>Reecover</a></span>
                </div>
            </form>
        </div>
        
        <script src="<?php echo Yii::app()->theme->baseUrl?>/js/jquery.min.js"></script>
		<script src="<?php echo Yii::app()->theme->baseUrl?>/js/plugins.js"></script>