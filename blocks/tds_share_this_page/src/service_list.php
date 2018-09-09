<?php
namespace Concrete\Package\TdsShareThisPage\Block\TdsShareThisPage\Src;

class ServiceList
{
    protected static function getServices()
    {
        return [
			['facebook',	\t('Share this page at %s', 'Facebook'),	'facebook'			],
            ['twitter',		\t('Share this page at %s', 'Twitter'),		'twitter'			],
            ['linkedin',	\t('Share this page at %s', 'LinkedIn'),	'linkedin-square'	],
            ['reddit',		\t('Share this page at %s', 'Reddit'),		'reddit'			],
            ['pinterest',	\t('Share this page at %s', 'Pinterest'),	'pinterest'			],
            ['google_plus', \t('Share this page at %s', 'Google Plus'),	'google-plus'		],
			['xing',		\t('Share this page at %s', 'Xing'),		'xing'				],
            ['print',		\t('Print this page'),						'print'				],
            ['email',		\t('Send page recommendation by email'),	'envelope'			],
		];
    }

    public static function get()
    {
        $services = static::getServices();
        $return = array();
        foreach ($services as $serviceArray) {
            $o = new Service($serviceArray[0], $serviceArray[1], $serviceArray[2]);
            $return[] = $o;
        }

        return $return;
    }
}
