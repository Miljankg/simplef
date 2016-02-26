<?php /* Smarty version 3.1.27, created on 2016-02-23 15:07:26
         compiled from "C:\xampp\htdocs\simplef\simplef\src\app\templates\out_components\login\login.tpl" */ ?>
<?php
/*%%SmartyHeaderCode:2002256cc679e81f8b0_89331801%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'acc452330859aba68db146fe2c6b21892b4caaed' => 
    array (
      0 => 'C:\\xampp\\htdocs\\simplef\\simplef\\src\\app\\templates\\out_components\\login\\login.tpl',
      1 => 1456236437,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2002256cc679e81f8b0_89331801',
  'variables' => 
  array (
    'username' => 0,
    'message' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_56cc679e984807_98474987',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_56cc679e984807_98474987')) {
function content_56cc679e984807_98474987 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '2002256cc679e81f8b0_89331801';
?>
<form name="login_form" method="post" action="">
    <input type="text" placeholder="Username" name="username" value="<?php echo $_smarty_tpl->tpl_vars['username']->value;?>
"/><br/>
    <input type="password" placeholder="Password" name="password"/><br/>
    <input type="submit" value="Login" name="login_form_submit"/>
</form>
<?php if (isset($_smarty_tpl->tpl_vars['message']->value)) {?>
    <?php echo $_smarty_tpl->tpl_vars['message']->value;?>

<?php }
}
}
?>