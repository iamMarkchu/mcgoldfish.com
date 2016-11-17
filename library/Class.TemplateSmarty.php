<?php
class TemplateSmarty extends Smarty 
{
	function TemplateSmarty($project="",$tpl_dir="")
	{
		if($tpl_dir && substr($tpl_dir,0,1) != "/") $tpl_dir = INCLUDE_ROOT . $tpl_dir;
		$this->template_dir = $tpl_dir ? $tpl_dir : INCLUDE_ROOT . 'view';
		$this->compile_dir  = INCLUDE_ROOT . 'storage/smarty_c/' . $project;
		$this->cache_dir  = INCLUDE_ROOT . 'storage/smarty_cache/' . $project;

		if(!is_dir($this->compile_dir)) {
			$this->mkdir_and_chmod($this->compile_dir);
		}
		$this->left_delimiter = '<{';
		$this->right_delimiter = '}>';
		if($project == "" && defined("DEBUG_MODE") && DEBUG_MODE == false)
		{
			//for production, we dont check the new tpl, and always use compiled file.
			$this->compile_check = false;
		}
		
		//cache open
		$this->caching = false;
		if($this->caching)
		{
			if(!is_dir($this->cache_dir)) $this->mkdir_and_chmod($this->cache_dir);
		}
		parent::__construct();
	}
	
	function mkdir_and_chmod($_dir)
	{
		@mkdir($_dir,0777,true);
		@chmod($_dir,0777);
	}
	public function display($template = null, $cache_id = null, $compile_id = null, $parent = null)
    {
    	$navList = [
    				[
    				  'displayname' => '代码',
    				  'requestpath' => '/code.html'
    				],
    				[
    				  'displayname' => '工作',
    				  'requestpath' => '/work.html'
    				],
    				[
    				  'displayname' => '游戏',
    				  'requestpath' => '/game.html'
    				],
    				[
    				  'displayname' => '电影',
    				  'requestpath' => '/movie.html'
    				],
    				[
    				  'displayname' => '音乐',
    				  'requestpath' => '/music.html'
    				],
    			   ];
        $sql = "SELECT * FROM `site_config`";
        $result = $GLOBALS['db']->getRows($sql);
        foreach ($result as $k => $v) {
        	$siteConfig[$v['Key']] = $v['Value'];
        }
    	parent::assign("navList", $navList);
    	parent::assign("siteConfig", $siteConfig);
    	parent::assign("weiboUrl", init_weibo_url());
        parent::display($template);
    }
}

