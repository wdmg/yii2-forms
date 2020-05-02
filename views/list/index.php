<?php

use wdmg\helpers\StringHelper;
use wdmg\widgets\SelectInput;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\vendor\wdmg\forms\models\FormsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/modules/forms', 'All forms');
$this->params['breadcrumbs'][] = Yii::t('app/modules/forms', 'Forms list');

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
<div class="forms-index">
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => '{summary}<br\/>{items}<br\/>{summary}<br\/><div class="text-center">{pager}</div>',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name',
            'alias',
            'title',
            [
                'attribute' => 'description',
                'format' => 'text',
                'value' => function($data) {
                    if (!empty($data->description))
                        return StringHelper::truncateWords(StringHelper::stripTags($data->description, "", " "),12,'…');
                    else
                        return null;
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
                                            'list/update', 'id' => $data->id
                                        ], [
                                            'title' => Yii::t('app/modules/forms','Edit source version: {language}', [
                                                'language' => $locale['name']
                                            ])
                                        ]
                                    );
                                else  // Other localization versions
                                    $output[] = Html::a($flag,
                                        [
                                            'list/update', 'id' => $data->id,
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
                                            'list/update', 'id' => $data->id
                                        ], [
                                            'title' => Yii::t('app/modules/forms','Edit source version: {language}', [
                                                'language' => $language
                                            ])
                                        ]
                                    );
                                else  // Other localization versions
                                    $output[] = Html::a($language,
                                        [
                                            'list/update', 'id' => $data->id,
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


                    if (is_countable($output)) {
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
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
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
                                        ]), ['list/view', 'id' => $data->id]);
                                    else  // Other localization versions
                                        $output[] = Html::a(Yii::t('app/modules/forms','View language version: {language}', [
                                            'language' => $locale['name']
                                        ]), ['list/view', 'id' => $data->id, 'locale' => $locale['locale']]);

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
                                        ]), ['list/view', 'id' => $data->id]);
                                    else  // Other localization versions
                                        $output[] = Html::a(Yii::t('app/modules/forms','View language version: {language}', [
                                            'language' => $language
                                        ]), ['list/view', 'id' => $data->id, 'locale' => $locale]);

                                }
                            }
                        }

                        if (is_countable($output)) {
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
                                'list/view',
                                'id' => $data->id
                            ], [
                                'class' => 'btn btn-link btn-xs'
                            ]
                        );
                    },
                    'update' => function($url, $data, $key) {
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
                                        ]), ['list/update', 'id' => $data->id]);
                                    else  // Other localization versions
                                        $output[] = Html::a(Yii::t('app/modules/forms','Edit language version: {language}', [
                                            'language' => $locale['name']
                                        ]), ['list/update', 'id' => $data->id, 'locale' => $locale['locale']]);

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
                                        ]), ['list/update', 'id' => $data->id]);
                                    else  // Other localization versions
                                        $output[] = Html::a(Yii::t('app/modules/forms','Edit language version: {language}', [
                                            'language' => $language
                                        ]), ['list/update', 'id' => $data->id, 'locale' => $locale]);

                                }
                            }
                        }

                        if (is_countable($output)) {
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
                                'list/update',
                                'id' => $data->id
                            ], [
                                'class' => 'btn btn-link btn-xs'
                            ]
                        );
                    },
                    'delete' => function($url, $data, $key) {
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
                                        ]), ['list/delete', 'id' => $data->id], [
                                            'data-method' => 'POST',
                                            'data-confirm' => Yii::t('app/modules/forms', 'Are you sure you want to delete the language version of this form?')
                                        ]);
                                    else  // Other localization versions
                                        $output[] = Html::a(Yii::t('app/modules/forms','Delete language version: {language}', [
                                            'language' => $locale['name']
                                        ]), ['list/delete', 'id' => $data->id, 'locale' => $locale['locale']], [
                                            'data-method' => 'POST',
                                            'data-confirm' => Yii::t('app/modules/forms', 'Are you sure you want to delete the language version of this form?')
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
                                        ]), ['list/delete', 'id' => $data->id], [
                                            'data-method' => 'POST',
                                            'data-confirm' => Yii::t('app/modules/forms', 'Are you sure you want to delete the language version of this form?')
                                        ]);
                                    else  // Other localization versions
                                        $output[] = Html::a(Yii::t('app/modules/forms','Delete language version: {language}', [
                                            'language' => $language
                                        ]), ['list/delete', 'id' => $data->id, 'locale' => $locale], [
                                            'data-method' => 'POST',
                                            'data-confirm' => Yii::t('app/modules/forms', 'Are you sure you want to delete the language version of this form?')
                                        ]);

                                }
                            }
                        }

                        if (is_countable($output)) {
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
                                'list/delete',
                                'id' => $data->id
                            ], [
                                'class' => 'btn btn-link btn-xs',
                                'data-method' => 'POST',
                                'data-confirm' => Yii::t('app/modules/forms', 'Are you sure you want to delete this form?')
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
            'prevPageCssClass' => '',
            'nextPageCssClass' => '',
            'firstPageCssClass' => 'previous',
            'lastPageCssClass' => 'next',
            'firstPageLabel' => Yii::t('app/modules/forms', 'First page'),
            'lastPageLabel'  => Yii::t('app/modules/forms', 'Last page'),
            'prevPageLabel'  => Yii::t('app/modules/forms', '&larr; Prev page'),
            'nextPageLabel'  => Yii::t('app/modules/forms', 'Next page &rarr;')
        ],
    ]); ?>
    <hr/>
    <div>
        <?= Html::a(Yii::t('app/modules/forms', 'Create new form'), ['list/create'], ['class' => 'btn btn-add btn-success pull-right']) ?>
    </div>
    <?php Pjax::end(); ?>
</div>

<?php echo $this->render('../_debug'); ?>