<?php

namespace App\Helper;

use Illuminate\Support\MessageBag;

/* Docs
    1. 
    $errors = [
        'email' => 'The email field is required',
        'password' => 'The password field is required',
    ]
    $apiErrors = new ApiErrorsMessageBag($errors);
    $messages = $apiErrors->all();

    2.
    $apiErrors = new ApiErrorsMessageBag([ 'noti' => 'Show errors']);
    $messages = $apiErrors->all();
    
    3. 
    $validator = Validator::make(['name' => 'Tom'], ['name' => 'required']);
    if ($validator->fails()) {
        $errors = $validator->errors()->messages();
        $apiErrors = new ApiErrorsMessageBag($errors);
        $messages = $apiErrors->all();
    }
*/

class ApiErrorsMessageBag extends MessageBag
{
    /**
     * Get all of the messages for every key in the message bag.
     *
     * @param  string|null  $format
     * @return array
     */
    public function all($format = 'ApiErrors')
    {
        $format = $this->checkFormat($format);

        $all = [];

        foreach ($this->messages as $key => $messages) {
            $all = array_merge($all, $this->transform($messages, $format, $key));
        }

        return $all;
    }

    /**
     * Format an array of messages.
     *
     * @param  array  $messages
     * @param  string  $format
     * @param  string  $messageKey
     * @return array
     */
    protected function transform($messages, $format, $messageKey)
    {
        if ($format == ':message') {
            return (array) $messages;
        }

        $errors = [];

        foreach ($messages as $message) {
            $errors[$messageKey] = $message;
        }

        return $errors;
    }
}
