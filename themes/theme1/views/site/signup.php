<link rel="stylesheet" href="<?php echo Yii::app()->theme->baseUrl?>/css/bootstrap.min.css" />
<link rel="stylesheet" href="<?php echo Yii::app()->theme->baseUrl?>/css/bootstrap-responsive.min.css" />
<link rel="stylesheet" href="<?php echo Yii::app()->theme->baseUrl?>/css/matrix-login.css" />
	<div id="loginbox">
			<?php $form = $this->beginWidget('CActiveForm', array(
				'id' => 'signupForm', 
				'enableClientValidation' => true,
				'clientOptions' => array(
					'validateOnSubmit' => true,
			)));?>
			<div class="control-group normal_text"><h3>Feeder</h3></div>
                <div class="control-group">
					<div class="main_input_box">
                    	<span class="add-on bg_lg"><i class="icon-user"></i></span>
                        <?php echo $form->textField($model, 'name', array(
							'placeholder' => 'Full Name'
						)); ?>
					</div>
                    <?php echo $form->error($model, 'name'); ?>
                </div>
                
                <div class="control-group">
					<div class="main_input_box">
                    	<span class="add-on bg_ly"><i class="icon-lock"></i></span>
                        <?php echo $form->textField($model, 'email', array(
							'placeholder' => 'Email ID'
						)); ?>
					</div>
                    <?php echo $form->error($model, 'email'); ?>
                </div>
                
                <div class="control-group">
					<div class="main_input_box">
                    	<span class="add-on bg_ly"><i class="icon-lock"></i></span>
                        <?php echo $form->passwordField($model, 'password', array(
							'placeholder' => 'Password'
						)); ?>
					</div>
                    <?php echo $form->error($model, 'password'); ?>
                </div>
                
                <div class="form-actions">
                    <span class="pull-right">
                   		<?php echo CHtml::submitButton('Signup', array(
							'class' => 'btn btn-success'
						)); ?>
                   	</span>
                </div>
            <?php $this->endWidget(); ?>
	</div>