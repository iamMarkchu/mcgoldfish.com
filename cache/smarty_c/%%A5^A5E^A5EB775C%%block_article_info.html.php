<?php /* Smarty version 2.6.26, created on 2016-08-01 17:56:53
         compiled from block_article_info.html */ ?>
<div class="well">
  <p><strong>非特殊说明，本文版权归原作者所有，转载请注明出处</strong></p>
  <p>本文地址：<a href="#"><?php echo @TOP_HTTP_LEVEL_DOMAIN_NAME; ?>
<?php echo $this->_tpl_vars['articleInfo']['requestpath']; ?>
</a></p>
  <?php if ($this->_tpl_vars['articleInfo']['tagInfo'] != ''): ?>
    <p>本文标签：
      <?php $_from = $this->_tpl_vars['articleInfo']['tagInfo']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item1']):
?>
        <a href="<?php echo $this->_tpl_vars['item1']['requestpath']; ?>
"><?php echo $this->_tpl_vars['item1']['displayname']; ?>
</a>
      <?php endforeach; endif; unset($_from); ?>
    </p>
  <?php endif; ?>
  <?php $_smarty_tpl_vars = $this->_tpl_vars;
$this->_smarty_include(array('smarty_include_tpl_file' => "block_share.html", 'smarty_include_vars' => array()));
$this->_tpl_vars = $_smarty_tpl_vars;
unset($_smarty_tpl_vars);
 ?>
</div>