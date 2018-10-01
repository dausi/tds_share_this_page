<?php
namespace Concrete\Package\TdsShareThisPage\Block\TdsShareThisPage;

use Concrete\Core\Block\BlockController;
use Config;

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

	public function getBlockUID($b = null)
	{
		$bUID = $this->bID;
        if ($b != null)
		{
			$proxyBlock = $b->getProxyBlock();
			$bUID = $proxyBlock ? $proxyBlock->getBlockID() : $b->bID;
		}
		return $bUID;
    }

	public function getBlockTypeDescription()
    {
        return t('Add EU-GDPR compliant FontAwesome social media share icons on your pages.');
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
		$this->set('iconSize', '20');
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

		$this->view();
    }

    public function view()
    {
		if (gettype($this->mediaList) == "string")
		{	// add from clipboard --> is array already
			$this->mediaList = unserialize($this->mediaList);
		}
		$this->setupMediaList();
    	$this->set('mediaList', $this->mediaList);
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
		$app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
		$req = $app->make(\Concrete\Core\Http\Request::class);
		$url = urlencode($req->getUri());
		
    	$concrete = Config::get('concrete');
    	$version = intval(substr($concrete['version_installed'], 0, 1));

		$sitename = $version < 8 ? Config::get('concrete.site') : $app->make('site')->getSite()->getSiteName();

		$c = $req->getCurrentPage();
        if (is_object($c) && !$c->isError()) {
            $title = $c->getCollectionName();
        } else {
            $title = $sitename;
        }

		$body = rawurlencode($this->titleType == 'personal'
								? t("Check out this article on %s:\n\n%s\n%s" , tc('SiteName', $sitename), $title, urldecode($url))
								: t("Read this article on %s:\n\n%s\n%s" , tc('SiteName', $sitename), $title, urldecode($url))		);
		$subject = rawurlencode($this->titleType == 'personal'
								? t('Thought you\'d enjoy this article.')
								: t('Please notice this article.')	);

    	$mediaListMaster = [
	    	//	name			 fa-					icon color				share address
	    	'Facebook'		=> [ 'fa' => 'facebook',	'icolor' => '#3B5998',	'sa' => "https://www.facebook.com/sharer/sharer.php?u=$url"		],
	    	'GooglePlus'	=> [ 'fa' => 'google-plus',	'icolor' => '#DD4B39',	'sa' => "https://plus.google.com/share?url=$url"				],
	    	'Linkedin'		=> [ 'fa' => 'linkedin',	'icolor' => '#007BB6',	
												'sa' => "https://www.linkedin.com/shareArticle?mini-true&url={$url}&title=".urlencode($title)	],
	    	'Pinterest'		=> [ 'fa' => 'pinterest-p',	'icolor' => '#CB2027',	'sa' => "https://www.pinterest.com/pin/create/button?url=$url"	],
			'Reddit'		=> [ 'fa' => 'reddit',		'icolor' => '#FF4500',	'sa' => "https://www.reddit.com/submit?url={$url}"				],
	    	'Twitter'		=> [ 'fa' => 'twitter',		'icolor' => '#55ACEE',	'sa' => "https://twitter.com/intent/tweet?url=$url"				],
	    	'Xing'			=> [ 'fa' => 'xing',		'icolor' => '#006567',	'sa' => "https://www.xing.com/social_plugins/share?url={$url}"	],
			'Print'			=> [ 'fa' => 'print',		'icolor' => '#696969',	'sa' => 'javascript:window.print();'							],
			'Mail'			=> [ 'fa' => 'envelope',	'icolor' => '#696969',	'sa' => "mailto:?body={$body}&subject={$subject}"				],
    	];

    	if ($version < 8)
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
