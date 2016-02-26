<?php /* Smarty version 3.1.27, created on 2016-02-25 21:59:53
         compiled from "C:\xampp\htdocs\simplef\src\app\templates\index\index.tpl" */ ?>
<?php
/*%%SmartyHeaderCode:1126356cf6b499a1cf2_77630240%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2a66541ef9376213323e56260ddb280855f4bb8e' => 
    array (
      0 => 'C:\\xampp\\htdocs\\simplef\\src\\app\\templates\\index\\index.tpl',
      1 => 1456433829,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1126356cf6b499a1cf2_77630240',
  'variables' => 
  array (
    'header' => 0,
    'mainContent' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_56cf6b499a9038_46770779',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_56cf6b499a9038_46770779')) {
function content_56cf6b499a9038_46770779 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '1126356cf6b499a1cf2_77630240';
?>
<html>
    
    <head>
        <?php echo $_smarty_tpl->tpl_vars['header']->value;?>

    </head>
    
    <body>
        <?php echo $_smarty_tpl->tpl_vars['mainContent']->value;?>

    </body>
    
</html>
<?php }
}
?>