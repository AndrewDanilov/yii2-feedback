Feedback forms
===================
Tools for creating feedback and callback forms

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
composer require andrewdanilov/yii2-feedback "dev-master"
```

or add

```
"andrewdanilov/yii2-feedback": "dev-master"
```

to the require section of your `composer.json` file.


Usage
-----

In main config file add:

```php
return [
    ...
    'components' => [
        ...
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'username' => 'admin@example.com',
                'password' => 'yourpassword',
                'host' => 'smtp.example.com',
                'port' => '465',
                'encryption' => 'ssl',
            ],
        ],
    ],
    ...
    'controllerMap' => [
        ...
        'callback' => [
            'class' => 'andrewdanilov\feedback\FeedbackController',
            'mailTpl' => '@andrewdanilov/feedback/mail/default', // optional
            'formTpl' => '@andrewdanilov/feedback/views/default', // optional
            'from' => ['admin@example.com' => 'My Site'],
            'to' => ['admin@example.com', 'admin2@example.com'],
            'subject' => 'Mail from site', // optional
            'fields' => [
                'name' => [
                    'required' => true, // optional
                    'label' => 'Name', // optional
                    'placeholder' => 'Enter your name', // optional
                    'type' => 'text',
                    'maxlength' => 255, // optional
                    'class' => 'field-name', // optional
                ],
                'email' => [
                    'required' => true,
                    'label' => 'Email',
                    'placeholder' => 'Enter your e-mail',
                    'type' => 'email',
                    'class' => 'field-email',
                ],
                'country' => [
                    'label' => 'Select country',
                    'type' => 'select',
                    'items' => [
                        0 => 'Select your country',
                        1 => 'Great Britain',
                        2 => 'Germany',
                        3 => 'Norway',
                    ],
                    'class' => 'field-country',
                ],
                'phone' => [
                    'label' => 'Phone',
                    'placeholder' => 'Enter your phone',
                    'type' => 'tel',
                    'class' => 'field-phone',
                ],
                'comment' => [
                    'label' => 'Comment',
                    'placeholder' => 'Enter your comment',
                    'type' => 'textarea',
                    'maxlength' => 1000,
                    'class' => 'field-comment',
                ],
                'accept_agreement' => [
                    'label' => 'Accept user agreement',
                    'type' => 'checkbox',
                    'default' => 1,
                    'class' => 'field-agreement',
                ],
            ],
        ],
    ],
];
```

You can add as many controller mappings as you want. Each controller mapping represents one feedback form instance.

If you use "'enableStrictParsing' => true" in your urlManager, than you need to add rule:

```php
return [
    ...
    'urlManager' => [
        ...
        'enableStrictParsing' => true,
        'rules' => [
            ...
            '<controller>/send' => '<controller>/send',
        ],
    ],
];
```

Instead of \<controller\> you can use particular controller id's defined in 'controllerMap'.

In View add widget call:

```php
<?= \andrewdanilov\feedback\FeedbackWidget::widget([
    // controller id configured in 'controllerMap' section of your config
    'controller' => 'callback',
    // optional: instead of form displays button, which call floating form on click
    'lightbox' => [
        // optional: tag represents button (div, span, i, a, etc.)
        'button' => 'div',
        // optional: button name
        'label' => 'Call me!',
        // optional: options for yii Html::tag() helper
        'options' => ['class' => 'callback-btn'],
        // optional: delay before lightbox form will be closed
        'delay' => 2500,
        // optional: close button temblate
        'closeBtn' => '<a href="javascript:$.fancybox.close();" class="close-btn">x</a>',
    ],
    // optional: javascript code to execute after success submit happen
    'jsCallback' => "ga('send', 'event', 'my_form', 'submit'); yaCounter100500.reachGoal('my_goal');",
    // optional: redirect visitor to page after submitting form
    'redirect' => \yii\helpers\Url::to(['site/index']),
    // optional: success form submit message
    'success_message' => 'Message sent. Please, wait for an answer.',
]) ?>
```