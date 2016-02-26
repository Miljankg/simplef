<?php /* Smarty version 3.1.27, created on 2016-02-21 16:00:01
         compiled from "C:\xampp\htdocs\simplef\simplef\src\app\templates\pages\test_page_second\test_page_second.tpl" */ ?>
<?php
/*%%SmartyHeaderCode:1777056c9d0f1bf7864_52163251%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '519423e1adb408382f2ad365989933b887a19c52' => 
    array (
      0 => 'C:\\xampp\\htdocs\\simplef\\simplef\\src\\app\\templates\\pages\\test_page_second\\test_page_second.tpl',
      1 => 1455980375,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1777056c9d0f1bf7864_52163251',
  'variables' => 
  array (
    'test' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_56c9d0f1c40c13_33090165',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_56c9d0f1c40c13_33090165')) {
function content_56c9d0f1c40c13_33090165 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '1777056c9d0f1bf7864_52163251';
?>
TEST_PAGE
<div style="background-color: red;">
    <?php echo $_smarty_tpl->tpl_vars['test']->value;?>

</div><?php }
}
?>