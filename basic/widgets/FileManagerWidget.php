<?php

namespace app\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

class FileManagerWidget extends Widget
{
    /**
     * Path to assets for this widget
     * The widget will publish its own directory if none is specified
     * @var string
     */
    public $assetsPath = null;
    /**
     * @var array the HTML attributes that should be rendered in the HTML tag representing the JUI widget.
     */
    public $htmlOptions=array();
    /**
     * @var CModel the data model associated with this widget.
     */
    public $model;
    /**
     * @var string the attribute associated with this widget.
     * The name can contain square brackets (e.g. 'name[1]') which is used to collect tabular data input.
     */
    public $attribute;
    /**
     * @var string the input name. This must be set if {@link model} is not set.
     */
    public $name;
    /**
     * @var string the input value.
     */
    public $value;
    /**
     * @var string id of fileManager container
     */
    public $fileManagerContainerId = 'fileManagerContainer';

    /**
     * Options for assets publish
     * @var array
     */
    private $assetOptions = [
        'depends' => 'yii\web\YiiAsset',
        'position' => \yii\web\View::POS_END
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];

    protected $assets_dir = 'views/fileManager/assets';



//    /**
//     * @return boolean whether this widget is associated with a data model.
//     */
//    protected function hasModel()
//    {
//        return $this->model instanceof CModel && $this->attribute!==null;
//    }


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->assetsPath === null) {
            $this->assetsPath = '@app/widgets';
            $this->registerClientScript();
        }

    }

    /**
     * Register file and move assets directory
     * @param string $path
     * @param array $assetOptions
     */
    public function registerJsFile($path, $assetOptions  = [])
    {
        Yii::$app->assetManager->publish($this->assetsPath . '/'.  $path,  ['forceCopy' => YII_DEBUG]);
        $this->getView()->registerJsFile(Yii::$app->assetManager->getPublishedUrl(
            $this->assetsPath . '/' . $path
        ),
            $assetOptions
        );
    }

    /**
     * Register file and move assets directory
     * @param string $path
     * @param array $assetOptions
     */
    public function registerCssFile($path, $assetOptions  = [])
    {
        Yii::$app->assetManager->publish($this->assetsPath . '/'.  $path,  ['forceCopy' => YII_DEBUG]);
        $this->getView()->registerCssFile(Yii::$app->assetManager->getPublishedUrl(
            $this->assetsPath . '/' . $path
        ),
            $assetOptions
        );
    }


    /**
     * Registers necessary client scripts.
     */
    public function registerClientScript()
    {
        $this->registerCssFile($this->getAssetsDir() . '/css/fileManager.css', $this->assetOptions);
        $this->registerJsFile($this->getAssetsDir() . '/js/jquery.fileManager.js', $this->assetOptions);
    }

    public function run()
    {

        if (isset($this->htmlOptions['id'])) {
            $id = $this->htmlOptions['id'];
        }


        if (isset($this->htmlOptions['name'])) {
            $name = $this->htmlOptions['name'];
        }

//        if ($this->hasModel()) {
//            echo CHtml::activeHiddenField($this->model, $this->attribute, $this->htmlOptions);
//        }
            echo Html::hiddenInput($this->attribute, $this->value, $this->htmlOptions);


        $fileManagerOptions = [
            'csrfTokenName' => Yii::$app->request->csrfParam ,
            'csrfToken' =>  yii::$app->request->csrfToken,
            'pickerSelector' => '#'.$this->fileManagerContainerId.'Uploader',
            'createFileRoute' => Url::toRoute('//fileManager/createFile'),
            'createDirectoryRoute' => Url::toRoute('//fileManager/createDirectory'),
            'deleteFileRoute' => Url::toRoute('//fileManager/deleteFile'),
            'deleteDirectoryRoute' => Url::toRoute('//fileManager/deleteDirectory'),
            'listFile' => Url::toRoute('//fileManager/listFile'),
            'fileIdInputSelector' => '#'.$id,
        ];

        $gOptions = json::encode($fileManagerOptions);
        $js = <<<EOD
jQuery('#{$this->fileManagerContainerId}').fileManager($gOptions);
EOD;
        $this->getView()->registerJs($js, \yii\web\View::POS_END);

        return  $this->render('fileManager/views/view', [
            'fileManagerContainerId' => $this->fileManagerContainerId
        ]);
    }


    /**
     * Get views directory
     * @return string
     */
    protected function  getAssetsDir()
    {
        return $this->assets_dir;
    }


}
