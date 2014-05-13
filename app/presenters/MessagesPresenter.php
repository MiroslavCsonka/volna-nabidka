<?php

use Nette\Application\UI\Form;

/**
 * Presenter, který operuje nad zprávami mezi uživateli
 */
class MessagesPresenter extends BasePresenter {

	/** @var \Mapper\Thread */
	private $threadMapper;

	public function startup() {
		parent::startup();
		$this->onlyForLoggedIn();
		$this->threadMapper = $this->context->threadMapper;
	}

	public function renderDefault() {
		$threads = $this->threadMapper->getThreadsByUser($this->oUser);
		$messages = array();
		/** @var $thread \Entity\Thread */
		foreach ($threads as $thread) {
			$lastMessage = $thread->getLastMessage();
			if ($lastMessage) {
				$messages[] = array('lastMessage' => $lastMessage,
										  'users'       => $thread->getUsers(),
										  'read'        => (bool)($lastMessage->last_id === $lastMessage->lastRead_id));
			}
		}
		$this->template->messages = $messages;
	}

	/**
	 * Provede vykreslení detailu jedné koverzace
	 * @param int $id Jméno vlákna
	 */
	public function renderDetail($id) {
		$thread = $this->threadMapper->find($id);
		if (!($thread instanceof \Entity\Thread)) {
			$this->bfm('Vlákno nenalezeno');
			$this->redirect('Homepage:');
		}

		if ($thread->isUserAtThread($this->oUser)) {
			$thread->markAsReadByUser($this->oUser);
			$this->template->messages = $thread->getMessages();
			$this['writeMessageToThreadForm']['threadId']->setDefaultValue($id);
			$this->template->users = $thread->getUsers();
		}
		else {
			$this->bfm('Do této diskuze, nemáte přístup');
			$this->redirect('Homepage:');
		}

	}

	/**
	 * Formulář na psaní zprávy do vlákna
	 * @return Nette\Application\UI\Form
	 */
	public function createComponentWriteMessageToThreadForm() {
		$form = new BaseForm();
		$form->addHidden('threadId');
		$form->addTextArea('text', 'Obsah')
                        ->setRequired("Vyplňte text zprávy");
		$form->addSubmit('submit', 'Odeslat');
		$form->onSuccess[] = callback($this, 'writeMessageToThreadSuccess');
		return $form;
	}

	/**
	 * Přijme požadavek na přidání zprávy do vlákna
	 * @param Nette\Application\UI\Form $form
	 */
	public function writeMessageToThreadSuccess(Form $form) {
		/** @var $thread \Entity\Thread */
		$thread = $this->context->threadMapper->find($form->values->threadId);
		if ($thread->isUserAtThread($this->oUser)) {
			$message = new Message($form->values->text, new DateTime);
			$status = $thread->addMessage($message, $this->oUser);
			$this->fm($status, 'Zpráva byla odeslána', 'Zprávu se nepodařilo odeslat');
			$this->redirect('this#userPanelWrapper');
		}
		else {
			$this->bfm($this->context->parameters['notAuthorized']);
			$this->redirect('Homepage:');
		}
	}

	/**
	 * Komponenta zajištující napšání zprávy do vlákna
	 * @return WriteMessageComponent
	 */
	public function createComponentMessageForm() {
		$component = new \WriteMessageComponent;
		return $component;
	}


	/**
	 * @param string $term kousek nicku uživatele podle kterého se vygeneruje response
	 */
	public function actionGetUsers($term) {
		$users = $this->context->userMapper->getNicks($term);
		$response = array();
		foreach ($users as $index => $name) {
			if ($index !== $this->user->id) {
				$response[] = array('value' => $index, 'text' => $name);
			}
		}
		$this->sendResponse(
			new \Nette\Application\Responses\JsonResponse($response)
		);
	}

}
