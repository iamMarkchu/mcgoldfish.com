<?php /* Smarty version 2.6.26, created on 2016-08-01 16:16:34
         compiled from block_right_area.html */ ?>
<div class="col-xs-6 col-sm-4 sidebar-offcanvas my_right" id="sidebar">
  <div class="panel panel-default my-color">
    <div class="panel-heading">本周排行</div>
    <div class="panel-body">
      <p><a href="#">backend.mcgoldfish.com后台开发日志</a>(500人看过)</p>
      <p><a href="#">在阿里云ubuntu14.04部署nginx+php+mysql开发环境</a>(500人看过)</p>
      <p><a href="#">后台开发计划-6月</a>(500人看过)</p>
      <p><a href="#">mcgoldfish.com网站开发计划</a>(500人看过)</p>
      <p><a href="#">在ubuntu下部署rsync服务实现代码同步</a>(500人看过)</p>
      <p><a href="#">[歌曲推荐]那些背后藏着故事的歌</a>(500人看过)</p>
      <p><a href="#">姑娘，我用你的名字写了一首歌</a>(500人看过)</p>
    </div>
  </div>
  <div class="panel panel-default my-color">
    <div class="panel-heading">热门标签</div>
    <div class="panel-body my-tag">
      <?php $_from = $this->_tpl_vars['hotTagList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['k'] => $this->_tpl_vars['item']):
?>
          <a href="#" class="btn btn-<?php if ($this->_tpl_vars['k']%4 == 0): ?>info
          <?php elseif ($this->_tpl_vars['k']%4 == 1): ?>success
          <?php elseif ($this->_tpl_vars['k']%4 == 2): ?>primary
          <?php elseif ($this->_tpl_vars['k']%4 == 3): ?>warning
          <?php endif; ?>
          ">
            <?php echo $this->_tpl_vars['item']['displayname']; ?>

          </a>
      <?php endforeach; endif; unset($_from); ?>
    </div>
  </div>
  <div class="tabbable tabbable-custom">
    <ul class="nav nav-tabs">
      <li role="presentation" class="active"><a href="#tab_1_1" data-toggle="tab">最新评论</a></li>
      <li role="presentation"><a href="#tab_1_3" data-toggle="tab">最新留言</a></li>
    </ul>
    <div class="tab-content">
      <div class="tab-pane active" id="tab_1_1">
        <p>1</p>
      </div>
      <div class="tab-pane" id="tab_1_2">
        <p>2</p>
      </div>
      <div class="tab-pane" id="tab_1_3">
        <p>3</p>
      </div>
    </div>
  </div>
  <div class="panel panel-default my-color">
    <div class="panel-heading">最新文章</div>
    <div class="panel-body">
      <?php $_from = $this->_tpl_vars['newestArticleList']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
        <p><a href="<?php echo $this->_tpl_vars['item']['requestpath']; ?>
"><?php echo $this->_tpl_vars['item']['title']; ?>
</a>(<?php echo $this->_tpl_vars['item']['addtime']; ?>
)</p>
      <?php endforeach; endif; unset($_from); ?>
    </div>
  </div>
</div>