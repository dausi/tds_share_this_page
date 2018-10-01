<?php
/**
 * TDS Share This Page add-on controller.
 *
 * Copyright 2018 - TDSystem Beratung & Training - Thomas Dausner (aka dausi)
 */
namespace Concrete\Package\TdsShareThisPage;

use Package;
use BlockType;
use Events;
use AssetList;
use View;

/*
 * FontAwesome Social Media "Share" Icons by Thomas Dausner (aka dausi)
 *
 * based on:
 *
 * FontAwesome Social Media "Vist" Icons by Thomas Dausner (aka dausi)
 * more credits see that add-on.
 */

class Controller extends Package
{

	protected $pkgHandle = 'tds_share_this_page';
    protected $appVersionRequired = '5.7.5.6';
	protected $pkgVersion = '0.9.3';

	public function getPackageName()
	{
        return t('TDS Social Media "Share this page" Icons (EU-GDPR compliant)');
	}

	public function getPackageDescription()
	{
        return t('Add EU-GDPR compliant social media "Share this page" icons on your pages.');
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
			$ph = $this->pkgHandle;

			$al->register('css', $ph.'/form', 'blocks/'.$ph.'/css/form.css', [], $ph);
			$al->registerGroup($ph, [
				['css', $ph.'/form'],
			]);

			$v = View::getInstance();
			$v->requireAsset($ph);
			$v->requireAsset('css', 'font-awesome');

			$script_tag = '<script type="text/javascript">var tds_share_messages = ' . json_encode($this->getMessages()) . '</script>';
			$v->addFooterItem($script_tag);
		});
	}

	public function getMessages()
	{
		return [
			'no_svc_selected'		=> \t('No social media service selected.'),
			'iconmargin_invalid'	=> \t('Icon spacing "%s" is not a valid number'),
		];
	}
	
}
