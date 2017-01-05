<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

$this->title = '配置权限【'.$role.'】';
$this->params['breadcrumbs'][] = ['label' => 'Auth Item Children', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wrapper wrapper-content">
    <div class="ibox-content">
    <h1><?= Html::encode($this->title) ?></h1>
    <hr>
    <form action="<?=Url::toRoute(['item/set','role'=>$role])?>" method="post">
        <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
        <div class="row">
            <?php foreach($items as $vo):?>
                    <div class="col-sm-12 permission-block">
                        <div class="ibox-permission">
                            <h3><label><div class="i-checks chkall"><input type="checkbox" class=" chk" name="permission[]" value="<?=$vo['route']?>"><?= $vo['name']?></div></label></h3>
                            <hr>
                            <input name="role" type="hidden" value="<?=$role?>">

                            <?php if(!empty($vo['_child'])):?>
                                <?php foreach($vo['_child'] as $v):?>
                                        <div class="chk2div" style="padding-left: 20px;">
                                        <label><div class="i-checks chk2"><input type="checkbox" class="chk " name="permission[]" value="<?=$v['route']?>"><?=$v['name']?></div></label><br/>
                                        <?php if(!empty($v['_child'])):?>
                                            <?php foreach($v['_child'] as $v3):?>
                                            <div class="">
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label><div class="i-checks chk3"><input type="checkbox" class="chk" name="permission[]" value="<?=$v3['route']?>"><?=$v3['name']?></div></label> <br/>
                                            </div>
                                            <?php endforeach;?>
                                        <?php endif;?>
                                        </div>
                                <?php endforeach;?>
                            <?php endif;?>
                        </div>
                    </div>
            <?php endforeach;?>
        </div>
        <br>
        <br>
        <div class="clear"></div>
        <input type="submit" value="保存" class="btn btn-primary">
    </form>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {

        $(".i-checks").iCheck({checkboxClass:"icheckbox_square-green",radioClass:"iradio_square-green"});

        var num = 0;
        var permission = <?= json_encode($permission);?>;
        $('.chk').each(function(){
            var self = $(this);
            var selfVal = self.val();
            $.each(permission, function(n, v){
                if(v.name == selfVal){
                    self.parent().iCheck('check');
                    num++;
                };
            });
        });

        //一级全选
        $(".chkall").on('ifChecked', function(event){
            $(this).parent().parent().parent().find('.chk').iCheck('check');
        });
        $(".chkall").on('ifUnchecked', function(event){
            $(this).parent().parent().parent().find('.chk').iCheck('uncheck');
        });


        //二级全选
        $(".chk2").on('ifChecked', function(event){
            $(this).parent().parent().find('.chk3').iCheck('check');
        });
        $(".chk2").on('ifUnchecked', function(event){
            $(this).parent().parent().find('.chk3').iCheck('uncheck');
        });

        //三级选中
        $(".chk3").on('ifChecked', function(event){
            $(this).iCheck('check');
        });
        $(".chk3").on('ifUnchecked', function(event){
            $(this).iCheck('uncheck');
        });

    });
</script>
