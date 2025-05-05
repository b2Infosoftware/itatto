<?php

namespace App\Rules;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Contracts\Validation\ValidationRule;

class AllowSpecialChars implements ValidationRule
{
    public $allowNumbers;

    /**
     * Class constructor.
     */
    public function __construct(bool $allowNumbers = false)
    {
        $this->allowNumbers = $allowNumbers;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $pattern = $this->allowNumbers
            ? '/^[\p{L}\s\p{P}\p{M}]+$/u'
            : '/^[\p{L}\s\p{P}\p{M}]+$/u';  

        if (Str::of($value)->match($pattern)->isEmpty()) {
            if ($this->allowNumbers) {
                $fail(trans('validation.special_chars'));
            } else {
                $fail(trans('validation.special_chars_not_numbers'));
            }
        }
    }
}
