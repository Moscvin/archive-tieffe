<?php $__env->startSection('content'); ?>
<!-- Theme style -->
<div class="container">
	<div class="login-box">
		<div class="login-logo">
			<a href="#" style="color:#FFFFFF"><img src="/img/v_logo.png" alt="logo"></a>
		</div><!-- /.login-logo -->
		<div class="login-box-body">
			<p class="login-box-msg">Effettua il login</p>
			<form method="POST" action="<?php echo e(route('login')); ?>">
				<?php echo e(csrf_field()); ?>

				<div class="form-group<?php echo e($errors->has('email') ? ' has-error' : ''); ?><?php echo e($errors->has('username') ? ' has-error' : ''); ?>">
				<div class="input-group">
					<span class="input-group-addon"><i class="fas fa-user"></i></span>
					<input id="email" type="text" class="form-control" name="email" value="<?php echo e(old('email')); ?>" required autofocus placeholder="username or email">
				</div>
				<?php if($errors->has('email')): ?>
					<span class="help-block">
						<strong><?php echo e($errors->first('email')); ?></strong>
					</span>
				<?php endif; ?>
				<?php if($errors->has('username')): ?>
					<span class="help-block">
					<strong><?php echo e($errors->first('username')); ?></strong>
				</span>
				<?php endif; ?>

				</div> <!-- form group-->
				<div class="form-group<?php echo e($errors->has('password') ? ' has-error' : ''); ?>">
					<div class="input-group">
						<span class="input-group-addon"><i class="fas fa-lock"></i></span>
						<input id="password" type="password" class="form-control" name="password" required placeholder="password">
					</div>
					<?php if($errors->has('password')): ?>
						<span class="help-block">
							<strong><?php echo e($errors->first('password')); ?></strong>
						</span>
					<?php endif; ?>
				</div> <!-- form group-->
				<div class="row">
					<div class="col-xs-8">
						<input type="checkbox" name="remember" <?php echo e(old('remember') ? 'checked' : ''); ?>> Ricordami!
					</div><!-- /.col -->
					<div class="col-xs-4">
						<button type="submit" class="btn btn-primary btn-block btn-flat">Entra</button>
					</div><!-- /.col -->
				</div><!-- row-->
				<a href="<?php echo e(route('first-login')); ?>">Primo accesso</a><br>
				<a href="<?php echo e(route('retrieve-password')); ?>" class="text-center">Reimposta la password</a>
			</form>
		</div> <!-- login-box-body -->
	</div> <!-- login - box-->
</div> <!--container-->

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>