<?php /* Smarty version 3.1.27, created on 2016-02-26 21:00:44
         compiled from "C:\xampp\htdocs\simplef\src\app\templates\pages\test_page_second\test_page_second.tpl" */ ?>
<?php
/*%%SmartyHeaderCode:1221256d0aeec8ea178_39734237%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '29205b406fa2860a3a0d0076ffdcf49a6e5f9b0f' => 
    array (
      0 => 'C:\\xampp\\htdocs\\simplef\\src\\app\\templates\\pages\\test_page_second\\test_page_second.tpl',
      1 => 1456438644,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1221256d0aeec8ea178_39734237',
  'variables' => 
  array (
    'oc_test' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_56d0aeec91dad2_56986137',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_56d0aeec91dad2_56986137')) {
function content_56d0aeec91dad2_56986137 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '1221256d0aeec8ea178_39734237';
?>
TEST_PAGE
<div style="background-color: red;">
    <?php echo $_smarty_tpl->tpl_vars['oc_test']->value;?>

</div><?php }
}
?>