<?php

namespace App\Form;


use Symfony\Component\Form\FormEvent;
use Symfony\Component\String\Slugger\AsciiSlugger;



class FormListenerFactory
{
   public function autoSlug(string $field): callable
   {
      return function (FormEvent $event) use ($field) {
         $data = $event->getData();
         if (empty($data['slug'])) {
            $slugger = new AsciiSlugger();
            $data['slug'] = strtolower($slugger->slug($data[$field]));
            $event->setData($data);
         }
      };
   }

   public function timesTamps(): callable
   {
      return function (FormEvent $event) {
         $data = $event->getData();

         $data->setUpdatedAt(new \DateTimeImmutable());
         if (!$data->getId()) {
            $data->setCreatedAt(new \DateTimeImmutable());
         }
      };
   }
}
