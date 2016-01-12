<?php
use app\widgets\FileManagerWidget;
/* @var $this yii\web\View */
$this->title = 'Beb file manager';
?>

<?=FileManagerWidget::widget([
    'model' => 'fileManger' ,
    'attribute' => 'name',
    'htmlOptions' => [
        'id' => 'file-manager-id',
    ],
]);
?>
