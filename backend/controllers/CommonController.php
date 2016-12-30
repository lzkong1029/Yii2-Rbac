<?php
/**
 * User: lzkong1029
 * Email: 272067516@qq.com
 * Date: 2016-7-28
 * Time: 9:05
 * Description:
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
        //如果未登录，则直接返回
        if(Yii::$app->user->isGuest){
            return $this->goHome();
        }

        //获取路径
        //$path = Yii::$app->request->pathInfo;
        $action = Yii::$app->controller->module->requestedRoute;

        $ignoreList = array('index/welcome','site/index');
        //忽略列表
        if (in_array($action, $ignoreList)) {
            return true;
        }
        return true;
        if (Yii::$app->user->can($action)) {
            return true;
        } else {
            //throw new ForbiddenHttpException(Yii::t('app', 'message 401'));
            echo '<div style="margin: 100px auto;text-align: center;background-color: #1ab394; color: #ffffff;width: 500px;height: 50px;line-height: 50px;border-radius: 5px;;"><h4>对不起，您现在还没获此操作的权限</h4></div>';
        }
    }

}