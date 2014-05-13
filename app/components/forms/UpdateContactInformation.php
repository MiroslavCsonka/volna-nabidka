<?php

use Nette\Application\UI\Form,
    Nette\ComponentModel\IContainer;

/**
 * Formulář na kontaktní informace
 */
class UpdateContactInformation extends \BaseForm {

   public function __construct(\Data $provider, IContainer $parent = NULL, $name = NULL) {
      parent::__construct($parent, $name);
      $this->addSelect("city", "Město", $provider->getCities())
              ->setPrompt('- Vyberte město -')
              ->setAttribute("class", "chosen")
              ->setAttribute("data-placeholder", "Vyberte město");

      $this->addText("icq", "ICQ:")
              ->addCondition(Form::FILLED)
              ->addRule(Form::PATTERN, "Vyplňte validní ICQ číslo", "^([0-9]{9})?$");

      $this->addText("skype", "Skype")
              ->addCondition(Form::FILLED)
              ->addRule(
                      Form::PATTERN, "Vyplňte validní Skype jméno", "^([a-z][a-z0-9.]{5,31})?$"
      );


      $this->addText("telephone", "Telefon")
              ->addCondition(Form::FILLED)
              ->addRule(
                      Form::PATTERN, "Vyplňte validní telefoní číslo", "^(\+420)?[0-9]{9}$"
               );
      ;

      $this->addSubmit("updateInformation", "Přidat");
   }

}
