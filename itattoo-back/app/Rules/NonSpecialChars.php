<?php

namespace App\Rules;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Contracts\Validation\ValidationRule;

class NonSpecialChars implements ValidationRule
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
        $pattern = $this->allowNumbers ? '/^[a-zA-Z0-9\s,\'.-]+$/' : '/^[a-zA-Z\s,\'.-]+$/';

        if (Str::of($value)->match($pattern)->isEmpty()) {
            if ($this->allowNumbers) {
                $fail(trans('validation.non_special_chars'));
            } else {
                $fail(trans('validation.non_special_nor_numbers'));
            }
        }
    }
}
