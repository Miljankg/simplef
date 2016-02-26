<?php /* Smarty version 3.1.27, created on 2016-02-25 23:17:26
         compiled from "C:\xampp\htdocs\simplef\src\app\templates\pages\test_page\test_page.tpl" */ ?>
<?php
/*%%SmartyHeaderCode:635856cf7d7630fc13_32454484%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1564695204db4e8308fd625cdb7171b8aaf152da' => 
    array (
      0 => 'C:\\xampp\\htdocs\\simplef\\src\\app\\templates\\pages\\test_page\\test_page.tpl',
      1 => 1456438640,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '635856cf7d7630fc13_32454484',
  'variables' => 
  array (
    'oc_test' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_56cf7d7633d798_53090289',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_56cf7d7633d798_53090289')) {
function content_56cf7d7633d798_53090289 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '635856cf7d7630fc13_32454484';
?>
TEST_PAGE
<div style="background-color: red;">
    <?php echo $_smarty_tpl->tpl_vars['oc_test']->value;?>

</div><?php }
}
?>