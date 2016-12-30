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
                            <h3><label><input type="checkbox" class="chkall chk" name="permission[]" value="<?=$vo['route']?>"><?= $vo['name']?></label></h3>
                            <hr>
                            <input name="role" type="hidden" value="<?=$role?>">

                            <?php if(!empty($vo['_child'])):?>
                                <?php foreach($vo['_child'] as $v):?>
                                        <div class="chk2div" style="padding-left: 20px;">
                                        <label><input type="checkbox" class="chk chk2" name="permission[]" value="<?=$v['route']?>"><?=$v['name']?></label><br/>
                                        <?php if(!empty($v['_child'])):?>
                                            <?php foreach($v['_child'] as $v3):?>
                                            <div class="chk3">
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label><input type="checkbox" class="chk chk3" name="permission[]" value="<?=$v3['route']?>"><?=$v3['name']?></label> <br/>
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
        var num = 0;
        var permission = <?= json_encode($permission);?>;
        $('.chk').each(function(){
            var self = $(this);
            var selfVal = self.val();
            $.each(permission, function(n, v){
                if(v.name == selfVal){
                    self.attr('checked' , 'true');
                    num++;
                };
            });
        });
        var nums = $('.chk').size(); //全选勾中
        if(nums == num){
            $('.chkall').attr("checked",true);
        }

        //一级全选
        $(".chkall").click(function(){
            var isChecked = $(this).prop("checked");
            $(this).parent().parent().parent().find('.chk').prop("checked", isChecked);
        });

        //二级全选
        $(".chk2").click(function(){
            var isChecked = $(this).prop("checked");
            $(this).parent().parent().find('.chk3').prop("checked", isChecked);
        });

    });
</script>
