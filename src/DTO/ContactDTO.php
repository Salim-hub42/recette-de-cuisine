<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;



class ContactDTO
{
   #[Assert\NotBlank]
   #[Assert\Length(min: 2, max: 255)]
   public string $name = '';


   #[Assert\NotBlank]
   #[Assert\Email(
      message: 'The email {{ value }} is not a valid email.',
   )]
   public string $email = '';


   #[Assert\NotBlank]
   #[Assert\Length(min: 10, max: 250)]
   public string $message = '';

   #[Assert\NotBlank]
   public string $service = '';
}
