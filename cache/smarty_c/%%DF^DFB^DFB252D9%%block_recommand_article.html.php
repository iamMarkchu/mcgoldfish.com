<?php /* Smarty version 2.6.26, created on 2016-08-02 17:38:47
         compiled from block_recommand_article.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'format_article_date', 'block_recommand_article.html', 12, false),)), $this); ?>
<?php $_from = $this->_tpl_vars['recommandArticleList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
  <div class="col-xs-12 col-lg-12 my-recommand-block">
    <a href="<?php echo $this->_tpl_vars['item']['requestpath']; ?>
" class="my-img-link">
        <img src="<?php echo @IMG_URL; ?>
<?php echo $this->_tpl_vars['item']['image']; ?>
" class="thumbnail" alt="">
    </a>
    <h4><a href="<?php echo $this->_tpl_vars['item']['requestpath']; ?>
"><!-- <i class="glyphicon glyphicon-music"> --></i> <?php echo $this->_tpl_vars['item']['title']; ?>
</a></h4>
    <p class="my-article-desc less">
      <?php echo $this->_tpl_vars['item']['shortDesc']; ?>

    </p>
    <p class="user-message">
      <i class="glyphicon glyphicon-user"></i><?php echo $this->_tpl_vars['item']['addeditor']; ?>
&nbsp;&nbsp;
      <i class="glyphicon glyphicon-time"></i><?php echo ((is_array($_tmp=$this->_tpl_vars['item']['addtime'])) ? $this->_run_mod_handler('format_article_date', true, $_tmp) : smarty_modifier_format_article_date($_tmp)); ?>
&nbsp;&nbsp;
      <?php if (is_array ( $this->_tpl_vars['item']['tagInfo'] ) && count ( $this->_tpl_vars['item']['tagInfo'] ) > 0): ?>
        <i class="glyphicon glyphicon-tag"></i>
        <?php $_from = $this->_tpl_vars['item']['tagInfo']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item1']):
?>
          <a href="<?php echo $this->_tpl_vars['item1']['requestpath']; ?>
"><?php echo $this->_tpl_vars['item1']['displayname']; ?>
</a>
        <?php endforeach; endif; unset($_from); ?>
      <?php endif; ?>
    </p>
  </div>
<?php endforeach; endif; unset($_from); ?>