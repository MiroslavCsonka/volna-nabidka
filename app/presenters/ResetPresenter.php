<?php
use Nette\Mail\Message;

class ResetPresenter extends BasePresenter {

	public function actionDefault($code) {
		$resetter = new \PasswordResetter($this->context->data);
		$result = $resetter->resetPassword($code);
		if ($result !== FALSE) {
			$mail = new Message();
			$mail->setFrom('Volná Nabídka <info@volnanabidka.cz>')
				 ->addTo($result['mail'])
				 ->setSubject('Nové heslo pro www.volnanabidka.cz')
				 ->setBody("Dobrý den,\n
                           vaše nové heslo je: " . $result['pass'] . ".\n
                           Po přihlášení si budete moci heslo změnit v sekci nastavení.
                           Přejeme pěkný den.\n")
				 ->send();
			$this->fm(TRUE, "Vaše nové heslo vám bylo zasláno na mail");
		}
		else {
			$this->bfm("Neplatný kód na změnu hesla");

		}
		$this->redirect("Homepage:");
	}

}
