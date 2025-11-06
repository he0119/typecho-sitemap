<?php
namespace TypechoPlugin\Sitemap;

use Typecho\Common;
use Typecho\Date;
use Typecho\Db;
use Typecho\Router;
use Widget\ActionInterface;
use Widget\Base;

if (!defined('__TYPECHO_ROOT_DIR__')) {
	exit;
}

class Action extends Base implements ActionInterface
{
	public function action()
	{
		$pages = $this->db->fetchAll($this->db->select()->from('table.contents')
			->where('table.contents.status = ?', 'publish')
			->where('table.contents.created < ?', $this->options->gmtTime)
			->where('table.contents.type = ?', 'page')
			->order('table.contents.created', Db::SORT_DESC));

		$articles = $this->db->fetchAll($this->db->select()->from('table.contents')
			->where('table.contents.status = ?', 'publish')
			->where('table.contents.created < ?', $this->options->gmtTime)
			->where('table.contents.type = ?', 'post')
			->order('table.contents.created', Db::SORT_DESC));

		$tags = $this->db->fetchAll($this->db->select()->from('table.metas')
			->where('table.metas.type = ?', 'tag')
			->order('table.metas.mid', Db::SORT_DESC));

		header("Content-Type: application/xml");
		echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		echo "<?xml-stylesheet type='text/xsl' href='" . $this->options->pluginUrl . "/Sitemap/sitemap.xsl'?>\n";
		echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
		foreach ($pages as $page) {
			$type = $page['type'];
			$routeExists = (NULL != Router::get($type));
			$page['pathinfo'] = $routeExists ? Router::url($type, $page) : '#';
			$page['permalink'] = Common::url($page['pathinfo'], $this->options->index);

			echo "\t<url>\n";
			echo "\t\t<loc>" . $page['permalink'] . "</loc>\n";
			echo "\t\t<lastmod>" . date('Y-m-d\TH:i:s\Z', $page['modified']) . "</lastmod>\n";
			echo "\t\t<changefreq>always</changefreq>\n";
			echo "\t\t<priority>0.9</priority>\n";
			echo "\t</url>\n";
		}
		foreach ($articles as $article) {
			// 如果加密则跳过
			if ($article['password'])
				continue;

			$type = $article['type'];
			$article['categories'] = $this->db->fetchAll($this->db->select()->from('table.metas')
				->join('table.relationships', 'table.relationships.mid = table.metas.mid')
				->where('table.relationships.cid = ?', $article['cid'])
				->where('table.metas.type = ?', 'category')
				->order('table.metas.order', Db::SORT_ASC));
			$article['category'] = urlencode(current(array_column($article['categories'], 'slug')));
			$article['slug'] = urlencode($article['slug']);
			$article['date'] = new Date($article['created']);
			$article['year'] = $article['date']->year;
			$article['month'] = $article['date']->month;
			$article['day'] = $article['date']->day;
			$routeExists = NULL != Router::get($type);
			$article['pathinfo'] = $routeExists ? Router::url($type, $article) : '#';
			$article['permalink'] = Common::url($article['pathinfo'], $this->options->index);

			echo "\t<url>\n";
			echo "\t\t<loc>" . $article['permalink'] . "</loc>\n";
			echo "\t\t<lastmod>" . date('Y-m-d\TH:i:s\Z', $article['modified']) . "</lastmod>\n";
			echo "\t\t<changefreq>always</changefreq>\n";
			echo "\t\t<priority>0.7</priority>\n";
			echo "\t</url>\n";
		}
		foreach ($tags as $tag) {
			$type = $tag['type'];
			$routeExists = NULL != Router::get($type);

			// 如果该标签下的文章均是加密的，则跳过这个标签
			$tag['skip'] = True;
			$tag_pages = $this->db->fetchAll($this->db->select()->from('table.contents')
				->join('table.relationships', 'table.relationships.cid = table.contents.cid')
				->where('table.relationships.mid = ?', $tag['mid']));
			foreach ($tag_pages as $tag_page)
				if (!$tag_page['password'])
					$tag['skip'] = False;
			if ($tag['skip'])
				continue;

			$tag['pathinfo'] = $routeExists ? Router::url($type, $tag) : '#';
			$tag['permalink'] = Common::url($tag['pathinfo'], $this->options->index);
			echo "\t<url>\n";
			echo "\t\t<loc>" . $tag['permalink'] . "</loc>\n";
			echo "\t\t<changefreq>always</changefreq>\n";
			echo "\t\t<priority>0.5</priority>\n";
			echo "\t</url>\n";
		}

		echo "</urlset>";
	}
}
