<?php /* Smarty version 3.1.27, created on 2016-02-21 16:00:47
         compiled from "C:\xampp\htdocs\simplef\simplef\src\app\templates\pages\test_page\test_page.tpl" */ ?>
<?php
/*%%SmartyHeaderCode:3178256c9d11f6493f1_92765049%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3a38b40ae380aecc1a4b94047df10ea3c4a60ca7' => 
    array (
      0 => 'C:\\xampp\\htdocs\\simplef\\simplef\\src\\app\\templates\\pages\\test_page\\test_page.tpl',
      1 => 1455980375,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3178256c9d11f6493f1_92765049',
  'variables' => 
  array (
    'test' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_56c9d11f66ed29_14724143',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_56c9d11f66ed29_14724143')) {
function content_56c9d11f66ed29_14724143 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '3178256c9d11f6493f1_92765049';
?>
TEST_PAGE
<div style="background-color: red;">
    <?php echo $_smarty_tpl->tpl_vars['test']->value;?>

</div><?php }
}
?>