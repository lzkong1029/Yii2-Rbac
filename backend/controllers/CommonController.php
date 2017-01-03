<?php
/**
 * User: lzkong1029
 * Email: 272067516@qq.com
 * Date: 2016-7-28
 * Time: 9:05
 * Description:基础控制器
 */

namespace backend\controllers;
use yii\web\Controller;
use Yii;

class CommonController extends Controller{
    /**
     * 在程序执行之前，对访问的方法进行权限验证.
     * @param \yii\base\Action $action
     * @return bool
     * @throws ForbiddenHttpException
     */
    public function beforeAction($action)
    {
        /*如果未登录，则直接返回*/
        if(Yii::$app->user->isGuest){
            return $this->goHome();
        }

        /*获取路径*/
        $action = Yii::$app->controller->module->requestedRoute;

        if(Yii::$app->user->identity->getId() == Yii::$app->params['admin']){   //是否是超级管理员UID
            return true;
        }

        $ignoreList = array('index/welcome','site/index','site/login','site/logout');        //权限忽略列表
        if (in_array($action, $ignoreList)) {
            return true;
        }

        if (Yii::$app->user->can($action)) {
            return true;
        } else {
            $this->error('对不起，您没有此操作的权限');
        }
    }

    /**
     * ----------------------------------------------
     * 操作错误跳转的快捷方法
     * @access protected
     * @param string $message 错误信息
     * @param string $jumpUrl 页面跳转地址
     * @param mixed $ajax 是否为Ajax方式 当数字时指定跳转时间
     * @return void
     * -----------------------------------------------
     */
    protected function error($message='',$jumpUrl='',$ajax=false) {
        $this->dispatchJump($message,0,$jumpUrl,$ajax);
    }

    /**
     * ----------------------------------------------
     * 操作成功跳转的快捷方法
     * @access protected
     * @param string $message 提示信息
     * @param string $jumpUrl 页面跳转地址
     * @param mixed $ajax 是否为Ajax方式 当数字时指定跳转时间
     * @return void
     * ----------------------------------------------
     */
    protected function success($message='',$jumpUrl='',$ajax=false) {
        $this->dispatchJump($message,1,$jumpUrl,$ajax);
    }

    /**
     * ----------------------------------------------
     * 默认跳转操作 支持错误导向和正确跳转
     * 调用模板显示 默认为public目录下面的success页面
     * 提示页面为可配置 支持模板标签
     * @param string $message 提示信息
     * @param int $status 状态
     * @param string $jumpUrl 页面跳转地址
     * @param mixed $ajax 是否为Ajax方式 当数字时指定跳转时间
     * @access private
     * @return void
     * ----------------------------------------------
     */
    private function dispatchJump($message,$status=1,$jumpUrl='',$ajax=false) {
        $jumpUrl = !empty($jumpUrl)? (is_array($jumpUrl)?Url::toRoute($jumpUrl):$jumpUrl):'';
        if(true === $ajax || Yii::$app->request->isAjax) {// AJAX提交
            $data           =   is_array($ajax)?$ajax:array();
            $data['info']   =   $message;
            $data['status'] =   $status;
            $data['url']    =   $jumpUrl;
            $this->ajaxReturn($data);
        }
        // 成功操作后默认停留1秒
        $waitSecond = 3;

        if($status) { //发送成功信息
            $message = $message ? $message : '提交成功' ;// 提示信息
            // 默认操作成功自动返回操作前页面
            echo $this->renderFile(Yii::$app->params['action_success'],[
                'message' => $message,
                'waitSecond' => $waitSecond,
                'jumpUrl' => $jumpUrl,
            ]);
        }else{
            $message = $message ? $message : '发生错误了' ;// 提示信息
            // 默认发生错误的话自动返回上页
            $jumpUrl = "javascript:history.back(-1);";
            echo $this->renderFile(Yii::$app->params['action_error'], [
                'message' => $message,
                'waitSecond' => $waitSecond,
                'jumpUrl' => $jumpUrl,
            ]);
        }
        exit;
    }

    /**
     * ------------------------------------------------
     * Ajax方式返回数据到客户端
     * @access protected
     * @param mixed $data 要返回的数据
     * @return void
     * ------------------------------------------------
     */
    protected function ajaxReturn($data) {
        // 返回JSON数据格式到客户端 包含状态信息
        header('Content-Type:application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

}