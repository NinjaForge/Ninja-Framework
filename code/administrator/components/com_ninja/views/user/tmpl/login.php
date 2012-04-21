<? defined( 'KOOWA' ) or die( 'Restricted access' ) ?>

<? @$user = JFactory::getUser() ?>
<? @$type = (!@$user->get('guest')) ? 'logout' : 'login' ?>

<form action="<?= @route('index.php') ?>" method="post" id="<?= @$helper('formid') ?>" >
	<fieldset class="input ninja-form">
	<p id="form-login-username">
		<label for="<?= @$helper('formid', 'username') ?>"><?= @text('COM_NINJA_USERNAME') ?></label><br />
		<input id="<?= @$helper('formid', 'username') ?>" type="text" name="username" class="inputbox" alt="username" size="18" />
	</p>
	<p id="form-login-password">
		<label for="<?= @$helper('formid', 'passwd') ?>"><?= @text('COM_NINJA_PASSWORD') ?></label><br />
		<input id="<?= @$helper('formid', 'passwd') ?>" type="password" name="passwd" class="inputbox" size="18" alt="password" />
	</p>
	<? if(JPluginHelper::isEnabled('system', 'remember')) : ?>
	<p id="form-login-remember">
		<label for="<?= @$helper('formid', 'remember') ?>"><?= @text('COM_NINJA_REMEMBER_ME') ?></label>
		<input id="<?= @$helper('formid', 'remember') ?>" type="checkbox" name="remember" class="inputbox" value="yes" alt="Remember Me" />
	</p>
	<? endif ?>
	<input type="submit" name="Submit" class="button" value="<?= @text('COM_NINJA_LOGIN') ?>" />
	</fieldset>
	<ul>
		<li>
			<a href="<?= @route( 'index.php?option=com_user&view=reset' ) ?>">
			<?= @text('COM_NINJA_FORGOT_YOUR_PASSWORD') ?></a>
		</li>
		<li>
			<a href="<?= @route( 'index.php?option=com_user&view=remind' ) ?>">
			<?= @text('COM_NINJA_FORGOT_YOUR_USERNAME') ?></a>
		</li>
		<? $usersConfig = &JComponentHelper::getParams( 'com_users' ) ?>
		<? if ($usersConfig->get('allowUserRegistration')) : ?>
		<li>
			<a href="<?= @route( 'index.php?option=com_user&view=register' ) ?>">
				<?= @text('COM_NINJA_REGISTER') ?></a>
		</li>
		<? endif ?>
	</ul>

	<input type="hidden" name="option" value="com_user" />
	<input type="hidden" name="task" value="login" />
	
	<? $url = KRequest::url() ?>
	<? $uri = KService::get('koowa:http.url') ?>
	<? $uri->path = $url->path ?>
	<? $uri->query = $url->getQuery(1) ?>
	<input type="hidden" name="return" value="<?= base64_encode($uri) ?>" />
	<?= JHTML::_( 'form.token' ) ?>
</form>