<?php

use Nette\Application\UI\Form,
	 Nette\Image;

/**
 * Prenseter který pracuje nad nastaveními uživatele
 */
class SettingsPresenter extends BasePresenter {

	/** @var int MINI_PIC_WIDTH Šířka profilového obrázku */

	const MINI_PIC_WIDTH = 70;

	/** @var int MINI_PIC_WIDTH Šířka profilového obrázku */
	const THUMBNAIL_PIC_WIDTH = 140;

	/** @var int MINI_PIC_WIDTH Šířka profilového obrázku */
	const PROFILE_PIC_WIDTH = 220;

	public function startup() {
		parent::startup();
		$this->onlyForLoggedIn();
	}

	public function beforeRender() {
		parent::beforeRender();
		if ($this->isAjax()) {
			$this->invalidateControl();
		}
	}

	/**
	 * Nastaví promenné pro vykreslení titulní stránky nastavení
	 */
	public function renderDefault() {
		$this->template->schools = $this->oUser->getSchools();
		$this->template->languages = $this->oUser->getLanguages();
		$this->template->oldProjects = $this->oUser->getOldProject();
		$this->template->categories = $this->oUser->getCategories();
	}

	/**
	 * Formulář na přidání vzdělání
	 * @return \our_forms\AddEducation
	 */
	public function createComponentAddEducation() {
		$form = new our_forms\AddEducation();
		$form->onSuccess[] = callback($this, "addEducationSuccess");
		return $form;
	}

	/**
	 * Zpracuje přidání vzdělání
	 * @param \Nette\Application\UI\Form $form
	 */
	public function addEducationSuccess(Form $form) {
		$values = $form->getValues(TRUE);
		$values["user_id"] = $this->oUser->getId();
		$education = new \Education($this->context->database);
		$status = $education->add($values);
		$this->fm($status, "Škola byla přidána", "Při přidávání školy nastal problém");
		$this->redirect("this");
	}

	/**
	 * Odebere aktuálnímu uživateli vzdělání
	 * @param int $id
	 */
	public function handleDeleteEducation($id) {
		$education = new \Education($this->context->database);
		$status = $education->delete($id, $this->oUser);
		$this->fm($status, "Záznam o vzdělání byl odebrán", "Nepodařilo se nám odebrat záznam");
	}

	/**
	 * Formulář na aktualizování kategorii do kterých spadá
	 * @return \Nette\Application\UI\Form
	 */
	public function createComponentUpdateCategory() {
		$form = new \our_forms\UpdateCategory();
		/** @var $provider \Data */
		$provider = $this->context->data;
		$form->setItems($provider->getCategories());
		$form['categories']->setDefaultValue(array_keys($this->oUser->getCategories()));
		$form->onSuccess[] = callback($this, 'updateCategorySuccess');
		return $form;
	}

	/**
	 * Aktualizuje uživatelovi kategorie
	 * @param \Nette\Application\UI\Form $form
	 */
	public function updateCategorySuccess(Form $form) {
		$categories = array_values($form->values->categories);
		$selectedCategories = count($categories);
		if ($selectedCategories <= 5) {
			$status = $this->oUser->updateCategories($categories);
			$this->fm($status, "Kategorie byly upraveny", "Nastala chyba");
		}
		else {
			$this->bfm(
				"Vybrali jste $selectedCategories kategorii.Můžete vybrat maximálně 5 kategorii, každá další bude ignorována"
			);
		}
		if ($this->isAjax()) {
			$this->invalidateControl();
		}
		else {
			$this->redirect('this');
		}
	}

