<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = '菜单';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wrapper wrapper-content">
    <div class="ibox float-e-margins">
        <div class="ibox-content">
            <p>
                <?= Html::a('创建菜单', ['create'], ['class' => 'btn btn-sm btn-primary']) ?>
            </p>

            <div class="row">
                <div class="col-sm-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-content">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>名称</th>
                                        <th>父级</th>
                                        <th>路由</th>
                                        <th>状态</th>
                                        <th>排序</th>
                                        <th>操作</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach($menu as $vo):?>
                                        <tr>
                                            <td><?=$vo['id']?></td>
                                            <td><?=$vo['name']?></td>
                                            <td><?=$vo['parent']?></td>
                                            <td><?=$vo['route']?></td>
                                            <td><?=$vo['status'] >0 ? '<span class="label label-info">显示</span>' : '<span class="label label-error">隐藏</span>'?></td>
                                            <td><?=($vo['sort']==''?'':$vo['sort'])?></td>
                                            <td><a class="btn btn-primary btn-xs" href="<?=Url::toRoute(['menu/update','id'=>$vo['id']])?>"><i class="fa fa-edit"></i>编辑</a>  <a class="btn btn-default btn-xs"  href="<?=Url::toRoute(['menu/delete','id'=>$vo['id']])?>"><i class="fa fa-close"></i>删除</a></td>
                                        </tr>
                                        <!--二级菜单-->
                                        <?php if(!empty($vo['_child'])):?>
                                            <?php foreach($vo['_child'] as $v):?>
                                                <tr>
                                                <td><?=$v['id']?></td>
                                                <td>&nbsp;&nbsp;&nbsp;&nbsp;┝<?=$v['name']?></td>
                                                <td><?=$v['parent']?></td>
                                                <td><?=$v['route']?></td>
                                                <td><?=$v['status'] > 0 ? '<span class="label label-info">显示</span>' : '<span class="label label-error">隐藏</span>'?></td>
                                                <td><?=($v['sort']==''?'':$v['sort'])?></td>
                                                <td><a class="btn btn-primary btn-xs" href="<?=Url::toRoute(['menu/update','id'=>$v['id']])?>"><i class="fa fa-edit"></i>编辑</a>  <a class="btn btn-default btn-xs" href="<?=Url::toRoute(['menu/delete','id'=>$v['id']])?>"><i class="fa fa-close"></i>删除</a></td>
                                                </tr>

                                                <!--三级菜单-->
                                                <?php if(!empty($v['_child'])):?>
                                                    <?php foreach($v['_child'] as $v3):?>
                                                        <tr>
                                                            <td><?=$v3['id']?></td>
                                                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;┝<?=$v3['name']?></td>
                                                            <td><?=$v3['parent']?></td>
                                                            <td><?=$v3['route']?></td>
                                                            <td><?=$v3['status'] > 0 ? '<span class="label label-info">显示</span>' : '<span class="label label-error">隐藏</span>'?></td>
                                                            <td><?=($v3['sort']==''?'':$v3['sort'])?></td>
                                                            <td><a class="btn btn-primary btn-xs" href="<?=Url::toRoute(['menu/update','id'=>$v3['id']])?>"><i class="fa fa-edit"></i>编辑</a>  <a class="btn btn-default btn-xs" href="<?=Url::toRoute(['menu/delete','id'=>$v3['id']])?>"><i class="fa fa-close"></i>删除</a></td>
                                                        </tr>
                                                    <?php endforeach;?>
                                                <?php endif;?>
                                                <!--三级菜单 end-->

                                            <?php endforeach;?>
                                        <?php endif;?>
                                        <!--二级菜单 end-->

                                    <?php endforeach;?>
                                    </tbody>
                                </table>
                                <!--分页-->
                                <!--<div class="f-r">
                                    <?/*= LinkPager::widget([
                                        'pagination'=>$pages,
                                        'firstPageLabel' => '首页',
                                        'nextPageLabel' => '下一页',
                                        'prevPageLabel' => '上一页',
                                        'lastPageLabel' => '末页',
                                    ]) */?>
                                </div>-->

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
