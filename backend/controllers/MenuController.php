<?php

namespace backend\controllers;

use Yii;
use backend\models\Menu;
use yii\web\NotFoundHttpException;


/**
 * 菜单控制器
 */
class MenuController extends CommonController
{

    /**
     * 菜单列表
     */
    public function actionIndex()
    {
        $menu = new Menu();
        $menu = $menu->getMenuList();
        return $this->render('index', [
            'menu' => $menu
        ]);
    }


    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * 创建菜单
     */
    public function actionCreate()
    {
        $auth = Yii::$app->authManager;
        $model = new Menu();
        $menu = $model->getCreateMenuList();
        $menuArr = array('0'=>"顶级菜单");

        foreach($menu as $vo){
            $menuArr[$vo['id']] = $vo['name'];
            if(!empty($vo['_child'])){
                foreach($vo['_child'] as $v){
                    $menuArr[$v['id']] = "|--".$v['name'];
                    if(!empty($v['_child'])){
                        foreach($v['_child'] as $v3){
                            $menuArr[$v3['id']] = "|----".$v3['name'];
                        }
                    }
                }
            }
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
                'menuArr' => $menuArr,
            ]);
        }
    }

    /**
     *更新菜单
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $menu = $model->getCreateMenuList();
        $menuArr = array('0'=>"顶级菜单");

        foreach($menu as $vo){
            $menuArr[$vo['id']] = $vo['name'];
            if(!empty($vo['_child'])){
                foreach($vo['_child'] as $v){
                    $menuArr[$v['id']] = "|--".$v['name'];
                    if(!empty($v['_child'])){
                        foreach($v['_child'] as $v3){
                            $menuArr[$v3['id']] = "|----".$v3['name'];
                        }
                    }
                }
            }
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
                'menuArr' => $menuArr,
            ]);
        }
    }

    /**
     * 删除菜单
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Menu::findOne($id)) !== null) {
            return $model;
        } else {
            $this->error('删除失败！');
        }
    }

}
