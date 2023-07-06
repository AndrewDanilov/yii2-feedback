Feedback forms
===================
Component for creating feedback forms with customizable set of fields, user validators,
html templates, file uploads, javascript callbacks.

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
            // Just copy views files from `src/views` and `src/mail` folders
            // of extension to your location, for example, to `@frontend/views/feedback`
            // and `@frontend/mail/feedback` and set correspondent `formView`,
            // `mailView` and `mailLayout` paths here:
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
                    'errors' => [ // optional, default values is:
                        'required' => 'Field "{label}" is required.', // if requred field is empty
                        'maxlength' => 'Field "{label}" length of {maxlength} exeeded.', // if field length exeeded
                        'error' => 'Field "{label}" is incorrect.', // if validator returns false or empty error string
                    ],
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
                'img' => [
                    'label' => 'Upload Image',
                    'type' => 'file',
                    'multiple' => true, // optional, default is false
                    'maxFiles' => 10, // optional, default is 0, that equals no restriction
                    'extensions' => 'pdf, docx', // optional, default is empty string, that equals to any extension
                    'uploadDir' => '@webroot/upload/files', // optional, default is '@webroot/upload'
                    'class' => 'field-file',
                ],
                'accept_agreement' => [
                    'label' => 'Accept user agreement',
                    'type' => 'checkbox',
                    'default' => 1,
                    'class' => 'field-agreement',
                    'exclude' => true, // field will be excluded from mail
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
// key is the field name
'my_email' => [
    //...
    'validator' => ['frontend\components\validators\MyValidatorClass', 'myEmailValidator'],
];
'my_phone' => [
    //...
    'validator' => ['frontend\components\validators\MyValidatorClass', 'myPhoneValidator'],
];
```

Then you need to create methods `myEmailValidator` and `myPhoneValidator` within class `MyValidatorClass`:

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
        // check field only if it is filled
        if ($field_value && !filter_var($field_value, FILTER_VALIDATE_EMAIL)) {
            // if it is not ok, return an arror
            return [
                'error' => 'Email is incorrect',
            ];
            // or you can just return boolean false: "return false;"
        }
        return true; // if all is ok - return boolean true
    }
    
    public static function myPhoneValidator($field_name, $field_value, $fields_values)
    {
        // check field only if it is filled
        if ($field_value && !preg_match('~^[\d\-\(\)\+ ]+$~', $field_value)) {
            // you can just return boolean false instead of an error array,
            // then default error message will be used
            return false;
        }
        return true; // if all is ok - return boolean true
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
            '<controller>/send' => '<controller>/send', // this needs to be added to represent ajax handler
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
    // optional: javascript code to execute after form send error happen
    'jsErrorCallback' => 'alert("Error happened");',
    // optional: redirect visitor to page after submitting form
    'redirect' => \yii\helpers\Url::to(['site/index']),
    // optional: success form submit message
    'successMessage' => 'Message sent. Please, wait for an answer.',
    // optional: name and options for submit button
    'submitButton' => [
        'name' => 'Send msg', // optional
        'options' => ['class' => 'form-submit-button'], // optional
    ],
    // optional: class to be added to input element and its parent in case an error, default is 'has-errors'
    'errorFieldClass' => 'error',
    // optional: scroll to first error field after form send if there is an error, default is true
    'scrollToFirstError' => false,
    // optional: class of element containing error message, default is 'help-block'
    'errorFieldAlertElementClass' => 'alert',
    // optional: form block options
    'options' => [
        'class' => 'form-block-class',
    ],
]) ?>
```

For error handling and successful submissions, you can define js callback functions with the `jsErrorCallback` and `jsCallback` parameters in your widget call. Also, you can use the corresponding defined js events. For example:

```javascript
$(function() {
    $(document).on('mywidgetID-form-submit', function() {
        alert('success');
    });
    $(document).on('mywidgetID-form-error', function() {
        alert('error');
    });	
});
```


Simple example
--------------

Controller map in `frontend/config/main.php`

```php
return [
    // ...
    'controllerMap' => [
        // ...
        'call_me' => [
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
                'img' => [
                    'type' => 'file',
                    'multiple' => true,
                ],
            ],
        ],
    ],
];
```

Widget call:

```php
<?= \andrewdanilov\feedback\FeedbackWidget::widget([
    'controller' => 'call_me',
    'jsCallback' => '$(".call_me-success-message").show();',
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
    <!-- variables needs to be placed into an array "data" -->
    <input type="text" name="data[name]">
    <p class="help-block"></p><!-- block for printing field error must be placed right after input element or right after its parent element -->
    <input type="text" name="data[email]">
    <p class="help-block"></p>
    <input type="text" name="data[phone]">
    <p class="help-block"></p>
    <textarea name="data[message]"></textarea>
    <!-- we can't place files variables into an array "data", so use direct variable name in attribute here -->
    <div class="my-file-uploader">
        <!-- for multiple file selecting you need to add "multiple" attribute to input and [] to field name attribute -->
        <input type="file" name="img[]" multiple>
    </div>
    <p class="help-block"></p><!-- block for printing field error must be placed right after input element or right after its parent element -->
    <input type="submit" value="Send">
</form>
<div class="call_me-success-message">
    <div>Thank you!</div>
</div>
```