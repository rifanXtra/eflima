<?php namespace modules\address\controllers\rest\v1;

// "Keep the essence of your code, code isn't just a code, it's an art." -- Rifan Firdhaus Widigdo
use modules\address\models\forms\province\ProvinceSearch;
use modules\address\models\Province;
use modules\core\rest\Controller;
use Yii;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\data\BaseDataProvider;

/**
 * @author Rifan Firdhaus Widigdo <rifanfirdhaus@gmail.com>
 */
class ProvinceController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator']['optional'] = [
            'get',
            'list',
        ];

        return $behaviors;
    }

    /**
     * @inheritDoc
     */
    public function verbs()
    {
        return [
            'get' => ['get'],
            'list' => ['get'],
        ];
    }

    /**
     * @param null $id
     *
     * @return null|Province|ActiveDataProvider|BaseDataProvider|array
     * @throws InvalidConfigException
     */
    public function actionGet($id = null)
    {
        if (empty($id)) {
            return $this->actionList();
        }

        $model = $this->getModel($id);

        if (!$model) {
            $this->failed()->addMessage('danger', Yii::t('app', '{object} you are looking for doesn\'t exists', [
                'object' => Yii::t('app', 'Province'),
            ]));

            return null;
        }

        return $model;
    }

    /**
     * @return ActiveDataProvider|BaseDataProvider
     */
    public function actionList()
    {
        $searchModel = new ProvinceSearch();

        return $searchModel->apply(Yii::$app->request->queryParams, '');
    }

    /**
     * @param string|int $id
     *
     * @return array|Province|null
     * @throws InvalidConfigException
     */
    public function getModel($id)
    {
        return Province::find()->andWhere(['id' => $id])->one();
    }
}