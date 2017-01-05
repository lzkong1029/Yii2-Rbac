<?php

namespace backend\controllers;
use backend\models\AuthItem;
use backend\models\PasswordForm;
use yii\data\Pagination;
use backend\models\User;

use Yii;

class UserController extends CommonController
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * 用户列表
     */
    public function actionList()
    {
        Yii::$app->user->identity->username;
        $data = User::find();
        $pages = new Pagination(['totalCount' =>$data->count(), 'pageSize' => '15']);
        $user = $data->joinWith('usergroup')->offset($pages->offset)->limit($pages->limit)->all();
        return $this->render('list',[
            'user'=>$user,
            'pages' => $pages
        ]);
    }

    /**
     * 新增用户
     */
    public function actionCreate()
    {
        $model = new User();
        $model1 = new AuthItem();

        $auth = Yii::$app->authManager;
        $item = $auth->getRoles();
        $itemArr =array();
        foreach($item as $v){
            $itemArr[] .= $v->name;
        }
        foreach($itemArr as $key=>$value)
        {
            $item_one[$value]=$value;
        }

        if ($model->load(Yii::$app->request->post())) {
            $post = Yii::$app->request->post();
            $model->username = $post['User']['username'];
            $model->email = $post['User']['email'];
            $model->setPassword($post['User']['auth_key']);
            $model->generateAuthKey();
            $model->created_at = time();
            $model->save();
            //获取插入后id
            $user_id = $model->attributes['id'];
            $role = $auth->createRole($post['AuthItem']['name']);     //创建角色对象
            $auth->assign($role, $user_id);                           //添加对应关系

            return $this->redirect(['list']);
        } else {
            return $this->render('create', [
                'model' => $model,
                'model1' => $model1,
                'item' => $item_one
            ]);
        }
    }

    /**
     * 更新用户
     */
    public function actionUpdate(){
        $id = Yii::$app->request->get('id');
        $model = User::find()->joinWith('usergroup')->where(['id'=>$id])->one();
        $auth = Yii::$app->authManager;
        $item = $auth->getRoles();
        $itemArr =array();
        foreach($item as $v){
            $itemArr[] .= $v->name;
        }
        foreach($itemArr as $key=>$value)
        {
            $item_one[$value]=$value;
        }
        $model1 = $this->findModel($id);
        if ($model1->load(Yii::$app->request->post())) {
            $post = Yii::$app->request->post();
            //更新密码
            if(!empty($post['User']['auth_key_new'])){
                $model1->setPassword($post['User']['auth_key_new']);
                $model1->generateAuthKey();
            }else{
                $model1->auth_key = $post['User']['auth_key'];
            }
            $model1->save($post);
            if(!empty($post['AuthAssignment']['item_name'])){
                //分配角色
                $role = $auth->createRole($post['AuthAssignment']['item_name']);    //创建角色对象
                $user_id = $id;
                $auth->revokeAll($user_id);
                $auth->assign($role, $user_id);       //分配角色与用户对应关系
            }

            return $this->redirect(['user/update', 'id' => $model1->id]);
        }

        return $this->render('update',[
            'model' => $model,
            'item' => $item_one
        ]);
    }

    /**
     * 删除用户
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['list']);
    }

    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            $this->error('删除失败！');
        }
    }

}
