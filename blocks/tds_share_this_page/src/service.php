<?php
namespace Concrete\Package\TdsShareThisPage\Block\TdsShareThisPage\Src;

use Concrete\Core\Page\Page;
use Concrete\Core\Sharing\SocialNetwork\Service as SocialNetworkService;
use Concrete\Package\TdsShareThisPage\Block\TdsShareThisPage\Src\ServiceList;
use Request;
use URL;
use Concrete\Core\Support\Facade\Application;

class Service extends SocialNetworkService
{
    public static function getByHandle($ssHandle)
    {
        $services = ServiceList::get();
        foreach ($services as $s) {
            if ($s->getHandle() == $ssHandle) {
                return $s;
            }
        }
    }

    public function getServiceLink(Page $c = null)
    {
        if (!is_object($c)) {
            $req = Request::getInstance();
            $c = $req->getCurrentPage();
            $url = urlencode($req->getUri());
        } elseif (!$c->isError()) {
            $url = urlencode(URL::to($c));
        }

		$app = Application::getFacadeApplication();
        if (is_object($c) && !$c->isError()) {
            $title = $c->getCollectionName();
        } else {
            $title = $app->make('site')->getSite()->getSiteName();
        }

        if (!empty($url)) {
            switch ($this->getHandle()) {
                case 'facebook':
                    return "https://www.facebook.com/sharer/sharer.php?u=$url";
                case 'twitter':
                    return "https://twitter.com/intent/tweet?url=$url";
                case 'linkedin':
                    return "https://www.linkedin.com/shareArticle?mini-true&url={$url}&title=".urlencode($title);
                case 'pinterest':
                    return "https://www.pinterest.com/pin/create/button?url=$url";
                case 'google_plus':
                    return "https://plus.google.com/share?url=$url";
                case 'reddit':
                    return "https://www.reddit.com/submit?url={$url}";
				case 'xing':
					return "https://www.xing.com/social_plugins/share?url={$url}";
                case 'print':
                    return "javascript:window.print();";
                case 'email':
                    $body = rawurlencode(t("Check out this article on %s:\n\n%s\n%s", tc('SiteName', $app->make('site')->getSite()->getSiteName()), $title, urldecode($url)));
                    $subject = rawurlencode(t('Thought you\'d enjoy this article.'));

                    return "mailto:?body={$body}&subject={$subject}";
            }
        }
    }
}
