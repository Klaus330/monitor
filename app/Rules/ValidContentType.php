<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidContentType implements Rule
{
    protected array $allowedContentTypes = [
        'text/html',
        'text/plain',
        'application/json',
    ];


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
        $contentTypes = explode(',', $value);

        $valid = true;

        foreach ($contentTypes as $contentType) {
            if (!in_array(trim($contentType, ' '), $this->allowedContentTypes)) {
                $valid = false;
            }
        }

        return $valid;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid content type. The allowed content types are: ' . implode(', ', $this->allowedContentTypes);
    }
}
