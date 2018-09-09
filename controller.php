<?php
/**
 * TDS Share This Page add-on controller.
 *
 * Copyright 2018 - TDSystem Beratung & Training - Thomas Dausner (aka dausi)
 */
namespace Concrete\Package\TdsShareThisPage;

use AssetList;
use Events;
use View;
use BlockType;

class Controller extends \Concrete\Core\Package\Package
{

	protected $pkgHandle = 'tds_share_this_page';
	protected $appVersionRequired = '8.1.0';
	protected $pkgVersion = '0.9.1';

	public function getPackageDescription()
	{
		return t('Add EU-GDPR compliant two-click social link share buttons to your page');
	}

	public function getPackageName()
	{
		return t('TDS Share This Page');
	}

 	public function install()
	{
		$pkg = parent::install();

        $blk = BlockType::getByHandle($this->pkgHandle);
        if (!is_object($blk)) {
            BlockType::installBlockType($this->pkgHandle, $pkg);
        }
 	}

 	public function uninstall()
	{
		$pkg = parent::uninstall();
 	}

	public function on_start()
	{
		Events::addListener('on_before_render', function($event) {

			$al = AssetList::getInstance();
			$al->register('css', 'view', 'blocks/' .$this->pkgHandle . '/view.css', [], $this->pkgHandle);
			$al->registerGroup('share', [
				['css', 'view'],
			]);
			$v = View::getInstance();
			$v->requireAsset('share');
		});
	}
}