	/**
	 * Formulář na přidání nového jazyka
	 * @return \our_forms\AddLanguage
	 */
	public function createComponentAddLanguage() {
		/** @var $provider Data */
		$provider = $this->context->data;
		$form = new our_forms\AddLanguage($provider);
		$form->onSuccess[] = function (Form $form) {
			$language = new Language($form->values->language);
			/** @var $user \Entity\User */
			$user = $form->presenter->context->userMapper->find($form->presenter->user->id);
			try {
				$status = $user->addLanguage($language, $form->values->level);
			} catch (PDOException $e) {
				$form->presenter->bfm("Nemůžete dvakrát přidat stejný jazyk");
				$form->presenter->redirect("this");
			}
			$form->presenter->fm($status, "Jazyk byl přidán", "Při přidávání jazyku došlo k chybě");
			$form->presenter->redirect("this");
		};
		return $form;
	}

	/**
	 * Odebere jazyk aktuálně přihlášenému uživateli
	 * @param int $id
	 */
	public function handleRemoveLanguage($id) {
		$lang = new Language((int)$id);
		$status = $this->oUser->removeLanguage($lang);
		$this->fm($status, "Jazyk byl odebrán", "Při odebírání jazyka nastala chyba");
		$this->redirect("this");
	}

	/**
	 * Formulář na změnu popisku uživatele
	 * @return Nette\Application\UI\Form
	 */
	public function createComponentAboutMeForm() {
		$form = new Form;
		$form->addTextArea('aboutMe', 'O mně:')
			 ->setDefaultValue($this->oUser->aboutMe);

		$form->addSubmit('submitButton', 'Aktualizovat');

		$form->onSuccess[] = function (Form $form) {
			/** @var $user \Entity\User */
			$user = $form->presenter->context->userMapper->find($form->presenter->user->id);
			$status = $user->update($form->values);
			if ($status === FALSE) {
				$form->presenter->bfm("Omlouváme se, ale nastala chyba");
				$form->presenter->redirect('this');
			}
			$form->presenter->fm(TRUE, 'Informace byly aktualizovány');
			$form->presenter->redirect('this');
		};
		return $form;
	}

	/**
	 * Formulář na upravení kontaktních informací + načtě defaultní hodnoty přihlášeného uživatele
	 * @return \UpdateContactInformation
	 */
	public function createComponentUpdateContactInformation() {
		$form = new UpdateContactInformation($this->context->data);
		$args = array(
			"city"      => $this->oUser->city,
			"icq"       => $this->oUser->icq,
			"skype"     => $this->oUser->skype,
			"telephone" => $this->oUser->telephone
		);
		$form->setDefaults($args);
		$form->onSuccess[] = callback($this, "updateInformationSuccess");
		return $form;
	}

	/**
	 * Aktualizuje uživatelovi informace
	 * @param \Nette\Application\UI\Form $form
	 */
	public function updateInformationSuccess(Form $form) {
		$this->onlyForLoggedIn();
                $values = $form->getValues();
                if (strlen($values->telephone) > 9){
                   $values->telephone = substr($values->telephone, 3);
                }
		$status = $this->oUser->update($values);
		if ($status === FALSE) {
			$this->bfm("Při aktualizování informací došlo k chybě");
			$this->redirect("this");
		}
		$this->fm(TRUE, "Informace byly aktualizovány");
		$this->redirect("this");
	}

	/**
	 * Formulář na přidání starých projektů do 'referencí'
	 * @return our_forms\OldProjectReference
	 */
	public function createComponentAddOldProjectReference() {
		$form = new \our_forms\OldProjectReference();
		$form->onSuccess[] = callback($this, 'addOldProjectReferenceSuccess');
		return $form;
	}

	/**
	 * Zpracuje formulář na přidání starých referencí
	 * @param Nette\Application\UI\Form $form
	 */
	public function addOldProjectReferenceSuccess(Form $form) {
		$values = $form->getValues(TRUE);
		$values['user_id'] = $this->user->id;
		$oldReference = new OldReference($this->context->database);
		$status = $oldReference->create($values);
		$this->fm($status, "Reference byla přidána", "Při přidávání reference došlo k chybě");
		$this->redirect("this");
	}

	/**
	 * Handler na odebrání staré reference
	 * @param int $id
	 */
	public function handleDeleteOldReference($id) {
		$oldReference = new OldReference($this->context->database);
		$status = $oldReference->delete($id, $this->oUser);
		$this->fm($status, "Reference byla odebrána", "Při odebírání reference došlo k chybě");
		$this->redirect("this");
	}

