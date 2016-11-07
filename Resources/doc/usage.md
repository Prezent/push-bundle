# Usage

## Using the manager
The bundle comes with a manager that allows for easy sending of push messages. This manager is a wrapper around the client of your selected provider. You can get this manager as a service from the Service Container. 

You always have to supply the content of the message. Optionally you can enter an array of extra data, and an array of specific pushtokens to send the message to.

```php
<?php
// Controller.php

public function sendPushMessage()
{
    $pushManager = $this->getContainer()->get('prezent_push.manager');
    $success = $pushManager->send('Hello Bob!', [], []);

    // Check if its ok
    if ($success) {
        print 'Message has been succesfully sent!';
    } else {
        print 'Something went wrong...'; 
        print 'Status code : ' . $pushManager->getErrorCode();
        print 'Status message : ' . $pushManager->getErrorMessage();
    }
}
```

You can also send a batch of push notifications in one request:
```php
<?php
// Controller.php

public function sendPushMessageBatch()
{
    $pushManager = $this->getContainer()->get('prezent_push.manager');
    $success = $pushManager->sendBatch(
        [
            [
                'content' => 'Hello Bob!',
                'data' => [],
                'devices' => []
            ],
            [
                'content' => 'Hello Alice!',
                'data' => [],
                'devices' => []
            ],
        ]
    );

    // Check if its ok
    if ($success) {
        print 'Messages has been succesfully sent!';
    } else {
        print 'Something went wrong...'; 
        print 'Status code : ' . $pushManager->getErrorCode();
        print 'Status message : ' . $pushManager->getErrorMessage();
    }
}
```

## Using the provider client directly
The bundle also creates a service for the actual provider client, that you can get from the Service Container. 
Using this client, you get more flexibility in sending push messages. You can use clients as described in their documentation:
* OneSignal [norkunas/onesignal-php-api](https://github.com/norkunas/onesignal-php-api/blob/master/README.md)
* Pushwoosh [gomoob/php-pushwoosh](http://gomoob.github.io/php-pushwoosh/)

### Example using the Pushwoosh client directly
```php
<?php
// Controller.php

public function sendPushMessage()
{
    $pushwoosh = $this->get('pushwoosh');
    
    // Create a request for the '/createMessage' Web Service
    $request = CreateMessageRequest::create()
        ->addNotification(Notification::create()->setContent('Hello Jean !'));
    
    // Call the REST Web Service
    $response = $pushwoosh->createMessage($request);
    
    // Check if its ok
    if($response->isOk()) {
        print 'Great, my message has been sent !';
    } else {
        print 'Oups, the sent failed :-('; 
        print 'Status code : ' . $response->getStatusCode();
        print 'Status message : ' . $response->getStatusMessage();
    }
}
```