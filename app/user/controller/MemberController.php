<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Powerless < wzxaini9@gmail.com>
// +----------------------------------------------------------------------
namespace app\user\controller;

use app\user\model\UserExtensionModel;
use cmf\controller\UserBaseController;
use think\Loader;
use think\Session;
use think\Validate;
use cmf\controller\HomeBaseController;
use app\user\model\UserModel;
use app\tools\controller\AjaxController;
use app\user\model\UserTokenModel;

class MemberController extends UserBaseController
{


	/**
	 *获取用户个人信息
	 *
	 * @author 张俊
	 * @return \think\response\Json
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 *
	 */
	public function getMemberData()
	{

		//实例化ajax工具
		$ajaxTools = new AjaxController();

		//实例化用户扩展表模型
		$userExtensionModel = new UserExtensionModel();

		//从session 中获取用户ID
		$userId = Session('user.id');

		//根据用户ID 查询扩展信息
		$userExtensionData = $userExtensionModel->getUserExtension($userId);

		//拼装数据
		$data = [
			'id'            => $userId,
			'avatar'        => Session('user.avatar'),
			'user_nickname' => Session('user.user_nickname'),
			'user_name'     => Session('user.user_login'),
			'user_email'    => Session('user.user_email'),
			'mobile'        => Session('user.mobile'),
			'microblog'     => $userExtensionData['weibo'],
			'user_truename' => $userExtensionData['user_truename'],
			'qq'            => $userExtensionData['qq'],
		];

		//返回数据
		$info = $ajaxTools->ajaxEcho($data, '成功获取用户信息', 1);
		return $info;
	}


	/**
	 * 编辑用户信息
	 * 接口地址：user/Member/setMemberData
	 * 参数：
	 *      nickname用户昵称
	 *      weibo用户微博
	 *      mailbox用户邮箱
	 *      name用户名称（真实姓名）
	 *      weChat用户微信
	 *      phone用户手机号码
	 *      qq用户QQ
	 */
	public function setMemberData()
	{
		//实例化ajax工具
		$ajaxTools = new AjaxController();

		//实例化模型
		$userModel          = new UserModel();
		$userExtensionModel = new UserExtensionModel();

		//获取用户id
		$userId = cmf_get_current_user_id();
		
		//将需要修改的数据拼装成数组
		$userData          = [
			'user_email'    => $this->request->post('mailbox'),
			'mobile'        => $this->request->post('phone'),
			'user_nickname' => $this->request->post('nickname'),
		];
		$userExtensionData = [
			'user_truename' => $this->request->post('name'),
			'weibo'         => $this->request->post('weibo'),
			'wechat'        => $this->request->post('mailbox'),
			'qq'            => $this->request->post('mailbox'),
		];

		//修改数据库中的数据
		$userExtensionRes = $userExtensionModel->setUserExtension($userId, $userExtensionData);
		$userUserRes      = $userModel->setUser($userId, $userData);

		//判断是否修改成功
		if ($userExtensionRes && $userUserRes) {
			$info = $ajaxTools->ajaxEcho(null, '成功', 1);
			return $info;
		} else {
			$info = $ajaxTools->ajaxEcho(null, '失败', 0);
			return $info;
		}

	}

}
