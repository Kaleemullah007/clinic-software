<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidReCaptcha implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Validate the reCaptcha
        $txtSpamCode = md5(request('txtSpamCode'));
        $Md5SpamCode = session('Md5SpamCode');

        if ($txtSpamCode ==$Md5SpamCode) {
            // Verified!
            return true;
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Enter valid Captcha validation code';
    }
}
