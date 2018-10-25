<?php
/**
 * TDS Share This Page add-on block controller.
 *
 * Copyright 2018 - TDSystem Beratung & Training - Thomas Dausner (aka dausi)
 */
namespace Concrete\Package\TdsShareThisPage\Block\TdsShareThisPage;

use Concrete\Core\Block\BlockController;
use Concrete\Core\View\View;
use Concrete\Core\Support\Facade\Config;
use Concrete\Core\Asset\AssetList;

class Controller extends BlockController
{
    protected $btInterfaceWidth = 600;
    protected $btInterfaceHeight = 720;
    protected $btCacheBlockOutput = true;
    protected $btTable = 'btTdsShareThisPage';
    protected $btDefaultSet = 'social';

	protected $iconStyles = '
		.ccm-block-share-this-page.block-%b% .icon-container .svc.activated span { %activeAttrs% }
		.ccm-block-share-this-page.block-%b% .social-icon:hover { %hoverAttrs% }
		.ccm-block-share-this-page.block-%b% .social-icon-color { color: #f8f8f8; background: %iconColor%; }
		.ccm-block-share-this-page.block-%b% .social-icon-color-inverse { color: %iconColor%; }
		.ccm-block-share-this-page.block-%b% .social-icon.activated, .ccm-block-share-this-page .social-icon.activated:hover { %activeAttrs% }
		.ccm-block-share-this-page.block-%b% .social-icon {	float: left; margin: 0 calc(%iconMargin%px / 2);
															height: %iconSize%px; width: %iconSize%px; border-radius: %borderRadius%px; }
		.ccm-block-share-this-page.block-%b% .social-icon i.fa { display: block; font-size: calc(%iconSize%px *.6); text-align: center; 
																 width: 100%; padding-top: calc((100% - 1em) / 2); }
	';
	protected $mediaList = [];
	protected $bUID = 0;

	public function getBlockTypeDescription()
    {
        return t('Add EU-GDPR compliant social media "Share this page" icons on your pages.');
    }

    public function getBlockTypeName()
    {
        return t('TDS Social Media Share Icons');
    }

    public function add()
    {
		$this->set('linkTarget', '_self');
		$this->set('align', 'left');
		$this->set('iconStyle', 'logo');
		$this->set('iconColor', '#00f');	/* blue */
		$this->set('iconSize', '20');
		$this->set('hoverIcon', '#ccc');	/* pale gray */
		$this->set('activeIcon', '#ff0');	/* yellow */
		$this->set('iconMargin', '0');
		$this->edit();
    }

    public function edit()
    {
		$this->set('targets', [
	        '_blank'	=> t('a new window or tab'),
	        '_self'		=> t('the same frame as it was clicked (this is default)'),
	        '_parent'	=> t('the parent frame'),
	        '_top'		=> t('the full body of the window'),
        ]);
		$this->set('orientation', [
	        'left'	=> t('left'),
	        'right'	=> t('right'),
        ]);
		$this->set('iconStyleList', [
			'logo'			=> t('logo'),
			'logo-inverse'	=> t('logo inverse'),
			'color'			=> t('color'),
			'color-inverse'	=> t('color inverse')
		]);
		$this->set('titleTextTemplate', t('Share this page at %s'));
		$this->set('bubbleTextTemplate', t('You now have enabled the button to share this page at "%s".'.
									' Next time you click at the button the page at "%s" shall be opened.'.
									' On opening your personal browser data is transmitted to the provider "%s".'.
									' To avoid this you can disable the checkbox at left (and the enabled button).'));

		$al = AssetList::getInstance();
		$ph = 'tds_share_this_page';
		$al->register('css', $ph.'/form', 'blocks/'.$ph.'/css/form.css', [], $ph);
		$al->registerGroup($ph, [
			['css', $ph.'/form'],
		]);
		$v = View::getInstance();
		$v->requireAsset($ph);
		
		$msgs = [
			'no_svc_selected'		=> \t('No social media service selected.'),
			'iconmargin_invalid'	=> \t('Icon spacing "%s" is not a valid number'),
		];
		$script_tag = '<script type="text/javascript">var tds_share_messages = ' . json_encode($msgs) . '</script>';
		$v->addFooterItem($script_tag);

		$this->view();
    }

    public function view()
    {
		if (gettype($this->mediaList) == "string")
		{	// add from clipboard --> is array already
			$this->mediaList = unserialize($this->mediaList);
		}
		$this->app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
		$this->setupMediaList();
    	$this->set('mediaList', $this->mediaList);
		$this->set('bUID', $this->app->make('helper/validation/identifier')->getString(8));
	}

	public function registerViewAssets($outputContent = '')
	{
		$this->requireAsset('font-awesome');
		$this->requireAsset('javascript', 'jquery');
	}
	public function save($args)
    {
    	$args['iconSize']	= intval($args['iconSize']);
        $args['iconMargin']	= intval($args['iconMargin']);
		$args['mediaList']	= serialize($args['mediaList']);

        parent::save($args);
    }

    public function getIconStyles($bUID)
    {
    	return str_replace(	'%b%', $bUID, $this->iconStyles );
	}

    public function getIconStylesExpanded($bUID)
    {
		$this->bUID = $bUID;
    	$borderRadius = $this->iconShape == 'round' ? $this->iconSize / 2: 0;
		$hoverAttrs = $this->hoverIcon != '' ? "background: $this->hoverIcon;" : '';
		$activeAttrs = $this->activeIcon != '' ? "background-color: $this->activeIcon;" : '';
		return '
<style id="iconStyles-' . $bUID . '" type="text/css">
	'. str_replace(	['%b%',	'%iconColor%',    '%iconMargin%',    '%iconSize%',    '%borderRadius%', '%hoverAttrs%', '%activeAttrs%'	],
					[ $bUID, $this->iconColor, $this->iconMargin, $this->iconSize, $borderRadius,    $hoverAttrs,     $activeAttrs	], $this->iconStyles ). '
</style>';
    }

