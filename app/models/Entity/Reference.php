<?php

namespace Entity;

/**
 * Třída pro práci s entitou reference
 */
class Reference extends Base {

	/**
	 * Získá projekt ke kterému se vztahuje reference
	 * @return \Entity\Project
	 */
	public function getProject() {
		if ($this->offsetExists('project')) {
			$project = $this->offsetGet('project');
			if ($project instanceof \Entity\Project) {
				return $project;
			}
		}
		$projectRow = $this->dataSource->ref('Project', 'project_id');
		$project = \Nette\Environment::getContext()->projectMapper->makeInstanceFromId($projectRow->id, $projectRow);
		$this->offsetSet('project', $project);
		return $project;
	}

	/**
	 * Vrátí ownera projektu, ke kterému se vztahuje reference
	 * @return \Entity\User
	 */
	public function getProjectOwner() {
		if ($this->offsetExists('owner') && ($this->offsetGet('owner') instanceof \Entity\User)) {
			return $this->offsetGet('owner');
		}
		$project = $this->getProject();
		$userRow = $project->dataSource->ref('User', 'owner');
		$user = \Nette\Environment::getContext()->userMapper->makeInstanceFromId($userRow->id, $userRow);
		$this->offsetSet('owner', $user);
		return $user;
	}

	/**
	 * Vrátí rating
	 * @return int
	 */
	public function getRating() {
		return $this->offsetGet('rating');
	}

	/**
	 * Vrátí komentář k referenci
	 * @return string
	 */
	public function getReview() {
		return $this->offsetGet('comment');
	}

}
