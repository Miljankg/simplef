<?php /* Smarty version 3.1.27, created on 2016-02-26 20:59:42
         compiled from "C:\xampp\htdocs\simplef\src\app\templates\out_components\login\login.tpl" */ ?>
<?php
/*%%SmartyHeaderCode:1610156d0aeaeb80cb1_86852398%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c37b7af08db8aa2770da87c3ea7cc7ee59e66dce' => 
    array (
      0 => 'C:\\xampp\\htdocs\\simplef\\src\\app\\templates\\out_components\\login\\login.tpl',
      1 => 1456236437,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1610156d0aeaeb80cb1_86852398',
  'variables' => 
  array (
    'username' => 0,
    'message' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_56d0aeaed354d6_08970283',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_56d0aeaed354d6_08970283')) {
function content_56d0aeaed354d6_08970283 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '1610156d0aeaeb80cb1_86852398';
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