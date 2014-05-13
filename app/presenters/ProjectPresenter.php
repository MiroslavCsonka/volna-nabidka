<?php

use Nette\Application\UI\Form;

/**
 * Presenter pokrývající práci s projektem
 */
class ProjectPresenter extends BasePresenter {

   /** @var \Entity\Project Aktuální projekt, o který se tento presenter stará */
   private $project;

   /** @var \Mapper\Project Mapper nad instancemi Project */
   private $projectMapper;

   /** @var \Mapper\User Mapper nad instancemi User */
   private $userMapper;

   public function __construct(\Nette\DI\Container $context) {
      parent::__construct($context);
      $this->projectMapper = $this->context->projectMapper;
      $this->userMapper = $this->context->userMapper;
   }

   /**
    * Akce, která se provede před renderDetail(). Setne do $this->project aktuální projekt
    * Rozhodne, které view pro projekt setnout. Uloží do private proměnné objekt Projektu
    * @param int    $id
    * @param string $name
    */
   public function actionDetail($id, $name) {
      $project = $this->projectMapper->find($id);
      $this->project = $project;
      if ($project instanceof \Entity\Project) {
         if ($project->isLocked()) {
            $this->setView("locked");
         }
         if ($project->isFinished()) {
            $this->setView("finished");
         }
      }
   }

   /**
    * Obslouží render detailu projektu
    * @param int    $id   Id projektu
    * @param string $name Jméno projektu webalized
    */
   public function renderDetail($id, $name) {
      $project = $this->project;
      if (!($project instanceof \Entity\Project)) {
		$this->redirect("Project:notFound");
      }
	  if ($project->location !== NULL) {
            $this->template->projectLocation = $this->context->cityMapper->find($project->location);
         } else {
            $this->template->projectLocation = NULL;
         }
      $this->template->comments = $project->getMessages();
      $this->template->project = $project;
      $this->template->owner = $project->getOwner();
   }

   /**
    * Ověři, zda má uživatel oprávnění modifikovat projekt
    * @param int $id Project id
    */
   public function actionModify($id) {
      $this->onlyForLoggedIn();
      $this->project = $this->projectMapper->find($id);
      if ($this->project instanceof \Entity\Project) {
         if (!$this->project->isOwner($this->oUser)) {
            $this->bfm($this->context->parameters['notAuthorized']);
            $this->redirect("Homepage:default");
         }
         if ($this->project->isLocked()) {
            $this->bfm($this->context->parameters['notAuthorized']);
            $this->redirect("Project:detail", $id);
         }
      }
   }

   /**
    * Vyrenderuje modify projektu
    * @param int $id
    */
   public function renderModify($id) {
      $predefinedCategories = array_keys($this->project->getCategories());
      $dateTime = DateTime::createFromFormat('Y-m-d H:i:s', $this->project->deadline);
      $deadline = $dateTime->format('d-m-Y');
      $predefined = array(
          "name" => $this->project->name,
          "categories" => $predefinedCategories,
          "location" => $this->project->location,
          "scale" => $this->project->scale,
          "pricing" => $this->project->pricing,
          "reward" => $this->project->reward,
          "description" => $this->project->description,
          "deadline" => $deadline
      );
      $this['modify']->setDefaults($predefined);
      $this->template->project = $this->project;
   }

   /**
    * Odebere project a všechny věci napojené na něj ze systému
    * @param int $projectId
    */
   public function handleRemoveProject($projectId) {
      if ($this->user->isAllowed('project', 'remove')) {
         $project = $this->projectMapper->find($projectId);
         if ($project instanceof \Entity\Project) {
            $status = $project->delete();
            if ($status) {
               $this->fm(TRUE, 'Projekt byl smazán');
               $this->redirect('Homepage:');
            } else {
               $this->bfm('Project se nepovedlo odebrat');
            }
         } else {
            $this->bfm("Project s číslem $projectId nebyl nalezen");
         }
      } else {
         $this->bfm('Nemáte právo mazat projekt !');
      }
      $this->redirect('this');
   }