    public function getMediaList()
    {
    	return $this->mediaList;
    }

    private function setupMediaList()
    {
		$req = $this->app->make(\Concrete\Core\Http\Request::class);
		$url = urlencode($req->getUri());
		
		$sitename = version_compare(APP_VERSION, '8.0', '>=') ? $this->app->make('site')->getSite()->getSiteName() : Config::get('concrete.site');

		$c = $req->getCurrentPage();
        if (is_object($c) && !$c->isError()) {
            $title = $c->getCollectionName();
        } else {
            $title = $sitename;
        }

		$body = rawurlencode(t("Check out this article on %s:\n\n%s\n%s" , tc('SiteName', $sitename), $title, urldecode($url)));
		$subject = rawurlencode(t('Please notice this article.'));

    	$mediaListMaster = [
	    	//	name			 fa-					icon color				share address
	    	'Facebook'		=> [ 'fa' => 'facebook',	'icolor' => '#3B5998',	'sa' => "https://www.facebook.com/sharer/sharer.php?u=$url"		],
	    	'Linkedin'		=> [ 'fa' => 'linkedin',	'icolor' => '#007BB6',	
												'sa' => "https://www.linkedin.com/shareArticle?mini-true&url={$url}&title=".urlencode($title)	],
	    	'Pinterest'		=> [ 'fa' => 'pinterest-p',	'icolor' => '#CB2027',	'sa' => "https://www.pinterest.com/pin/create/button?url=$url"	],
			'Reddit'		=> [ 'fa' => 'reddit',		'icolor' => '#FF4500',	'sa' => "https://www.reddit.com/submit?url={$url}"				],
	    	'Twitter'		=> [ 'fa' => 'twitter',		'icolor' => '#55ACEE',	'sa' => "https://twitter.com/intent/tweet?url=$url"				],
			'Xing'			=> [ 'fa' => 'xing',		'icolor' => '#006567',	'sa' => "https://www.xing.com/spi/shares/new?url={$url}"		],
			'Print'			=> [ 'fa' => 'print',		'icolor' => '#696969',	'sa' => 'javascript:window.print();'							],
			'Mail'			=> [ 'fa' => 'envelope',	'icolor' => '#696969',	'sa' => "mailto:?body={$body}&subject={$subject}"				],
    	];

    	if (version_compare(APP_VERSION, '8.0', '<'))
    	{
    		$mediaListMaster['Pinterest']['fa'] = 'pinterest';
    	}

		$colors = strpos($this->iconStyle, 'logo') === FALSE;
		$inverse = strpos($this->iconStyle,'inverse') !== FALSE;
		$blockClass = '	.ccm-block-share-this-page.block-%b%';
		foreach ($mediaListMaster as $key => $mProps)
    	{
			$this->iconStyles .= $blockClass . ' .social-icon-' . $key . ' { color: #ffffff; background: ' . $mProps['icolor'] . '; }'."\n";
			$this->iconStyles .= $blockClass . ' .social-icon-' . $key . '-inverse { color: ' . $mProps['icolor'] . '; }'."\n";
			$iconClass = 'social-icon  social-icon-';
			$iconClass .= $colors ? 'color' : $key;
			$iconClass .= $inverse ? '-inverse' : '';
		
			if (empty($this->mediaList[$key]))
			{
				$this->mediaList[$key] = [];
			}
			$props = $this->mediaList[$key];
			$title = h(str_replace('%s', $key, $this->titleText));
			if ( $key == 'Print' || $key == 'Mail' )
			{
				$title = $key == 'Print' ? t('Print this page') : t('Share this page by email');
				$iconClass .= ' local';
			}
			$trg = $this->linkTarget;
			$icon = '<span class="' . $iconClass . '" data-key="' . $key . '" data-href="'. $mProps['sa'] .'" data-target="' . $trg . '">'.
						'<i class="fa fa-' . $mProps['fa'] . '" title="' . $title . '"></i>'.
					'</span>';

			if ($props['checked'])
			{
				$this->mediaList[$key]['html'] = '
					<div class="svc '. $mProps['fa'] . '">
					   ' . $icon . '
				   </div>';
			}

			$this->mediaList[$key]['iconHtml'] = $icon;
			$this->mediaList[$key]['sa'] = $mProps['sa'];
    	}
    }
	
}
