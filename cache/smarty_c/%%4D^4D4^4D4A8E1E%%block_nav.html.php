<?php /* Smarty version 2.6.26, created on 2016-08-01 16:16:34
         compiled from block_nav.html */ ?>
<nav class="navbar my-navbar">
  <div class="container my-container">
    <div class="navbar-header my-logo">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="/">Mcgoldfish</a>
    </div>
    <div id="navbar" class="collapse navbar-collapse my-nav">
      <ul class="nav navbar-nav">
        <li class="active"><a href="#">首页</a></li>
        <li role="presentation" class="nav-cate dropdown">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
             我的类别&nbsp<i class="glyphicon glyphicon-chevron-down"></i>
          </a>
          <!-- <ul class="dropdown-menu">
            <?php $_from = $this->_tpl_vars['primaryCategory']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['item']):
?>
              <li><a href="<?php echo $this->_tpl_vars['item']['requestpath']; ?>
"><?php echo $this->_tpl_vars['item']['displayname']; ?>
</a></li>
            <?php endforeach; endif; unset($_from); ?>
          </ul> -->
        </li>
        <li><a href="#contact">我的标签</a></li>
        <li><a href="#contact">我的说说</a></li>
        <li><a href="#contact">我的相册</a></li>
      </ul>
      <div class="my-search">
        <form class="form-inline" action="/search/" method="get">
            <div class="form-group search-form">
                <i class="glyphicon glyphicon-search my-search-btn"></i>
                <input type="text" class="form-control input-search" name="q" id="keywords" placeholder="搜索">
            </div>
        </form>
    </div>
    </div>
  </div>
</nav>