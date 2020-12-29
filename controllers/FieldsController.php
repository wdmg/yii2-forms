<?php

namespace wdmg\forms\controllers;

use Yii;
use wdmg\forms\models\Fields;
use wdmg\forms\models\FieldsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * FieldsController implements the CRUD actions for Fields model.
 */
class FieldsController extends Controller
{

    /**
     * @var string|null Selected language (locale)
     */
    private $_locale;

    /**
     * @var string|null Selected id of source
     */
    private $_source_id;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'roles' => ['admin'],
                        'allow' => true
                    ],
                ],
            ],
        ];

        // If auth manager not configured use default access control
        if (!Yii::$app->authManager) {
            $behaviors['access'] = [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'roles' => ['@'],
                        'allow' => true
                    ],
                ]
            ];
        } else if ($this->module->moduleExist('admin/rbac')) { // Ok, then we check access according to the rules
            $behaviors['access'] = [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['update', 'create'],
                        'roles' => ['updatePosts'],
                        'allow' => true
                    ], [
                        'roles' => ['viewDashboard'],
                        'allow' => true
                    ],
                ],
            ];
        }

        return $behaviors;
    }

    /**
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {
        $this->_locale = Yii::$app->request->get('locale', null);
        $this->_source_id = Yii::$app->request->get('source_id', null);
        return parent::beforeAction($action);
    }

    /**
     * Lists all Fields models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FieldsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Fields model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Fields model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Fields();

        // No language is set for this model, we will use the current user language
        if (is_null($model->locale)) {
            if (is_null($this->_locale)) {

                $model->locale = Yii::$app->sourceLanguage;
                if (!Yii::$app->request->isPost) {

                    $languages = $model->getLanguagesList(false);
                    Yii::$app->getSession()->setFlash(
                        'danger',
                        Yii::t(
                            'app/modules/forms',
                            'No display language has been set. Source language will be selected: {language}',
                            [
                                'language' => (isset($languages[Yii::$app->sourceLanguage])) ? $languages[Yii::$app->sourceLanguage] : Yii::$app->sourceLanguage
                            ]
                        )
                    );
                }
            } else {
                $model->locale = $this->_locale;
            }
        }

        if (!is_null($this->_source_id)) {
            $model->source_id = $this->_source_id;
            if ($source = $model::findOne(['id' => $this->_source_id])) {
                if ($source->id) {
                    $model->source_id = $source->id;
                    $model->name = $source->name;
                    $model->is_required = $source->is_required;
                    $model->type = $source->type;
                }
            }
        }

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->validate())
                    $success = true;
                else
                    $success = false;

                return $this->asJson(['success' => $success, 'name' => $model->name, 'errors' => $model->errors]);
            }
        } else {
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {

                if ($model->save()) {
                    // Log activity
                    $this->module->logActivity(
                        'New form field `' . $model->label . '` with ID `' . $model->id . '` has been successfully added.',
                        $this->uniqueId . ":" . $this->action->id,
                        'success',
                        1
                    );

                    Yii::$app->getSession()->setFlash(
                        'success',
                        Yii::t('app/modules/forms', 'Form field has been successfully added!')
                    );

                    return $this->redirect(['fields/view', 'id' => $model->id]);
                } else {
                    // Log activity
                    $this->module->logActivity(
                        'An error occurred while add the new form field: ' . $model->label,
                        $this->uniqueId . ":" . $this->action->id,
                        'danger',
                        1
                    );

                    Yii::$app->getSession()->setFlash(
                        'danger',
                        Yii::t('app/modules/forms', 'An error occurred while add the form field.')
                    );
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Fields model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        // No language is set for this model, we will use the current user language
        if (is_null($model->locale)) {

            $model->locale = Yii::$app->sourceLanguage;
            if (!Yii::$app->request->isPost) {

                $languages = $model->getLanguagesList(false);
                Yii::$app->getSession()->setFlash(
                    'danger',
                    Yii::t(
                        'app/modules/forms',
                        'No display language has been set. Source language will be selected: {language}',
                        [
                            'language' => (isset($languages[Yii::$app->sourceLanguage])) ? $languages[Yii::$app->sourceLanguage] : Yii::$app->sourceLanguage
                        ]
                    )
                );
            }
        }

        if (Yii::$app->request->isAjax) {
            if ($model->load(Yii::$app->request->post())) {
                if ($model->validate())
                    $success = true;
                else
                    $success = false;

                return $this->asJson(['success' => $success, 'name' => $model->name, 'errors' => $model->errors]);
            }
        } else {
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                if ($model->save()) {
                    // Log activity
                    $this->module->logActivity(
                        'Form field `' . $model->label . '` with ID `' . $model->id . '` has been successfully updated.',
                        $this->uniqueId . ":" . $this->action->id,
                        'success',
                        1
                    );

                    Yii::$app->getSession()->setFlash(
                        'success',
                        Yii::t(
                            'app/modules/forms',
                            'OK! Form field `{label}` successfully updated.',
                            [
                                'label' => $model->label
                            ]
                        )
                    );

                    return $this->redirect(['fields/view', 'id' => $model->id]);
                } else {
                    // Log activity
                    $this->module->logActivity(
                        'An error occurred while update the form field `' . $model->label . '` with ID `' . $model->id . '`.',
                        $this->uniqueId . ":" . $this->action->id,
                        'danger',
                        1
                    );

                    Yii::$app->getSession()->setFlash(
                        'danger',
                        Yii::t(
                            'app/modules/forms',
                            'An error occurred while update a form field `{label}`.',
                            [
                                'label' => $model->label
                            ]
                        )
                    );
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Fields model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model->delete()) {
            // Log activity
            $this->module->logActivity(
                'Form field `' . $model->label . '` with ID `' . $model->id . '` has been successfully deleted.',
                $this->uniqueId . ":" . $this->action->id,
                'success',
                1
            );

            Yii::$app->getSession()->setFlash(
                'success',
                Yii::t(
                    'app/modules/forms',
                    'OK! Form field `{label}` successfully deleted.',
                    [
                        'label' => $model->label
                    ]
                )
            );
        } else {
            // Log activity
            $this->module->logActivity(
                'An error occurred while deleting the form field `' . $model->label . '` with ID `' . $model->id . '`.',
                $this->uniqueId . ":" . $this->action->id,
                'danger',
                1
            );

            Yii::$app->getSession()->setFlash(
                'danger',
                Yii::t(
                    'app/modules/forms',
                    'An error occurred while deleting a form field `{label}`.',
                    [
                        'label' => $model->label
                    ]
                )
            );
        }

        return $this->redirect(['fields/index']);
    }

    /**
     * Finds the Fields model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return Fields the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {

        if (is_null($this->_locale) && ($model = Fields::findOne($id)) !== null) {
            return $model;
        } else {
            if (($model = Fields::findOne(['source_id' => $id, 'locale' => $this->_locale])) !== null)
                return $model;
        }

        throw new NotFoundHttpException(Yii::t('app/modules/forms', 'The requested field does not exist.'));
    }

    /**
     * Return current locale for dashboard
     *
     * @return string|null
     */
    public function getLocale() {
        return $this->_locale;
    }

    /**
     * Return current Source ID for dashboard
     *
     * @return string|null
     */
    public function getSourceId() {
        return $this->_source_id;
    }
}
