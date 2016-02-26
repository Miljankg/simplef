<?php /* Smarty version 3.1.27, created on 2016-02-21 16:00:01
         compiled from "C:\xampp\htdocs\simplef\simplef\src\app\templates\index\index.tpl" */ ?>
<?php
/*%%SmartyHeaderCode:265156c9d0f1c72063_06125212%%*/
if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd4530abbf93f2abacf2bec5434c0a893c119cd3f' => 
    array (
      0 => 'C:\\xampp\\htdocs\\simplef\\simplef\\src\\app\\templates\\index\\index.tpl',
      1 => 1455538409,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '265156c9d0f1c72063_06125212',
  'variables' => 
  array (
    'header' => 0,
    'mainContent' => 0,
  ),
  'has_nocache_code' => false,
  'version' => '3.1.27',
  'unifunc' => 'content_56c9d0f1c7a446_17319722',
),false);
/*/%%SmartyHeaderCode%%*/
if ($_valid && !is_callable('content_56c9d0f1c7a446_17319722')) {
function content_56c9d0f1c7a446_17319722 ($_smarty_tpl) {

$_smarty_tpl->properties['nocache_hash'] = '265156c9d0f1c72063_06125212';
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