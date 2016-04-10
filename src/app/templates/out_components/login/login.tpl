<form name="login_form" method="post" action="">
    <input type="text" placeholder="Username" name="username" value="{$username}"/><br/><br/>
    <input type="password" placeholder="Password" name="password"/><br/><br/>
    <input type="submit" value="Login" name="login_form_submit"/>
	<input type="hidden" value="{$refererPage}" name="referer_page"/>
</form>
{if isset($message)}
    {$message}
{/if}