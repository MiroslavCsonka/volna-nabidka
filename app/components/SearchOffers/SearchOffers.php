<?php

use Nette\Application\UI\Control;
use Nette\Database\Connection;

/**
 * Komponenta, která se stará o vykreslení vyhledávání projektů
 */
class SearchOffers extends Control {

	private $db;

	/** @var \Mapper\Project */
	private $projectMapper;

	private $default;

	public function __construct ($defaultValues,
								 Connection $db,
								 \Mapper\Project $projectMapper,
								 \Nette\ComponentModel\IContainer $parent = null,
								 $name = null
	) {
		parent::__construct($parent, $name);
		$this->db = $db;
		$this->default = $defaultValues;
		$this->projectMapper = $projectMapper;
		$this['projectSearch']->setDefaults($defaultValues);
	}

	public function render () {
		$this->template->setFile(dirname(__FILE__) . '/template.latte');
		$perProjectWage = $this->projectMapper->getWageRange(\Entity\Project::REWARD_PER_PROJECT);
		$perHourWage = $this->projectMapper->getWageRange(\Entity\Project::REWARD_PER_HOUR);
		if (isset( $this->default['type'] )) {
			switch ($this->default['type']) {
				case 'perHour' :
				{
					$defaultPerHourWage = array( 'min' => $this->default['min'], 'max' => $this->default['max'] );
					break;
				}
				case 'perProject':
				{
					$defaultPerProjectWage = array( 'min' => $this->default['min'], 'max' => $this->default['max'] );
					break;
				}
				default:
					{
					throw new \Nette\UnexpectedValueException( "Not support value in type of wage '$this->default[type]'" );
					}
			}
		}
		$this->template->defaultPerProjectWage = isset( $defaultPerProjectWage ) ? $defaultPerProjectWage : $perProjectWage;

		$this->template->defaultPerHourWage = isset( $defaultPerHourWage ) ? $defaultPerHourWage : $perHourWage;
		$this->template->perProjectWage = $perProjectWage;
		$this->template->perHourWage = $perHourWage;
		$this->template->render();
	}

	public function createComponentProjectSearch () {
		$form = new \our_forms\ProjectSearch( new \Data( $this->db ) );
		return $form;
	}

	/**
	 * @return \our_forms\ProjectSearch|NULL
	 */
	public function getForm () {
		return $this->getComponent('projectSearch');
	}

}