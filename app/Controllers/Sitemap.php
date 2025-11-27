<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\ItemModel;
use App\Models\CategoryModel;
use App\Models\LocationModel;
use App\Models\LanguageModel;
use App\Models\SourceModel;

class Sitemap extends Controller
{
  private $maxPerSitemap = 50000;

  protected $categoryModel;
  protected $locationModel;
  protected $itemModel;
  protected $languageModel;
  protected $sourceModel;
  private $languages;
  private $models;
  private $xhtml;

  public function __construct()
  {
    $this->categoryModel = new CategoryModel();
    $this->locationModel = new LocationModel();
    $this->itemModel = new ItemModel();
    $this->languageModel = new languageModel();
    $this->languages = $this->languageModel->getActiveList();
    $this->models = [
      'categories' => $this->categoryModel,
      'locations'  => $this->locationModel,
      'items'      => $this->itemModel,
    ];
    $this->xhtml = 'http://www.w3.org/1999/xhtml';
  }

  public function index()
  {
    $sitemaps = [];
    $sitemaps[] = base_url("sitemap/pages-sitemap.xml");

    foreach ($this->models as $type => $model) {
      $count = $model->countAllResults();
      $pages = ceil($count / $this->maxPerSitemap);

      for ($i = 1; $i <= $pages; $i++) {
        $sitemaps[] = base_url("sitemap/{$type}-sitemap-{$i}.xml");
      }
    }

    // Tạo sitemap index
    $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></sitemapindex>');

    foreach ($sitemaps as $url) {
      $sm = $xml->addChild('sitemap');
      $sm->addChild('loc', $url);
      $sm->addChild('lastmod', date(DATE_W3C));
    }

    return $this->response
      ->setHeader('Content-Type', 'application/xml; charset=UTF-8')
      ->setBody($xml->asXML());
  }

  public function pages()
  {
    $xml = new \SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\"?><urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:xhtml=\"{$this->xhtml}\"></urlset>");
    $this->addMultiLangUrl($xml, base_url(), $this->xhtml, date(DATE_W3C), 'daily', '1.0');
    $this->addMultiLangUrl($xml, base_url('search'), $this->xhtml, date(DATE_W3C), 'daily', '1.0');

    return $this->response
      ->setHeader('Content-Type', 'application/xml; charset=UTF-8')
      ->setBody($xml->asXML());
  }

  public function detail($type, $page = 1)
  {
    $limit  = $this->maxPerSitemap;
    $offset = ($page - 1) * $limit;
    $model = $this->models[$type];
    if (!$model) {
      throw new \CodeIgniter\Exceptions\PageNotFoundException();
    }

    $records = $model->getPaged($limit, $offset);

    $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml"></urlset>');

    if ($type == 'items') {
      $this->sourceModel = new SourceModel();
      $srcLangs = [];
    }

    foreach ($records as $record) {
      if ($type == 'items') {
        $srcId = $record['source_id'] ?? 0;
        $src = $this->sourceModel->find($srcId);
        if (!empty($srcLangs[(string)$srcId])) {
          $languages = $srcLangs[(string)$srcId];
        } else {
          $languages = $srcLangs[(string)$srcId] = sourceLanguages($this->languages, $src);
        }
      } else {
        $languages = $this->languages;
      }

      $this->addMultiLangUrl($xml, $model->getLink($record), $this->xhtml, date(DATE_W3C, strtotime($record['updated_at'] ?? $record['created_at'])), 'weekly', '0.8', $languages);
    }

    return $this->response
      ->setHeader('Content-Type', 'application/xml; charset=UTF-8')
      ->setBody($xml->asXML());
  }

  private function addMultiLangUrl($xml, $defaultUrl, $xhtml, $lastmod, $changefreq, $priority, $languages = null)
  {
    $urlChild = $xml->addChild('url');
    $urlChild->addChild('loc', $defaultUrl);

    $link = $urlChild->addChild('link', null, $xhtml);
    $link->addAttribute('rel', 'alternate');
    $link->addAttribute('hreflang', 'x-default');
    $link->addAttribute('href', $defaultUrl);


    foreach ($languages ?? $this->languages as $lang) {
      $link = $urlChild->addChild('link', null, $xhtml);
      $link->addAttribute('rel', 'alternate');
      $link->addAttribute('hreflang', $lang['code']);
      $link->addAttribute('href', language_link($lang['locale'], $defaultUrl));
    }

    $urlChild->addChild('lastmod', $lastmod);
    $urlChild->addChild('changefreq', $changefreq);
    $urlChild->addChild('priority', $priority);
  }
}
