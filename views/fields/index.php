<?php

use wdmg\widgets\SelectInput;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\vendor\wdmg\forms\models\FieldsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/modules/forms', 'All fields');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/modules/forms', 'Forms'), 'url' => ['list/index']];
$this->params['breadcrumbs'][] = Yii::t('app/modules/forms', 'Fields list');

$bundle = false;
if (isset(Yii::$app->translations) && class_exists('\wdmg\translations\FlagsAsset')) {
    $bundle = \wdmg\translations\FlagsAsset::register(Yii::$app->view);
}

?>
<div class="page-header">
    <h1>
        <?= Html::encode($this->title) ?> <small class="text-muted pull-right">[v.<?= $this->context->module->version ?>]</small>
    </h1>
</div>
<div class="forms-fields-index">
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => '{summary}<br\/>{items}<br\/>{summary}<br\/><div class="text-center">{pager}</div>',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'form_id',
                'format' => 'html',
                'label' => Yii::t('app/modules/forms', 'Form'),
                'filter' => SelectInput::widget([
                    'model' => $searchModel,
                    'attribute' => 'form_id',
                    'items' => $searchModel->getAllFormsList(true),
                    'options' => [
                        'class' => 'form-control'
                    ]
                ]),
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
                'value' => function($data) {
                    $output = "";
                    $output = Html::a($data->form->name, ['list/view', 'id' => $data->form->id], [
                        'target' => '_blank',
                        'data-pjax' => 0
                    ]);
                    return $output;
                }
            ],

            'label',
            'name',
            [
                'attribute' => 'description',
                'format' => 'raw',
                'value' => function($model) {
                    return mb_strimwidth(strip_tags($model->description), 0, 120, '…');
                }
            ],
            [
                'attribute' => 'type',
                'format' => 'html',
                'filter' => SelectInput::widget([
                    'model' => $searchModel,
                    'attribute' => 'type',
                    'items' => $searchModel->getFieldsTypesList(true),
                    'options' => [
                        'class' => 'form-control'
                    ]
                ]),
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
                'value' => function($data) {
                    $types = $data->getFieldsTypesList(true);
                    if (isset($types[$data->type]))
                        return $types[$data->type];
                    else
                        return $data->type;
                }
            ],

            //'sort_order',
            //'params:ntext',
            [
                'attribute' => 'is_required',
                'format' => 'html',
                'filter' => SelectInput::widget([
                    'model' => $searchModel,
                    'attribute' => 'is_required',
                    'items' => $searchModel->getIsRequiredList(true),
                    'options' => [
                        'class' => 'form-control'
                    ]
                ]),
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
                'value' => function($data) {
                    if ($data->is_required)
                        return '<span class="fa fa-check text-success"></span>';
                    else
                        return '';
                }
            ],
            [
                'attribute' => 'locale',
                'label' => Yii::t('app/modules/forms','Language versions'),
                'format' => 'raw',
                'filter' => false,
                'headerOptions' => [
                    'class' => 'text-center',
                    'style' => 'min-width:96px;'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
                'value' => function($data) use ($bundle) {

                    $output = [];
                    $separator = ", ";
                    $versions = $data->getAllVersions($data->id, true);
                    $locales = ArrayHelper::map($versions, 'id', 'locale');

                    if (isset(Yii::$app->translations)) {
                        foreach ($locales as $item_locale) {

                            $locale = Yii::$app->translations->parseLocale($item_locale, Yii::$app->language);

                            if ($item_locale === $locale['locale']) { // Fixing default locale from PECL intl

                                if (!($country = $locale['domain']))
                                    $country = '_unknown';

                                $flag = \yii\helpers\Html::img($bundle->baseUrl . '/flags-iso/flat/24/' . $country . '.png', [
                                    'alt' => $locale['name']
                                ]);

                                if ($data->locale === $locale['locale']) // It`s source version
                                    $output[] = Html::a($flag,
                                        [
                                            'fields/update', 'id' => $data->id
                                        ], [
                                            'title' => Yii::t('app/modules/forms','Edit source version: {language}', [
                                                'language' => $locale['name']
                                            ])
                                        ]
                                    );
                                else  // Other localization versions
                                    $output[] = Html::a($flag,
                                        [
                                            'fields/update', 'id' => $data->id,
                                            'locale' => $locale['locale']
                                        ], [
                                            'title' => Yii::t('app/modules/forms','Edit language version: {language}', [
                                                'language' => $locale['name']
                                            ])
                                        ]
                                    );

                            }

                        }
                        $separator = "";
                    } else {
                        foreach ($locales as $locale) {
                            if (!empty($locale)) {

                                if (extension_loaded('intl'))
                                    $language = mb_convert_case(trim(\Locale::getDisplayLanguage($locale, Yii::$app->language)), MB_CASE_TITLE, "UTF-8");
                                else
                                    $language = $locale;

                                if ($data->locale === $locale) // It`s source version
                                    $output[] = Html::a($language,
                                        [
                                            'fields/update', 'id' => $data->id
                                        ], [
                                            'title' => Yii::t('app/modules/forms','Edit source version: {language}', [
                                                'language' => $language
                                            ])
                                        ]
                                    );
                                else  // Other localization versions
                                    $output[] = Html::a($language,
                                        [
                                            'fields/update', 'id' => $data->id,
                                            'locale' => $locale
                                        ], [
                                            'title' => Yii::t('app/modules/forms','Edit language version: {language}', [
                                                'language' => $language
                                            ])
                                        ]
                                    );
                            }
                        }
                    }


                    if (is_array($output)) {
                        if (count($output) > 0) {
                            $onMore = false;
                            if (count($output) > 3)
                                $onMore = true;

                            if ($onMore)
                                return join(array_slice($output, 0, 3), $separator) . "&nbsp;…";
                            else
                                return join($separator, $output);

                        }
                    }

                    return null;
                }
            ],
            [
                'attribute' => 'status',
                'format' => 'html',
                'filter' => SelectInput::widget([
                    'model' => $searchModel,
                    'attribute' => 'status',
                    'items' => $searchModel->getStatusesList(true),
                    'options' => [
                        'class' => 'form-control'
                    ]
                ]),
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
                'value' => function($data) {
                    if ($data->status == $data::STATUS_PUBLISHED)
                        return '<span class="label label-success">'.Yii::t('app/modules/forms','Published').'</span>';
                    elseif ($data->status == $data::STATUS_DRAFT)
                        return '<span class="label label-default">'.Yii::t('app/modules/forms','Draft').'</span>';
                    else
                        return $data->status;
                }
            ],

            [
                'attribute' => 'created',
                'label' => Yii::t('app/modules/forms','Created'),
                'format' => 'html',
                'value' => function($data) {

                    $output = "";
                    if ($user = $data->createdBy) {
                        $output = Html::a($user->username, ['../admin/users/view/?id='.$user->id], [
                            'target' => '_blank',
                            'data-pjax' => 0
                        ]);
                    } else if ($data->created_by) {
                        $output = $data->created_by;
                    }

                    if (!empty($output))
                        $output .= ", ";

                    $output .= Yii::$app->formatter->format($data->created_at, 'datetime');
                    return $output;
                }
            ],
            [
                'attribute' => 'updated',
                'label' => Yii::t('app/modules/forms','Updated'),
                'format' => 'html',
                'value' => function($data) {

                    $output = "";
                    if ($user = $data->updatedBy) {
                        $output = Html::a($user->username, ['../admin/users/view/?id='.$user->id], [
                            'target' => '_blank',
                            'data-pjax' => 0
                        ]);
                    } else if ($data->updated_by) {
                        $output = $data->updated_by;
                    }

                    if (!empty($output))
                        $output .= ", ";

                    $output .= Yii::$app->formatter->format($data->updated_at, 'datetime');
                    return $output;
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'header' => Yii::t('app/modules/forms','Actions'),
                'headerOptions' => [
                    'class' => 'text-center'
                ],
                'contentOptions' => [
                    'class' => 'text-center'
                ],
                'buttons'=> [
                    'view' => function($url, $data, $key) {
                        $output = [];
                        $versions = $data->getAllVersions($data->id, true);
                        $locales = ArrayHelper::map($versions, 'id', 'locale');
                        if (isset(Yii::$app->translations)) {
                            foreach ($locales as $item_locale) {
                                $locale = Yii::$app->translations->parseLocale($item_locale, Yii::$app->language);
                                if ($item_locale === $locale['locale']) { // Fixing default locale from PECL intl

                                    if ($data->locale === $locale['locale']) // It`s source version
                                        $output[] = Html::a(Yii::t('app/modules/forms','View source version: {language}', [
                                            'language' => $locale['name']
                                        ]), ['fields/view', 'id' => $data->id]);
                                    else  // Other localization versions
                                        $output[] = Html::a(Yii::t('app/modules/forms','View language version: {language}', [
                                            'language' => $locale['name']
                                        ]), ['fields/view', 'id' => $data->id, 'locale' => $locale['locale']]);

                                }
                            }
                        } else {
                            foreach ($locales as $locale) {
                                if (!empty($locale)) {

                                    if (extension_loaded('intl'))
                                        $language = mb_convert_case(trim(\Locale::getDisplayLanguage($locale, Yii::$app->language)), MB_CASE_TITLE, "UTF-8");
                                    else
                                        $language = $locale;

                                    if ($data->locale === $locale) // It`s source version
                                        $output[] = Html::a(Yii::t('app/modules/forms','View source version: {language}', [
                                            'language' => $language
                                        ]), ['fields/view', 'id' => $data->id]);
                                    else  // Other localization versions
                                        $output[] = Html::a(Yii::t('app/modules/forms','View language version: {language}', [
                                            'language' => $language
                                        ]), ['fields/view', 'id' => $data->id, 'locale' => $locale]);

                                }
                            }
                        }

                        if (is_array($output)) {
                            if (count($output) > 1) {
                                $html = '';
                                $html .= '<div class="btn-group">';
                                $html .= Html::a(
                                    '<span class="glyphicon glyphicon-eye-open"></span> ' .
                                    Yii::t('app/modules/forms', 'View') .
                                    ' <span class="caret"></span>',
                                    '#',
                                    [
                                        'class' => "btn btn-block btn-link btn-xs dropdown-toggle",
                                        'data-toggle' => "dropdown",
                                        'aria-haspopup' => "true",
                                        'aria-expanded' => "false"
                                    ]);
                                $html .= '<ul class="dropdown-menu dropdown-menu-right">';
                                $html .= '<li>' . implode("</li><li>", $output) . '</li>';
                                $html .= '</ul>';
                                $html .= '</div>';
                                return $html;
                            }
                        }
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span> ' .
                            Yii::t('app/modules/forms', 'View'),
                            [
                                'fields/view',
                                'id' => $data->id
                            ], [
                                'class' => 'btn btn-link btn-xs'
                            ]
                        );
                    },
                    'update' => function($url, $data, $key) {

                        if (Yii::$app->authManager && $this->context->module->moduleExist('rbac') && !Yii::$app->user->can('updatePosts', [
                                'created_by' => $data->created_by,
                                'updated_by' => $data->updated_by
                            ])) {
                            return false;
                        }

                        $output = [];
                        $versions = $data->getAllVersions($data->id, true);
                        $locales = ArrayHelper::map($versions, 'id', 'locale');
                        if (isset(Yii::$app->translations)) {
                            foreach ($locales as $item_locale) {
                                $locale = Yii::$app->translations->parseLocale($item_locale, Yii::$app->language);
                                if ($item_locale === $locale['locale']) { // Fixing default locale from PECL intl

                                    if ($data->locale === $locale['locale']) // It`s source version
                                        $output[] = Html::a(Yii::t('app/modules/forms','Edit source version: {language}', [
                                            'language' => $locale['name']
                                        ]), ['fields/update', 'id' => $data->id]);
                                    else  // Other localization versions
                                        $output[] = Html::a(Yii::t('app/modules/forms','Edit language version: {language}', [
                                            'language' => $locale['name']
                                        ]), ['fields/update', 'id' => $data->id, 'locale' => $locale['locale']]);

                                }
                            }
                        } else {
                            foreach ($locales as $locale) {
                                if (!empty($locale)) {

                                    if (extension_loaded('intl'))
                                        $language = mb_convert_case(trim(\Locale::getDisplayLanguage($locale, Yii::$app->language)), MB_CASE_TITLE, "UTF-8");
                                    else
                                        $language = $locale;

                                    if ($data->locale === $locale) // It`s source version
                                        $output[] = Html::a(Yii::t('app/modules/forms','Edit source version: {language}', [
                                            'language' => $language
                                        ]), ['fields/update', 'id' => $data->id]);
                                    else  // Other localization versions
                                        $output[] = Html::a(Yii::t('app/modules/forms','Edit language version: {language}', [
                                            'language' => $language
                                        ]), ['fields/update', 'id' => $data->id, 'locale' => $locale]);

                                }
                            }
                        }

                        if (is_array($output)) {
                            if (count($output) > 1) {
                                $html = '';
                                $html .= '<div class="btn-group">';
                                $html .= Html::a(
                                    '<span class="glyphicon glyphicon-pencil"></span> ' .
                                    Yii::t('app/modules/forms', 'Edit') .
                                    ' <span class="caret"></span>',
                                    '#',
                                    [
                                        'class' => "btn btn-block btn-link btn-xs dropdown-toggle",
                                        'data-toggle' => "dropdown",
                                        'aria-haspopup' => "true",
                                        'aria-expanded' => "false"
                                    ]);
                                $html .= '<ul class="dropdown-menu dropdown-menu-right">';
                                $html .= '<li>' . implode("</li><li>", $output) . '</li>';
                                $html .= '</ul>';
                                $html .= '</div>';
                                return $html;
                            }
                        }
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span> ' .
                            Yii::t('app/modules/forms', 'Edit'),
                            [
                                'fields/update',
                                'id' => $data->id
                            ], [
                                'class' => 'btn btn-link btn-xs'
                            ]
                        );
                    },
                    'delete' => function($url, $data, $key) {

                        if (Yii::$app->authManager && $this->context->module->moduleExist('rbac') && !Yii::$app->user->can('updatePosts', [
                                'created_by' => $data->created_by,
                                'updated_by' => $data->updated_by
                            ])) {
                            return false;
                        }

                        $output = [];
                        $versions = $data->getAllVersions($data->id, true);
                        $locales = ArrayHelper::map($versions, 'id', 'locale');
                        if (isset(Yii::$app->translations)) {
                            foreach ($locales as $item_locale) {
                                $locale = Yii::$app->translations->parseLocale($item_locale, Yii::$app->language);
                                if ($item_locale === $locale['locale']) { // Fixing default locale from PECL intl

                                    if ($data->locale === $locale['locale']) // It`s source version
                                        $output[] = Html::a(Yii::t('app/modules/forms','Delete source version: {language}', [
                                            'language' => $locale['name']
                                        ]), ['fields/delete', 'id' => $data->id], [
                                            'data-method' => 'POST',
                                            'data-confirm' => Yii::t('app/modules/forms', 'Are you sure you want to delete the language version of this field?')
                                        ]);
                                    else  // Other localization versions
                                        $output[] = Html::a(Yii::t('app/modules/forms','Delete language version: {language}', [
                                            'language' => $locale['name']
                                        ]), ['fields/delete', 'id' => $data->id, 'locale' => $locale['locale']], [
                                            'data-method' => 'POST',
                                            'data-confirm' => Yii::t('app/modules/forms', 'Are you sure you want to delete the language version of this field?')
                                        ]);

                                }
                            }
                        } else {
                            foreach ($locales as $locale) {
                                if (!empty($locale)) {

                                    if (extension_loaded('intl'))
                                        $language = mb_convert_case(trim(\Locale::getDisplayLanguage($locale, Yii::$app->language)), MB_CASE_TITLE, "UTF-8");
                                    else
                                        $language = $locale;

                                    if ($data->locale === $locale) // It`s source version
                                        $output[] = Html::a(Yii::t('app/modules/forms','Delete source version: {language}', [
                                            'language' => $language
                                        ]), ['fields/delete', 'id' => $data->id], [
                                            'data-method' => 'POST',
                                            'data-confirm' => Yii::t('app/modules/forms', 'Are you sure you want to delete the language version of this field?')
                                        ]);
                                    else  // Other localization versions
                                        $output[] = Html::a(Yii::t('app/modules/forms','Delete language version: {language}', [
                                            'language' => $language
                                        ]), ['fields/delete', 'id' => $data->id, 'locale' => $locale], [
                                            'data-method' => 'POST',
                                            'data-confirm' => Yii::t('app/modules/forms', 'Are you sure you want to delete the language version of this field?')
                                        ]);

                                }
                            }
                        }

                        if (is_array($output)) {
                            if (count($output) > 1) {
                                $html = '';
                                $html .= '<div class="btn-group">';
                                $html .= Html::a(
                                    '<span class="glyphicon glyphicon-trash"></span> ' .
                                    Yii::t('app/modules/forms', 'Delete') .
                                    ' <span class="caret"></span>',
                                    '#',
                                    [
                                        'class' => "btn btn-block btn-link btn-xs dropdown-toggle",
                                        'data-toggle' => "dropdown",
                                        'aria-haspopup' => "true",
                                        'aria-expanded' => "false"
                                    ]);
                                $html .= '<ul class="dropdown-menu dropdown-menu-right">';
                                $html .= '<li>' . implode("</li><li>", $output) . '</li>';
                                $html .= '</ul>';
                                $html .= '</div>';
                                return $html;
                            }
                        }
                        return Html::a('<span class="glyphicon glyphicon-trash"></span> ' .
                            Yii::t('app/modules/forms', 'Delete'),
                            [
                                'fields/delete',
                                'id' => $data->id
                            ], [
                                'class' => 'btn btn-link btn-xs',
                                'data-method' => 'POST',
                                'data-confirm' => Yii::t('app/modules/forms', 'Are you sure you want to delete this field?')
                            ]
                        );
                    }
                ],
            ]
        ],
        'pager' => [
            'options' => [
                'class' => 'pagination',
            ],
            'maxButtonCount' => 5,
            'activePageCssClass' => 'active',
            'prevPageCssClass' => 'prev',
            'nextPageCssClass' => 'next',
            'firstPageCssClass' => 'first',
            'lastPageCssClass' => 'last',
            'firstPageLabel' => Yii::t('app/modules/forms', 'First page'),
            'lastPageLabel'  => Yii::t('app/modules/forms', 'Last page'),
            'prevPageLabel'  => Yii::t('app/modules/forms', '&larr; Prev page'),
            'nextPageLabel'  => Yii::t('app/modules/forms', 'Next page &rarr;')
        ],
    ]); ?>
    <hr/>
    <div>
        <?= Html::a(Yii::t('app/modules/forms', 'Create new field'), ['fields/create'], ['class' => 'btn btn-add btn-success pull-right']) ?>
    </div>
    <?php Pjax::end(); ?>
</div>

<?php echo $this->render('../_debug'); ?>