<?php
/**
 * TDS Share This Page add-on controller.
 *
 * Copyright 2018 - TDSystem Beratung & Training - Thomas Dausner (aka dausi)
 */
namespace Concrete\Package\TdsShareThisPage;

use Concrete\Core\Package\Package;
use Concrete\Core\Block\BlockType\BlockType;

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
	protected $pkgVersion = '0.9.4';

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

}