   /**
    * Stará se o vykreslení uzamčeného projektu
    * @param int    $id   Id projektu
    * @param string $name Jméno projektu
    */
   public function renderLocked($id, $name) {
      $project = $this->project;
      if ($project instanceof \Entity\Project) {
         $this->template->project = $project;
         $this->template->comments = $project->getMessages();
         $this->template->owner = $project->getOwner();
         $this->template->worker = $this->userMapper->getWorkerOf($project);
         if ($project->location !== NULL) {
            $this->template->projectLocation = $this->context->cityMapper->find($project->location);
         } else {
            $this->template->projectLocation = NULL;
         }
      } else {
         $this->redirect("Project:notFound");
      }
   }

   public function renderFinished($id, $name) {
      $project = $this->project;
      if ($project instanceof \Entity\Project) {
         $this->template->project = $project;
         $this->template->comments = $project->getMessages();
         $this->template->owner = $project->getOwner();
         $this->template->employee = $project->getEmployee();
         if ($project->location !== NULL) {
            $this->template->projectLocation = $this->context->cityMapper->find($project->location);
         } else {
            $this->template->projectLocation = NULL;
         }
      } else {
         $this->redirect("Project:notFound");
      }
   }

   /**
    *  Vytvoří komponentu \our_forms\newProject
    * @return \our_forms\NewProject
    */
   public function createComponentModify() {
      $form = new \our_forms\NewProject($this->context->data);
      $form->onSuccess[] = callback($this, "modifyProject");
      return $form;
   }

   /**
    * Zpracuje modifikaci projektu
    * @param \Nette\Application\UI\Form $form
    */
   public function modifyProject(Form $form) {
      $values = $form->getValues(TRUE);
      $status = $this->project->updateExtended($values);
      $this->fm($status, "Základní informace projektu byly aktualizovány", "Nepodařilo se aktualizovat váš projekt");
      $this->redirect("Project:detail", $this->project->id);
   }

   /**
    * Vytvoří komponentu na přidávání komentářů pod projekt
    * @return \our_forms\AddProjectMessage
    */
   public function createComponentAddMessage() {
      $form = new \our_forms\AddProjectMessage();
      $form->onSuccess[] = callback($this, "addMessageSuccess");
      return $form;
   }

   /**
    * Přidá zprávu pod projekt
    * @param \Nette\Application\UI\Form $form
    */
   public function addMessageSuccess(Form $form) {
      $this->onlyForLoggedIn();
      $values = $form->getValues();
      $message = new Message($values->message, date("Y-m-d H:i:s"));
      $project = $this->projectMapper->find($this->getParameter("id"));
      $status = $project->addMessage($message, $this->oUser);
      $this->fm($status, "Vaše zpráva byla úspěšně přidána", "Vaši zprávu se nepodařilo přidat");
      if ($this->isAjax()) {
         $this->invalidateControl('flashMessage');
         $this->invalidateControl('messages');
      } else {
         $this->redirect("this");
      }
   }

   /**
    * Přihlásí uživatele k projektu
    * @param int $projectId (Id projektu ke kterému se uživatel přihlašuje)
    */
   public function handleSignToProject($projectId) {
      $project = $this->project;
      if ($project instanceof \Entity\Project && !$project->isOwner($this->oUser) && $this->oUser->canSignToProject($projectId) && $project->isAlive() && !$project->isExpired()) {
         if ($this->oUser->signToProject($project)) {
            $this->flashMessage("Byl jste úspěšně přihlášen k projektu", Form::SUCCESS);
         } else {
            $this->flashMessage("Nepodařilo se přihlásit vás k projektu", Form::ERROR);
         }
      } else {
         $this->bfm($this->context->parameters['notAuthorized']);
      }
      if ($this->isAjax()) {
         $this->invalidateControl("buttons");
         $this->invalidateControl("flashMessage");
      } else {
         $this->redirect('this');
      }
   }

   /**
    * Ohlásí uživatele od projektu
    * @param int $projectId
    */
   public function handleSignOutFromProject($projectId) {
      if ($this->oUser->isSignedToProject($projectId)) {
         $status = $this->oUser->signOutFromProject($projectId);
         $this->fm($status, "Byl jste úspěšně odhlášen z projektu", "Nepovedlo se Vás odebrat z projektu");
      } else {
         $this->bfm($this->context->parameters['notAuthorized']);
      }
      if ($this->isAjax()) {
         $this->invalidateControl("buttons");
         $this->invalidateControl("flashMessage");
      } else {
         $this->redirect("this");
      }
   }

