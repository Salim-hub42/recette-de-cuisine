<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
final class BanWord extends Constraint
{

    public function __construct(
        public string $message = 'Le champ contient un mot interdit : "{{ banWord }}".',
        public array $banWords = ['spam', 'viagra'],
        ?array $groups = null,
        mixed $payload = null
    ) {
        parent::__construct($groups, $payload);
    }
}
