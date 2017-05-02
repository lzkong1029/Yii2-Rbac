<?php

namespace backend\controllers;

use Yii;
use backend\models\AuthItem;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use backend\models\ItemForm;
use yii\helpers\Json;
use backend\models\Menu;
use common\core\rbac;

/**
 * 权限控制器
 */
class ItemController extends CommonController
{

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    //角色列表
    public function actionIndex()
    {
        $model = new AuthItem();
        //分页
        $curPage = Yii:: $app-> request->get( 'page',1);
        $pageSize = 10;
        //搜索
        $type = 1;
        $value = Yii:: $app-> request->get( 'value', '');
        $search = ($type&&$value)?[ 'like',$type,$value]: '';
        //查询语句
        $query = $model->find()->where(['type'=>1])->orderBy( 'created_at DESC');  //列表只显示角色
        $data = $model->getPages($query,$curPage,$pageSize,$search);
        $pages = new Pagination([ 'totalCount' =>$data[ 'count'], 'pageSize' => $pageSize]);

        return $this->render('index',['pages'=>$pages,'data'=>$data,'type' => $type]);
    }

    //权限列表
    public function actionPermission()
    {
        $model = new AuthItem();
        //分页
        $curPage = Yii:: $app-> request->get( 'page',1);
        $pageSize = 10;
        //搜索
        $type = 2;
        $value = Yii:: $app-> request->get( 'value', '');
        $search = ($type&&$value)?[ 'like',$type,$value]: '';
        //查询语句
        $query = $model->find()->where(['type'=>2])->orderBy( 'created_at DESC');  //列表只显示角色
        $data = $model->getPages($query,$curPage,$pageSize,$search);
        $pages = new Pagination([ 'totalCount' =>$data[ 'count'], 'pageSize' => $pageSize]);

        return $this->render('index',['pages'=>$pages,'data'=>$data,'type' => $type]);
    }

    /**
     * 角色配置权限
     * */
    public function actionSet($role)
    {
        $auth = Yii::$app->authManager;
        $permission = $auth->getPermissionsByRole($role);

        $items = Menu::find()->asArray()->all();
        $items =$this->list_to_tree($items,'id','parent');

        if (Yii::$app->request->post()) {
            $rules = Yii::$app->request->post('permission');
            /* 判断角色是否存在 */
            if (!$parent = $auth->getRole($role)) {
                $this->error('角色不存在！');
            }

            /* 删除角色所有child */
            $auth->removeChildren($parent);

            if (is_array($rules)) {
                foreach ($rules as $rule) {
                    /* 更新auth_rule表 与 auth_item表 */
                    $rbac = new rbac\DbManager();
                    $rbac->saveRule($rule);
                    /* 更新auth_item_child表 */
                    $rbac->saveChild($parent->name, $rule);
                }
            }

            return $this->redirect(['item/set','role'=>$role]);
        } else {
            return $this->render('set', [
                'permission' => $permission,
                'items'=>$items,
                'role'=>$role
            ]);
        }
    }

    /**
     * 创建角色或权限
     * */
    public function actionCreate()
    {
        $model = new AuthItem();
        //设置场景
        $model->setScenario(ItemForm:: SCENARIOS_CREATE);
        if ($model->load(Yii::$app->request->post())) {
            if($model->validate()){
                $model->addItem();
                return $this->redirect(['index']);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * 更新角色或权限
     */
    public function actionUpdate($id)
    {
        $model = new AuthItem();
        $model = $model-> getItem($id);
        //设置场景
        $model->setScenario(AuthItem:: SCENARIOS_UPDATE);
        if ($model->load(Yii:: $app-> request->post())) {
            if($model->validate()){
                $model->updateItem($id);
                return $this->redirect([ 'view', 'id' => $model->name]);
            }
        }

        return $this->render( 'update', [
            'model' => $model,
        ]);
    }

    /**
     * 删除角色或权限
     */
    public function actionDelete()
    {
        $model = new AuthItem();
        $model->setScenario(AuthItem:: SCENARIOS_DELETE);
        $model-> name = Yii:: $app-> request->post('id');
        $res =  $model->romoveItem();
        if(!$res){
            return Json:: encode(['status'=>false,'msg'=>'删除失败！']);
        }

        return Json::encode(['status' =>true]);
    }

    protected function findModel($id)
    {
        if (($model = AuthItem::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 树形数组
     **/
    function list_to_tree($list, $pk='id', $pid = 'pid', $child = '_child', $root = 0) {
        // 创建Tree
        $tree = array();
        if(is_array($list)) {
            // 创建基于主键的数组引用
            $refer = array();
            foreach ($list as $key => $data) {
                $refer[$data[$pk]] =& $list[$key];
            }
            foreach ($list as $key => $data) {
                // 判断是否存在parent
                $parentId =  $data[$pid];
                if ($root == $parentId) {
                    $tree[] =& $list[$key];
                }else{
                    if (isset($refer[$parentId])) {
                        $parent =& $refer[$parentId];
                        //$list[$key]['name'] ='&nbsp;&nbsp;&nbsp;&nbsp;|--'.$list[$key]['name'];
                        $list[$key]['name'] =$list[$key]['name'];
                        $parent[$child][] =& $list[$key];
                    }
                }
            }
        }
        return $tree;
    }
}
