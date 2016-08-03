<?php /* Smarty version 2.6.26, created on 2016-08-01 17:56:53
         compiled from block_comment.html */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'format_article_date', 'block_comment.html', 47, false),)), $this); ?>
<div class="well comment-frame">
    <p class="bold">5人参与,8条评论</p>
    <div class="comment-body clearfix comment-copy">
      <div class="comment-image">
        <img src="http://img.mcgoldfish.com/thumb/575d7121b6b3f.png" alt="" class="img-circle my-image" width="20px">
      </div>
      <div class="comment-text">
        <p>
          <textarea class="form-control my-comment-data" rows="3" placeholder="我有话说...."></textarea>
        </p>
        <form class="form-inline hidden my-comment-form">
          <div class="form-group">
            <label for="exampleInputName2">姓名</label>
            <input type="text" class="form-control my-comment-usernanme" placeholder="Jane Doe">
          </div>
          <div class="form-group">
            <label for="exampleInputEmail2">电子邮件</label>
            <input type="email" class="form-control my-comment-useremail" placeholder="jane.doe@example.com">
          </div>
          <button type="button" class="btn btn-primary btn-sm my-comment-submit">发布</button>
        </form>
      </div>
    </div>
    <p class="bold">评论列表</p>
    <div class="comment-body clearfix small-comment hidden">
      <div class="comment-image">
        <img src="http://img.mcgoldfish.com/thumb/575d7121b6b3f.png" alt="" class="img-circle my-image" width="20px">
      </div>
      <div class="comment-text">
        <p class="comment-user">
          <!-- <i class="glyphicon glyphicon-user"></i>褚魁&nbsp&nbsp -->
          <i class="glyphicon glyphicon-time"></i>&nbsp刚刚&nbsp
          <span class="comment-notice">待审核!</span>
        </p>
        <p class="comment-content"></p>
      </div>
    </div>
    <?php if (is_array ( $this->_tpl_vars['commentList'] ) && count ( $this->_tpl_vars['commentList'] ) > 0): ?>
      <?php $_from = $this->_tpl_vars['commentList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
        <div class="comment-body clearfix small-comment" data-comment-id="<?php echo $this->_tpl_vars['item']['id']; ?>
">
          <div class="comment-image">
            <img src="http://img.mcgoldfish.com/thumb/575d7121b6b3f.png" alt="" class="img-circle my-image" width="20px">
          </div>
          <div class="comment-text">
            <p>
              <i class="glyphicon glyphicon-user"></i><?php echo $this->_tpl_vars['item']['username']; ?>
&nbsp&nbsp
              <i class="glyphicon glyphicon-time"></i>&nbsp<?php echo ((is_array($_tmp=$this->_tpl_vars['item']['addtime'])) ? $this->_run_mod_handler('format_article_date', true, $_tmp) : smarty_modifier_format_article_date($_tmp)); ?>
&nbsp
              <a href="javascript:void(0);" class="btn btn-success hidden btn-xs data-comment-reply">回复</a>
              <a href="#" class="btn btn-success hidden btn-xs">赞(<?php echo $this->_tpl_vars['item']['goodvote']; ?>
)</a>
              <a href="#" class="btn btn-success hidden btn-xs">鄙视(<?php echo $this->_tpl_vars['item']['badvote']; ?>
)</a>
            </p>
            <p>
              <?php echo $this->_tpl_vars['item']['content']; ?>

            </p>
          </div>
        </div>
      <?php endforeach; endif; unset($_from); ?>
    <?php endif; ?>
</div>