   /**
    * Odebere uživatele z ucházejících se o projekt a zakáže se mu přihlásit znovu
    * @param int $userId
    * @param int $projectId
    */
   public function handleRevokeUser($userId, $projectId) {
      $project = $this->project;
      if ($project->isOwner($this->oUser)) {
         $res = $project->revokeUser($userId);
         $this->fm($res, "Uživatel byl vyřazen z projektu", "Uživatele se nepodařilo vyřadit z projektu");
         if ($this->isAjax()) {
            $this->invalidateControl("flashMessage");
            $this->invalidateControl("attendees");
         } else {
            $this->redirect("this");
         }
      } else {
         $this->bfm($this->context->parameters['notAuthorized']);
      }
   }

   /**
    * Zpracuje uzamykání projektu a odešle notifikace uživatelům (uživateli, s kterým je práce zahájena a ostatním uchazečům)
    * @param int $userId    ID Usera se kterým chceme uzamknout projekt
    */
   public function handleLock($userId) {
      $user = $this->userMapper->find($userId);
      $project = $this->project;
      if ($user instanceof \Entity\User && $project instanceof \Entity\Project) {
         if ($project->isOwner($this->oUser) && $user->isSignedToProject($project->id) && !$project->isLocked()) {
            $project->registerObserver($user);
            $messageText = "Byl jste vybrán jako pracovník projektu
                           <a href='" . $this->link('Project:detail', $project->id) . "'>" . $project->name . "</a>. Gratulujeme!";
            $message = new \Message($messageText, new \DateTime());
            $res = $project->lockWith($user, $message);
            // Pokud byl projekt úspěšně zahájen, nastavíme flash message a odešleme ostatním uchazečům notifikace
            if ($res) {
               $messageText2 = "Bohužel jste nebyl vybrán jako pracovník na projektu
                                 <a href='" . $this->link('Project:detail', $project->id) . "'>" . $project->name . "</a>. Je nám líto.";
               $message2 = new \Message($messageText2, new \DateTime());
               $attendees = $project->getAttendees();
               // Revoke ostatních uchazečů a zaslání notifikací
               foreach ($attendees as $attendee) {
                  $project->revokeUser($attendee->id);
                  $project->registerObserver($attendee);
               }
               $project->notifyObservers($message2);
               $this->fm(TRUE, "Projekt byl úspěšně zahájen s uživatelem " . $user->name);
            }
         } else {
            $this->bfm($this->context->parameters['notAuthorized']);
         }
      } else {
         $this->bfm($this->context->parameters['notAuthorized']);
      }
      $this->redirect("this");
   }

   public function createComponentFinishProject() {
      $form = new our_forms\FinishProject();
      $form->onSuccess[] = callback($this, "finishSuccess");
      return $form;
   }

   public function finishSuccess(Form $form) {
      $this->onlyForLoggedIn();
      $project = $this->projectMapper->find($this->getParameter("id"));
      if ($project instanceof \Entity\Project && $project->isOwner($this->oUser) && $project->isLocked()) {
         $values = $form->getValues();
         // Zpráva pro pracovníka
         $message = new Message(
                         "Uživatel <a href='" . $this->link('User:default', $this->oUser->id) . "'>" . $this->oUser->name . "</a>
                          označil projekt <a href='" . $this->link("Project:detail", $project->id) . "'>" . $project->name . "</a>,
                          na kterém jste pracoval, za dokončený.
                          Ohodnocení najdete na vašem profilu v sekci reference.",
                         new DateTime()
         );
         $worker = $this->userMapper->getWorkerOf($project);
         $project->registerObserver($worker);
         $res = $project->finish($values, $worker, $message);
         $this->fm($res, "Projekt byl úspěšně ukončen", "Projekt se nepodařilo ukončit");
      } else {
         $this->bfm($this->context->parameters['notAuthorized']);
      }
      $this->redirect("this");
   }

}