	/**
	 * Formulář na změnu profilové fotky
	 * @return \Nette\Application\UI\Form
	 */
	public function createComponentUpdateProfilePicture() {
		$form = new \our_forms\UpdateProfilePicture();
		$form->onSuccess[] = callback($this, 'updateProfilePictureSuccess');
		return $form;
	}

	/**
	 * Změní profilový obrázek
	 * @param \Nette\Application\UI\Form $form
	 */
	public function updateProfilePictureSuccess(Form $form) {
		// todo :: trošku refaktornout
		$values = $form->getValues();
		$profileImageFolder = "./images/profile/";
		$miniImageFolder = "./images/mini/";
		$thumbnailImageFolder = "./images/thumbnail/";
		// Smazání starých profilovek
		if (file_exists($profileImageFolder . $this->user->getId() . ".png")) {
			unlink($profileImageFolder . $this->user->getId() . ".png");
		}
		if (file_exists($miniImageFolder . $this->user->getId() . ".png")) {
			unlink($miniImageFolder . $this->user->getId() . ".png");
		}
		if (file_exists($thumbnailImageFolder . $this->user->getId() . ".png")) {
			unlink($thumbnailImageFolder . $this->user->getId() . ".png");
		}
		// Vytvoření 3 kopií obrázku s různými velikostmi
		$profileImage = Image::fromFile($values->picture)
			 ->resize(self::PROFILE_PIC_WIDTH, NULL);

		$miniImage = Image::fromFile($values->picture)
			 ->resize(self::MINI_PIC_WIDTH, NULL);

		$thumbNail = Image::fromFile($values->picture)
			 ->resize(self::THUMBNAIL_PIC_WIDTH, NULL);
		// Uložení do složek
		$profileImage->save($profileImageFolder . $this->user->getId() . ".png");
		$miniImage->save($miniImageFolder . $this->user->getId() . ".png");
		$thumbNail->save($thumbnailImageFolder . $this->user->getId() . ".png");
		$this->fm(TRUE, "Profilová fotka byla změněna");
		$this->redirect("this");
	}

	/**
	 * Formulář na změnu hesla
	 * @return Nette\Application\UI\Form
	 */
	public function createComponentChangePassword() {
		$form = new BaseForm();

		$form->addPassword('old', 'Zajdete stávající heslo:')
			 ->addRule(Form::FILLED, 'Vyplňte původní heslo');

		$form->addPassword('pass', 'Nové heslo:')
			 ->setRequired('Zvolte prosím nové heslo')
			 ->addRule(Form::MIN_LENGTH, 'Heslo musí mít minimálně %d znaků', 5);

		$form->addPassword('passwordAgain', 'Heslo znovu')
			 ->setRequired('Zvolte prosím heslo pro kontrolu')
			 ->addRule(Form::EQUAL, 'Hesla se musí schodovat', $form['pass']);

		$form->addSubmit('submitButton', 'Změnit!');

		$form->onSuccess[] = callback($this, 'changePasswordSuccess');
		$form->onValidate[] = callback($this, 'changePasswordValidate');

		return $form;
	}

	/**
	 * Zjistí, zda uživatel zadal dobře původní heslo
	 * @param Nette\Application\UI\Form $form
	 * @return bool
	 */
	public function changePasswordValidate(Form $form) {
		$new = Authenticator::calculateHash($form->values->old);
		if (!$this->oUser->isPasswordSame($new)) {
			$form->addError('Původní hesla se neshodují');
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Nastaví uživateli nové heslo
	 * @param Nette\Application\UI\Form $form
	 */
	public function changePasswordSuccess(Form $form) {
		$status = $this->oUser->update(
			array('pass' => Authenticator::calculateHash($form->values->pass))
		);
		$this->fm($status, 'Heslo bylo změněno');
		$this->redirect('this');
	}

}