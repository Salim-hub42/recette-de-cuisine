<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class BanWordValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        /* @var BanWord $constraint */

        if (null === $value || '' === $value) {
            return;
        }


        $value = strtolower($value); // Convertir la valeur en minuscules pour une comparaison insensible à la casse
        foreach ($constraint->banWords as $banWord) { // Parcourir la liste des mots interdits
            if (str_contains($value, $banWord)) { // Vérifier si le mot interdit est présent dans la valeur
                // Le mot interdit est trouvé dans la valeur, ajouter une violation
                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{ banWord }}', $banWord)
                    ->addViolation()
                ;
                return; // Sortir de la boucle après la première violation trouvée
            }
        }
    }
}
