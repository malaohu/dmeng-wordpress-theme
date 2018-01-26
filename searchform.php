<?php
/*
 * 欢迎来到代码世界，如果你想修改多梦主题的代码，那我猜你是有更好的主意了～
 * 那么请到多梦网络（ http://www.dmeng.net/ ）说说你的想法，数以万计的童鞋们会因此受益哦～
 * 同时，你的名字将出现在多梦主题贡献者名单中，并有一定的积分奖励～
 * 注释和代码同样重要～
 * @author 多梦 @email chihyu@aliyun.com 
 */
 ?>
<form class="input-group" role="search" method="get" id="searchform" action="<?php echo home_url( '/' ); ?>">
	<span class="input-group-addon"><?php _e('搜索','dmeng');?></span>
	<input type="text" class="form-control" placeholder="<?php _e('请输入检索关键词 &hellip;','dmeng');?>" name="s" id="s" required>
	<span class="input-group-btn"><button type="submit" class="btn btn-default" id="searchsubmit"><span class="glyphicon glyphicon-search"></span></button></span>

</form>
