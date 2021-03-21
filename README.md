Feedback forms
===================
Tools for creating feedback and callback forms

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
composer require andrewdanilov/yii2-feedback "~1.0.0"
```

or add

```
"andrewdanilov/yii2-feedback": "~1.0.0"
```

to the `require` section of your `composer.json` file.


Usage
-----

In common/config/main_local.php config file setup mailer component:

```php
return [
    // ...
    'components' => [
        // ...
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
];
```

In `frontend/config/main.php` add following lines to controllerMap section:

```php
return [
    // ...
    'controllerMap' => [
        // ...
        'callback' => [
            'class' => 'andrewdanilov\feedback\FeedbackController',
            // If you want you can use your own views for form and mail.
            // Just copy views files from extension folder to your location
            // and set formView, mailView and mailLayout paths here:
            'formView' => '@frontend/views/feedback/default', // optional
            'mailView' => '@frontend/mail/feedback/default', // optional
            'mailLayout' => '@frontend/mail/feedback/layouts/html', // optional
            // label for extra field in mail template            
            'extraFieldLabel' => 'Extra data', // optional
            'from' => ['admin@example.com' => 'My Site'],
            'to' => ['admin@example.com', 'admin2@example.com'],
            'subject' => 'Mail from site', // optional
            'fields' => [
                'name' => [
                    'required' => true, // optional
                    'label' => 'Name', // optional
                    'placeholder' => 'Enter your name', // optional
                    'type' => 'text', // optional, default 'text'
                    'maxlength' => 255, // optional
                    'class' => 'field-name', // optional
                    'style' => 'margin-bottom: 10px;', // optional
                ],
                'address', // simple notation without config array
                'email' => [
                    'required' => true,
                    'label' => 'Email',
                    'placeholder' => 'Enter your e-mail',
                    'type' => 'email',
                    'class' => 'field-email',
                    'validator' => ['MyValidatorClass', 'myEmailValidator'], // optional, validator as an anonymous function, a function name as a string, or a valid PHP callable array
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
                    'exclude' => true, // field will be excluded form mail
                ],
            ],
        ],
    ],
];
```

You can add as many controller mappings as you want. Each controller mapping represents one feedback form instance.

You can use your own validators for field values. You just need to define 'validator' property of field defintion (see config above).
For example, you can use php callable array:

```
'field_name' => [
    //...
    'validator' => ['frontend\components\validators\MyValidatorClass', 'myEmailValidator'],
];
```

Then you need to create method `myEmailValidator` within class `MyValidatorClass`:

```php
<?php
namespace frontend\components\validators;

class MyValidatorClass
{
    // method accepts three parameters: first is validating field name,
    // second - its value, third - all form values in case if you need
    // to check some other fields to correctly validate current field
    public static function myEmailValidator($field_name, $field_value, $fields_values)
    {
        if (!preg_match('~@~', $field_value)) {
            // if it is not ok, return an arror
            return [
                'error' => 'Email is incorrect',
            ];
        }
        return true; // if all is ok - return boolean true;
    }
}
```

If you use "'enableStrictParsing' => true" in your urlManager, then you need to add rule:

```php
return [
    // ...
    'urlManager' => [
        // ...
        'enableStrictParsing' => true,
        'rules' => [
            // ...
            '<controller>/send' => '<controller>/send', // this needs to be add to represent ajax handler
            '' => 'site/index',
        ],
    ],
];
```

Instead of \<controller\> you can use particular controller ids defined in 'controllerMap'.

In View add widget call:

```php
<?= \andrewdanilov\feedback\FeedbackWidget::widget([
    // controller id configured in 'controllerMap' section of your config
    'controller' => 'callback',
    // optional: widget ID
    'id' => 'mywidgetID',
    // optional: instead of form displays button, which call floating form on click
    'lightbox' => [
        // optional: tag represents button triggering form appearance (div, span, i, a, etc.)
        // if not set or null, button will not be displayed, than you need to
        // create it manually, i.e.:
        // <a href="javascript:;" data-fancybox data-src="#feedback-mywidgetID" data-extra="extra message">Call me back!</a>
        // in that case you can pass extra data to your form, and each triggering button
        // can provide its own data to single form instance.
        'button' => 'div',
        // optional: button name
        'label' => 'Call me!',
        // optional: options for yii Html::tag() helper
        'options' => ['class' => 'callback-btn'],
        // optional: delay before lightbox form will be closed
        'delay' => 2500,
        // optional: close button template
        'closeBtn' => '<a href="javascript:$("[data-fancybox-close]").click();$.fancybox.close(true);" class="close-btn">x</a>',
        // optional: lightbox window title
        'title' => 'Call me back!',
    ],
    // optional: javascript code to execute after success submit happen
    'jsCallback' => 'ga("send", "event", "my_form", "submit"); yaCounter100500.reachGoal("my_goal");',
    // optional: redirect visitor to page after submitting form
    'redirect' => \yii\helpers\Url::to(['site/index']),
    // optional: success form submit message
    'successMessage' => 'Message sent. Please, wait for an answer.',
    // optional: name and options for submit button
    'submitButton' => [
        'name' => 'Send msg', // optional
        'options' => ['class' => 'form-submit-button'], // optional
    ],
    // optional: form block options
    'options' => [
        'class' => 'form-block-class',
    ],
]) ?>
```

Simple example
--------------

Controller map in `frontend/config/main.php`

```php
return [
    // ...
    'controllerMap' => [
        // ...
        'callback' => [
            'class' => 'andrewdanilov\feedback\FeedbackController',
            'formView' => '@frontend/views/feedback/default',
            'from' => ['admin@example.com' => 'My Site'],
            'to' => ['admin@example.com', 'admin2@example.com'],
            'subject' => 'Mail from site',
            'fields' => [
                'name',
                'email',
                'phone',
                'message',
            ],
        ],
    ],
];
```

Widget call:

```php
<?= \andrewdanilov\feedback\FeedbackWidget::widget([
    'controller' => 'callback',
    'jsCallback' => '$(".callback-success-message").show();',
]) ?>
```

Form view `frontend/views/feedback/default.php`

```php
<?php

/* @var $this yii\web\View */
/* @var $route string */
/* @var $options array */
/* @var $model \andrewdanilov\feedback\FeedbackForm */
/* @var $fields array */
/* @var $successMessage string */
/* @var $submitButton array */

?>

<form action="<?= $route ?>" id="<?= $options['id'] ?>">
    <input type="hidden" name="<?= \Yii::$app->request->csrfParam ?>" value="<?= \Yii::$app->request->csrfToken ?>">
    <input type="text" name="data[name]">
    <input type="text" name="data[email]">
    <input type="text" name="data[phone]">
    <textarea name="data[message]"></textarea>
    <input type="submit" value="Send">
</form>
<div class="callback-success-message">
    <div>Thank you!</div>
</div>
```