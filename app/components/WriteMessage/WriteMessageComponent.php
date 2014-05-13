<?php
use Nette\Application\UI\Control,
    \Nette\Application\UI\Form;

class WriteMessageComponent extends Control {

	public function __construct(\Nette\ComponentModel\IContainer $parent = NULL, $name = NULL) {
		parent::__construct($parent, $name);
	}

	public function render(\Entity\User $user = NULL, $hideTo = FALSE) {
		$this->template->hideTo = $hideTo;
		if ($user instanceOf \Entity\User) {
			$this['messageForm-users']
				 ->setItems(array($user->getId() => $user->name))
				 ->setDefaultValue($user->id);
		}
		$this->template->setFile(dirname(__FILE__) . '/default.latte');
		$this->template->render();
	}

	/**
	 * Formulář na odeslání zprávy
	 * @return our_forms\messageForm
	 */
	public function createComponentMessageForm() {
		$form = new \BaseForm();

		$form->addMultiSelect('users', 'Uživatelé:');

		$form->addTextArea('text', 'Obsah')
                        ->setRequired("Vyplňtě obsah zprávy");

		$form->addSubmit('submit', 'Odeslat');

		$form->onSuccess[] = callback($this, "addMessageSuccess");

		return $form;
	}

	/**
	 * Provede odeslání zprávy
	 * @param BaseForm $form
	 */
	public function addMessageSuccess(BaseForm $form) {
		$values = $form->getHttpData();
		/** @var $thread \Entity\Thread */
		$values['users'][] = $this->presenter->user->id;
		/** @var $mapper \Mapper\Thread */
		$mapper = $this->presenter->context->threadMapper;
		$thread = $mapper->getThreadByUsers($values['users']);

		$message = new Message($values['text'], new DateTime);
		$status = $thread->addMessage($message, $this->presenter->user->id);
		if ($status) {
			$this->presenter->fm(TRUE, 'Zpráva byla odeslána');
			$this->presenter->redirect('Messages:detail', $thread->id);
		}
		else {
			$this->presenter->bfm('Zprávu se nepodařilo odeslat');
		}
	}
}