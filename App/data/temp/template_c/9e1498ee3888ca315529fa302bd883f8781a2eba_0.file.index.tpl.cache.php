<?php
/* Smarty version 3.1.30, created on 2017-12-07 10:10:27
  from "E:\phpStudy\WWW\markphp\App\web\template\index.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_5a28a313228739_95045927',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '9e1498ee3888ca315529fa302bd883f8781a2eba' => 
    array (
      0 => 'E:\\phpStudy\\WWW\\markphp\\App\\web\\template\\index.tpl',
      1 => 1503644074,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a28a313228739_95045927 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->compiled->nocache_hash = '80705a28a31319cfc6_67359476';
?>
<form action="/index.php?c=index&a=testUpload" method="post" enctype="multipart/form-data">
    <input type="file" name="test"/>
    <input type="submit" value="sub">

</form><?php }
}
