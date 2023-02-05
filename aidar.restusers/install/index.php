<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\main\IO\Directory;



class aidar_somemodule extends CModule
{
	public $MODULE_ID;
	public $MODULE_GROUP_RIGHTS;
	public $MODULE_VERSION;
	public $MODULE_VERSION_DATE;
	public $MODULE_NAME;
	public $MODULE_DESCRIPTION;

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		$arModuleVersion = [];
		include(__DIR__ . '/version.php');
		$this->MODULE_ID = 'aidar_restusers';
		$this->MODULE_GROUP_RIGHTS = 'Y';
		$this->MODULE_VERSION = $arModuleVersion['VERSION'];
		$this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
		$this->MODULE_NAME = 'Rest Api Users';
		$this->MODULE_DESCRIPTION = 'Модуль для просмотра и управления пользователей через REST API ';
	}

	/**
	 * Call all install methods.
	 * @returm void
	 */
	public function doInstall()
	{
		global $DB, $APPLICATION;
		if (CheckVersion(ModuleManager::getVersion("main"), "14.00.00")) {

			$this->installFiles();
			$this->installDB();
		}
	}

	/**
	 * Call all uninstall methods, include several steps.
	 * @returm void
	 */
	public function doUninstall()
	{


		$this->uninstallDB(false);
		$this->uninstallFiles();
	}

	/**
	 * Install DB, events, etc.
	 * @return boolean
	 */
	public function installDB()
	{
		global $DB, $APPLICATION;

		// module
		ModuleManager::registerModule($this->MODULE_ID);

		return true;
	}



	/**
	 * Install files.
	 * @return boolean
	 */

	public function InstallFiles()
	{

		CopyDirFiles(
			__DIR__ . "/routes",
			$_SERVER['DOCUMENT_ROOT'] . "/bitrix/routes/",
			true,
			true
		);

		return false;
	}


	/**
	 * Uninstall DB, events, etc.
	 * @param array $arParams Some params.
	 * @return boolean
	 */
	public function uninstallDB($arParams = array())
	{
		global $APPLICATION, $DB;

		// module
		ModuleManager::unregisterModule($this->MODULE_ID);


		return true;
	}

	/**
	 * Uninstall files.
	 * @return boolean
	 */
	public function uninstallFiles(): bool
	{

		Directory::deleteDirectory(
			$_SERVER['DOCUMENT_ROOT'] . "/bitrix/routes"
		);

		return false;
	}

}