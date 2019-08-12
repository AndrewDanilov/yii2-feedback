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
            'jsCallback' => 'formSubmitted', // optional: js-function name to call after success submit happen
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
You can add as many controller mappings as you want. Each mapping item for one feedback form instance.

In view-file add widget call:

```php
<?= \andrewdanilov\feedback\FeedbackWidget::widget([
    'controller' => 'callback',
]) ?>
```

Where 'callback' is controller id configured in 'controllerMap' section of your config.

You can use 'jsCallback' param, to define js-function, which would be called after successfull form submit. In that function you can execute analytic goals, i.e. Google Analytics or Yandex Metrika:

```js
function formSubmitted(form) {
    ga('send', 'event', 'my_form', 'submit');
    yaCounter100500.reachGoal('my_goal');
}
```