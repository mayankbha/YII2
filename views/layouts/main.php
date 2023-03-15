<?php
/**
 * @copyright 2017 Champion Computer Consulting Inc. - All Rights Reserved.
 * @var $this \yii\web\View
 * @var $content string
 */
use yii\bootstrap\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Url;
use app\assets\AppAsset;

if(!isset($_REQUEST['ajax'])){
    AppAsset::register($this);
    ?>

    <?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <title><?= Html::encode($this->title) ?></title>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <?= Html::csrfMetaTags() ?>
        <?php $this->head() ?>
    </head>
    <body class="<?= (isset($this->params['showBear']) && $this->params['showBear'] == true) ? 'bear_bg' : '' ?> ">
    <?php $this->beginBody() ?>

    <div class="wrap">
        <?php NavBar::begin([
            'brandImage' => Url::toRoute('/img/logo.png', null),
            'brandUrl' => Yii::$app->homeUrl,
            'options' => [
                'class' => 'navbar navbar-default navbar-fixed-top',
            ],
        ]);

        if (!Yii::$app->user->isGuest) {
            $menuItems = [
                [
                    'label' => Yii::t('app', 'Account settings'),
                    'active' => in_array($this->context->id, ['group', 'user', 'tenant', 'logo', 'security-filter']),
                    'items' => [
                        [
                            'label' => Yii::t('app', 'Groups'),
                            'url' => ['/admin/group'],
                            'active' => $this->context->id == 'group'
                        ],
                        [
                            'label' => Yii::t('app', 'Users'),
                            'url' => ['/admin/user'],
                            'active' => $this->context->id == 'user'
                        ],
                        [
                            'label' => Yii::t('app', 'Tenant'),
                            'url' => ['/admin/tenant'],
                            'active' => $this->context->id == 'tenant'
                        ],
                        [
                            'label' => Yii::t('app', 'Logo'),
                            'url' => ['/admin/logo'],
                            'active' => $this->context->id == 'logo'
                        ],
                        [
                            'label' => Yii::t('app', 'Security filters'),
                            'url' => ['/admin/security-filter'],
                            'active' => $this->context->id == 'security-filter'
                        ],
                    ]
                ],
                [
                    'label' => Yii::t('app', 'Screen configurations'),
                    'active' => in_array($this->context->id, ['menu', 'group-screen', 'screen']),
                    'items' => [
                        [
                            'label' => Yii::t('app', 'Menu'),
                            'url' => ['/admin/menu'],
                            'active' => $this->context->id == 'menu'
                        ],
                        [
                            'label' => Yii::t('app', 'Group screens'),
                            'url' => ['/admin/group-screen'],
                            'active' => $this->context->id == 'group-screen'
                        ],
                        [
                            'label' => Yii::t('app', 'Screens'),
                            'url' => ['/admin/screen'],
                            'active' => $this->context->id == 'screen'
                        ],
                    ]
                ],
                [
                    'label' => Yii::t('app', 'Document access'),
                    'active' => in_array($this->context->id, ['document-family', 'document-group']),
                    'items' => [
                        [
                            'label' => Yii::t('app', 'Document family'),
                            'url' => ['/admin/document-family'],
                            'active' => $this->context->id == 'document-family'
                        ],
                        [
                            'label' => Yii::t('app', 'Document groups'),
                            'url' => ['/admin/document-group'],
                            'active' => $this->context->id == 'document-group'
                        ],
                    ]
                ],
                [
                    'label' => Yii::t('app', 'System data'),
                    'active' => in_array($this->context->id, ['alias', 'autofill', 'error-managemen', 'lists', 'template', 'custom-data-source', 'extension-function', 'servers', 'security-questions', 'notification']),
                    'items' => [
                        [
                            'label' => Yii::t('app', 'Alias Management'),
                            'url' => ['/admin/alias'],
                            'active' => $this->context->id == 'alias'
                        ],
                        [
                            'label' => Yii::t('app', 'AutoFill '),
                            'url' => ['/admin/autofill'],
                            'active' => $this->context->id == 'autofill'
                        ],
                        [
                            'label' => Yii::t('app', 'Error Messages Management'),
                            'url' => ['/admin/error-management'],
                            'active' => $this->context->id == 'error-management'
                        ],
                        [
                            'label' => Yii::t('app', 'Lists'),
                            'url' => ['/admin/lists'],
                            'active' => $this->context->id == 'lists'
                        ],
                        [
                            'label' => Yii::t('app', 'Templates'),
                            'url' => ['/admin/template'],
                            'active' => $this->context->id == 'template'
                        ],
                        [
                            'label' => Yii::t('app', 'Custom data sources'),
                            'url' => ['/admin/custom-data-source'],
                            'active' => $this->context->id == 'custom-data-source'
                        ],
                        [
                            'label' => Yii::t('app', 'Extension functions'),
                            'url' => ['/admin/extension-function'],
                            'active' => $this->context->id == 'extension-function'
                        ],
                        [
                            'label' => Yii::t('app', 'Servers'),
                            'url' => ['/admin/servers'],
                            'active' => $this->context->id == 'servers'
                        ],
                        [
                            'label' => Yii::t('app', 'Security questions'),
                            'url' => ['/admin/security-questions'],
                            'active' => $this->context->id == 'security-questions'
                        ],
                        [
                            'label' => Yii::t('app', 'Notifications'),
                            'url' => ['/admin/notification'],
                            'active' => $this->context->id == 'notification'
                        ],
                    ]
                ],
                [
                    'label' => '<span class="glyphicon glyphicon-user" aria-hidden="true"></span>&nbsp;' . Yii::t('app', 'Features'),
                    'encode' => false,
                    'active' => in_array($this->context->id, ['custom-query', 'management', 'default']),
                    'items' => [
                        [
                            'label' => '<span class="glyphicon glyphicon-comment" aria-hidden="true"></span> ' . Yii::t('app', 'Chat'),
                            'encode' => false,
                            'url' => ['/chat'],
                            'active' => $this->context->id == 'chat'
                        ],
                        [
                            'label' => '<span class="glyphicon glyphicon-wrench" aria-hidden="true"></span>&nbsp;' . Yii::t('app', 'Custom query'),
                            'encode' => false,
                            'url' => ['/admin/custom-query'],
                            'active' => $this->context->id == 'custom-query'
                        ],
                        [
                            'label' => '<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>&nbsp;' . Yii::t('app', 'Management'),
                            'encode' => false,
                            'url' => ['/admin/management'],
                            'active' => $this->context->id == 'management'
                        ],
                        [
                            'label' => '<span class="glyphicon glyphicon-floppy-save" aria-hidden="true"></span>&nbsp;' . Yii::t('app', 'Import files'),
                            'encode' => false,
                            'url' => ['/files'],
                            'active' => $this->context->id == 'default'
                        ],
                        [
                            'label' => '<span class="glyphicon glyphicon-wrench" aria-hidden="true"></span>&nbsp;' . Yii::t('app', 'Job Scheduler'),
                            'encode' => false,
                            'url' => ['/admin/job-scheduler'],
                            'active' => $this->context->id == 'job-scheduler'
                        ],
						[
                            'label' => '<span class="glyphicon glyphicon-wrench" aria-hidden="true"></span>&nbsp;' . Yii::t('app', 'Tables'),
                            'encode' => false,
                            'url' => ['/admin/table'],
                            'active' => $this->context->id == 'tables'
                        ],
                        [
                            'label' => '<span class="glyphicon glyphicon-log-out" aria-hidden="true"></span>&nbsp;' . Yii::t('app', 'Logout'),
                            'url' => ['/logout'],
                            'encode' => false,
                            'linkOptions' => [
                                'aria-hidden' => true,
                                'data-method' => "post"
                            ]
                        ]
                    ]

                ]
            ];
        } else {
            $menuItems = [
                [
                    'label' => Yii::t('app', 'Login'),
                    'url' => ['/site/login']
                ],
            ];
        }

        $menuItems[] = [
            'label' => Yii::t('app', 'Language'),
            'encode' => false,
            'items' => [
                [
                    'label' => '<a class="lg" id="ar-AR" name="ar-AR" ><img src="' . Url::toRoute('/img/flags/arab_league.png') . '" class="flags" > '.Yii::t('app', 'Arabic').'</a>&nbsp;',
                    'encode' => false,
                    'linkOptions' => [
                        'aria-hidden' => true,
                        'data-method' => "post"
                    ]
                ],
                [
                    'label' => '<a class="lg" id="chs-CHS" name="chs-CHS" ><img src="' . Url::toRoute('/img/flags/china.png') . '" class="flags" > '.Yii::t('app', 'Chinese Simplified').'</a>&nbsp;',
                    'encode' => false,
                    'linkOptions' => [
                        'aria-hidden' => true,
                        'data-method' => "post"
                    ]
                ],
                [
                    'label' => '<a class="lg" id="cht-CHT" name="cht-CHT" ><img src="' . Url::toRoute('/img/flags/china.png') . '" class="flags" > '.Yii::t('app', 'Chinese Traditional').'</a>&nbsp;',
                    'encode' => false,
                    'linkOptions' => [
                        'aria-hidden' => true,
                        'data-method' => "post"
                    ]
                ],
                [
                    'label' => '<a class="lg" id="en-US" name="en-US" ><img src="' . Url::toRoute('/img/flags/usa.png') . '" class="flags" > '.Yii::t('app', 'English').'</a>&nbsp;',
                    'encode' => false,
                    'linkOptions' => [
                        'aria-hidden' => true,
                        'data-method' => "post"
                    ]
                ],
                [
                    'label' => '<a class="lg" id="fil-FIL" name="fil-FIL" ><img src="' . Url::toRoute('/img/flags/ph.png') . '" class="flags" > '.Yii::t('app', 'Filipino').'</a>&nbsp;',
                    'encode' => false,
                    'linkOptions' => [
                        'aria-hidden' => true,
                        'data-method' => "post"
                    ]
                ],
                [
                    'label' => '<a class="lg" id="fr-FR" name="fr-FR" ><img src="' . Url::toRoute('/img/flags/france.png') . '" class="flags" > '.Yii::t('app', 'French').'</a>&nbsp;',
                    'encode' => false,
                    'linkOptions' => [
                        'aria-hidden' => true,
                        'data-method' => "post"
                    ]
                ],
                [
                    'label' => '<a class="lg" id="gr-GR" name="gr-GR" ><img src="' . Url::toRoute('/img/flags/germany.png') . '" class="flags" > '.Yii::t('app', 'German').'</a>&nbsp;',
                    'encode' => false,
                    'linkOptions' => [
                        'aria-hidden' => true,
                        'data-method' => "post"
                    ]
                ],
                [
                    'label' => '<a class="lg" id="hi-HI" name="hi-HI" ><img src="' . Url::toRoute('/img/flags/hindi.png') . '" class="flags" > '.Yii::t('app', 'Hindi').'</a>&nbsp;',
                    'encode' => false,
                    'linkOptions' => [
                        'aria-hidden' => true,
                        'data-method' => "post"
                    ]
                ],
                [
                    'label' => '<a class="lg" id="ja-JA" name="ja-JA" ><img src="' . Url::toRoute('/img/flags/japan.png') . '" class="flags" > '.Yii::t('app', 'Japanese').'</a>&nbsp;',
                    'encode' => false,
                    'linkOptions' => [
                        'aria-hidden' => true,
                        'data-method' => "post"
                    ]
                ],
                [
                    'label' => '<a class="lg" id="ko-KO" name="gr-GR" ><img src="' . Url::toRoute('/img/flags/Korea.png') . '" class="flags" > '.Yii::t('app', 'Korean').'</a>&nbsp;',
                    'encode' => false,
                    'linkOptions' => [
                        'aria-hidden' => true,
                        'data-method' => "post"
                    ]
                ],
                [
                    'label' => '<a class="lg" id="pt-PT" name="pt-PT" ><img src="' . Url::toRoute('/img/flags/portugal.png') . '" class="flags" > '.Yii::t('app', 'Portuguese').'</a>&nbsp;',
                    'encode' => false,
                    'linkOptions' => [
                        'aria-hidden' => true,
                        'data-method' => "post"
                    ]
                ],
                [
                    'label' => '<a class="lg" id="ru-RU" name="ru-RU" ><img src="' . Url::toRoute('/img/flags/russia.png') . '" class="flags" > '.Yii::t('app', 'Russian').'</a>&nbsp;',
                    'encode' => false,
                    'linkOptions' => [
                        'aria-hidden' => true,
                        'data-method' => "post"
                    ]
                ],
                [
                    'label' => '<a class="lg" id="sp-SP" name="sp-SP" ><img src="' . Url::toRoute('/img/flags/spain.png') . '" class="flags" > '.Yii::t('app', 'Spanish').'</a>&nbsp;',
                    'encode' => false,
                    'linkOptions' => [
                        'aria-hidden' => true,
                        'data-method' => "post"
                    ]
                ]

            ]
        ];

        echo Nav::widget([
            'options' => ['class' => 'navbar-nav navbar-left header-menu'],
            'items' => $menuItems,
        ]);

        NavBar::end() ?>

        <div class="container">
            <?= $content ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p><?php echo Yii::t('app', '{copy} {year} Champion Computer Consulting Inc.', ['copy' => '&copy;', 'year' =>  date('Y')]) ?></p>
        </div>
    </footer>

    <?php $this->endBody() ?>

    <?php
    $current_url = Url::current();
    $current_url = explode("?", $current_url);
    $current_url = $current_url[0];
    $current_urlarr = explode("/", $current_url);
    if($current_urlarr){
        if(isset($current_urlarr[count($current_urlarr)-2])){
            $page_url = $current_urlarr[count($current_urlarr)-2];
            if(($page_url == 'alias' || $page_url == 'alias-dependency' || $page_url == 'alias-relationship') && $current_urlarr[count($current_urlarr)-1] != 'api'){
                echo '<script src="'.Url::toRoute('/js/alias.js').'"></script>';
                echo '<script>setAliasBaseUrl("' . Url::toRoute('/', true) . '")</script>';
            }
        }
        if(isset($current_urlarr[count($current_urlarr)-3])){
            $page_url = $current_urlarr[count($current_urlarr)-3];
            if(($page_url == 'alias' || $page_url == 'alias-dependency' || $page_url == 'alias-relationship') && $current_urlarr[count($current_urlarr)-1] != 'api'){
                echo '<script src="'.Url::toRoute('/js/alias.js').'"></script>';
                echo '<script>setAliasBaseUrl("' . Url::toRoute('/', true) . '")</script>';
            }
        }
    }
    ?>
    </body>
    </html>
    <?php $this->endPage() ?>
<?php } else {
    echo $content;
} ?>