<form name="login_form" method="post" action="">
    <input type="text" placeholder="Username" name="username" value="{$username}"/><br/>
    <input type="password" placeholder="Password" name="password"/><br/>
    <input type="submit" value="Login" name="login_form_submit"/>
</form>
{if isset($message)}
    {$message}
{/if}