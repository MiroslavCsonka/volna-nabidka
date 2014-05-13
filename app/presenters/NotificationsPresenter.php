<?php

/**
 * Presenter, který se stará o vykreslování notifikací uživatele
 */
class NotificationsPresenter extends BasePresenter {

	/** @var \Mapper\Notification */
	private $notificationMapper;

	/**
	 * Metoda, která se stará o vykreslení defaultního náhledu na notifikace
	 */
	public function startup() {
		parent::startup();
		$this->onlyForLoggedIn();
		$this->notificationMapper = $this->context->notificationMapper;
	}

	/**
	 * Stará se o vykreslení defaultního templatu
	 */
	public function renderDefault() {
		$this->template->notifications = $this->notificationMapper->getNotificationsByUser($this->oUser);
	}

	/**
	 * Přijme request na označení notifikace za přečtenou
	 * @param int $id ID notifikace
	 */
	public function handleMarkRead($id) {
		$notification = $this->notificationMapper->find($id);
		if ($notification instanceof \Entity\Notification && $notification->isOwner($this->oUser) && $notification->getState() != \Message::DELETED) {
			$res = $notification->markRead();
			$this->fm($res, "Notifikace byla označena za přečtenou", "Notifikaci se nepodařilo označit za přečtenou");
		}
		else {
			$this->bfm($this->context->parameters['notAuthorized']);
		}
		$this->redirect("this");
	}

	/**
	 * Přijme request na označení notifikace za nepřečtenou
	 * @param int $id ID notifikace
	 */
	public function handleMarkUnread($id) {
		$notification = $this->notificationMapper->find($id);
		if ($notification instanceof \Entity\Notification && $notification->isOwner($this->oUser) && $notification->getState() != \Message::DELETED) {
			$res = $notification->markUnread();
			$this->fm($res, "Notifikace byla označena za nepřečtenou", "Notifikaci se nepodařilo označit za nepřečtenou");
		}
		else {
			$this->bfm($this->context->parameters['notAuthorized']);
		}
		$this->redirect("this");
	}

	/**
	 * Přijme request na označení notifikace za smazanou
	 * @param int $id ID notifikace
	 */
	public function handleMarkDeleted($id) {
		$notification = $this->notificationMapper->find($id);
		if ($notification instanceof \Entity\Notification && $notification->isOwner($this->oUser)) {
			$res = $notification->markDeleted();
			$this->fm($res, "Notifikace byla označena za smazanou", "Notifikaci se nepodařilo označit za smazanou");
		}
		else {
			$this->bfm($this->context->parameters['notAuthorized']);
		}
		$this->redirect("this");
	}

}
