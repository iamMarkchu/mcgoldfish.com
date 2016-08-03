<?php /* Smarty version 2.6.26, created on 2016-08-01 17:56:53
         compiled from block_breadcrumb.html */ ?>
<ol class="breadcrumb">
  <?php $_from = $this->_tpl_vars['breadcrumb']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['item']):
?>
  	<li>
  		<?php if ($this->_tpl_vars['item']['url'] != ''): ?>
  			<a href="<?php echo $this->_tpl_vars['item']['url']; ?>
"><?php echo $this->_tpl_vars['item']['title']; ?>
</a>
  		<?php else: ?>
  			<?php echo $this->_tpl_vars['item']['title']; ?>

  		<?php endif; ?>
  	</li>
  <?php endforeach; endif; unset($_from); ?>
</ol>